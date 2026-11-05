<?php

namespace App\Livewire\Parent\Timetable;

use App\Models\SchoolParent;
use App\Models\TimetableEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TimetableIndex extends Component
{
    public ?int $studentId = null;

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasRole('Parent'),
            403
        );

        abort_unless($user->school_id, 403);

        $parent = SchoolParent::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $children = $parent->students()
            ->wherePivot('school_id', $user->school_id)
            ->get();

        if ($children->isEmpty()) {
            abort(404, 'No linked students were found.');
        }

        $this->studentId = (int) $children->first()->id;
    }

    public function updatedStudentId(): void
    {
        $this->authorizeSelectedChild();
    }

    private function authorizeSelectedChild(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasRole('Parent'),
            403
        );

        $parent = SchoolParent::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        abort_unless(
            $parent->students()
                ->wherePivot('school_id', $user->school_id)
                ->where('students.id', $this->studentId)
                ->exists(),
            403
        );
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasRole('Parent'),
            403
        );

        $parent = SchoolParent::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $children = $parent->students()
            ->wherePivot('school_id', $user->school_id)
            ->get();

        $timetable = collect();

        if ($this->studentId !== null) {
            $this->authorizeSelectedChild();

            $student = $children->firstWhere('id', $this->studentId);

            if ($student && $student->class_room_id) {
                $timetable = TimetableEntry::query()
                    ->where('school_id', $user->school_id)
                    ->where('class_room_id', $student->class_room_id)
                    ->where(function ($query) use ($student) {
                        if (isset($student->section_id)) {
                            $query->where('section_id', $student->section_id)
                                ->orWhereNull('section_id');
                        } else {
                            $query->whereNull('section_id');
                        }
                    })
                    ->with([
                        'academicSession',
                        'term',
                        'classRoom',
                        'section',
                        'subject',
                        'teacher',
                    ])
                    ->orderByRaw("
                        CASE day_of_week
                            WHEN 'Monday' THEN 1
                            WHEN 'Tuesday' THEN 2
                            WHEN 'Wednesday' THEN 3
                            WHEN 'Thursday' THEN 4
                            WHEN 'Friday' THEN 5
                            WHEN 'Saturday' THEN 6
                            WHEN 'Sunday' THEN 7
                            ELSE 8
                        END
                    ")
                    ->orderBy('starts_at')
                    ->get()
                    ->groupBy('day_of_week');
            }
        }

        return view(
            'livewire.parent.timetable.timetable-index',
            compact('children', 'timetable')
        )->layout('components.layouts.parent');
    }
}
