<?php

namespace App\Livewire\Teacher;

use App\Models\Teacher as TeacherModel;
use App\Models\TimetableEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyTimetable extends Component
{
    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless($user->hasRole('Teacher'), 403);

        abort_unless(
            $user->school_id,
            403,
            'Your account is not assigned to a school.'
        );

        // Find the authenticated teacher within their own school.
        $teacher = TeacherModel::query()
            ->where('school_id', $user->school_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Retrieve only timetable entries assigned to this teacher.
        $timetable = TimetableEntry::query()
            ->where('school_id', $user->school_id)
            ->where('teacher_id', $teacher->id)
            ->with([
                'academicSession',
                'term',
                'classRoom',
                'section',
                'subject',
            ])
            ->get()
            ->sortBy(function ($entry) {
                $days = [
                    'monday' => 1,
                    'tuesday' => 2,
                    'wednesday' => 3,
                    'thursday' => 4,
                    'friday' => 5,
                    'saturday' => 6,
                    'sunday' => 7,
                ];

                $day = strtolower(trim((string) $entry->day_of_week));

                return [
                    $days[$day] ?? 99,
                    (string) $entry->starts_at,
                ];
            })
            ->values();

        return view('livewire.teacher.my-timetable', [
            'teacher' => $teacher,
            'timetable' => $timetable,
        ])->layout('components.layouts.teacher', [
            'title' => 'My Timetable',
        ]);
    }
}
