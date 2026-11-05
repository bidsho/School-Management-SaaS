<?php

namespace App\Livewire\Parent\Attendance;

use App\Models\Attendance;
use App\Models\SchoolParent;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.parent')]
#[Title('Student Attendance')]
class AttendanceIndex extends Component
{
    #[Locked]
    public int $parentId;

    public ?int $studentId = null;

    public string $fromDate = '';

    public string $toDate = '';

    public string $status = 'all';

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless($user->hasRole('Parent'), 403);
        abort_unless($user->school_id, 403);

        $parent = $user->parentProfile()
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $this->parentId = (int) $parent->id;

        $firstChild = $parent->students()
            ->wherePivot('school_id', $user->school_id)
            ->orderBy('students.first_name')
            ->first();

        $this->studentId = $firstChild
            ? (int) $firstChild->id
            : null;
    }

    /**
     * Resolve the parent's profile again using the authenticated user.
     */
    private function parentProfile(): SchoolParent
    {
        $user = Auth::user();

        abort_unless($user && $user->hasRole('Parent'), 403);
        abort_unless($user->school_id, 403);

        return $user->parentProfile()
            ->whereKey($this->parentId)
            ->where('school_id', $user->school_id)
            ->firstOrFail();
    }

    /**
     * Only return children linked to this parent in this school.
     */
    private function childrenQuery(): Builder
    {
        $parent = $this->parentProfile();

        return Student::query()
            ->whereIn(
                'students.id',
                $parent->students()
                    ->wherePivot(
                        'school_id',
                        Auth::user()->school_id
                    )
                    ->select('students.id')
            );
    }

    public function updatedStudentId(): void
    {
        $this->validateSelectedChild();
    }

    private function validateSelectedChild(): void
    {
        if ($this->studentId === null) {
            return;
        }

        abort_unless(
            $this->childrenQuery()
                ->whereKey($this->studentId)
                ->exists(),
            404
        );
    }

    public function applyFilters(): void
    {
        $this->validate([
            'fromDate' => ['nullable', 'date'],
            'toDate' => [
                'nullable',
                'date',
                'after_or_equal:fromDate',
            ],
            'status' => [
                'required',
                'in:all,present,absent,late,excused',
            ],
        ], [
            'toDate.after_or_equal' =>
                'The end date must be on or after the start date.',
        ]);

        $this->validateSelectedChild();
    }

    public function resetFilters(): void
    {
        $this->fromDate = '';
        $this->toDate = '';
        $this->status = 'all';
    }

    private function attendanceQuery(): Builder
    {
        $this->validateSelectedChild();

        $query = Attendance::query()
            ->with(['classRoom', 'section', 'term', 'academicSession'])
            ->where('student_id', $this->studentId);

        if ($this->fromDate !== '') {
            $query->whereDate('attendance_date', '>=', $this->fromDate);
        }

        if ($this->toDate !== '') {
            $query->whereDate('attendance_date', '<=', $this->toDate);
        }

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function render()
    {
        $children = $this->childrenQuery()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $attendance = collect();
        $summary = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
            'percentage' => 0,
        ];

        if ($this->studentId !== null) {
            $baseQuery = Attendance::query()
                ->where('student_id', $this->studentId);

            if ($this->fromDate !== '') {
                $baseQuery->whereDate(
                    'attendance_date',
                    '>=',
                    $this->fromDate
                );
            }

            if ($this->toDate !== '') {
                $baseQuery->whereDate(
                    'attendance_date',
                    '<=',
                    $this->toDate
                );
            }

            $summary['total'] = (clone $baseQuery)->count();

            foreach (['present', 'absent', 'late', 'excused'] as $value) {
                $summary[$value] = (clone $baseQuery)
                    ->where('status', $value)
                    ->count();
            }

            // Present and late both count as attended.
            $summary['percentage'] = $summary['total'] > 0
                ? round(
                    (($summary['present'] + $summary['late'])
                        / $summary['total']) * 100,
                    1
                )
                : 0;

            $attendance = $this->attendanceQuery()
                ->orderByDesc('attendance_date')
                ->orderByDesc('id')
                ->paginate(15);
        }

        return view('livewire.parent.attendance.attendance-index', [
            'children' => $children,
            'attendance' => $attendance,
            'summary' => $summary,
        ]);
    }
}
