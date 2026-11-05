<?php

namespace App\Livewire\School\Enrollments;

use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Concerns\UsesPortalLayout;
class EnrollmentIndex extends Component
{
    use WithPagination, UsesPortalLayout;

    public string $search = '';
    public string $status = '';

    protected string $paginationTheme = 'bootstrap';

    /**
     * Reset pagination when searching.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when filtering by status.
     */
    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Clear all filters.
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = '';

        $this->resetPage();
    }

    /**
     * Delete an enrollment belonging to the current school.
     */
    public function deleteEnrollment(int $enrollmentId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);

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

        abort_unless(
            $user->can('manage students'),
            403,
            'You do not have permission to delete enrollments.'
        );

        // Always restrict the record to the authenticated user's school.
        $enrollment = Enrollment::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->whereKey($enrollmentId)
            ->firstOrFail();

        // Delete only the enrollment, not the student.
        $enrollment->delete();

        session()->flash(
            'success',
            'Student enrollment deleted successfully.'
        );

        $this->resetPage();
    }

    /**
     * Render the enrollment list.
     */
    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);

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

        $schoolId = $user->school_id;

        /*
        |--------------------------------------------------------------------------
        | Enrollment Query
        |--------------------------------------------------------------------------
        */

        $query = Enrollment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with([
                'student',
                'academicSession',
                'term',
                'classRoom',
                'section',
            ]);

        // Search by student, admission number, class or section.
        if (trim($this->search) !== '') {
            $search = trim($this->search);

            $query->where(function ($builder) use ($search) {
                $builder->whereHas(
                    'student',
                    function ($studentQuery) use ($search) {
                        $studentQuery->where(function ($studentFields) use ($search) {
                            $studentFields
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('admission_number', 'like', "%{$search}%");
                        });
                    }
                )
                ->orWhereHas(
                    'classRoom',
                    function ($classQuery) use ($search) {
                        $classQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                )
                ->orWhereHas(
                    'section',
                    function ($sectionQuery) use ($search) {
                        $sectionQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
            });
        }

        // Filter by admission status.
        if ($this->status !== '') {
            $query->where(
                'admission_status',
                $this->status
            );
        }

        $enrollments = $query
            ->latest()
            ->paginate(10);

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $statsQuery = Enrollment::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        $totalEnrollments = (clone $statsQuery)->count();

        $activeEnrollments = (clone $statsQuery)
            ->where('admission_status', 'active')
            ->count();

        $pendingEnrollments = (clone $statsQuery)
            ->where('admission_status', 'pending')
            ->count();

        return view(
            'livewire.school.enrollments.enrollment-index',
            [
                'enrollments' => $enrollments,
                'totalEnrollments' => $totalEnrollments,
                'activeEnrollments' => $activeEnrollments,
                'pendingEnrollments' => $pendingEnrollments,
            ]
        )->layout($this->portalLayout(), [
            'title' => 'Student Enrollments',
        ]);
    }
}
