<?php

namespace App\Livewire\School\Dashboard;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SchoolDashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless(
            $user->school_id,
            403,
            'No school is assigned to this account.'
        );

        $schoolId = (int) $user->school_id;
        $today = today();

        // Each query is restricted to the authenticated user's school.
        $students = Student::query()
            ->where('school_id', $schoolId);

        $teachers = Teacher::query()
            ->where('school_id', $schoolId);

        $classes = ClassRoom::query()
            ->where('school_id', $schoolId);

        $sections = Section::query()
            ->where('school_id', $schoolId);

        $attendance = Attendance::query()
            ->where('school_id', $schoolId)
            ->whereDate('attendance_date', $today);

        // Student statistics
        $totalStudents = (clone $students)->count();

        $activeStudents = (clone $students)
            ->where('status', 'active')
            ->count();

        $inactiveStudents = (clone $students)
            ->where('status', 'inactive')
            ->count();

        // Teacher statistics
        $totalTeachers = (clone $teachers)->count();

        $activeTeachers = (clone $teachers)
            ->where('employment_status', 'active')
            ->count();

        // Class and section statistics
        $totalClasses = (clone $classes)->count();
        $totalSections = (clone $sections)->count();

        // Today's attendance
        $attendanceMarked = (clone $attendance)->count();

        $presentToday = (clone $attendance)
            ->where('status', 'present')
            ->count();

        $absentToday = (clone $attendance)
            ->where('status', 'absent')
            ->count();

        $lateToday = (clone $attendance)
            ->where('status', 'late')
            ->count();

        $excusedToday = (clone $attendance)
            ->where('status', 'excused')
            ->count();

        $attendanceRate = $attendanceMarked > 0
            ? round(($presentToday / $attendanceMarked) * 100, 1)
            : 0;

        $stats = [
            'totalStudents' => $totalStudents,
            'activeStudents' => $activeStudents,
            'inactiveStudents' => $inactiveStudents,
            'totalTeachers' => $totalTeachers,
            'activeTeachers' => $activeTeachers,
            'totalClasses' => $totalClasses,
            'totalSections' => $totalSections,
            'attendanceMarked' => $attendanceMarked,
            'presentToday' => $presentToday,
            'absentToday' => $absentToday,
            'lateToday' => $lateToday,
            'excusedToday' => $excusedToday,
            'attendanceRate' => $attendanceRate,
        ];

        // Latest student registrations
        $recentStudents = (clone $students)
            ->latest('created_at')
            ->take(5)
            ->get();

        // Latest attendance records for today
        $recentAttendance = (clone $attendance)
            ->latest('created_at')
            ->take(8)
            ->get();

        return view(
            'livewire.school.dashboard.school-dashboard',
            compact('stats', 'recentStudents', 'recentAttendance')
        );
    }
}
