<?php

namespace App\Livewire\Teacher\Promotions;

use App\Models\AcademicSession;
use App\Models\ClassHeadTeacher;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\ExamScore;
use App\Models\StudentPromotion;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PromotionRecommendations extends Component
{
    public array $recommendations = [];

    public ?string $pageError = null;

    public function mount(): void
    {
        if (! $this->teacher()) {
            $this->pageError =
                'Your account is not linked to a teacher record in this school.';

            return;
        }

        if (! $this->classHeadTeacher()) {
            $this->pageError =
                'You do not have an active class-head-teacher assignment for an active academic session. Please contact your School Admin.';

            return;
        }

        if (! $this->sourceSession()) {
            $this->pageError =
                'Your assigned class does not have a valid academic session.';

            return;
        }

        // A future academic session is no longer required here.
        $this->loadStudents();
    }

    private function schoolId(): int
    {
        $schoolId = auth()->user()?->school_id;

        abort_unless(
            $schoolId,
            403,
            'Your account is not linked to a school.'
        );

        return (int) $schoolId;
    }

    private function teacher(): ?Teacher
    {
        return Teacher::query()
            ->where('school_id', $this->schoolId())
            ->where('user_id', auth()->id())
            ->first();
    }

    private function classHeadTeacher(): ?ClassHeadTeacher
    {
        $teacher = $this->teacher();

        if (! $teacher) {
            return null;
        }

        return ClassHeadTeacher::query()
            ->where('school_id', $this->schoolId())
            ->where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->whereHas('academicSession', function ($query) {
                $query
                    ->where('school_id', $this->schoolId())
                    ->where('status', 'active');
            })
            ->with(['academicSession', 'classRoom'])
            ->first();
    }

    private function sourceSession(): ?AcademicSession
    {
        $assignment = $this->classHeadTeacher();

        if (! $assignment) {
            return null;
        }

        return AcademicSession::query()
            ->where('school_id', $this->schoolId())
            ->find($assignment->academic_session_id);
    }

    private function activeEnrollments()
    {
        $assignment = $this->classHeadTeacher();

        if (! $assignment) {
            return collect();
        }

        return Enrollment::query()
            ->with('student')
            ->where('school_id', $this->schoolId())
            ->where(
                'academic_session_id',
                $assignment->academic_session_id
            )
            ->where('class_room_id', $assignment->class_room_id)
            ->where(
                'admission_status',
                Enrollment::STATUS_ACTIVE
            )
            ->whereHas('student', function ($query) {
                $query->where('school_id', $this->schoolId());
            })
            ->orderBy('student_id')
            ->get();
    }

    /**
     * Load current students, approved exam results, and existing recommendations.
     * The teacher does not need to select a destination academic session.
     */
    public function loadStudents(): void
    {
        $this->recommendations = [];
        $this->pageError = null;

        $assignment = $this->classHeadTeacher();
        $sourceSession = $this->sourceSession();

        if (! $assignment || ! $sourceSession) {
            $this->pageError =
                'An active class-head-teacher assignment and academic session are required.';

            return;
        }

        $enrollments = $this->activeEnrollments();

        $enrollmentIds = $enrollments->pluck('id')->all();
        $studentIds = $enrollments->pluck('student_id')->all();

        /*
         * Load existing records for these source enrollments.
         * New teacher recommendations will have a null destination session.
         * Existing older recommendations may already have a destination.
         */
        $existingRecommendations = StudentPromotion::query()
            ->where('school_id', $this->schoolId())
            ->where('from_academic_session_id', $sourceSession->id)
            ->where('from_class_room_id', $assignment->class_room_id)
            ->whereIn('from_enrollment_id', $enrollmentIds)
            ->whereIn('student_id', $studentIds)
            ->latest('created_at')
            ->get()
            ->groupBy('student_id')
            ->map(fn ($items) => $items->first());

        foreach ($enrollments as $enrollment) {
            $student = $enrollment->student;

            if (! $student) {
                continue;
            }

            $approvedScores = ExamScore::query()
                ->with('exam')
                ->where('school_id', $this->schoolId())
                ->where('student_id', $student->id)
                ->whereHas('exam', function ($query) use ($assignment) {
                    $query
                        ->where('school_id', $this->schoolId())
                        ->where(
                            'academic_session_id',
                            $assignment->academic_session_id
                        )
                        ->where(
                            'class_room_id',
                            $assignment->class_room_id
                        )
                        ->where('results_status', 'approved');
                })
                ->get();

            // Convert scores to percentages before calculating the average.
            $percentages = $approvedScores
                ->filter(fn ($score) =>
                    $score->exam &&
                    (float) $score->exam->total_marks > 0
                )
                ->map(fn ($score) =>
                    ((float) $score->total_score /
                        (float) $score->exam->total_marks) * 100
                );

            $average = $percentages->isNotEmpty()
                ? round((float) $percentages->avg(), 2)
                : null;

            $existing = $existingRecommendations->get($student->id);

            $this->recommendations[$student->id] = [
                'student_id' => (int) $student->id,
                'enrollment_id' => (int) $enrollment->id,
                'name' => trim(
                    $student->first_name . ' ' .
                    ($student->middle_name
                        ? $student->middle_name . ' '
                        : '') .
                    $student->last_name
                ),
                'admission_number' => $student->admission_number,
                'approved_exam_count' => $approvedScores->count(),
                'average_score' => $average,
                'decision' => $existing?->decision ?? 'promote',
                'to_class_room_id' =>
                    $existing?->to_class_room_id ??
                    $assignment->class_room_id,
                'recommendation_reason' =>
                    $existing?->recommendation_reason ?? '',
                'existing_status' => $existing?->status,
            ];
        }
    }

    public function submitRecommendations(): void
    {
        $assignment = $this->classHeadTeacher();
        $sourceSession = $this->sourceSession();

        if (! $assignment || ! $sourceSession) {
            throw ValidationException::withMessages([
                'recommendations' =>
                    'An active class-head-teacher assignment and source session are required.',
            ]);
        }

        $enrollments = $this->activeEnrollments();

        if ($enrollments->isEmpty()) {
            throw ValidationException::withMessages([
                'recommendations' =>
                    'There are no active students in your assigned class.',
            ]);
        }

        $rules = [];

        foreach ($enrollments as $enrollment) {
            $studentId = (int) $enrollment->student_id;

            $rules["recommendations.$studentId.decision"] = [
                'required',
                'in:promote,repeat,review',
            ];

            $rules["recommendations.$studentId.to_class_room_id"] = [
                'nullable',
                'integer',
            ];

            $rules["recommendations.$studentId.recommendation_reason"] = [
                'nullable',
                'string',
                'max:2000',
            ];
        }

        $validated = $this->validate($rules);

        $schoolClassIds = ClassRoom::query()
            ->where('school_id', $this->schoolId())
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        DB::transaction(function () use (
            $assignment,
            $sourceSession,
            $enrollments,
            $validated,
            $schoolClassIds
        ): void {
            foreach ($enrollments as $enrollment) {
                $studentId = (int) $enrollment->student_id;
                $data = $validated['recommendations'][$studentId] ?? null;

                if (! $data) {
                    throw ValidationException::withMessages([
                        'recommendations' =>
                            'Complete a recommendation for every student.',
                    ]);
                }

                // Require approved examination results before submission.
                $approvedScoreCount = ExamScore::query()
                    ->where('school_id', $this->schoolId())
                    ->where('student_id', $studentId)
                    ->whereHas('exam', function ($query) use ($assignment) {
                        $query
                            ->where('school_id', $this->schoolId())
                            ->where(
                                'academic_session_id',
                                $assignment->academic_session_id
                            )
                            ->where(
                                'class_room_id',
                                $assignment->class_room_id
                            )
                            ->where('results_status', 'approved');
                    })
                    ->count();

                if ($approvedScoreCount === 0) {
                    throw ValidationException::withMessages([
                        "recommendations.$studentId.decision" =>
                            'This student needs approved examination results before a promotion recommendation can be submitted.',
                    ]);
                }

                $decision = $data['decision'];
                $reason = trim($data['recommendation_reason'] ?? '');

                if ($decision === 'review' && $reason === '') {
                    throw ValidationException::withMessages([
                        "recommendations.$studentId.recommendation_reason" =>
                            'Explain why this student needs further review.',
                    ]);
                }

                if ($decision === 'promote') {
                    $destinationClassId = filter_var(
                        $data['to_class_room_id'] ?? null,
                        FILTER_VALIDATE_INT
                    );

                    if (
                        ! $destinationClassId ||
                        ! in_array(
                            (int) $destinationClassId,
                            $schoolClassIds,
                            true
                        )
                    ) {
                        throw ValidationException::withMessages([
                            "recommendations.$studentId.to_class_room_id" =>
                                'Select a valid destination class belonging to your school.',
                        ]);
                    }
                } elseif ($decision === 'repeat') {
                    $destinationClassId = (int) $assignment->class_room_id;
                } else {
                    $destinationClassId = null;
                }

                /*
                 * Do not permit a second pending, approved, or executed
                 * recommendation for the same source enrollment.
                 */
                $lockedExisting = StudentPromotion::query()
                    ->where('school_id', $this->schoolId())
                    ->where('student_id', $studentId)
                    ->where('from_enrollment_id', $enrollment->id)
                    ->where('from_academic_session_id', $sourceSession->id)
                    ->where('from_class_room_id', $assignment->class_room_id)
                    ->lockForUpdate()
                    ->get();

                $hasLockedRecommendation = $lockedExisting->contains(
                    fn (StudentPromotion $promotion) => in_array(
                        $promotion->status,
                        [
                            StudentPromotion::STATUS_SUBMITTED,
                            StudentPromotion::STATUS_APPROVED,
                            StudentPromotion::STATUS_EXECUTED,
                        ],
                        true
                    )
                );

                if ($hasLockedRecommendation) {
                    throw ValidationException::withMessages([
                        "recommendations.$studentId.decision" =>
                            'A recommendation for this student has already been submitted, approved, or executed.',
                    ]);
                }

                // Reuse a rejected/draft record if available; otherwise create one.
                $promotion = $lockedExisting
                    ->whereIn('status', [
                        StudentPromotion::STATUS_REJECTED,
                        StudentPromotion::STATUS_DRAFT,
                    ])
                    ->sortByDesc('created_at')
                    ->first();

                $promotion ??= new StudentPromotion();

                $promotion->fill([
                    'school_id' => $this->schoolId(),
                    'student_id' => $studentId,
                    'from_enrollment_id' => $enrollment->id,
                    'from_academic_session_id' => $sourceSession->id,
                    'from_class_room_id' => $assignment->class_room_id,

                    // Admin chooses this later during review.
                    'to_academic_session_id' => null,

                    'to_class_room_id' => $destinationClassId,
                    'decision' => $decision,
                    'status' => StudentPromotion::STATUS_SUBMITTED,
                    'recommendation_reason' => $reason !== '' ? $reason : null,
                    'recommended_by' => auth()->id(),
                    'recommended_at' => now(),
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'approval_note' => null,
                    'executed_at' => null,
                ]);

                $promotion->save();
            }
        });

        session()->flash(
            'success',
            'Promotion recommendations submitted successfully for School Admin or Principal review.'
        );

        $this->loadStudents();
    }

    public function render()
    {
        $assignment = $this->classHeadTeacher();
        $sourceSession = $this->sourceSession();

        $classes = collect();

        if ($assignment && $sourceSession) {
            $classes = ClassRoom::query()
                ->where('school_id', $this->schoolId())
                ->orderBy('name')
                ->get();
        }

        return view(
            'livewire.teacher.promotions.promotion-recommendations',
            [
                'assignment' => $assignment,
                'sourceSession' => $sourceSession,
                'classes' => $classes,
            ]
        )->layout('components.layouts.teacher');
    }
}
