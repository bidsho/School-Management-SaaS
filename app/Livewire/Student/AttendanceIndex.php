<?php

namespace App\Livewire\Student;

use App\Models\Attendance;
use App\Models\Student as StudentModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $filterStatus = '';

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

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $student = $this->getStudent();

        $attendanceQuery = Attendance::query()
            ->where('school_id', $student->school_id)
            ->where('student_id', $student->id);

        $totalRecords = (clone $attendanceQuery)->count();

        $presentCount = (clone $attendanceQuery)
            ->where('status', 'present')
            ->count();

        $absentCount = (clone $attendanceQuery)
            ->where('status', 'absent')
            ->count();

        $lateCount = (clone $attendanceQuery)
            ->where('status', 'late')
            ->count();

        $attendanceRecords = (clone $attendanceQuery)
            ->when(
                $this->filterStatus !== '',
                fn ($query) => $query->where(
                    'status',
                    $this->filterStatus
                )
            )
            ->with([
                'classRoom',
                'section',
                'academicSession',
                'term',
            ])
            ->orderByDesc('attendance_date')
            ->paginate(10);

        return view('livewire.student.attendance-index', [
            'student' => $student,
            'attendanceRecords' => $attendanceRecords,
            'totalRecords' => $totalRecords,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'lateCount' => $lateCount,
        ])->layout('components.layouts.student', [
            'title' => 'My Attendance',
        ]);
    }
}
