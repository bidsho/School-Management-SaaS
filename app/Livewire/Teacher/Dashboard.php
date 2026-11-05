<?php

namespace App\Livewire\Teacher;

use App\Models\Teacher as TeacherModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);

        // This dashboard is for teacher accounts only.
        abort_unless($user->hasRole('Teacher'), 403);

        abort_unless(
            $user->school_id,
            403,
            'Your account is not assigned to a school.'
        );

        // Always scope the teacher profile to the logged-in user
        // and their school.
        $teacher = TeacherModel::query()
            ->where('school_id', $user->school_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $stats = [
            'teaching_assignments' => $teacher->assignments()->count(),
            'scheduled_lessons' => $teacher->timetableEntries()->count(),
            'assignments_created' => $teacher->assignmentsGiven()->count(),
        ];

        return view('livewire.teacher.dashboard', [
            'user' => $user,
            'teacher' => $teacher,
            'stats' => $stats,
        ])->layout('components.layouts.teacher', [
            'title' => 'Teacher Dashboard',
        ]);
    }
}
