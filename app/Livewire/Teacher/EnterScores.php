<?php

namespace App\Livewire\Teacher;

use App\Models\AcademicSession;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamScore;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Models\Term;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class EnterScores extends Component
{
    public ?int $academicSessionId = null;
    public ?int $termId = null;
    public ?int $assignmentId = null;
    public ?int $examId = null;

    public array $scores = [];

    public function mount(): void
    {
        $schoolId = $this->schoolId();

        $session = AcademicSession::query()
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->first();

        if ($session) {
            $this->academicSessionId = $session->id;

            $term = Term::query()
                ->where('school_id', $schoolId)
                ->where('academic_session_id', $session->id)
                ->where('status', 'active')
                ->first();

            $this->termId = $term?->id;
        }
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

    private function teacher(): Teacher
    {
        return Teacher::query()
            ->where('school_id', $this->schoolId())
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }

    public function updatedAcademicSessionId(): void
    {
        $this->termId = null;
        $this->assignmentId = null;
        $this->examId = null;
        $this->scores = [];
    }

    public function updatedTermId(): void
    {
        $this->assignmentId = null;
        $this->examId = null;
        $this->scores = [];
    }

    public function updatedAssignmentId(): void
    {
        $this->examId = null;
        $this->scores = [];
    }

    public function updatedExamId(): void
    {
        $this->loadStudents();
    }

    private function assignmentQuery()
    {
        return TeacherAssignment::query()
            ->where('school_id', $this->schoolId())
            ->where('teacher_id', $this->teacher()->id)
            ->where('academic_session_id', $this->academicSessionId)
            ->where('term_id', $this->termId);
    }

    private function selectedAssignment(): TeacherAssignment
    {
        abort_unless(
            $this->academicSessionId
                && $this->termId
                && $this->assignmentId,
            422,
            'Select the academic session, term and assigned subject.'
        );

        return $this->assignmentQuery()
            ->with(['classRoom', 'subject'])
            ->findOrFail($this->assignmentId);
    }

    private function selectedExam(): Exam
    {
        $assignment = $this->selectedAssignment();

        abort_unless($this->examId, 422, 'Select an examination.');

        return Exam::query()
            ->where('school_id', $this->schoolId())
            ->where('academic_session_id', $this->academicSessionId)
            ->where('term_id', $this->termId)
            ->where('class_room_id', $assignment->class_room_id)
            ->where('subject_id', $assignment->subject_id)
            ->findOrFail($this->examId);
    }

    /**
     * Get active enrolled students in the teacher's assigned class/section.
     */
    private function activeStudentIds(TeacherAssignment $assignment): array
    {
        return Enrollment::query()
            ->where('school_id', $this->schoolId())
            ->where('academic_session_id', $this->academicSessionId)
            ->where('class_room_id', $assignment->class_room_id)
            ->where('admission_status', Enrollment::STATUS_ACTIVE)
            ->when(
                $assignment->section_id,
                fn ($query) => $query->where(
                    'section_id',
                    $assignment->section_id
                )
            )
            ->where(function ($query) {
                $query->whereNull('term_id')
                    ->orWhere('term_id', $this->termId);
            })
            ->whereHas('student', fn ($query) =>
                $query->where('school_id', $this->schoolId())
            )
            ->distinct()
            ->pluck('student_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function ensureScoresEditable(Exam $exam): void
    {
        if (in_array(
            $exam->results_status,
            ['submitted', 'approved'],
            true
        )) {
            throw ValidationException::withMessages([
                'examId' => $exam->results_status === 'approved'
                    ? 'These results have been approved and are locked.'
                    : 'These results have been submitted and are awaiting review.',
            ]);
        }
    }

    /**
     * Load enrolled students and their previously saved scores, grades and remarks.
     */
    public function loadStudents(): void
    {
        $this->scores = [];

        if (!$this->examId || !$this->assignmentId) {
            return;
        }

        $assignment = $this->selectedAssignment();
        $exam = $this->selectedExam();

        $enrollments = Enrollment::query()
            ->with('student')
            ->where('school_id', $this->schoolId())
            ->where('academic_session_id', $this->academicSessionId)
            ->where('class_room_id', $assignment->class_room_id)
            ->where('admission_status', Enrollment::STATUS_ACTIVE)
            ->when(
                $assignment->section_id,
                fn ($query) => $query->where(
                    'section_id',
                    $assignment->section_id
                )
            )
            ->where(function ($query) {
                $query->whereNull('term_id')
                    ->orWhere('term_id', $this->termId);
            })
            ->whereHas('student', fn ($query) =>
                $query->where('school_id', $this->schoolId())
            )
            ->get();

        $existingScores = ExamScore::query()
            ->where('school_id', $this->schoolId())
            ->where('exam_id', $exam->id)
            ->where('subject_id', $exam->subject_id)
            ->whereIn('student_id', $enrollments->pluck('student_id'))
            ->get()
            ->keyBy('student_id');

        foreach ($enrollments as $enrollment) {
            $student = $enrollment->student;

            if (!$student) {
                continue;
            }

            $existing = $existingScores->get($student->id);

            $this->scores[$student->id] = [
                'student_id' => $student->id,
                'name' => trim(
                    $student->first_name . ' ' .
                    ($student->middle_name
                        ? $student->middle_name . ' '
                        : '') .
                    $student->last_name
                ),
                'admission_number' => $student->admission_number,
                'ca_score' => $existing?->ca_score ?? '',
                'exam_score' => $existing?->exam_score ?? '',
                'grade' => $existing?->grade ?? '',
                'remark' => $existing?->remark ?? '',
            ];
        }
    }

    /**
     * Validate score, grade and remark fields.
     */
    private function scoreValidationRules(): array
    {
        $rules = [];

        foreach (array_keys($this->scores) as $studentId) {
            $rules["scores.$studentId.ca_score"] = [
                'required',
                'numeric',
                'min:0',
            ];

            $rules["scores.$studentId.exam_score"] = [
                'required',
                'numeric',
                'min:0',
            ];

            $rules["scores.$studentId.grade"] = [
                'required',
                'string',
                'max:10',
            ];

            $rules["scores.$studentId.remark"] = [
                'required',
                'string',
                'max:255',
            ];
        }

        return $rules;
    }

    /**
     * Check that each score belongs to an enrolled student and is within range.
     */
    private function validateStudentScores(
        array $validated,
        array $allowedStudentIds,
        Exam $exam
    ): void {
        foreach ($validated['scores'] as $studentId => $score) {
            if (!in_array((int) $studentId, $allowedStudentIds, true)) {
                throw ValidationException::withMessages([
                    'scores' => 'One or more students are not in your assigned class.',
                ]);
            }

            $ca = (float) $score['ca_score'];
            $examScore = (float) $score['exam_score'];

            if ($ca + $examScore > (float) $exam->total_marks) {
                throw ValidationException::withMessages([
                    "scores.$studentId.exam_score" =>
                        'The CA and examination total cannot exceed ' .
                        $exam->total_marks . ' marks.',
                ]);
            }
        }
    }

    /**
     * Save scores, grades and remarks without submitting the results.
     */
    public function saveScores(): void
    {
        $assignment = $this->selectedAssignment();
        $exam = $this->selectedExam();

        $this->ensureScoresEditable($exam);

        if (empty($this->scores)) {
            throw ValidationException::withMessages([
                'examId' => 'Select an examination and load its students first.',
            ]);
        }

        $allowedStudentIds = $this->activeStudentIds($assignment);
        $validated = $this->validate($this->scoreValidationRules());

        $this->validateStudentScores(
            $validated,
            $allowedStudentIds,
            $exam
        );

        DB::transaction(function () use ($validated, $exam): void {
            $lockedExam = Exam::query()
                ->where('school_id', $this->schoolId())
                ->lockForUpdate()
                ->findOrFail($exam->id);

            $this->ensureScoresEditable($lockedExam);

            foreach ($validated['scores'] as $studentId => $score) {
                $ca = round((float) $score['ca_score'], 2);
                $examScore = round((float) $score['exam_score'], 2);
                $total = round($ca + $examScore, 2);

                ExamScore::query()->updateOrCreate(
                    [
                        'exam_id' => $lockedExam->id,
                        'student_id' => (int) $studentId,
                        'subject_id' => $lockedExam->subject_id,
                    ],
                    [
                        'school_id' => $this->schoolId(),
                        'ca_score' => $ca,
                        'exam_score' => $examScore,
                        'total_score' => $total,
                        'grade' => trim($score['grade']),
                        'remark' => trim($score['remark']),
                        'entered_by' => auth()->id(),
                    ]
                );
            }
        });

        session()->flash(
            'success',
            'Scores, grades and remarks saved successfully.'
        );

        $this->loadStudents();
    }

    /**
     * Submit a complete score sheet to the School Admin/Principal.
     */
    public function submitResults(): void
    {
        $assignment = $this->selectedAssignment();
        $exam = $this->selectedExam();

        if (!in_array(
            $exam->results_status,
            ['not_submitted', 'returned'],
            true
        )) {
            throw ValidationException::withMessages([
                'examId' => 'These results cannot be submitted in their current status.',
            ]);
        }

        if (empty($this->scores)) {
            throw ValidationException::withMessages([
                'examId' => 'Load the student score sheet first.',
            ]);
        }

        $studentIds = $this->activeStudentIds($assignment);

        if (count($studentIds) === 0) {
            throw ValidationException::withMessages([
                'examId' => 'There are no active enrolled students in this class.',
            ]);
        }

        // Validate the currently displayed form before checking saved records.
        $validated = $this->validate($this->scoreValidationRules());

        $this->validateStudentScores(
            $validated,
            $studentIds,
            $exam
        );

        DB::transaction(function () use ($exam, $studentIds): void {
            $lockedExam = Exam::query()
                ->where('school_id', $this->schoolId())
                ->lockForUpdate()
                ->findOrFail($exam->id);

            if (!in_array(
                $lockedExam->results_status,
                ['not_submitted', 'returned'],
                true
            )) {
                throw ValidationException::withMessages([
                    'examId' => 'These results have already been submitted or approved.',
                ]);
            }

            $savedScores = ExamScore::query()
                ->where('school_id', $this->schoolId())
                ->where('exam_id', $lockedExam->id)
                ->where('subject_id', $lockedExam->subject_id)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->keyBy('student_id');

            foreach ($studentIds as $studentId) {
                $score = $savedScores->get($studentId);

                if (!$score) {
                    throw ValidationException::withMessages([
                        'scores' => 'Save scores for every active student before submitting results.',
                    ]);
                }

                if (
                    $score->grade === null ||
                    trim((string) $score->grade) === '' ||
                    $score->remark === null ||
                    trim((string) $score->remark) === ''
                ) {
                    throw ValidationException::withMessages([
                        'scores' => 'Every student must have a saved grade and remark before submission.',
                    ]);
                }

                if (
                    (float) $score->ca_score < 0 ||
                    (float) $score->exam_score < 0 ||
                    (float) $score->ca_score +
                        (float) $score->exam_score >
                        (float) $lockedExam->total_marks
                ) {
                    throw ValidationException::withMessages([
                        'scores' => 'One or more saved scores are invalid. Correct them before submitting.',
                    ]);
                }
            }

            $lockedExam->results_status = 'submitted';
            $lockedExam->results_submitted_by = auth()->id();
            $lockedExam->results_submitted_at = now();
            $lockedExam->results_reviewed_by = null;
            $lockedExam->results_reviewed_at = null;
            $lockedExam->results_review_note = null;
            $lockedExam->save();
        });

        session()->flash(
            'success',
            'Results submitted successfully. Scores, grades and remarks are now locked while awaiting review.'
        );

        $this->loadStudents();
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $sessions = AcademicSession::query()
            ->where('school_id', $schoolId)
            ->orderByDesc('starts_at')
            ->get();

        $terms = Term::query()
            ->where('school_id', $schoolId)
            ->where('academic_session_id', $this->academicSessionId)
            ->orderBy('starts_at')
            ->get();

        $assignments = collect();

        if ($this->academicSessionId && $this->termId) {
            $assignments = $this->assignmentQuery()
                ->with(['classRoom', 'subject'])
                ->get();
        }

        $exams = collect();
        $selectedExam = null;

        if (
            $this->assignmentId &&
            $this->academicSessionId &&
            $this->termId
        ) {
            $assignment = $this->selectedAssignment();

            $exams = Exam::query()
                ->where('school_id', $schoolId)
                ->where('academic_session_id', $this->academicSessionId)
                ->where('term_id', $this->termId)
                ->where('class_room_id', $assignment->class_room_id)
                ->where('subject_id', $assignment->subject_id)
                ->orderBy('exam_date')
                ->get();

            if ($this->examId) {
                $selectedExam = $exams->firstWhere('id', $this->examId);
            }
        }

        return view('livewire.teacher.enter-scores', [
            'sessions' => $sessions,
            'terms' => $terms,
            'assignments' => $assignments,
            'exams' => $exams,
            'selectedExam' => $selectedExam,
        ])->layout('components.layouts.teacher');
    }
}
