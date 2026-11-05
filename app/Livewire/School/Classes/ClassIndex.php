<?php

namespace App\Livewire\School\Classes;

use App\Models\ClassRoom;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ClassIndex extends Component
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
        $this->reset(['search', 'status']);
        $this->resetPage();
    }

    public function deleteClass(int $classId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $class = ClassRoom::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->whereKey($classId)
            ->firstOrFail();

        // Never delete a class containing related academic records.
        if (
            $class->sections()->exists()
            || $class->enrollments()->exists()
            || $class->teacherAssignments()->exists()
            || $class->exams()->exists()
        ) {
            session()->flash(
                'error',
                'This class cannot be deleted because it has associated sections, enrollments, teacher assignments, or exams.'
            );

            return;
        }

        $class->delete();

        session()->flash('success', 'Class deleted successfully.');

        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if(
            $user->isSuperAdmin(),
            403,
            'Super Admin accounts must use platform routes.'
        );
        abort_unless($user->school_id, 403);

        $schoolId = (int) $user->school_id;

        $query = ClassRoom::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        if (trim($this->search) !== '') {
            $search = trim($this->search);

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('level', 'like', "%{$search}%");
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        $classes = $query
            ->withCount('sections')
            ->latest()
            ->paginate(10);

        $statsQuery = ClassRoom::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        return view('livewire.school.classes.class-index', [
            'classes' => $classes,
            'totalClasses' => (clone $statsQuery)->count(),
            'activeClasses' => (clone $statsQuery)
                ->where('status', 'active')
                ->count(),
            'inactiveClasses' => (clone $statsQuery)
                ->where('status', 'inactive')
                ->count(),
        ])->layout('components.layouts.school', [
            'title' => 'Classes',
        ]);
    }
}
