<?php

namespace App\Livewire\School\Subscription;

use App\Models\School;
use App\Models\SubscriptionPayment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentHistory extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Locked]
    public int $schoolId;

    public string $search = '';

    public string $status = 'all';

    public string $dateFrom = '';

    public string $dateTo = '';

    public ?int $selectedPaymentId = null;

    public bool $showDetails = false;

    /*
    |--------------------------------------------------------------------------
    | Initialize Component
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user && $user->school_id, 403);

        $this->schoolId = (int) $user->school_id;

        School::query()
            ->whereKey($this->schoolId)
            ->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | Search And Filter Updates
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Filters
    |--------------------------------------------------------------------------
    */

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'status',
            'dateFrom',
            'dateTo',
        ]);

        $this->status = 'all';

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Base Payment Query
    |--------------------------------------------------------------------------
    */

    protected function paymentQuery(): Builder
    {
        $this->ensureSchoolAccess();

        return SubscriptionPayment::query()
            ->where('school_id', $this->schoolId);
    }

    /*
    |--------------------------------------------------------------------------
    | Apply Search And Filters
    |--------------------------------------------------------------------------
    */

    protected function filteredPayments(): Builder
    {
        $query = $this->paymentQuery()
            ->with([
                'plan',
                'subscription',
            ]);

        if (filled($this->search)) {
            $search = trim($this->search);

            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('plan', function (Builder $planQuery) use ($search) {
                        $planQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
            });
        }

        if (
            in_array(
                $this->status,
                ['pending', 'successful', 'failed'],
                true
            )
        ) {
            $query->where('status', $this->status);
        }

        if (filled($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if (filled($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | View Payment Details
    |--------------------------------------------------------------------------
    */

    public function viewPayment(int $paymentId): void
    {
        $payment = $this->paymentQuery()
            ->whereKey($paymentId)
            ->firstOrFail();

        $this->selectedPaymentId = (int) $payment->id;
        $this->showDetails = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Close Payment Details
    |--------------------------------------------------------------------------
    */

    public function closeDetails(): void
    {
        $this->showDetails = false;
        $this->selectedPaymentId = null;
    }

    /*
    |--------------------------------------------------------------------------
    | Ensure Authenticated User Belongs To School
    |--------------------------------------------------------------------------
    */

   protected function ensureSchoolAccess(): void
{
    $user = Auth::user();

    abort_unless(
        $user &&
        $user->school_id &&
        (int) $user->school_id === $this->schoolId &&
        $user->hasRole('School Admin'),
        403,
        'Only the School Admin can manage subscription payments.'
    );
}

    /*
    |--------------------------------------------------------------------------
    | Render Component
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $this->ensureSchoolAccess();

        $school = School::query()
            ->whereKey($this->schoolId)
            ->firstOrFail();

        $baseQuery = $this->paymentQuery();

        $statistics = [
            'total_paid' => (clone $baseQuery)
                ->where('status', 'successful')
                ->sum('amount'),

            'successful' => (clone $baseQuery)
                ->where('status', 'successful')
                ->count(),

            'pending' => (clone $baseQuery)
                ->where('status', 'pending')
                ->count(),

            'failed' => (clone $baseQuery)
                ->where('status', 'failed')
                ->count(),

            'total_transactions' => (clone $baseQuery)->count(),
        ];

        $payments = $this->filteredPayments()
            ->latest('created_at')
            ->paginate(10);

        $selectedPayment = null;

        if ($this->showDetails && $this->selectedPaymentId) {
            $selectedPayment = $this->paymentQuery()
                ->with(['plan', 'subscription'])
                ->whereKey($this->selectedPaymentId)
                ->first();
        }

        return view(
            'livewire.school.subscription.payment-history',
            [
                'school' => $school,
                'statistics' => $statistics,
                'payments' => $payments,
                'selectedPayment' => $selectedPayment,
            ]
        )->layout('components.layouts.school', [
            'title' => 'Subscription Payment History',
        ]);
    }
}
