<?php

namespace App\Livewire\Treasury;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /**
     * Only allow Treasury users to access their own school's finances.
     */
    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user, 403);

        abort_unless(
            $user->hasRole('Treasury') && $user->school_id,
            403,
            'You are not authorized to access this Treasury dashboard.'
        );

        return (int) $user->school_id;
    }

    /**
     * Payments belonging to the current school.
     */
    private function paymentsQuery(): Builder
    {
        return Payment::query()
            ->where('school_id', $this->schoolId());
    }

    /**
     * Invoices belonging to the current school.
     */
    private function invoicesQuery(): Builder
    {
        return Invoice::query()
            ->where('school_id', $this->schoolId());
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        /*
        |--------------------------------------------------------------------------
        | 1. Successful collections
        |--------------------------------------------------------------------------
        |
        | Count only completed payments with a payment date.
        | Keep currencies separate so different currencies are not added
        | together as though they were the same currency.
        */

        $collectedPayments = $this->paymentsQuery()
            ->where('status', 'completed')
            ->whereNotNull('paid_at');

        $collectionsByCurrency = (clone $collectedPayments)
            ->selectRaw('currency, SUM(amount) as total')
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 2. Collections received today
        |--------------------------------------------------------------------------
        */

        $todayByCurrency = (clone $collectedPayments)
            ->whereDate('paid_at', today())
            ->selectRaw('currency, SUM(amount) as total')
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 3. Pending payments
        |--------------------------------------------------------------------------
        */

        $pendingPayments = $this->paymentsQuery()
            ->where('status', 'pending')
            ->count();

        $pendingByCurrency = $this->paymentsQuery()
            ->where('status', 'pending')
            ->selectRaw('currency, SUM(amount) as total')
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 4. Outstanding invoices
        |--------------------------------------------------------------------------
        |
        | This includes invoices with a positive balance even when the
        | student has never made a payment.
        */

        $outstandingInvoices = $this->invoicesQuery()
            ->where('balance', '>', 0);

        $unpaidInvoices = (clone $outstandingInvoices)->count();

        $outstandingByCurrency = (clone $outstandingInvoices)
            ->selectRaw('currency, SUM(balance) as total')
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 5. Completely unpaid invoices
        |--------------------------------------------------------------------------
        |
        | A positive balance alone does not mean that no payment was made.
        | Check amount_paid as well.
        */

        $completelyUnpaidInvoices = (clone $outstandingInvoices)
            ->where(function (Builder $query) {
                $query->whereNull('amount_paid')
                    ->orWhere('amount_paid', '<=', 0);
            });

        $unpaidInvoiceCount = (clone $completelyUnpaidInvoices)->count();

        /*
        |--------------------------------------------------------------------------
        | 6. Partially paid invoices
        |--------------------------------------------------------------------------
        */

        $partiallyPaidInvoices = (clone $outstandingInvoices)
            ->where('amount_paid', '>', 0);

        $partiallyPaidInvoiceCount = (clone $partiallyPaidInvoices)->count();

        /*
        |--------------------------------------------------------------------------
        | 7. Overdue invoices
        |--------------------------------------------------------------------------
        */

        $overdueInvoices = (clone $outstandingInvoices)
            ->whereNotNull('due_at')
            ->whereDate('due_at', '<', today());

        $overdueInvoiceCount = (clone $overdueInvoices)->count();

        $overdueByCurrency = (clone $overdueInvoices)
            ->selectRaw('currency, SUM(balance) as total')
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 8. Total invoices
        |--------------------------------------------------------------------------
        */

        $totalInvoices = $this->invoicesQuery()->count();

        /*
        |--------------------------------------------------------------------------
        | 9. Verified completed payments
        |--------------------------------------------------------------------------
        */

        $verifiedPayments = $this->paymentsQuery()
            ->where('status', 'completed')
            ->whereNotNull('verified_at')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 10. Recent payments
        |--------------------------------------------------------------------------
        |
        | Eager-load invoice and receipt relationships to avoid unnecessary
        | database queries in the Blade template.
        */

        $recentPayments = $this->paymentsQuery()
            ->with(['invoice', 'receipt'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        /*
        |--------------------------------------------------------------------------
        | 11. Render dashboard
        |--------------------------------------------------------------------------
        */

        return view('livewire.treasury.dashboard', [
            // Existing dashboard variables
            'collectionsByCurrency' => $collectionsByCurrency,
            'todayByCurrency' => $todayByCurrency,
            'pendingPayments' => $pendingPayments,
            'pendingByCurrency' => $pendingByCurrency,
            'outstandingByCurrency' => $outstandingByCurrency,
            'recentPayments' => $recentPayments,
            'totalInvoices' => $totalInvoices,
            'unpaidInvoices' => $unpaidInvoices,
            'verifiedPayments' => $verifiedPayments,

            // Additional statistics for dashboard improvements
            'unpaidInvoiceCount' => $unpaidInvoiceCount,
            'partiallyPaidInvoiceCount' => $partiallyPaidInvoiceCount,
            'overdueInvoiceCount' => $overdueInvoiceCount,
            'overdueByCurrency' => $overdueByCurrency,
        ])->layout('components.layouts.treasury', [
            'title' => 'Treasury Dashboard',
        ]);
    }
}
