<?php

namespace App\Livewire\School\Promotions;

use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\StudentPromotion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class PromotionReviewIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $status = 'submitted';

    public string $reviewNote = '';

    public ?int $toAcademicSessionId = null;

    #[Locked]
    public ?int $selectedPromotionId = null;

    public bool $showReviewModal = false;

    public function mount(): void
    {
        $this->authorizeReviewer();
    }

    private function authorizeReviewer(): void
    {
        abort_unless(auth()->check(), 403);

        abort_unless(
            auth()->user()->hasAnyRole(['School Admin', 'Principal']),
            403,
            'Only the School Admin or Principal can review promotion recommendations.'
        );

        abort_unless(
            auth()->user()->school_id,
            403,
            'Your account is not linked to a school.'
        );
    }

    private function schoolId(): int
    {
        $this->authorizeReviewer();

        return (int) auth()->user()->school_id;
    }

    private function promotionQuery(): Builder
    {
        return StudentPromotion::query()
            ->where('school_id', $this->schoolId())
            ->with([
                'student',
                'fromEnrollment',
                'fromAcademicSession',
                'fromClassRoom',
                'toAcademicSession',
                'toClassRoom',
                'recommender',
                'reviewer',
            ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedToAcademicSessionId(): void
    {
        $this->resetValidation('toAcademicSessionId');
    }

    public function viewPromotion(int $promotionId): void
    {
        $promotion = $this->promotionQuery()
            ->whereKey($promotionId)
            ->firstOrFail();

        $this->selectedPromotionId = (int) $promotion->id;

        $this->reviewNote = '';
        $this->toAcademicSessionId = $promotion->to_academic_session_id
            ? (int) $promotion->to_academic_session_id
            : null;

        $this->resetValidation();
        $this->showReviewModal = true;
    }

    public function closeReview(): void
    {
        $this->showReviewModal = false;
        $this->selectedPromotionId = null;
        $this->reviewNote = '';
        $this->toAcademicSessionId = null;

        $this->resetValidation();
    }

    public function approvePromotion(int $promotionId): void
    {
        $this->authorizeReviewer();

        $validated = $this->validate([
            'toAcademicSessionId' => [
                'required',
                'integer',
            ],
            'reviewNote' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'toAcademicSessionId.required' =>
                'Select the destination academic session before approving.',
            'reviewNote.max' =>
                'The approval note cannot exceed 2000 characters.',
        ]);

        DB::transaction(function () use ($promotionId, $validated): void {
            $schoolId = $this->schoolId();

            $promotion = StudentPromotion::query()
                ->where('school_id', $schoolId)
                ->whereKey($promotionId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($promotion->status !== StudentPromotion::STATUS_SUBMITTED) {
                throw ValidationException::withMessages([
                    'reviewNote' =>
                        'Only submitted recommendations can be approved.',
                ]);
            }

            if (! in_array($promotion->decision, [
                StudentPromotion::DECISION_PROMOTE,
                StudentPromotion::DECISION_REPEAT,
            ], true)) {
                throw ValidationException::withMessages([
                    'reviewNote' =>
                        'This recommendation needs a final promote or repeat decision before approval.',
                ]);
            }

            // The destination session must belong to this school.
            $targetSession = AcademicSession::query()
                ->where('school_id', $schoolId)
                ->whereKey($validated['toAcademicSessionId'])
                ->first();

            if (! $targetSession) {
                throw ValidationException::withMessages([
                    'toAcademicSessionId' =>
                        'Select a valid academic session belonging to your school.',
                ]);
            }

            // Confirm the source session belongs to the same school.
            $sourceSession = AcademicSession::query()
                ->where('school_id', $schoolId)
                ->whereKey($promotion->from_academic_session_id)
                ->first();

            if (! $sourceSession) {
                throw ValidationException::withMessages([
                    'toAcademicSessionId' =>
                        'The source academic session could not be verified.',
                ]);
            }

            // Prevent selecting the source session or an earlier session
            // when the relevant dates are available.
            if (
                $sourceSession->ends_at &&
                $targetSession->starts_at &&
                $targetSession->starts_at <= $sourceSession->ends_at
            ) {
                throw ValidationException::withMessages([
                    'toAcademicSessionId' =>
                        'Select a destination session that starts after the source session ends.',
                ]);
            }

            if (
                $promotion->decision === StudentPromotion::DECISION_PROMOTE
                && ! $promotion->to_class_room_id
            ) {
                throw ValidationException::withMessages([
                    'reviewNote' =>
                        'A destination class must be selected before approving a promotion.',
                ]);
            }

            if ($promotion->to_class_room_id) {
                $destinationClassExists = ClassRoom::query()
                    ->where('school_id', $schoolId)
                    ->whereKey($promotion->to_class_room_id)
                    ->exists();

                if (! $destinationClassExists) {
                    throw ValidationException::withMessages([
                        'reviewNote' =>
                            'The destination class does not belong to this school.',
                    ]);
                }
            }

            $promotion->update([
                'to_academic_session_id' => $targetSession->id,
                'status' => StudentPromotion::STATUS_APPROVED,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'approval_note' => filled($validated['reviewNote'] ?? null)
                    ? trim($validated['reviewNote'])
                    : null,
            ]);
        });

        $this->closeReview();

        session()->flash(
            'success',
            'Promotion recommendation approved for the selected academic session. It is ready for execution.'
        );
    }

    public function rejectPromotion(int $promotionId): void
    {
        $this->authorizeReviewer();

        $validated = $this->validate([
            'reviewNote' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],
        ], [
            'reviewNote.required' =>
                'Enter a reason for rejecting this recommendation.',
            'reviewNote.min' =>
                'The rejection reason must contain at least 5 characters.',
            'reviewNote.max' =>
                'The rejection reason cannot exceed 2000 characters.',
        ]);

        DB::transaction(function () use ($promotionId, $validated): void {
            $promotion = StudentPromotion::query()
                ->where('school_id', $this->schoolId())
                ->whereKey($promotionId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($promotion->status !== StudentPromotion::STATUS_SUBMITTED) {
                throw ValidationException::withMessages([
                    'reviewNote' =>
                        'Only submitted recommendations can be rejected.',
                ]);
            }

            $promotion->update([
                'status' => StudentPromotion::STATUS_REJECTED,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'approval_note' => trim($validated['reviewNote']),
            ]);
        });

        $this->closeReview();

        session()->flash(
            'success',
            'Promotion recommendation rejected. The reason has been recorded.'
        );
    }

    public function render()
    {
        $this->authorizeReviewer();

        $schoolId = $this->schoolId();

        $validStatuses = [
            StudentPromotion::STATUS_SUBMITTED,
            StudentPromotion::STATUS_APPROVED,
            StudentPromotion::STATUS_REJECTED,
            StudentPromotion::STATUS_EXECUTED,
            StudentPromotion::STATUS_DRAFT,
        ];

        $promotions = $this->promotionQuery()
            ->when(
                in_array($this->status, $validStatuses, true),
                fn (Builder $query) => $query->where('status', $this->status)
            )
            ->when(
                trim($this->search) !== '',
                function (Builder $query): void {
                    $search = trim($this->search);

                    $query->where(function (Builder $query) use ($search) {
                        $query
                            ->whereHas('student', function (Builder $studentQuery) use ($search) {
                                $studentQuery->where(function (Builder $nameQuery) use ($search) {
                                    $nameQuery
                                        ->where('first_name', 'like', "%{$search}%")
                                        ->orWhere('middle_name', 'like', "%{$search}%")
                                        ->orWhere('last_name', 'like', "%{$search}%")
                                        ->orWhere('admission_number', 'like', "%{$search}%");
                                });
                            })
                            ->orWhereHas('fromClassRoom', function (Builder $classQuery) use ($search) {
                                $classQuery->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('toClassRoom', function (Builder $classQuery) use ($search) {
                                $classQuery->where('name', 'like', "%{$search}%");
                            });
                    });
                }
            )
            ->latest()
            ->paginate(15);

        $counts = StudentPromotion::query()
            ->where('school_id', $schoolId)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $selectedPromotion = null;

        if ($this->showReviewModal && $this->selectedPromotionId) {
            $selectedPromotion = $this->promotionQuery()
                ->whereKey($this->selectedPromotionId)
                ->first();
        }

        $academicSessions = AcademicSession::query()
            ->where('school_id', $schoolId)
            ->orderBy('starts_at')
            ->get();

        return view('livewire.school.promotions.promotion-review-index', [
            'promotions' => $promotions,
            'counts' => $counts,
            'selectedPromotion' => $selectedPromotion,
            'academicSessions' => $academicSessions,
        ])->layout('components.layouts.school');
    }
}
