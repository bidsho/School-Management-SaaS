<?php

namespace App\Livewire\School\Timetables;

use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\TimetableEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class TimetableIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $dayFilter = '';

    public string $classFilter = '';

    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    public $academic_session_id = '';
    public $term_id = '';
    public $class_room_id = '';
    public $section_id = '';
    public $subject_id = '';
    public $teacher_id = '';

    public string $day_of_week = 'Monday';
    public string $starts_at = '';
    public string $ends_at = '';
    public string $room = '';

    private function schoolId(): int
    {
        abort_unless(
            auth()->check() && auth()->user()->school_id,
            403,
            'You are not assigned to a school.'
        );

        return (int) auth()->user()->school_id;
    }

    private function authorizeManager(): void
    {
        abort_unless(
            auth()->user()?->hasRole('School Admin'),
            403,
            'Only a School Admin can manage the timetable.'
        );
    }

    private function entries(): Builder
    {
        return TimetableEntry::query()
            ->where('school_id', $this->schoolId());
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDayFilter(): void
    {
        $this->resetPage();
    }

    public function updatingClassFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorizeManager();

        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeManager();

        $entry = $this->entries()->findOrFail($id);

        $this->editingId = $entry->id;
        $this->academic_session_id = (string) $entry->academic_session_id;
        $this->term_id = (string) ($entry->term_id ?? '');
        $this->class_room_id = (string) $entry->class_room_id;
        $this->section_id = (string) ($entry->section_id ?? '');
        $this->subject_id = (string) $entry->subject_id;
        $this->teacher_id = (string) ($entry->teacher_id ?? '');
        $this->day_of_week = $entry->day_of_week;
        $this->starts_at = substr((string) $entry->starts_at, 0, 5);
        $this->ends_at = substr((string) $entry->ends_at, 0, 5);
        $this->room = (string) ($entry->room ?? '');

        $this->showForm = true;
    }

    protected function rules(): array
    {
        $schoolId = $this->schoolId();

        return [
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
                    ->where('school_id', $schoolId),
            ],

            'subject_id' => [
                'required',
                'integer',
                Rule::exists('subjects', 'id')
                    ->where('school_id', $schoolId),
            ],

            'teacher_id' => [
                'nullable',
                'integer',
                Rule::exists('teachers', 'id')
                    ->where('school_id', $schoolId),
            ],

            'day_of_week' => [
                'required',
                Rule::in([
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday',
                    'Sunday',
                ]),
            ],

            'starts_at' => ['required', 'date_format:H:i'],
            'ends_at' => [
                'required',
                'date_format:H:i',
                'after:starts_at',
            ],

            'room' => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function messages(): array
    {
        return [
            'academic_session_id.required' => 'Please select an academic session.',
            'class_room_id.required' => 'Please select a class.',
            'subject_id.required' => 'Please select a subject.',
            'ends_at.after' => 'The ending time must be later than the starting time.',
            '*.exists' => 'One of your selected options is invalid for this school.',
        ];
    }

    public function save(): void
    {
        $this->authorizeManager();

        $validated = $this->validate();

        // Re-check the existing record within the authenticated school.
        $entry = $this->editingId
            ? $this->entries()->findOrFail($this->editingId)
            : new TimetableEntry();

        // Extra validation: section must belong to the selected class.
        if ($this->section_id !== '') {
            $section = Section::query()
                ->where('school_id', $this->schoolId())
                ->whereKey($this->section_id)
                ->firstOrFail();

            if ((int) $section->class_room_id !== (int) $this->class_room_id) {
                $this->addError(
                    'section_id',
                    'The selected section does not belong to this class.'
                );

                return;
            }
        }

        $validated['school_id'] = $this->schoolId();

        $validated['term_id'] = $this->term_id ?: null;
        $validated['section_id'] = $this->section_id ?: null;
        $validated['teacher_id'] = $this->teacher_id ?: null;
        $validated['room'] = trim($this->room) !== ''
            ? trim($this->room)
            : null;

        $entry->fill($validated);
        $entry->save();

        $this->resetForm();
        $this->resetPage();

        session()->flash(
            'success',
            $this->editingId
                ? 'Timetable entry updated successfully.'
                : 'Timetable entry created successfully.'
        );
    }

    public function delete(int $id): void
    {
        $this->authorizeManager();

        $entry = $this->entries()->findOrFail($id);
        $entry->delete();

        $this->resetPage();

        session()->flash('success', 'Timetable entry deleted successfully.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->resetValidation();

        $this->editingId = null;
        $this->academic_session_id = '';
        $this->term_id = '';
        $this->class_room_id = '';
        $this->section_id = '';
        $this->subject_id = '';
        $this->teacher_id = '';
        $this->day_of_week = 'Monday';
        $this->starts_at = '';
        $this->ends_at = '';
        $this->room = '';
        $this->showForm = false;
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $entries = $this->entries()
            ->with([
                'academicSession',
                'term',
                'classRoom',
                'section',
                'subject',
                'teacher',
            ])
            ->when($this->dayFilter !== '', function (Builder $query) {
                $query->where('day_of_week', $this->dayFilter);
            })
            ->when($this->classFilter !== '', function (Builder $query) {
                $query->where('class_room_id', $this->classFilter);
            })
            ->when(trim($this->search) !== '', function (Builder $query) {
                $search = '%' . trim($this->search) . '%';

                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery
                        ->where('day_of_week', 'like', $search)
                        ->orWhere('room', 'like', $search)
                        ->orWhereHas('classRoom', function (Builder $q) use ($search) {
                            $q->where('name', 'like', $search);
                        })
                        ->orWhereHas('subject', function (Builder $q) use ($search) {
                            $q->where('name', 'like', $search);
                        })
                        ->orWhereHas('teacher', function (Builder $q) use ($search) {
                            $q->where(function (Builder $teacherQuery) use ($search) {
                                $teacherQuery
                                    ->where('first_name', 'like', $search)
                                    ->orWhere('middle_name', 'like', $search)
                                    ->orWhere('last_name', 'like', $search);
                            });
                        });
                });
            })
            ->orderByRaw(
                "CASE day_of_week
                    WHEN 'Monday' THEN 1
                    WHEN 'Tuesday' THEN 2
                    WHEN 'Wednesday' THEN 3
                    WHEN 'Thursday' THEN 4
                    WHEN 'Friday' THEN 5
                    WHEN 'Saturday' THEN 6
                    WHEN 'Sunday' THEN 7
                    ELSE 8
                END"
            )
            ->orderBy('starts_at')
            ->paginate(10);

        return view('livewire.school.timetables.timetable-index', [
            'entries' => $entries,
            'sessions' => AcademicSession::where('school_id', $schoolId)
                ->orderByDesc('id')
                ->get(),
            'terms' => Term::where('school_id', $schoolId)
                ->orderByDesc('id')
                ->get(),
            'classes' => ClassRoom::where('school_id', $schoolId)
                ->orderBy('name')
                ->get(),
            'sections' => Section::where('school_id', $schoolId)
                ->orderBy('name')
                ->get(),
            'subjects' => Subject::where('school_id', $schoolId)
                ->orderBy('name')
                ->get(),
            'teachers' => Teacher::where('school_id', $schoolId)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'days' => [
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday',
            ],
        ])->layout('components.layouts.school', [
            'title' => 'Timetable Management',
        ]);
    }
}
