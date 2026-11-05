<?php

namespace App\Livewire\Student;

use App\Models\ExamScore;
use App\Models\Student as StudentModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless($user->hasRole('Student'), 403);
        abort_unless($user->school_id, 403);

        /*
        |--------------------------------------------------------------------------
        | Student Profile
        |--------------------------------------------------------------------------
        */

        $student = StudentModel::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->withCount([
                'attendanceRecords',
                'examScores',
                'invoices',
                'payments',
            ])
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Current Active Enrollment
        |--------------------------------------------------------------------------
        */

        $currentEnrollment = $student->enrollments()
            ->where('school_id', $user->school_id)
            ->where('admission_status', 'active')
            ->whereDate('start_date', '<=', today())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', today());
            })
            ->with([
                'classRoom',
                'academicSession',
                'term',
            ])
            ->latest('start_date')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Latest Invoice
        |--------------------------------------------------------------------------
        */

        $latestInvoice = $student->invoices()
            ->where('school_id', $user->school_id)
            ->latest('issued_at')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Recent Payments
        |--------------------------------------------------------------------------
        */

        $recentPayments = $student->payments()
            ->where('school_id', $user->school_id)
            ->latest('paid_at')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Examination Performance
        |--------------------------------------------------------------------------
        | Retrieve only this student's scores from their own school.
        */

        $recentExamScores = ExamScore::query()
            ->where('school_id', $user->school_id)
            ->where('student_id', $student->id)
            ->with([
                'subject',
                'exam',
            ])
            ->latest('created_at')
            ->take(8)
            ->get()
            ->reverse()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Chart Data
        |--------------------------------------------------------------------------
        */

        $chartLabels = $recentExamScores->map(function ($score) {
            $subject = $score->subject?->name
                ?? $score->exam?->name
                ?? 'Examination';

            $exam = $score->exam?->name;

            return $exam && $exam !== $subject
                ? $subject . ' - ' . $exam
                : $subject;
        })->values()->all();

        $chartScores = $recentExamScores->map(function ($score) {
            return round((float) $score->total_score, 2);
        })->values()->all();

        /*
        |--------------------------------------------------------------------------
        | Average Performance
        |--------------------------------------------------------------------------
        */

        $averageScore = count($chartScores) > 0
            ? round(array_sum($chartScores) / count($chartScores), 2)
            : 0;

        $highestScore = count($chartScores) > 0
            ? max($chartScores)
            : 0;

        return view('livewire.student.dashboard', [
            'student' => $student,
            'currentEnrollment' => $currentEnrollment,
            'latestInvoice' => $latestInvoice,
            'recentPayments' => $recentPayments,

            'recentExamScores' => $recentExamScores,
            'chartLabels' => $chartLabels,
            'chartScores' => $chartScores,

            'averageScore' => $averageScore,
            'highestScore' => $highestScore,
        ])->layout('components.layouts.student', [
            'title' => 'Student Dashboard',
        ]);
    }
}