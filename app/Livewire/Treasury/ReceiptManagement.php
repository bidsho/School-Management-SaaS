<?php

namespace App\Livewire\Treasury;

use App\Models\Receipt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReceiptManagement extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public ?int $selectedReceiptId = null;

    /**
     * Ensure the authenticated user is a Treasury user
     * belonging to a school.
     */
    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless(
            $user
                && $user->hasRole('Treasury')
                && $user->school_id,
            403,
            'You are not authorized to access Treasury receipts.'
        );

        return (int) $user->school_id;
    }

    /**
     * Base query: receipts belonging to the current school only.
     */
    private function receiptQuery(): Builder
    {
        return Receipt::query()
            ->where('school_id', $this->schoolId())
            ->with([
                'payment.student',
                'invoice',
            ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    /**
     * Clear all filters.
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->dateFrom = '';
        $this->dateTo = '';

        $this->resetPage();
    }

    /**
     * Open a receipt's details after checking school ownership.
     */
    public function viewReceipt(int $receiptId): void
    {
        $receipt = $this->receiptQuery()->findOrFail($receiptId);

        $this->selectedReceiptId = (int) $receipt->id;
    }

    /**
     * Close the receipt details panel.
     */
    public function closeReceipt(): void
    {
        $this->selectedReceiptId = null;
    }

    /**
     * Return a receipt to the print view.
     * The query remains school-scoped.
     */
    public function printReceipt(int $receiptId): void
    {
        $receipt = $this->receiptQuery()->findOrFail($receiptId);

        $this->selectedReceiptId = (int) $receipt->id;
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $query = Receipt::query()
            ->where('school_id', $schoolId)
            ->with([
                'payment.student',
                'invoice',
            ]);

        if (trim($this->search) !== '') {
            $search = '%' . trim($this->search) . '%';

            $query->where(function (Builder $q) use ($search) {
                $q->where('receipt_number', 'like', $search)
                    ->orWhereHas('payment', function (Builder $paymentQuery) use ($search) {
                        $paymentQuery
                            ->where('reference', 'like', $search)
                            ->orWhere('provider_reference', 'like', $search);
                    })
                    ->orWhereHas('invoice', function (Builder $invoiceQuery) use ($search) {
                        $invoiceQuery->where('invoice_number', 'like', $search);
                    });
            });
        }

        if ($this->dateFrom !== '') {
            $query->whereDate('issued_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo !== '') {
            $query->whereDate('issued_at', '<=', $this->dateTo);
        }

        $receipts = $query
            ->latest('issued_at')
            ->latest('id')
            ->paginate(10);

        $counts = Receipt::query()
            ->where('school_id', $schoolId)
            ->selectRaw('COUNT(*) as total_receipts')
            ->selectRaw(
                'COUNT(CASE WHEN issued_at >= ? THEN 1 END) as receipts_this_month',
                [now()->startOfMonth()]
            )
            ->first();

        $selectedReceipt = $this->selectedReceiptId
            ? $this->receiptQuery()->find($this->selectedReceiptId)
            : null;

        return view('livewire.treasury.receipt-management', [
            'receipts' => $receipts,
            'counts' => $counts,
            'selectedReceipt' => $selectedReceipt,
        ])->layout('components.layouts.treasury', [
            'title' => 'Receipt Management',
        ]);
    }
}
