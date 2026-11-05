<?php

namespace App\Livewire\Parent\Payments;

use App\Models\Payment;
use App\Models\SchoolParent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentHistory extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $status = 'all';

    public string $studentId = 'all';

    #[Locked]
    public int $parentId;

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasRole('Parent'),
            403
        );

        $parent = SchoolParent::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $this->parentId = $parent->id;
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedStudentId(): void
    {
        $this->resetPage();
    }

    /**
     * Return only the students belonging to this parent
     * within the authenticated user's school.
     */
    private function childrenQuery()
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasRole('Parent'),
            403
        );

        $parent = SchoolParent::query()
            ->whereKey($this->parentId)
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        return $parent->students()
            ->wherePivot('school_id', $user->school_id);
    }

    /**
     * Build the parent-scoped payment query.
     */
    private function paymentsQuery(): Builder
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasRole('Parent'),
            403
        );

        $childIds = $this->childrenQuery()
            ->select('students.id');

        $query = Payment::query()
            ->where('payments.school_id', $user->school_id)
            ->whereIn('payments.student_id', $childIds)
            ->with([
                'student',
                'invoice',
                'receipt',
            ]);

        if ($this->status !== 'all') {
            $query->where('payments.status', $this->status);
        }

        if ($this->studentId !== 'all') {
            $query->where(
                'payments.student_id',
                (int) $this->studentId
            );
        }

        return $query;
    }

    public function render()
    {
        $children = $this->childrenQuery()
            ->orderBy('students.first_name')
            ->get();

        // Prevent selecting a student who is not linked
        // to the authenticated parent.
        if (
            $this->studentId !== 'all' &&
            !$children->contains('id', (int) $this->studentId)
        ) {
            $this->studentId = 'all';
        }

        $payments = $this->paymentsQuery()
            ->latest('payments.created_at')
            ->paginate(10);

        $summaryQuery = $this->paymentsQuery();

        $verifiedStatuses = [
            'successful',
            'success',
            'paid',
            'completed',
        ];

        return view(
            'livewire.parent.payments.payment-history',
            [
                'children' => $children,
                'payments' => $payments,

                'totalPayments' => (clone $summaryQuery)
                    ->count(),

                'verifiedAmount' => (clone $summaryQuery)
                    ->whereNotNull('payments.verified_at')
                    ->whereIn(
                        'payments.status',
                        $verifiedStatuses
                    )
                    ->sum('payments.amount'),

                'pendingAmount' => (clone $summaryQuery)
                    ->where('payments.status', 'pending')
                    ->sum('payments.amount'),
            ]
        )->layout('components.layouts.parent');
    }
}
