<?php

namespace App\Livewire\School\Enrollments;

use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class EnrollmentEdit extends Component
{
    #[Locked]
    public int $enrollmentId;

    public string $student_id = '';
    public string $academic_session_id = '';
    public string $term_id = '';
    public string $class_room_id = '';
    public string $section_id = '';
    public string $admission_status = 'active';
    public string $start_date = '';
    public string $end_date = '';

    public function mount(int $enrollmentId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $enrollment = Enrollment::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->whereKey($enrollmentId)
            ->firstOrFail();

        $this->enrollmentId = $enrollment->id;
        $this->student_id = (string) $enrollment->student_id;
        $this->academic_session_id = (string) $enrollment->academic_session_id;
        $this->term_id = (string) ($enrollment->term_id ?? '');
        $this->class_room_id = (string) $enrollment->class_room_id;
        $this->section_id = (string) ($enrollment->section_id ?? '');
        $this->admission_status = $enrollment->admission_status;

        $this->start_date = $enrollment->start_date
            ? date('Y-m-d', strtotime($enrollment->start_date))
            : '';

        $this->end_date = $enrollment->end_date
            ? date('Y-m-d', strtotime($enrollment->end_date))
            : '';
    }

    public function updatedAcademicSessionId(): void
    {
        $this->term_id = '';
    }

    public function updatedClassRoomId(): void
    {
        $this->section_id = '';
    }

    public function save()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = $user->school_id;

        $validated = $this->validate([
            'student_id' => [
                'required',
                Rule::exists('students', 'id')
                    ->where('school_id', $schoolId),
            ],
            'academic_session_id' => [
                'required',
                Rule::exists('academic_sessions', 'id')
                    ->where('school_id', $schoolId),
            ],
            'term_id' => [
                'nullable',
                Rule::exists('terms', 'id')
                    ->where('school_id', $schoolId),
            ],
            'class_room_id' => [
                'required',
                Rule::exists('class_rooms', 'id')
                    ->where('school_id', $schoolId),
            ],
            'section_id' => [
                'nullable',
                Rule::exists('sections', 'id')
                    ->where('school_id', $schoolId),
            ],
            'admission_status' => [
                'required',
                Rule::in(['active', 'pending', 'completed', 'withdrawn']),
            ],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        // Verify the term belongs to the selected academic session.
        if ($validated['term_id'] !== null && $validated['term_id'] !== '') {
            $termExists = Term::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->whereKey($validated['term_id'])
                ->where('academic_session_id', $validated['academic_session_id'])
                ->exists();

            if (! $termExists) {
                $this->addError('term_id', 'Select a term from the chosen academic session.');
                return;
            }
        }

        // Verify the section belongs to the selected class.
        if ($validated['section_id'] !== null && $validated['section_id'] !== '') {
            $sectionExists = Section::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->whereKey($validated['section_id'])
                ->where('class_room_id', $validated['class_room_id'])
                ->exists();

            if (! $sectionExists) {
                $this->addError('section_id', 'Select a section from the chosen class.');
                return;
            }
        }

        // Re-fetch the enrollment to enforce tenant ownership at save time.
        $enrollment = Enrollment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereKey($this->enrollmentId)
            ->firstOrFail();

        $enrollment->update([
            'student_id' => $validated['student_id'],
            'academic_session_id' => $validated['academic_session_id'],
            'term_id' => $validated['term_id'] ?: null,
            'class_room_id' => $validated['class_room_id'],
            'section_id' => $validated['section_id'] ?: null,
            'admission_status' => $validated['admission_status'],
            'start_date' => $validated['start_date'] ?: null,
            'end_date' => $validated['end_date'] ?: null,
        ]);

        session()->flash('success', 'Enrollment updated successfully.');

        return $this->redirectRoute('school.enrollments.index');
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = $user->school_id;

        return view('livewire.school.enrollments.enrollment-edit', [
            'students' => Student::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderBy('first_name')
                ->get(),

            'sessions' => AcademicSession::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderByDesc('starts_at')
                ->get(),

            'terms' => Term::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('academic_session_id', $this->academic_session_id ?: 0)
                ->orderBy('starts_at')
                ->get(),

            'classes' => ClassRoom::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderBy('name')
                ->get(),

            'sections' => Section::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('class_room_id', $this->class_room_id ?: 0)
                ->orderBy('name')
                ->get(),
        ])->layout('components.layouts.school', [
            'title' => 'Edit Enrollment',
        ]);
    }
}