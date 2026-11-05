<?php

namespace App\Livewire\SchoolAdmin;

use App\Models\AttendanceTermSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceSubmissions extends Component
{
    use WithPagination;

    public string $statusFilter = 'pending';
    public string $adminRemarks = '';
    public $reviewingSubmissionId = null;

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    private function authorizeSchoolAdmin(): void
    {
        $user = Auth::user();

        abort_unless($user, 401);

        abort_unless(
            $user->hasAnyRole(['School Admin', 'Principal']),
            403,
            'You are not authorized to review attendance submissions.'
        );

        abort_unless(
            $user->school_id,
            403,
            'Your account is not assigned to a school.'
        );
    }

    public function approve(int $submissionId): void
    {
        $this->authorizeSchoolAdmin();

        $this->validate([
            'adminRemarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = Auth::user();

        $submission = AttendanceTermSubmission::query()
            ->where('school_id', $user->school_id)
            ->whereKey($submissionId)
            ->where('status', 'pending')
            ->firstOrFail();

        $submission->update([
            'status' => 'approved',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'admin_remarks' => $this->adminRemarks ?: null,
        ]);

        $this->reset(['adminRemarks', 'reviewingSubmissionId']);

        session()->flash('success', 'Attendance report approved successfully.');
    }

    public function returnForCorrection(int $submissionId): void
    {
        $this->authorizeSchoolAdmin();

        $this->validate([
            'adminRemarks' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $user = Auth::user();

        $submission = AttendanceTermSubmission::query()
            ->where('school_id', $user->school_id)
            ->whereKey($submissionId)
            ->where('status', 'pending')
            ->firstOrFail();

        $submission->update([
            'status' => 'returned',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'admin_remarks' => $this->adminRemarks,
        ]);

        $this->reset(['adminRemarks', 'reviewingSubmissionId']);

        session()->flash(
            'success',
            'The report has been returned to the teacher with your feedback.'
        );
    }

    public function render()
    {
        $this->authorizeSchoolAdmin();

        $user = Auth::user();

        $query = AttendanceTermSubmission::query()
            ->where('school_id', $user->school_id)
            ->with([
                'teacher.user',
                'classRoom',
                'section',
                'academicSession',
                'term',
                'submitter',
                'reviewer',
            ])
            ->latest('submitted_at');

        if (in_array($this->statusFilter, [
            'pending',
            'approved',
            'returned',
        ], true)) {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.school-admin.attendance-submissions', [
            'submissions' => $query->paginate(15),
        ])->layout('components.layouts.school', [
            'title' => 'Attendance Submissions',
        ]);
    }
}