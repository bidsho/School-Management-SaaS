<?php

namespace App\Livewire\School\Exams;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class ResultsApprovalIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $statusFilter = 'submitted';

    #[Locked]
    public ?int $returningExamId = null;

    #[Locked]
    public ?int $viewingExamId = null;

    public string $reviewNote = '';

    /**
     * Only school administrators and principals can review results.
     */
    public function mount(): void
    {
        $this->authorizeReviewer();
    }

    protected function authorizeReviewer(): void
    {
        $user = auth()->user();

        abort_unless(
            $user
            && $user->hasAnyRole(['School Admin', 'Principal'])
            && $user->school_id,
            403,
            'You are not authorized to review examination results.'
        );
    }

    /**
     * Return the authenticated reviewer's school ID.
     */
    protected function schoolId(): int
    {
        $this->authorizeReviewer();

        return (int) auth()->user()->school_id;
    }

    /**
     * Base query: never retrieve examinations from another school.
     */
    protected function examQuery(): Builder
    {
        return Exam::query()
            ->where('school_id', $this->schoolId());
    }

    /**
     * Find an examination belonging to this school.
     */
    protected function findSchoolExam(int $examId): Exam
    {
        return $this->examQuery()->findOrFail($examId);
    }

    /**
     * Search and filter the examination list.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Open the examination's student score sheet.
     */
    public function viewScores(int $examId): void
    {
        $exam = $this->findSchoolExam($examId);

        abort_unless(
            in_array(
                $exam->results_status,
                ['submitted', 'approved', 'returned'],
                true
            ),
            422,
            'This examination does not have results available for review.'
        );

        $this->viewingExamId = $exam->id;
    }

    public function closeScores(): void
    {
        $this->viewingExamId = null;
    }

    /**
     * Approve submitted results.
     */
    public function approveResults(int $examId): void
    {
        $this->authorizeReviewer();

        DB::transaction(function () use ($examId): void {
            $exam = $this->examQuery()
                ->whereKey($examId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($exam->results_status !== 'submitted') {
                throw ValidationException::withMessages([
                    'review' => 'Only submitted results can be approved.',
                ]);
            }

            $exam->forceFill([
                'results_status' => 'approved',
                'results_reviewed_by' => auth()->id(),
                'results_reviewed_at' => now(),
                'results_review_note' => null,
            ])->save();
        });

        $this->closeScores();

        session()->flash(
            'success',
            'Examination results have been approved successfully.'
        );
    }

    /**
     * Open the return-for-correction form.
     */
    public function openReturnForm(int $examId): void
    {
        $exam = $this->findSchoolExam($examId);

        if ($exam->results_status !== 'submitted') {
            throw ValidationException::withMessages([
                'review' => 'Only submitted results can be returned for correction.',
            ]);
        }

        $this->returningExamId = $exam->id;
        $this->reviewNote = '';
        $this->resetValidation();
    }

    /**
     * Return submitted results with a mandatory correction note.
     */
    public function returnResults(): void
    {
        $this->authorizeReviewer();

        if (!$this->returningExamId) {
            throw ValidationException::withMessages([
                'reviewNote' => 'Select an examination to return.',
            ]);
        }

        $validated = $this->validate([
            'reviewNote' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],
        ], [
            'reviewNote.required' => 'Enter a correction note for the teacher.',
            'reviewNote.min' => 'The correction note must contain at least 5 characters.',
            'reviewNote.max' => 'The correction note cannot exceed 2,000 characters.',
        ]);

        DB::transaction(function () use ($validated): void {
            $exam = $this->examQuery()
                ->whereKey($this->returningExamId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($exam->results_status !== 'submitted') {
                throw ValidationException::withMessages([
                    'review' => 'Only submitted results can be returned for correction.',
                ]);
            }

            $exam->forceFill([
                'results_status' => 'returned',
                'results_reviewed_by' => auth()->id(),
                'results_reviewed_at' => now(),
                'results_review_note' => trim($validated['reviewNote']),
            ])->save();
        });

        $this->returningExamId = null;
        $this->reviewNote = '';
        $this->viewingExamId = null;

        $this->resetValidation();

        session()->flash(
            'success',
            'Results have been returned to the teacher with your correction note.'
        );
    }

    /**
     * Close the return form.
     */
    public function closeReturnForm(): void
    {
        $this->returningExamId = null;
        $this->reviewNote = '';

        $this->resetValidation();
    }

    public function render()
    {
        $allowedStatuses = ['submitted', 'approved', 'returned'];

        if (!in_array($this->statusFilter, $allowedStatuses, true)) {
            $this->statusFilter = 'submitted';
        }

        $exams = $this->examQuery()
            ->with([
                'academicSession',
                'term',
                'classRoom',
                'subject',
                'resultsSubmitter',
                'resultsReviewer',
            ])
            ->withCount('scores')
            ->where('results_status', $this->statusFilter)
            ->when(trim($this->search) !== '', function (Builder $query): void {
                $search = '%' . trim($this->search) . '%';

                $query->where(function (Builder $nested) use ($search): void {
                    $nested
                        ->where('name', 'like', $search)
                        ->orWhereHas('classRoom', function (Builder $class) use ($search): void {
                            $class->where('name', 'like', $search);
                        })
                        ->orWhereHas('subject', function (Builder $subject) use ($search): void {
                            $subject->where('name', 'like', $search);
                        })
                        ->orWhereHas('academicSession', function (Builder $session) use ($search): void {
                            $session->where('name', 'like', $search);
                        })
                        ->orWhereHas('term', function (Builder $term) use ($search): void {
                            $term->where('name', 'like', $search);
                        });
                });
            })
            ->latest('results_submitted_at')
            ->paginate(10);

        $viewingExam = null;

        if ($this->viewingExamId) {
            $viewingExam = $this->examQuery()
                ->with([
                    'academicSession',
                    'term',
                    'classRoom',
                    'subject',
                    'scores.student',
                ])
                ->find($this->viewingExamId);
        }

        $returningExam = null;

        if ($this->returningExamId) {
            $returningExam = $this->findSchoolExam($this->returningExamId);
        }

        return view('livewire.school.exams.results-approval-index', [
            'exams' => $exams,
            'viewingExam' => $viewingExam,
            'returningExam' => $returningExam,
        ])->layout('components.layouts.school');
    }
}
