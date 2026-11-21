<?php

namespace App\Livewire\Parent\Timetable;

use App\Models\SchoolParent;
use App\Models\Enrollment;
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

        abort_if(
            $children->isEmpty(),
            404,
            'No linked students were found.'
        );

        $this->studentId = (int) $children->first()->id;
    }

    public function updatedStudentId(): void
    {
        $this->authorizeSelectedChild();
    }

    /**
     * Ensure the selected student belongs to the authenticated parent.
     */
    private function authorizeSelectedChild(): void
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

        abort_unless(
            $parent->students()
                ->wherePivot('school_id', $user->school_id)
                ->where('students.id', $this->studentId)
                ->exists(),
            403,
            'You are not authorized to view this student.'
        );
    }

    public function render()
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

        $timetable = collect();

        $enrollment = null;
        $student = null;

        if ($this->studentId !== null) {

            // Security: confirm selected child belongs to this parent.
            $this->authorizeSelectedChild();

            $student = $children->firstWhere('id', $this->studentId);

            if ($student) {

                /*
                |--------------------------------------------------------------------------
                | Find the student's active enrollment
                |--------------------------------------------------------------------------
                | Class, section, academic session and term are stored
                | in the enrollments table, NOT in the students table.
                |--------------------------------------------------------------------------
                */

                $enrollment = Enrollment::query()
                    ->where('school_id', $user->school_id)
                    ->where('student_id', $student->id)
                    ->where('admission_status', 'active')
                    ->whereNull('end_date')
                    ->orderByDesc('start_date')
                    ->orderByDesc('id')
                    ->first();

                if ($enrollment) {

                    /*
                    |--------------------------------------------------------------------------
                    | Retrieve timetable for the student's exact enrollment
                    |--------------------------------------------------------------------------
                    */

                    $query = TimetableEntry::query()
                        ->where('school_id', $user->school_id)
                        ->where(
                            'academic_session_id',
                            $enrollment->academic_session_id
                        )
                        ->where(
                            'term_id',
                            $enrollment->term_id
                        )
                        ->where(
                            'class_room_id',
                            $enrollment->class_room_id
                        );

                    // Match the student's exact section.
                    if ($enrollment->section_id) {
                        $query->where(
                            'section_id',
                            $enrollment->section_id
                        );
                    } else {
                        $query->whereNull('section_id');
                    }

                    $timetable = $query
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
        }

        return view(
            'livewire.parent.timetable.timetable-index',
            compact(
                'children',
                'timetable',
                'student',
                'enrollment'
            )
        )->layout('components.layouts.parent');
    }
}