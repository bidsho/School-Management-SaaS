<?php

namespace App\Livewire\School\Teachers;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TeacherIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $employmentStatus = '';

    /**
     * Reset pagination when the search changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when the employment status changes.
     */
    public function updatedEmploymentStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Clear all search and filter values.
     */
    public function clearFilters(): void
    {
        $this->reset(['search', 'employmentStatus']);

        $this->resetPage();
    }

    /**
     * Get the authenticated user's school ID.
     */
    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user, 401);

        // Super Admin must use platform routes.
        abort_if(
            $user->isSuperAdmin(),
            403,
            'Super Admin accounts must use platform routes.'
        );

        abort_unless(
            $user->school_id,
            403,
            'Your account is not assigned to a school.'
        );

        return (int) $user->school_id;
    }

    /**
     * Build the school-scoped teacher query.
     */
    private function teacherQuery(): Builder
    {
        $schoolId = $this->schoolId();

        return Teacher::query()
            ->where('school_id', $schoolId);
    }

    /**
     * Render the teacher management page.
     */
    public function render()
    {
        $schoolId = $this->schoolId();

        $search = trim($this->search);

        $allowedStatuses = [
            'active',
            'inactive',
            'on_leave',
            'terminated',
        ];

        // Only apply recognized employment statuses.
        $status = in_array(
            $this->employmentStatus,
            $allowedStatuses,
            true
        )
            ? $this->employmentStatus
            : '';

        $teachers = $this->teacherQuery()
            ->with('user:id,email')
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('staff_number', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', function (Builder $query) use ($status) {
                $query->where('employment_status', $status);
            })
            ->latest('id')
            ->paginate(10);

        // Total number of teachers registered at this school.
        $totalTeachers = Teacher::query()
            ->where('school_id', $schoolId)
            ->count();

        return view('livewire.school.teachers.teacher-index', [
            'teachers' => $teachers,
            'totalTeachers' => $totalTeachers,
        ])->layout('components.layouts.school', [
            'title' => 'Manage Teachers',
        ]);
    }
}
