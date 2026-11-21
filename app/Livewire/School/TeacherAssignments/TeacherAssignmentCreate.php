<?php

namespace App\Livewire\School\TeacherAssignments;

use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class TeacherAssignmentCreate extends Component
{
    public $teacher_id = '';
    public $teacherSearch = '';
    public $subject_id = '';
    public $class_room_id = '';
    public $section_id = '';
    public $academic_session_id = '';
    public $term_id = '';

    /**
     * Ensure only authorized school users can access this page.
     */
    private function authorizeAccess(): void
    {
        $user = Auth::user();

        abort_unless($user && $user->school_id, 403);

        abort_unless(
            $user->can('create teacher assignments'),
            403,
            'You do not have permission to create teacher assignments.'
        );
    }

    private function schoolId(): int
    {
        $this->authorizeAccess();

        return (int) Auth::user()->school_id;
    }

    protected function rules(): array
    {
        $schoolId = $this->schoolId();

        return [
            'teacher_id' => [
                'required',
                'integer',
                Rule::exists('teachers', 'id')
                    ->where('school_id', $schoolId)
                    ->where('employment_status', 'active'),
            ],

            'subject_id' => [
                'required',
                'integer',
                Rule::exists('subjects', 'id')
                    ->where('school_id', $schoolId)
                    ->where('status', 'active'),
            ],

            'class_room_id' => [
                'required',
                'integer',
                Rule::exists('class_rooms', 'id')
                    ->where('school_id', $schoolId)
                    ->where('status', 'active'),
            ],

            'section_id' => [
                'nullable',
                'integer',
                Rule::exists('sections', 'id')
                    ->where('school_id', $schoolId)
                    ->where('class_room_id', $this->class_room_id)
                    ->where('status', 'active'),
            ],

            'academic_session_id' => [
                'required',
                'integer',
                Rule::exists('academic_sessions', 'id')
                    ->where('school_id', $schoolId),
            ],

            'term_id' => [
                'nullable',
                'integer',
                Rule::exists('terms', 'id')
                    ->where('school_id', $schoolId)
                    ->where('academic_session_id', $this->academic_session_id),
            ],
        ];
    }

    /**
     * Clear selected teacher when search changes.
     */
    public function updatedTeacherSearch(): void
    {
        $this->teacher_id = '';

        $this->resetValidation('teacher_id');
    }

    /**
     * Select a teacher from search results.
     */
    public function selectTeacher(int $teacherId): void
    {
        $teacher = Teacher::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->where('employment_status', 'active')
            ->findOrFail($teacherId);

        $this->teacher_id = (string) $teacher->id;

        $this->teacherSearch = trim(
            ($teacher->first_name ?? '') . ' ' .
            ($teacher->middle_name ?? '') . ' ' .
            ($teacher->last_name ?? '')
        );

        $this->resetValidation('teacher_id');
    }

    /**
     * Clear selected teacher.
     */
    public function clearTeacher(): void
    {
        $this->teacher_id = '';
        $this->teacherSearch = '';

        $this->resetValidation('teacher_id');
    }

    /**
     * Reset section when class changes.
     */
    public function updatedClassRoomId(): void
    {
        $this->section_id = '';

        $this->resetValidation('section_id');
    }

    /**
     * Reset term when academic session changes.
     */
    public function updatedAcademicSessionId(): void
    {
        $this->term_id = '';

        $this->resetValidation('term_id');
    }

    /**
     * Save teacher assignment.
     */
    public function save(): void
    {
        $schoolId = $this->schoolId();

        $validated = $this->validate();

        $sectionId = $validated['section_id'] ?: null;
        $termId = $validated['term_id'] ?: null;

        // Check for an existing assignment.
        $duplicateQuery = TeacherAssignment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('teacher_id', $validated['teacher_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('class_room_id', $validated['class_room_id'])
            ->where('academic_session_id', $validated['academic_session_id']);

        $sectionId === null
            ? $duplicateQuery->whereNull('section_id')
            : $duplicateQuery->where('section_id', $sectionId);

        $termId === null
            ? $duplicateQuery->whereNull('term_id')
            : $duplicateQuery->where('term_id', $termId);

        if ($duplicateQuery->exists()) {
            $message = 'This teacher has already been assigned to this subject, class and academic period.';

            $this->addError('teacher_id', $message);

            $this->dispatch(
                'assignment-duplicate',
                message: $message
            );

            return;
        }

        DB::transaction(function () use (
            $validated,
            $schoolId,
            $sectionId,
            $termId
        ) {
            TeacherAssignment::create([
                'school_id' => $schoolId,
                'teacher_id' => $validated['teacher_id'],
                'subject_id' => $validated['subject_id'],
                'class_room_id' => $validated['class_room_id'],
                'section_id' => $sectionId,
                'academic_session_id' => $validated['academic_session_id'],
                'term_id' => $termId,
            ]);
        });

        session()->flash(
            'success',
            'Teacher assignment created successfully.'
        );

        $this->redirectRoute('school.teacher-assignments.index');
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $teachers = collect();

        // Search active teachers in the current school only.
        if (!$this->teacher_id && trim($this->teacherSearch) !== '') {
            $search = trim($this->teacherSearch);

            $teachers = Teacher::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('employment_status', 'active')
                ->where(function ($query) use ($search) {
                    $query->where('first_name', 'ilike', "%{$search}%")
                        ->orWhere('middle_name', 'ilike', "%{$search}%")
                        ->orWhere('last_name', 'ilike', "%{$search}%")
                        ->orWhere('staff_number', 'ilike', "%{$search}%");
                })
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->limit(10)
                ->get();
        }

        $selectedTeacher = $this->teacher_id
            ? Teacher::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('employment_status', 'active')
                ->find($this->teacher_id)
            : null;

        return view(
            'livewire.school.teacher-assignments.teacher-assignment-create',
            [
                'teachers' => $teachers,
                'selectedTeacher' => $selectedTeacher,

                'subjects' => Subject::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get(),

                'classes' => ClassRoom::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get(),

                'sections' => Section::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('school_id', $schoolId)
                    ->when(
                        $this->class_room_id,
                        fn ($query) => $query->where(
                            'class_room_id',
                            $this->class_room_id
                        )
                    )
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get(),

                'sessions' => AcademicSession::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->orderByDesc('starts_at')
                    ->get(),

                'terms' => Term::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->when(
                        $this->academic_session_id,
                        fn ($query) => $query->where(
                            'academic_session_id',
                            $this->academic_session_id
                        )
                    )
                    ->orderBy('starts_at')
                    ->get(),
            ]
        )->layout('components.layouts.school');
    }
}
