<?php

namespace App\Livewire\School\Exams;

use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class ExamIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    // Filters
    public string $search = '';
    public string $status = '';
    public string $type = '';
    public string $exam_date = '';
    public string $class_room_id = '';
    public string $subject_id = '';
    public string $academic_session_id = '';
    public string $term_id = '';

    // Create exam form
    public bool $showCreateModal = false;
    public string $name = '';
    public string $new_type = 'exam';
    public string $new_exam_date = '';
    public string $total_marks = '100';
    public string $new_class_room_id = '';
    public string $new_subject_id = '';
    public string $new_academic_session_id = '';
    public string $new_term_id = '';

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

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function updatingExamDate(): void
    {
        $this->resetPage();
    }

    public function updatingClassRoomId(): void
    {
        $this->resetPage();
    }

    public function updatingSubjectId(): void
    {
        $this->resetPage();
    }

    public function updatingAcademicSessionId(): void
    {
        $this->term_id = '';
        $this->resetPage();
    }

    public function updatingTermId(): void
    {
        $this->resetPage();
    }

    public function updatedNewAcademicSessionId(): void
    {
        $this->new_term_id = '';
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'status',
            'type',
            'exam_date',
            'class_room_id',
            'subject_id',
            'academic_session_id',
            'term_id',
        ]);

        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();

        $this->reset([
            'name',
            'new_exam_date',
            'new_class_room_id',
            'new_subject_id',
            'new_term_id',
        ]);

        $this->new_type = 'exam';
        $this->total_marks = '100';

        $schoolId = $this->schoolId();

        $session = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->first();

        $this->new_academic_session_id = $session
            ? (string) $session->id
            : '';

        $term = $session
            ? Term::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('academic_session_id', $session->id)
                ->where('status', 'active')
                ->first()
            : null;

        $this->new_term_id = $term ? (string) $term->id : '';

        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function saveExam(): void
    {
        $schoolId = $this->schoolId();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'new_type' => ['required', 'in:exam,test,quiz,assessment,practical,other'],
            'new_exam_date' => ['nullable', 'date'],
            'total_marks' => ['required', 'numeric', 'gt:0', 'max:999999.99'],
            'new_class_room_id' => ['required', 'integer'],
            'new_subject_id' => ['required', 'integer'],
            'new_academic_session_id' => ['required', 'integer'],
            'new_term_id' => ['required', 'integer'],
        ]);

        // Confirm that all selected records belong to this school.
        $classExists = ClassRoom::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereKey($validated['new_class_room_id'])
            ->exists();

        $subjectExists = Subject::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereKey($validated['new_subject_id'])
            ->exists();

        $sessionExists = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereKey($validated['new_academic_session_id'])
            ->exists();

        $termExists = Term::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('academic_session_id', $validated['new_academic_session_id'])
            ->whereKey($validated['new_term_id'])
            ->exists();

        if (!$classExists || !$subjectExists || !$sessionExists || !$termExists) {
            throw ValidationException::withMessages([
                'new_term_id' => 'The selected class, subject, session or term is invalid for your school.',
            ]);
        }

        Exam::create([
            'school_id' => $schoolId,
            'name' => trim($validated['name']),
            'type' => $validated['new_type'],
            'exam_date' => $validated['new_exam_date'] ?: null,
            'total_marks' => $validated['total_marks'],
            'class_room_id' => $validated['new_class_room_id'],
            'subject_id' => $validated['new_subject_id'],
            'academic_session_id' => $validated['new_academic_session_id'],
            'term_id' => $validated['new_term_id'],
            'status' => 'scheduled',
            'created_by' => Auth::id(),
        ]);

        $this->showCreateModal = false;

        $this->reset([
            'name',
            'new_exam_date',
            'new_class_room_id',
            'new_subject_id',
            'new_term_id',
        ]);

        $this->new_type = 'exam';
        $this->total_marks = '100';

        session()->flash('success', 'Examination created successfully. The assigned teacher can now enter scores.');

        $this->resetPage();
    }

    private function examsQuery(): Builder
    {
        $schoolId = $this->schoolId();

        return Exam::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with([
                'academicSession',
                'term',
                'classRoom',
                'subject',
                'creator',
            ])
            ->when(trim($this->search) !== '', function (Builder $query) {
                $search = trim($this->search);

                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhereHas('subject', function (Builder $subject) use ($search) {
                            $subject->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('classRoom', function (Builder $class) use ($search) {
                            $class->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($this->status !== '', fn (Builder $query) =>
                $query->where('status', $this->status)
            )
            ->when($this->type !== '', fn (Builder $query) =>
                $query->where('type', $this->type)
            )
            ->when($this->exam_date !== '', fn (Builder $query) =>
                $query->whereDate('exam_date', $this->exam_date)
            )
            ->when($this->class_room_id !== '', fn (Builder $query) =>
                $query->where('class_room_id', $this->class_room_id)
            )
            ->when($this->subject_id !== '', fn (Builder $query) =>
                $query->where('subject_id', $this->subject_id)
            )
            ->when($this->academic_session_id !== '', fn (Builder $query) =>
                $query->where('academic_session_id', $this->academic_session_id)
            )
            ->when($this->term_id !== '', fn (Builder $query) =>
                $query->where('term_id', $this->term_id)
            );
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $baseQuery = Exam::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        return view('livewire.school.exams.exam-index', [
            'exams' => $this->examsQuery()
                ->orderByDesc('exam_date')
                ->orderByDesc('id')
                ->paginate(15),

            'totalExams' => (clone $baseQuery)->count(),

            'scheduledExams' => (clone $baseQuery)
                ->where('status', 'scheduled')
                ->count(),

            'completedExams' => (clone $baseQuery)
                ->where('status', 'completed')
                ->count(),

            'classes' => ClassRoom::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderBy('name')
                ->get(),

            'subjects' => Subject::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderBy('name')
                ->get(),

            'sessions' => AcademicSession::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderByDesc('starts_at')
                ->get(),

            'terms' => Term::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->when($this->academic_session_id !== '', fn ($query) =>
                    $query->where('academic_session_id', $this->academic_session_id)
                )
                ->orderBy('starts_at')
                ->get(),

            'createSessions' => AcademicSession::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->orderByDesc('starts_at')
                ->get(),

            'createTerms' => Term::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->when($this->new_academic_session_id !== '', fn ($query) =>
                    $query->where('academic_session_id', $this->new_academic_session_id)
                )
                ->orderBy('starts_at')
                ->get(),
        ])->layout('components.layouts.school');
    }
}
