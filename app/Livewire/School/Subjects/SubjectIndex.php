<?php

namespace App\Livewire\School\Subjects;

use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SubjectIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    protected string $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = '';

        $this->resetPage();
    }

    public function deleteSubject(int $subjectId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        abort_unless(
            $user->can('manage subjects'),
            403,
            'You do not have permission to delete subjects.'
        );

        $subject = Subject::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->whereKey($subjectId)
            ->firstOrFail();

        // Preserve subjects referenced by other academic records.
        if (
            $subject->teacherAssignments()->exists()
            || $subject->exams()->exists()
            || $subject->examScores()->exists()
            || $subject->assignments()->exists()
        ) {
            session()->flash(
                'error',
                'This subject has related academic records and cannot be deleted. Deactivate it instead.'
            );

            return;
        }

        $subject->delete();

        session()->flash('success', 'Subject deleted successfully.');

        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = $user->school_id;

        $query = Subject::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        if (trim($this->search) !== '') {
            $search = trim($this->search);

            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        $subjects = $query
            ->withCount([
                'teacherAssignments',
                'exams',
                'assignments',
            ])
            ->orderBy('name')
            ->paginate(10);

        $statsQuery = Subject::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        $totalSubjects = (clone $statsQuery)->count();

        $activeSubjects = (clone $statsQuery)
            ->where('status', 'active')
            ->count();

        $inactiveSubjects = (clone $statsQuery)
            ->where('status', 'inactive')
            ->count();

        return view('livewire.school.subjects.subject-index', [
            'subjects' => $subjects,
            'totalSubjects' => $totalSubjects,
            'activeSubjects' => $activeSubjects,
            'inactiveSubjects' => $inactiveSubjects,
        ])->layout('components.layouts.school', [
            'title' => 'Subjects',
        ]);
    }
}
