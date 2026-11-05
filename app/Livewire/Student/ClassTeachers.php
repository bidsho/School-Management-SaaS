<?php

namespace App\Livewire\Student;

use App\Models\Student as StudentModel;
use App\Models\TeacherAssignment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClassTeachers extends Component
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

        // Get the student's active enrollment.
        $enrollment = $student->enrollments()
            ->where('school_id', $student->school_id)
            ->where('admission_status', 'active')
            ->with([
                'classRoom',
                'academicSession',
                'term',
            ])
            ->latest('id')
            ->first();

        $assignments = collect();

        if ($enrollment) {
            // Restrict assignments to the student's school and class.
            $baseQuery = TeacherAssignment::query()
                ->where('school_id', $student->school_id)
                ->where('class_room_id', $enrollment->class_room_id);

            // Prefer assignments matching the student's session and term.
            $currentAssignments = (clone $baseQuery)
                ->where(
                    'academic_session_id',
                    $enrollment->academic_session_id
                )
                ->where('term_id', $enrollment->term_id)
                ->with([
                    'teacher.user',
                    'subject',
                    'classRoom',
                    'section',
                ])
                ->get();

            if ($currentAssignments->isNotEmpty()) {
                $assignments = $currentAssignments;
            } else {
                // Fallback when session or term values do not match.
                $assignments = (clone $baseQuery)
                    ->with([
                        'teacher.user',
                        'subject',
                        'classRoom',
                        'section',
                    ])
                    ->get();
            }

            // If the student has a section, include its assignments
            // and class-wide assignments with no section.
            if ($enrollment->section_id) {
                $assignments = $assignments
                    ->filter(function ($assignment) use ($enrollment) {
                        return $assignment->section_id === null
                            || (string) $assignment->section_id
                                === (string) $enrollment->section_id;
                    })
                    ->values();
            }

            // Keep one record per teacher and subject combination.
            $assignments = $assignments
                ->unique(fn ($assignment) =>
                    $assignment->teacher_id . '-' . $assignment->subject_id
                )
                ->values();
        }

        // Only count assignments with an existing teacher record.
        $teachers = $assignments
            ->filter(fn ($assignment) => $assignment->teacher !== null)
            ->unique('teacher_id')
            ->values();

        // Keep one assignment per subject for the subjects list.
        $subjects = $assignments
            ->filter(fn ($assignment) => $assignment->subject !== null)
            ->unique('subject_id')
            ->values();

        // Build a name using the Teacher model's actual name fields.
        $teacherName = function ($teacher): string {
            if (! $teacher) {
                return 'Not assigned';
            }

            $name = trim(implode(' ', array_filter([
                $teacher->first_name,
                $teacher->middle_name,
                $teacher->last_name,
            ])));

            if ($name !== '') {
                return $name;
            }

            return $teacher->user?->name ?: 'Name unavailable';
        };

        return view('livewire.student.class-teachers', [
            'student' => $student,
            'enrollment' => $enrollment,
            'assignments' => $assignments,
            'teachers' => $teachers,
            'subjects' => $subjects,
            'teacherName' => $teacherName,
        ])->layout('components.layouts.student', [
            'title' => 'My Class & Teachers',
        ]);
    }
}
