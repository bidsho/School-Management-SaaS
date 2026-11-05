<?php

namespace App\Livewire\School\Attendance;

use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Section;
use App\Models\Term;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $date = '';
    public string $status = '';
    public string $class_room_id = '';
    public string $section_id = '';
    public string $academic_session_id = '';
    public string $term_id = '';

    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user && $user->school_id, 403);

        return (int) $user->school_id;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDate(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingClassRoomId(): void
    {
        $this->section_id = '';
        $this->resetPage();
    }

    public function updatingSectionId(): void
    {
        $this->resetPage();
    }

    public function updatingAcademicSessionId(): void
    {
        $this->term_id = '';
        $this->resetPage();
    }

    public function updatingTermId(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'date',
            'status',
            'class_room_id',
            'section_id',
            'academic_session_id',
            'term_id',
        ]);

        $this->resetPage();
    }

    private function attendanceQuery(): Builder
    {
        $schoolId = $this->schoolId();

        return Attendance::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with([
                'student',
                'classRoom',
                'section',
                'academicSession',
                'term',
                'marker',
            ])
            ->when(trim($this->search) !== '', function (Builder $query) {
                $search = trim($this->search);

                $query->where(function (Builder $query) use ($search) {
                    $query->whereHas('student', function (Builder $student) use ($search) {
                        $student->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('admission_number', 'like', "%{$search}%");
                    });
                });
            })
            ->when($this->date, fn (Builder $query) =>
                $query->whereDate('attendance_date', $this->date)
            )
            ->when($this->status, fn (Builder $query) =>
                $query->where('status', $this->status)
            )
            ->when($this->class_room_id, fn (Builder $query) =>
                $query->where('class_room_id', $this->class_room_id)
            )
            ->when($this->section_id, fn (Builder $query) =>
                $query->where('section_id', $this->section_id)
            )
            ->when($this->academic_session_id, fn (Builder $query) =>
                $query->where('academic_session_id', $this->academic_session_id)
            )
            ->when($this->term_id, fn (Builder $query) =>
                $query->where('term_id', $this->term_id)
            );
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $baseQuery = Attendance::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        return view('livewire.school.attendance.attendance-index', [
            'attendances' => $this->attendanceQuery()
                ->orderByDesc('attendance_date')
                ->orderByDesc('id')
                ->paginate(15),

            'totalRecords' => (clone $baseQuery)->count(),

            'presentCount' => (clone $baseQuery)
                ->where('status', 'present')
                ->count(),

            'absentCount' => (clone $baseQuery)
                ->where('status', 'absent')
                ->count(),

            'lateCount' => (clone $baseQuery)
                ->where('status', 'late')
                ->count(),

            'classes' => ClassRoom::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderBy('name')
                ->get(),

            'sections' => Section::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->when($this->class_room_id, fn ($query) =>
                    $query->where('class_room_id', $this->class_room_id)
                )
                ->orderBy('name')
                ->get(),

            'sessions' => AcademicSession::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderByDesc('starts_at')
                ->get(),

            'terms' => Term::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->when($this->academic_session_id, fn ($query) =>
                    $query->where('academic_session_id', $this->academic_session_id)
                )
                ->orderBy('starts_at')
                ->get(),
        ])->layout('components.layouts.school');
    }
}
