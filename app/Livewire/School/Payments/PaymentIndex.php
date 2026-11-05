<?php

namespace App\Livewire\School\Payments;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $status = '';
    public string $provider = '';
    public string $payment_date = '';

    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user && $user->school_id, 403);

        return (int) $user->school_id;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingProvider(): void
    {
        $this->resetPage();
    }

    public function updatingPaymentDate(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'status',
            'provider',
            'payment_date',
        ]);

        $this->resetPage();
    }

    private function paymentsQuery(): Builder
    {
        $schoolId = $this->schoolId();

        return Payment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with([
                'student',
                'invoice',
                'receipt',
            ])
            ->when(trim($this->search) !== '', function (Builder $query) {
                $search = trim($this->search);

                $query->where(function (Builder $query) use ($search) {
                    $query->where('reference', 'like', "%{$search}%")
                        ->orWhere('provider_reference', 'like', "%{$search}%")
                        ->orWhereHas('student', function (Builder $student) use ($search) {
                            $student->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('admission_number', 'like', "%{$search}%");
                        });

                    if (ctype_digit($search)) {
                        $query->orWhere('invoice_id', (int) $search)
                            ->orWhere('id', (int) $search);
                    }
                });
            })
            ->when($this->status !== '', fn (Builder $query) =>
                $query->where('status', $this->status)
            )
            ->when($this->provider !== '', fn (Builder $query) =>
                $query->where('provider', $this->provider)
            )
            ->when($this->payment_date !== '', fn (Builder $query) =>
                $query->whereDate('created_at', $this->payment_date)
            );
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $baseQuery = Payment::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        return view('livewire.school.payments.payment-index', [
            'payments' => $this->paymentsQuery()
                ->latest('id')
                ->paginate(15),

            'totalPayments' => (clone $baseQuery)->count(),

            'successfulPayments' => (clone $baseQuery)
                ->where('status', 'paid')
                ->count(),

            'pendingPayments' => (clone $baseQuery)
                ->where('status', 'pending')
                ->count(),

            'failedPayments' => (clone $baseQuery)
                ->where('status', 'failed')
                ->count(),

            'providers' => (clone $baseQuery)
                ->whereNotNull('provider')
                ->where('provider', '!=', '')
                ->distinct()
                ->orderBy('provider')
                ->pluck('provider'),
        ])->layout('components.layouts.school');
    }
}
