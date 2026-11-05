<?php

namespace App\Livewire\School\Promotions;

use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\StudentPromotion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class PromotionExecutionIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $status = 'approved';

    public string $sourceSessionId = '';

    public string $classRoomId = '';

    #[Locked]
    public ?int $selectedPromotionId = null;

    public bool $showDetailsModal = false;

    public function mount(): void
    {
        $this->authorizeExecutor();
    }

    private function authorizeExecutor(): void
    {
        abort_unless(auth()->check(), 403);

        abort_unless(
            auth()->user()->hasAnyRole(['School Admin', 'Principal']),
            403,
            'Only the School Admin or Principal can execute student promotions.'
        );

        abort_unless(
            auth()->user()->school_id,
            403,
            'Your account is not linked to a school.'
        );
    }

    private function schoolId(): int
    {
        $this->authorizeExecutor();

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

    public function updatedSourceSessionId(): void
    {
        $this->resetPage();
    }

    public function updatedClassRoomId(): void
    {
        $this->resetPage();
    }

    public function viewPromotion(int $promotionId): void
    {
        $promotion = $this->promotionQuery()
            ->whereKey($promotionId)
            ->firstOrFail();

        $this->selectedPromotionId = (int) $promotion->id;
        $this->showDetailsModal = true;

        $this->resetValidation();
    }

    public function closeDetails(): void
    {
        $this->showDetailsModal = false;
        $this->selectedPromotionId = null;

        $this->resetValidation();
    }

    /**
     * Execute an approved promotion or repeat recommendation.
     *
     * Promote: use the approved destination class.
     * Repeat: retain the source class in the destination session.
     *
     * The source enrollment, destination enrollment, and promotion
     * status are updated atomically.
     */
    public function executePromotion(int $promotionId): void
    {
        $this->authorizeExecutor();

        DB::transaction(function () use ($promotionId): void {
            $schoolId = $this->schoolId();

            $promotion = StudentPromotion::query()
                ->where('school_id', $schoolId)
                ->whereKey($promotionId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($promotion->status !== StudentPromotion::STATUS_APPROVED) {
                throw ValidationException::withMessages([
                    'execution' =>
                        'Only approved promotion recommendations can be executed.',
                ]);
            }

            if (! in_array($promotion->decision, [
                StudentPromotion::DECISION_PROMOTE,
                StudentPromotion::DECISION_REPEAT,
            ], true)) {
                throw ValidationException::withMessages([
                    'execution' =>
                        'Only Promote or Repeat recommendations can be executed.',
                ]);
            }

            if (! $promotion->from_enrollment_id) {
                throw ValidationException::withMessages([
                    'execution' =>
                        'The source enrollment is missing. Review this recommendation before continuing.',
                ]);
            }

            if (! $promotion->from_academic_session_id) {
                throw ValidationException::withMessages([
                    'execution' =>
                        'The source academic session is missing.',
                ]);
            }

            if (! $promotion->to_academic_session_id) {
                throw ValidationException::withMessages([
                    'execution' =>
                        'The approved destination academic session is missing. Reopen the review step and select a destination session.',
                ]);
            }

            $student = $promotion->student()
                ->where('school_id', $schoolId)
                ->firstOrFail();

            $sourceSession = AcademicSession::query()
                ->where('school_id', $schoolId)
                ->whereKey($promotion->from_academic_session_id)
                ->firstOrFail();

            $targetSession = AcademicSession::query()
                ->where('school_id', $schoolId)
                ->whereKey($promotion->to_academic_session_id)
                ->firstOrFail();

            if ((int) $sourceSession->id === (int) $targetSession->id) {
                throw ValidationException::withMessages([
                    'execution' =>
                        'The destination session must be different from the source session.',
                ]);
            }

            // Where dates exist, the destination must start after
            // the source session ends.
            if (
                $sourceSession->ends_at &&
                $targetSession->starts_at &&
                Carbon::parse($targetSession->starts_at)->lte(
                    Carbon::parse($sourceSession->ends_at)
                )
            ) {
                throw ValidationException::withMessages([
                    'execution' =>
                        'The destination academic session must start after the source session ends.',
                ]);
            }

            $sourceEnrollment = Enrollment::query()
                ->where('school_id', $schoolId)
                ->whereKey($promotion->from_enrollment_id)
                ->where('student_id', $student->id)
                ->where('academic_session_id', $sourceSession->id)
                ->where('class_room_id', $promotion->from_class_room_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $sourceEnrollment->admission_status
                !== Enrollment::STATUS_ACTIVE
            ) {
                throw ValidationException::withMessages([
                    'execution' =>
                        'The source enrollment is not active. Review the enrollment before proceeding.',
                ]);
            }

            /*
             * Determine the destination class.
             *
             * Promote: use the class selected by the teacher and
             * approved by the School Admin or Principal.
             *
             * Repeat: retain the student's current class.
             */
            if (
                $promotion->decision
                === StudentPromotion::DECISION_REPEAT
            ) {
                $destinationClassId = $promotion->from_class_room_id;
            } else {
                $destinationClassId = $promotion->to_class_room_id;

                if (! $destinationClassId) {
                    throw ValidationException::withMessages([
                        'execution' =>
                            'A destination class is required for a Promote recommendation.',
                    ]);
                }
            }

            if (! $destinationClassId) {
                throw ValidationException::withMessages([
                    'execution' =>
                        'The destination class could not be determined.',
                ]);
            }

            $destinationClass = ClassRoom::query()
                ->where('school_id', $schoolId)
                ->whereKey($destinationClassId)
                ->firstOrFail();

            // Lock and check any enrollment the student already has
            // in the destination session.
            $existingEnrollment = Enrollment::query()
                ->where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('academic_session_id', $targetSession->id)
                ->lockForUpdate()
                ->exists();

            if ($existingEnrollment) {
                throw ValidationException::withMessages([
                    'execution' =>
                        'This student already has an enrollment in the destination session. Review that enrollment before executing this recommendation.',
                ]);
            }

            $startDate = $targetSession->starts_at
                ? Carbon::parse($targetSession->starts_at)->toDateString()
                : now()->toDateString();

            $endDate = $sourceSession->ends_at
                ? Carbon::parse($sourceSession->ends_at)->toDateString()
                : now()->toDateString();

            // Preserve the old enrollment as historical data.
            $sourceEnrollment->update([
                'admission_status' => Enrollment::STATUS_COMPLETED,
                'end_date' => $endDate,
            ]);

            // Create the new enrollment for the destination session.
            Enrollment::query()->create([
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'academic_session_id' => $targetSession->id,
                'term_id' => null,
                'class_room_id' => $destinationClass->id,
                'section_id' => null,
                'admission_status' => Enrollment::STATUS_ACTIVE,
                'start_date' => $startDate,
                'end_date' => null,
            ]);

            // Mark the recommendation executed only after enrollment
            // changes have succeeded within this transaction.
            $promotion->update([
                'status' => StudentPromotion::STATUS_EXECUTED,
                'executed_at' => now(),
            ]);
        });

        $this->closeDetails();

        session()->flash(
            'success',
            'The student enrollment was updated successfully. The previous enrollment has been preserved.'
        );
    }

    public function render()
    {
        $this->authorizeExecutor();

        $schoolId = $this->schoolId();

        $validStatuses = [
            StudentPromotion::STATUS_APPROVED,
            StudentPromotion::STATUS_EXECUTED,
        ];

        $promotions = $this->promotionQuery()
            ->when(
                in_array($this->status, $validStatuses, true),
                fn (Builder $query) => $query->where(
                    'status',
                    $this->status
                )
            )
            ->when(
                $this->sourceSessionId !== '',
                fn (Builder $query) => $query->where(
                    'from_academic_session_id',
                    (int) $this->sourceSessionId
                )
            )
            ->when(
                $this->classRoomId !== '',
                fn (Builder $query) => $query->where(
                    'from_class_room_id',
                    (int) $this->classRoomId
                )
            )
            ->when(
                trim($this->search) !== '',
                function (Builder $query): void {
                    $search = trim($this->search);

                    $query->whereHas(
                        'student',
                        function (Builder $studentQuery) use ($search): void {
                            $studentQuery->where(function (Builder $nameQuery) use ($search) {
                                $nameQuery
                                    ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('middle_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('admission_number', 'like', "%{$search}%");
                            });
                        }
                    );
                }
            )
            ->latest()
            ->paginate(15);

        $counts = StudentPromotion::query()
            ->where('school_id', $schoolId)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $sessions = AcademicSession::query()
            ->where('school_id', $schoolId)
            ->orderByDesc('starts_at')
            ->get();

        $classes = ClassRoom::query()
            ->where('school_id', $schoolId)
            ->orderBy('name')
            ->get();

        $selectedPromotion = null;

        if ($this->showDetailsModal && $this->selectedPromotionId) {
            $selectedPromotion = $this->promotionQuery()
                ->whereKey($this->selectedPromotionId)
                ->first();
        }

        return view('livewire.school.promotions.promotion-execution-index', [
            'promotions' => $promotions,
            'counts' => $counts,
            'sessions' => $sessions,
            'classes' => $classes,
            'selectedPromotion' => $selectedPromotion,
        ])->layout('components.layouts.school');
    }
}
