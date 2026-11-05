<?php

namespace App\Livewire\Student;

use App\Models\Student as StudentModel;
use App\Models\TimetableEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClassTimetable extends Component
{
    private function getStudent(): StudentModel
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless($user->hasRole('Student'), 403);
        abort_unless($user->school_id, 403);

        return StudentModel::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();
    }

    public function render()
    {
        $student = $this->getStudent();

        $enrollment = $student->enrollments()
            ->where('school_id', $student->school_id)
            ->where('admission_status', 'active')
            ->with(['classRoom', 'academicSession', 'term'])
            ->latest('id')
            ->first();

        $timetable = collect();

        if ($enrollment) {
            $timetable = TimetableEntry::query()
                ->where('school_id', $student->school_id)
                ->where('class_room_id', $enrollment->class_room_id)
                ->where('academic_session_id', $enrollment->academic_session_id)
                ->where('term_id', $enrollment->term_id)
                ->with(['subject', 'teacher.user', 'classRoom', 'section'])
                ->orderBy('starts_at')
                ->get()
                ->groupBy('day_of_week');
        }

        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ];

        return view('livewire.student.class-timetable', [
            'student' => $student,
            'enrollment' => $enrollment,
            'timetable' => $timetable,
            'days' => $days,
        ])->layout('components.layouts.student', [
            'title' => 'Class Timetable',
        ]);
    }
}
