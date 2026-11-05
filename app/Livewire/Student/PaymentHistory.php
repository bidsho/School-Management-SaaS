<?php

namespace App\Livewire\Student;

use App\Models\Payment;
use App\Models\Student as StudentModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentHistory extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    private const SUCCESS_STATUSES = [
        'success',
        'successful',
        'completed',
        'paid',
        'verified',
    ];

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

        $baseQuery = Payment::query()
            ->where('school_id', $student->school_id)
            ->where('student_id', $student->id);

        // Calculate totals from successful payments only.
        $successfulPayments = (clone $baseQuery)
            ->whereIn('status', self::SUCCESS_STATUSES);

        $totalPaid = (clone $successfulPayments)->sum('amount');

        $successfulCount = (clone $successfulPayments)->count();

        $pendingCount = (clone $baseQuery)
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        $failedCount = (clone $baseQuery)
            ->whereIn('status', ['failed', 'cancelled', 'canceled'])
            ->count();

        $payments = (clone $baseQuery)
            ->with('invoice')
            ->latest('created_at')
            ->latest('id')
            ->paginate(10);

        return view('livewire.student.payment-history', [
            'student' => $student,
            'payments' => $payments,
            'totalPaid' => (float) $totalPaid,
            'successfulCount' => $successfulCount,
            'pendingCount' => $pendingCount,
            'failedCount' => $failedCount,
        ])->layout('components.layouts.student', [
            'title' => 'Payment History',
        ]);
    }
}
