<?php

namespace App\Livewire\School\ClassHeadTeachers;

use App\Models\AcademicSession;
use App\Models\ClassHeadTeacher;
use App\Models\ClassRoom;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ClassHeadTeacherIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $teacher_id = '';
    public string $class_room_id = '';
    public string $academic_session_id = '';
    public string $starts_at = '';
    public string $ends_at = '';

    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user && $user->school_id, 403);

        return (int) $user->school_id;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();

        $this->academic_session_id = (string) (
            AcademicSession::withoutGlobalScopes()
                ->where('school_id', $this->schoolId())
                ->where('status', 'active')
                ->value('id') ?? ''
        );

        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $assignment = ClassHeadTeacher::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->findOrFail($id);

        $this->editingId = $assignment->id;
        $this->teacher_id = (string) $assignment->teacher_id;
        $this->class_room_id = (string) $assignment->class_room_id;
        $this->academic_session_id = (string) $assignment->academic_session_id;
        $this->starts_at = $assignment->starts_at?->format('Y-m-d') ?? '';
        $this->ends_at = $assignment->ends_at?->format('Y-m-d') ?? '';

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $schoolId = $this->schoolId();

        $validated = $this->validate([
            'teacher_id' => [
                'required',
                'integer',
                Rule::exists('teachers', 'id')
                    ->where('school_id', $schoolId),
            ],
            'class_room_id' => [
                'required',
                'integer',
                Rule::exists('class_rooms', 'id')
                    ->where('school_id', $schoolId),
            ],
            'academic_session_id' => [
                'required',
                'integer',
                Rule::exists('academic_sessions', 'id')
                    ->where('school_id', $schoolId),
            ],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $classId = (int) $validated['class_room_id'];
        $sessionId = (int) $validated['academic_session_id'];

        // The database allows one assignment per class and session.
        // Reuse that record instead of creating a duplicate.
        $assignment = ClassHeadTeacher::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('class_room_id', $classId)
            ->where('academic_session_id', $sessionId)
            ->first();

        if ($this->editingId) {
            $assignment = ClassHeadTeacher::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->findOrFail($this->editingId);

            // Do not let an edit collide with another class/session record.
            $duplicate = ClassHeadTeacher::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('class_room_id', $classId)
                ->where('academic_session_id', $sessionId)
                ->whereKeyNot($assignment->id)
                ->exists();

            if ($duplicate) {
                $this->addError(
                    'class_room_id',
                    'This class already has an assignment for that academic session. Edit the existing assignment instead.'
                );

                return;
            }
        }

        $assignment ??= new ClassHeadTeacher();

        $assignment->fill([
            'school_id' => $schoolId,
            'teacher_id' => (int) $validated['teacher_id'],
            'class_room_id' => $classId,
            'academic_session_id' => $sessionId,
            'assigned_by' => Auth::id(),
            'status' => 'active',
            'starts_at' => $validated['starts_at'] ?: null,
            'ends_at' => $validated['ends_at'] ?: null,
        ]);

        $assignment->save();

        session()->flash(
            'success',
            'Class Head Teacher assignment saved successfully.'
        );

        $this->showModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function deactivate(int $id): void
    {
        $assignment = ClassHeadTeacher::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->findOrFail($id);

        $assignment->update(['status' => 'inactive']);

        session()->flash('success', 'Assignment deactivated successfully.');
    }

    public function reactivate(int $id): void
    {
        $assignment = ClassHeadTeacher::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->findOrFail($id);

        $duplicate = ClassHeadTeacher::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->where('class_room_id', $assignment->class_room_id)
            ->where('academic_session_id', $assignment->academic_session_id)
            ->where('status', 'active')
            ->whereKeyNot($assignment->id)
            ->exists();

        if ($duplicate) {
            session()->flash(
                'error',
                'Another active assignment already exists for this class and session.'
            );

            return;
        }

        $assignment->update(['status' => 'active']);

        session()->flash('success', 'Assignment reactivated successfully.');
    }

    private function assignmentsQuery(): Builder
    {
        return ClassHeadTeacher::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->with(['teacher', 'classRoom', 'academicSession', 'assignedBy'])
            ->when(trim($this->search) !== '', function (Builder $query) {
                $search = trim($this->search);

                $query->where(function (Builder $q) use ($search) {
                    $q->whereHas('teacher', function (Builder $teacher) use ($search) {
                        $teacher->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('staff_number', 'like', "%{$search}%");
                    })->orWhereHas('classRoom', function (Builder $class) use ($search) {
                        $class->where('name', 'like', "%{$search}%");
                    })->orWhereHas('academicSession', function (Builder $session) use ($search) {
                        $session->where('name', 'like', "%{$search}%");
                    });
                });
            });
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'teacher_id',
            'class_room_id',
            'academic_session_id',
            'starts_at',
            'ends_at',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        return view('livewire.school.class-head-teachers.class-head-teacher-index', [
            'assignments' => $this->assignmentsQuery()
                ->orderByDesc('id')
                ->paginate(10),

            'teachers' => Teacher::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),

            'classes' => ClassRoom::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderBy('name')
                ->get(),

            'sessions' => AcademicSession::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderByDesc('starts_at')
                ->get(),

            'totalAssignments' => ClassHeadTeacher::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->count(),

            'activeAssignments' => ClassHeadTeacher::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('status', 'active')
                ->count(),

            'inactiveAssignments' => ClassHeadTeacher::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('status', 'inactive')
                ->count(),
        ])->layout('components.layouts.school');
    }
}

