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
use Livewire\Attributes\Locked;
use Livewire\Component;

class TeacherAssignmentEdit extends Component
{
    #[Locked]
    public int $assignmentId;

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

    public function mount(int $assignmentId): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->can('edit teacher assignments'),
            403
        );

        $assignment = TeacherAssignment::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->findOrFail($assignmentId);

        $this->assignmentId = $assignment->id;
        $this->teacher_id = (string) $assignment->teacher_id;
        $this->subject_id = (string) $assignment->subject_id;
        $this->class_room_id = (string) $assignment->class_room_id;
        $this->section_id = (string) ($assignment->section_id ?? '');
        $this->academic_session_id = (string) $assignment->academic_session_id;
        $this->term_id = (string) ($assignment->term_id ?? '');
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

    public function update(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->can('edit teacher assignments'),
            403
        );

        $validated = $this->validate();

        $schoolId = $this->schoolId();

        $assignment = TeacherAssignment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->findOrFail($this->assignmentId);

        $duplicate = TeacherAssignment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('id', '!=', $assignment->id)
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

        $assignment->update([
            ...$validated,
            'section_id' => $validated['section_id'] ?: null,
            'term_id' => $validated['term_id'] ?: null,
        ]);

        session()->flash('success', 'Teacher assignment updated successfully.');

        $this->redirectRoute('school.teacher-assignments.index');
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        return view('livewire.school.teacher-assignments.teacher-assignment-edit', [
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
