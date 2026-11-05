<?php

namespace App\Livewire\Teacher;

use App\Models\Attendance;
use App\Models\AttendanceTermSubmission;
use App\Models\Enrollment;
use App\Models\Teacher as TeacherModel;
use App\Models\TeacherAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class TakeAttendance extends Component
{
    public $selectedAssignmentId = '';
    public $attendanceDate = '';
    public $attendance = [];
    public $remarks = [];
    public $teacherRemarks = '';

    #[Locked]
    public ?int $teacherId = null;

    public function mount(): void
    {
        $this->attendanceDate = now()->toDateString();

        $teacher = $this->currentTeacher();

        $this->teacherId = $teacher->id;

        $firstAssignment = $this->assignments()->first();

        if ($firstAssignment) {
            $this->selectedAssignmentId = (string) $firstAssignment->id;
            $this->loadExistingAttendance();
        }
    }

    private function currentTeacher(): TeacherModel
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless($user->hasRole('Teacher'), 403);

        abort_unless(
            $user->school_id,
            403,
            'Your account is not assigned to a school.'
        );

        return TeacherModel::query()
            ->where('school_id', $user->school_id)
            ->where('user_id', $user->id)
            ->firstOrFail();
    }

    private function assignments()
    {
        $teacher = $this->currentTeacher();

        return TeacherAssignment::query()
            ->where('school_id', $teacher->school_id)
            ->where('teacher_id', $teacher->id)
            ->whereNotNull('academic_session_id')
            ->whereNotNull('term_id')
            ->with([
                'classRoom',
                'section',
                'subject',
                'academicSession',
                'term',
            ])
            ->orderBy('class_room_id')
            ->get();
    }

    private function selectedAssignment(): ?TeacherAssignment
    {
        if (! is_numeric($this->selectedAssignmentId)) {
            return null;
        }

        $teacher = $this->currentTeacher();

        return TeacherAssignment::query()
            ->whereKey((int) $this->selectedAssignmentId)
            ->where('school_id', $teacher->school_id)
            ->where('teacher_id', $teacher->id)
            ->whereNotNull('academic_session_id')
            ->whereNotNull('term_id')
            ->with([
                'classRoom',
                'section',
                'subject',
                'academicSession',
                'term',
            ])
            ->first();
    }

    private function studentsForAssignment(TeacherAssignment $assignment)
    {
        $query = Enrollment::query()
            ->where('school_id', $assignment->school_id)
            ->where('class_room_id', $assignment->class_room_id)
            ->where('academic_session_id', $assignment->academic_session_id)
            ->where('term_id', $assignment->term_id)
            ->where('admission_status', 'active');

        if ($assignment->section_id === null) {
            $query->whereNull('section_id');
        } else {
            $query->where('section_id', $assignment->section_id);
        }

        return $query
            ->with('student')
            ->get()
            ->filter(fn ($enrollment) => $enrollment->student !== null)
            ->unique('student_id')
            ->values();
    }

    public function updatedSelectedAssignmentId(): void
    {
        $this->resetValidation();
        $this->attendance = [];
        $this->remarks = [];

        $this->loadExistingAttendance();
    }

    public function updatedAttendanceDate(): void
    {
        $this->resetValidation();
        $this->loadExistingAttendance();
    }

    private function loadExistingAttendance(): void
    {
        $this->attendance = [];
        $this->remarks = [];

        $assignment = $this->selectedAssignment();

        if (! $assignment || ! $this->validAttendanceDate()) {
            return;
        }

        $studentIds = $this->studentsForAssignment($assignment)
            ->pluck('student_id')
            ->all();

        if (empty($studentIds)) {
            return;
        }

        $existing = Attendance::query()
            ->where('school_id', $assignment->school_id)
            ->where('class_room_id', $assignment->class_room_id)
            ->where('academic_session_id', $assignment->academic_session_id)
            ->where('term_id', $assignment->term_id)
            ->whereDate('attendance_date', $this->attendanceDate)
            ->whereIn('student_id', $studentIds)
            ->when(
                $assignment->section_id === null,
                fn ($query) => $query->whereNull('section_id'),
                fn ($query) => $query->where(
                    'section_id',
                    $assignment->section_id
                )
            )
            ->get()
            ->keyBy('student_id');

        foreach ($studentIds as $studentId) {
            $record = $existing->get($studentId);

            $this->attendance[$studentId] = $record?->status ?? '';
            $this->remarks[$studentId] = $record?->remarks ?? '';
        }
    }

    private function validAttendanceDate(): bool
    {
        return is_string($this->attendanceDate)
            && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->attendanceDate)
            && $this->attendanceDate <= now()->toDateString()
            && checkdate(
                (int) substr($this->attendanceDate, 5, 2),
                (int) substr($this->attendanceDate, 8, 2),
                (int) substr($this->attendanceDate, 0, 4)
            );
    }

    public function saveAttendance(): void
    {
        $assignment = $this->selectedAssignment();

        if (! $assignment) {
            throw ValidationException::withMessages([
                'selectedAssignmentId' => 'Select a valid assigned class.',
            ]);
        }

        if (! $this->validAttendanceDate()) {
            throw ValidationException::withMessages([
                'attendanceDate' => 'Select a valid date that is not in the future.',
            ]);
        }

        $teacher = $this->currentTeacher();
        $students = $this->studentsForAssignment($assignment);
        $studentIds = $students->pluck('student_id')->map(fn ($id) => (string) $id)->all();

        if ($students->isEmpty()) {
            throw ValidationException::withMessages([
                'attendance' => 'There are no active enrolled students in this class.',
            ]);
        }

        $this->validate([
            'attendance' => ['required', 'array'],
            'remarks' => ['nullable', 'array'],
        ]);

        $allowedStatuses = ['present', 'absent', 'late', 'excused'];

        foreach ($studentIds as $studentId) {
            $status = $this->attendance[$studentId] ?? null;

            if (! in_array($status, $allowedStatuses, true)) {
                throw ValidationException::withMessages([
                    "attendance.{$studentId}" =>
                        'Choose an attendance status for every student.',
                ]);
            }

            $remark = $this->remarks[$studentId] ?? null;

            if ($remark !== null && mb_strlen((string) $remark) > 1000) {
                throw ValidationException::withMessages([
                    "remarks.{$studentId}" =>
                        'A remark cannot exceed 1000 characters.',
                ]);
            }
        }

        DB::transaction(function () use (
            $assignment,
            $teacher,
            $studentIds
        ) {
            foreach ($studentIds as $studentId) {
                Attendance::updateOrCreate(
                    [
                        'school_id' => $teacher->school_id,
                        'student_id' => $studentId,
                        'attendance_date' => $this->attendanceDate,
                    ],
                    [
                        'class_room_id' => $assignment->class_room_id,
                        'section_id' => $assignment->section_id,
                        'academic_session_id' => $assignment->academic_session_id,
                        'term_id' => $assignment->term_id,
                        'status' => $this->attendance[$studentId],
                        'marked_by' => Auth::id(),
                        'remarks' => $this->remarks[$studentId] ?? null,
                    ]
                );
            }
        });

        session()->flash('success', 'Attendance saved successfully.');

        $this->loadExistingAttendance();
    }

    public function submitTermReport(): void
    {
        $assignment = $this->selectedAssignment();

        if (! $assignment) {
            throw ValidationException::withMessages([
                'selectedAssignmentId' => 'Select a valid assigned class.',
            ]);
        }

        $teacher = $this->currentTeacher();

        $this->validate([
            'teacherRemarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $existingPending = AttendanceTermSubmission::query()
            ->where('school_id', $teacher->school_id)
            ->where('teacher_id', $teacher->id)
            ->where('class_room_id', $assignment->class_room_id)
            ->where('academic_session_id', $assignment->academic_session_id)
            ->where('term_id', $assignment->term_id)
            ->when(
                $assignment->section_id === null,
                fn ($query) => $query->whereNull('section_id'),
                fn ($query) => $query->where(
                    'section_id',
                    $assignment->section_id
                )
            )
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            throw ValidationException::withMessages([
                'selectedAssignmentId' =>
                    'This class attendance report has already been submitted and is awaiting administrator review.',
            ]);
        }

        $records = Attendance::query()
            ->where('school_id', $teacher->school_id)
            ->where('class_room_id', $assignment->class_room_id)
            ->where('academic_session_id', $assignment->academic_session_id)
            ->where('term_id', $assignment->term_id)
            ->when(
                $assignment->section_id === null,
                fn ($query) => $query->whereNull('section_id'),
                fn ($query) => $query->where(
                    'section_id',
                    $assignment->section_id
                )
            )
            ->get();

        if ($records->isEmpty()) {
            throw ValidationException::withMessages([
                'selectedAssignmentId' =>
                    'No attendance records exist for this class and term. Mark and save attendance before submitting.',
            ]);
        }

        $counts = [
            'total_records' => $records->count(),
            'present_count' => $records->where('status', 'present')->count(),
            'absent_count' => $records->where('status', 'absent')->count(),
            'late_count' => $records->where('status', 'late')->count(),
            'excused_count' => $records->where('status', 'excused')->count(),
        ];

        AttendanceTermSubmission::create([
            'school_id' => $teacher->school_id,
            'teacher_id' => $teacher->id,
            'class_room_id' => $assignment->class_room_id,
            'section_id' => $assignment->section_id,
            'academic_session_id' => $assignment->academic_session_id,
            'term_id' => $assignment->term_id,
            'submitted_by' => Auth::id(),
            'submitted_at' => now(),
            'status' => 'pending',
            'teacher_remarks' => $this->teacherRemarks,
            ...$counts,
        ]);

        session()->flash(
            'success',
            'Your term attendance report has been submitted to the school administrator for review.'
        );

        $this->teacherRemarks = '';
    }

    public function render()
    {
        $assignments = $this->assignments();
        $selectedAssignment = $this->selectedAssignment();

        $students = $selectedAssignment
            ? $this->studentsForAssignment($selectedAssignment)
            : collect();

        $submission = null;

        if ($selectedAssignment) {
            $teacher = $this->currentTeacher();

            $submission = AttendanceTermSubmission::query()
                ->where('school_id', $teacher->school_id)
                ->where('teacher_id', $teacher->id)
                ->where('class_room_id', $selectedAssignment->class_room_id)
                ->where('academic_session_id', $selectedAssignment->academic_session_id)
                ->where('term_id', $selectedAssignment->term_id)
                ->when(
                    $selectedAssignment->section_id === null,
                    fn ($query) => $query->whereNull('section_id'),
                    fn ($query) => $query->where(
                        'section_id',
                        $selectedAssignment->section_id
                    )
                )
                ->latest('id')
                ->first();
        }

        $termAttendance = collect();

        if ($selectedAssignment) {
            $termAttendance = Attendance::query()
                ->where('school_id', $selectedAssignment->school_id)
                ->where('class_room_id', $selectedAssignment->class_room_id)
                ->where('academic_session_id', $selectedAssignment->academic_session_id)
                ->where('term_id', $selectedAssignment->term_id)
                ->when(
                    $selectedAssignment->section_id === null,
                    fn ($query) => $query->whereNull('section_id'),
                    fn ($query) => $query->where(
                        'section_id',
                        $selectedAssignment->section_id
                    )
                )
                ->get();
        }

        return view('livewire.teacher.take-attendance', [
            'assignments' => $assignments,
            'selectedAssignment' => $selectedAssignment,
            'students' => $students,
            'submission' => $submission,
            'termAttendance' => $termAttendance,
        ])->layout('components.layouts.teacher', [
            'title' => 'Take Attendance',
        ]);
    }
}