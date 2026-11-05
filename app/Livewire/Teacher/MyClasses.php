<?php

namespace App\Livewire\Teacher;

use App\Models\Enrollment;
use App\Models\Teacher as TeacherModel;
use App\Models\TeacherAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyClasses extends Component
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

        // Find this teacher within their own school.
        $teacher = TeacherModel::query()
            ->where('school_id', $user->school_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Retrieve only this teacher's assignments.
        $classAssignments = TeacherAssignment::query()
            ->where('school_id', $user->school_id)
            ->where('teacher_id', $teacher->id)
            ->with([
                'subject',
                'classRoom',
                'section',
                'academicSession',
                'term',
            ])
            ->orderBy('class_room_id')
            ->get();

        // Load students enrolled in each assigned class and section.
        foreach ($classAssignments as $assignment) {
            $enrollments = Enrollment::query()
                ->where('school_id', $user->school_id)
                ->where('class_room_id', $assignment->class_room_id)

                ->when(
                    $assignment->section_id !== null,
                    fn (Builder $query) => $query->where(
                        'section_id',
                        $assignment->section_id
                    )
                )

                ->when(
                    $assignment->academic_session_id !== null,
                    fn (Builder $query) => $query->where(
                        'academic_session_id',
                        $assignment->academic_session_id
                    )
                )

                ->when(
                    $assignment->term_id !== null,
                    fn (Builder $query) => $query->where(
                        'term_id',
                        $assignment->term_id
                    )
                )

                ->with(['student'])
                ->get()
                ->filter(
                    fn (Enrollment $enrollment) =>
                        $enrollment->student !== null
                )
                ->values();

            $assignment->setRelation('enrollments', $enrollments);
        }

        // Count unique students across the teacher's assigned classes.
        $totalStudents = $classAssignments
            ->flatMap(
                fn ($assignment) => $assignment->enrollments
            )
            ->pluck('student_id')
            ->filter()
            ->unique()
            ->count();

        $totalClasses = $classAssignments
            ->pluck('class_room_id')
            ->filter()
            ->unique()
            ->count();

        return view('livewire.teacher.my-classes', [
            'teacher' => $teacher,
            'classAssignments' => $classAssignments,
            'totalClasses' => $totalClasses,
            'totalStudents' => $totalStudents,
        ])->layout('components.layouts.teacher', [
            'title' => 'My Classes & Students',
        ]);
    }
}
