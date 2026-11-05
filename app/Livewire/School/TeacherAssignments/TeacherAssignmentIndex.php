<?php

namespace App\Livewire\School\TeacherAssignments;

use App\Models\TeacherAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TeacherAssignmentIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user && $user->school_id, 403);

        return (int) $user->school_id;
    }

    private function assignmentsQuery(): Builder
    {
        return TeacherAssignment::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->with([
                'teacher',
                'subject',
                'classRoom',
                'section',
                'academicSession',
                'term',
            ])
            ->when(trim($this->search) !== '', function (Builder $query) {
                $search = trim($this->search);

                $query->where(function (Builder $query) use ($search) {
                    $query->whereHas('teacher', function (Builder $teacher) use ($search) {
                        $teacher->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('staff_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('subject', fn (Builder $q) =>
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%")
                    )
                    ->orWhereHas('classRoom', fn (Builder $q) =>
                        $q->where('name', 'like', "%{$search}%")
                    )
                    ->orWhereHas('section', fn (Builder $q) =>
                        $q->where('name', 'like', "%{$search}%")
                    )
                    ->orWhereHas('academicSession', fn (Builder $q) =>
                        $q->where('name', 'like', "%{$search}%")
                    )
                    ->orWhereHas('term', fn (Builder $q) =>
                        $q->where('name', 'like', "%{$search}%")
                    );
                });
            });
    }

    public function deleteAssignment(int $assignmentId): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->can('manage teacher assignments'),
            403
        );

        $assignment = TeacherAssignment::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->findOrFail($assignmentId);

        $assignment->delete();

        session()->flash('success', 'Teacher assignment deleted successfully.');
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $baseQuery = TeacherAssignment::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        return view('livewire.school.teacher-assignments.teacher-assignment-index', [
            'assignments' => $this->assignmentsQuery()
                ->latest('id')
                ->paginate(10),

            'totalAssignments' => (clone $baseQuery)->count(),

            'totalTeachers' => (clone $baseQuery)
                ->distinct()
                ->count('teacher_id'),

            'totalSubjects' => (clone $baseQuery)
                ->distinct()
                ->count('subject_id'),
        ])->layout('components.layouts.school');
    }
}
