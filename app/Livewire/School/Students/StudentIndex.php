<?php

namespace App\Livewire\School\Students;

use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;
use App\Livewire\Concerns\UsesPortalLayout;

class StudentIndex extends Component
{
    use WithPagination, UsesPortalLayout;

    public string $search = '';

    public string $status = '';

    #[Locked]
    public ?int $deleteStudentId = null;

    #[Locked]
    public string $deleteStudentName = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'status',
        ]);

        $this->resetPage();
    }

    /**
     * Open the delete confirmation modal.
     */
    public function confirmDelete(int $studentId): void
    {
        $user = Auth::user();

        abort_unless($user, 403);

        abort_if(
            $user->isSuperAdmin(),
            403,
            'Super Admin cannot delete students from the school portal.'
        );

        abort_unless($user->school_id, 403);

        abort_unless(
            $user->can('delete students'),
            403,
            'You do not have permission to delete students.'
        );

        // Always restrict the lookup to the authenticated user's school.
        $student = Student::query()
            ->where('school_id', $user->school_id)
            ->findOrFail($studentId);

        $this->deleteStudentId = $student->id;

        $this->deleteStudentName = $this->getStudentName($student);

        $this->resetValidation('deleteStudent');
    }

    /**
     * Close the delete confirmation modal.
     */
    public function cancelDelete(): void
    {
        $this->deleteStudentId = null;

        $this->deleteStudentName = '';

        $this->resetValidation('deleteStudent');
    }

    /**
     * Delete a student only when no protected records exist.
     */
    public function deleteStudent(): void
    {
        $user = Auth::user();

        abort_unless($user, 403);

        abort_if(
            $user->isSuperAdmin(),
            403,
            'Super Admin cannot delete students from the school portal.'
        );

        abort_unless($user->school_id, 403);

        abort_unless(
            $user->can('delete students'),
            403,
            'You do not have permission to delete students.'
        );

        if ($this->deleteStudentId === null) {
            $this->addError(
                'deleteStudent',
                'Please select a student to delete.'
            );

            return;
        }

        $studentId = $this->deleteStudentId;
        $schoolId = $user->school_id;

        try {
            $studentName = DB::transaction(function () use (
                $studentId,
                $schoolId
            ) {
                // Re-fetch and verify ownership before deletion.
                $student = Student::query()
                    ->where('school_id', $schoolId)
                    ->lockForUpdate()
                    ->findOrFail($studentId);

                /*
                 * Preserve students who already have academic,
                 * attendance, examination or financial records.
                 */
                $hasProtectedRecords =
                    $student->enrollments()->exists()
                    || $student->attendanceRecords()->exists()
                    || $student->examScores()->exists()
                    || $student->invoices()->exists()
                    || $student->payments()->exists();

                if ($hasProtectedRecords) {
                    throw new \DomainException(
                        'This student has enrollment, attendance, examination, invoice, or payment records. Deactivate the student instead to preserve school records.'
                    );
                }

                $name = $this->getStudentName($student);

                // Remove only this school's parent/student pivot links.
                DB::table('parent_student')
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->delete();

                $student->delete();

                return $name;
            });

            $this->cancelDelete();

            $this->resetPage();

            session()->flash(
                'success',
                "Student {$studentName} was deleted successfully."
            );
        } catch (\DomainException $exception) {
            $this->addError(
                'deleteStudent',
                $exception->getMessage()
            );
        } catch (Throwable $exception) {
            Log::error('Student deletion failed.', [
                'student_id' => $studentId,
                'school_id' => $schoolId,
                'user_id' => $user->id,
                'exception' => $exception->getMessage(),
            ]);

            $this->addError(
                'deleteStudent',
                'The student could not be deleted. The record may have related data. Please try again or contact your administrator.'
            );
        }
    }

    /**
     * Format the student's full name.
     */
    private function getStudentName(Student $student): string
    {
        return trim(implode(' ', array_filter([
            $student->first_name,
            $student->middle_name,
            $student->last_name,
        ])));
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 403);

        abort_if($user->isSuperAdmin(), 403);

        abort_unless($user->school_id, 403);

        $students = Student::query()
            ->where('school_id', $user->school_id)

            ->when(
                trim($this->search) !== '',
                function (Builder $query) {
                    $search = trim($this->search);

                    $query->where(function (Builder $subQuery) use ($search) {
                        $subQuery
                            ->where('admission_number', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                }
            )

            ->when(
                $this->status !== '',
                fn (Builder $query) => $query->where(
                    'status',
                    $this->status
                )
            )

            ->orderByDesc('id')
            ->paginate(10);

        return view(
            'livewire.school.students.student-index',
            [
                'students' => $students,
            ]
        )->layout(
            $this->portalLayout(),
            [
                'title' => 'Students',
            ]
        );
    }
}
