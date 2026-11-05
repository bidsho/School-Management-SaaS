<?php

namespace App\Livewire\School\Sections;

use App\Models\ClassRoom;
use App\Models\Section;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SectionIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $classFilter = '';

    protected string $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingClassFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status', 'classFilter']);
        $this->resetPage();
    }

    public function deleteSection(int $sectionId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $section = Section::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->whereKey($sectionId)
            ->firstOrFail();

        if (
            $section->enrollments()->exists()
            || $section->teacherAssignments()->exists()
        ) {
            session()->flash(
                'error',
                'This section cannot be deleted because it has enrollments or teacher assignments.'
            );

            return;
        }

        $section->delete();

        session()->flash('success', 'Section deleted successfully.');

        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = (int) $user->school_id;

        $query = Section::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with('classRoom')
            ->withCount(['enrollments', 'teacherAssignments']);

        if (trim($this->search) !== '') {
            $search = trim($this->search);

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhereHas('classRoom', function ($classQuery) use ($search) {
                        $classQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->classFilter !== '') {
            $query->where('class_room_id', $this->classFilter);
        }

        $sections = $query->latest()->paginate(10);

        $statsQuery = Section::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        $classes = ClassRoom::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.school.sections.section-index', [
            'sections' => $sections,
            'classes' => $classes,
            'totalSections' => (clone $statsQuery)->count(),
            'activeSections' => (clone $statsQuery)
                ->where('status', 'active')->count(),
            'inactiveSections' => (clone $statsQuery)
                ->where('status', 'inactive')->count(),
        ])->layout('components.layouts.school', [
            'title' => 'Sections',
        ]);
    }
}
