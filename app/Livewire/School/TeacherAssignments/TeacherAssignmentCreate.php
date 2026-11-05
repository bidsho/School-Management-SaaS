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
use Illuminate\Validation\Rule;
use Livewire\Component;

class TeacherAssignmentCreate extends Component
{
    public $teacher_id = '';
    public $subject_id = '';
    public $class_room_id = '';
    public $section_id = '';
    public $academic_session_id = '';
    public $term_id = '';

    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user && $user->school_id, 403);

        return (int) $user->school_id;
    }

    protected function rules(): array
    {
        $schoolId = $this->schoolId();

        return [
            'teacher_id' => [
                'required',
                'integer',
                Rule::exists('teachers', 'id')
                    ->where('school_id', $schoolId),
            ],
            'subject_id' => [
                'required',
                'integer',
                Rule::exists('subjects', 'id')
                    ->where('school_id', $schoolId),
            ],
            'class_room_id' => [
                'required',
                'integer',
                Rule::exists('class_rooms', 'id')
                    ->where('school_id', $schoolId),
            ],
            'section_id' => [
                'nullable',
                'integer',
                Rule::exists('sections', 'id')
                    ->where('school_id', $schoolId)
                    ->where('class_room_id', $this->class_room_id),
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

    public function updatedClassRoomId(): void
    {
        $this->section_id = '';
    }

    public function updatedAcademicSessionId(): void
    {
        $this->term_id = '';
    }

    public function save(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->can('create teacher assignments'),
            403
        );

        $validated = $this->validate();

        $schoolId = $this->schoolId();

        $duplicate = TeacherAssignment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('teacher_id', $validated['teacher_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('class_room_id', $validated['class_room_id'])
            ->where('section_id', $validated['section_id'] ?: null)
            ->where('academic_session_id', $validated['academic_session_id'])
            ->where('term_id', $validated['term_id'] ?: null)
            ->exists();

        if ($duplicate) {
            $this->addError(
                'teacher_id',
                'This teacher assignment already exists.'
            );

            return;
        }

        TeacherAssignment::create([
            ...$validated,
            'school_id' => $schoolId,
            'section_id' => $validated['section_id'] ?: null,
            'term_id' => $validated['term_id'] ?: null,
        ]);

        session()->flash('success', 'Teacher assignment created successfully.');

        $this->redirectRoute('school.teacher-assignments.index');
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        return view('livewire.school.teacher-assignments.teacher-assignment-create', [
            'teachers' => Teacher::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),

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
                ->when($this->class_room_id, fn ($query) =>
                    $query->where('class_room_id', $this->class_room_id)
                )
                ->orderBy('name')
                ->get(),

            'sessions' => AcademicSession::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderByDesc('starts_at')
                ->get(),

            'terms' => Term::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->when($this->academic_session_id, fn ($query) =>
                    $query->where(
                        'academic_session_id',
                        $this->academic_session_id
                    )
                )
                ->orderBy('starts_at')
                ->get(),
        ])->layout('components.layouts.school');
    }
}
