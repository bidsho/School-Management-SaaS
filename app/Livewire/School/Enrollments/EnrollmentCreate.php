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
use App\Livewire\Concerns\UsesPortalLayout;
use Livewire\Component;

class EnrollmentCreate extends Component
{
    use  UsesPortalLayout;
    public $student_id = '';
    public $academic_session_id = '';
    public $term_id = '';
    public $class_room_id = '';
    public $section_id = '';

    public string $admission_status = 'active';
    public string $start_date = '';
    public string $end_date = '';

    // Searchable student field
    public string $studentSearch = '';
    public bool $showStudentResults = false;

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user, 401);

        abort_if(
            $user->isSuperAdmin(),
            403,
            'Super Admin accounts must use platform routes.'
        );

        abort_unless(
            $user->school_id,
            403,
            'Your account is not assigned to a school.'
        );

        $this->start_date = now()->toDateString();
    }

    /**
     * Clear the previous selection when the search changes.
     */
    public function updatedStudentSearch(): void
    {
        $this->student_id = '';
        $this->showStudentResults = true;

        $this->resetValidation('student_id');
    }

    /**
     * Display the student search results.
     */
    public function openStudentResults(): void
    {
        $this->showStudentResults = true;
    }

    /**
     * Select a student from the results.
     */
    public function selectStudent(int $studentId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $student = Student::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->findOrFail($studentId);

        $this->student_id = (string) $student->id;

        $this->studentSearch = trim(
            $student->first_name . ' ' .
            $student->last_name . ' — ' .
            $student->admission_number
        );

        $this->showStudentResults = false;

        $this->resetValidation('student_id');
    }

    /**
     * Clear the selected student.
     */
    public function clearStudent(): void
    {
        $this->student_id = '';
        $this->studentSearch = '';
        $this->showStudentResults = false;

        $this->resetValidation('student_id');
    }

    /**
     * Clear dependent fields when the academic session changes.
     */
    public function updatedAcademicSessionId(): void
    {
        $this->term_id = '';
        $this->resetValidation('term_id');
    }

    /**
     * A section must belong to the selected class.
     */
    public function updatedClassRoomId(): void
    {
        $this->section_id = '';
        $this->resetValidation('section_id');
    }

    public function save()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = (int) $user->school_id;

        $validated = $this->validate([
            'student_id' => [
                'required',
                'integer',
                Rule::exists('students', 'id')
                    ->where('school_id', $schoolId),
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

            'admission_status' => [
                'required',
                Rule::in([
                    'pending',
                    'active',
                    'completed',
                    'withdrawn',
                    'transferred',
                ]),
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        // Ensure the selected term belongs to the selected session.
        if (
            !empty($validated['term_id']) &&
            !Term::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where(
                    'academic_session_id',
                    $validated['academic_session_id']
                )
                ->whereKey($validated['term_id'])
                ->exists()
        ) {
            $this->addError(
                'term_id',
                'Select a term belonging to the chosen academic session.'
            );

            return;
        }

        // Recheck that the student belongs to this school.
        $studentExists = Student::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereKey($validated['student_id'])
            ->exists();

        if (!$studentExists) {
            $this->addError(
                'student_id',
                'Please select a valid student from your school.'
            );

            return;
        }

        Enrollment::create([
            'school_id' => $schoolId,
            'student_id' => $validated['student_id'],
            'academic_session_id' => $validated['academic_session_id'],
            'term_id' => $validated['term_id'] ?: null,
            'class_room_id' => $validated['class_room_id'],
            'section_id' => $validated['section_id'] ?: null,
            'admission_status' => $validated['admission_status'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?: null,
        ]);

        session()->flash(
            'success',
            'Student enrollment created successfully.'
        );

        return $this->redirectRoute('school.enrollments.index');
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = (int) $user->school_id;

        // Search only when the user has entered a search term.
        $students = collect();
        $search = trim($this->studentSearch);

        if ($this->showStudentResults && $search !== '') {
            $students = Student::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where(function ($query) use ($search) {
                    $query->where(
                        'first_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'last_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'admission_number',
                        'like',
                        "%{$search}%"
                    );
                })
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->limit(10)
                ->get([
                    'id',
                    'first_name',
                    'last_name',
                    'admission_number',
                ]);
        }

        // Keep the selected student available after selection.
        $selectedStudent = null;

        if ($this->student_id !== '') {
            $selectedStudent = Student::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->find($this->student_id);
        }

        $academicSessions = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->get(['id', 'name']);

        $terms = Term::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->when(
                $this->academic_session_id,
                fn ($query) => $query->where(
                    'academic_session_id',
                    $this->academic_session_id
                )
            )
            ->orderBy('name')
            ->get(['id', 'name']);

        $classes = ClassRoom::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $sections = Section::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->when(
                $this->class_room_id,
                fn ($query) => $query->where(
                    'class_room_id',
                    $this->class_room_id
                )
            )
            ->orderBy('name')
            ->get(['id', 'name', 'class_room_id']);

        return view('livewire.school.enrollments.enrollment-create', [
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'academicSessions' => $academicSessions,
            'terms' => $terms,
            'classes' => $classes,
            'sections' => $sections,
        ])->layout($this->portalLayout(), [
            'title' => 'Create Enrollment',
        ]);
    }
}
