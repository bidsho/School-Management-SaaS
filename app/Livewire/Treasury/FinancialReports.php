<?php

namespace App\Livewire\Treasury;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class FinancialReports extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $dateFrom = '';
    public string $dateTo = '';
    public string $search = '';
    public string $statusFilter = 'all';

    public string $invoiceSearch = '';
    public string $outstandingFilter = 'all';

    private function successfulStatuses(): array
    {
        return ['completed'];
    }

    private function schoolId(): int
    {
        $user = auth()->user();

        abort_unless($user, 403);
        abort_unless($user->hasRole('Treasury'), 403);

        abort_unless(
            $user->school_id,
            403,
            'Your account is not assigned to a school.'
        );

        return (int) $user->school_id;
    }

    private function paymentsQuery(): Builder
    {
        return Payment::query()
            ->where('school_id', $this->schoolId())
            ->with(['student', 'invoice']);
    }

    private function invoicesQuery(): Builder
    {
        return Invoice::query()
            ->where('school_id', $this->schoolId())
            ->with(['student', 'academicSession', 'term']);
    }

    private function applyPaymentDateFilters(Builder $query): Builder
    {
        return $query
            ->when($this->dateFrom !== '', function (Builder $query) {
                $query->whereDate('paid_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo !== '', function (Builder $query) {
                $query->whereDate('paid_at', '<=', $this->dateTo);
            });
    }

    private function applyInvoiceDateFilters(Builder $query): Builder
    {
        return $query
            ->when($this->dateFrom !== '', function (Builder $query) {
                $query->whereDate('issued_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo !== '', function (Builder $query) {
                $query->whereDate('issued_at', '<=', $this->dateTo);
            });
    }

    private function applyStudentSearch(
        Builder $query,
        string $search
    ): Builder {
        $term = '%' . trim($search) . '%';

        return $query->where(function (Builder $query) use ($term) {
            $query->whereHas('student', function (Builder $studentQuery) use ($term) {
                $studentQuery
                    ->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('admission_number', 'like', $term);
            });
        });
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingInvoiceSearch(): void
    {
        $this->resetPage();
    }

    public function updatingOutstandingFilter(): void
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

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->search = '';
        $this->statusFilter = 'all';
        $this->invoiceSearch = '';
        $this->outstandingFilter = 'all';

        $this->resetPage();
    }

    /**
     * Export payment records as CSV.
     */
    public function exportCsv()
    {
        $this->schoolId();

        $filename = 'treasury-report-' . now()->format('Y-m-d-His') . '.csv';

        $payments = $this->applyPaymentDateFilters($this->paymentsQuery())
            ->when(
                $this->statusFilter !== 'all',
                fn (Builder $query) => $query->where(
                    'status',
                    $this->statusFilter
                )
            )
            ->when(
                trim($this->search) !== '',
                fn (Builder $query) => $this->applyStudentSearch(
                    $query,
                    $this->search
                )
            )
            ->orderByDesc('paid_at')
            ->get();

        return response()->streamDownload(function () use ($payments) {
            $output = fopen('php://output', 'w');

            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'Payment ID',
                'Payment Reference',
                'Provider Reference',
                'Student',
                'Admission Number',
                'Invoice ID',
                'Amount',
                'Currency',
                'Status',
                'Paid At',
            ]);

            foreach ($payments as $payment) {
                $student = $payment->student;

                fputcsv($output, [
                    $payment->id,
                    $payment->reference,
                    $payment->provider_reference,
                    $student
                        ? trim(
                            ($student->first_name ?? '') . ' ' .
                            ($student->last_name ?? '')
                        )
                        : '',
                    $student?->admission_number ?? '',
                    $payment->invoice_id,
                    $payment->amount,
                    $payment->currency,
                    $payment->status,
                    $payment->paid_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportOutstandingCsv()
    {
        $this->schoolId();

        $filename = 'outstanding-fees-' . now()->format('Y-m-d-His') . '.csv';

        $invoices = $this->applyInvoiceDateFilters($this->invoicesQuery())
            ->where('balance', '>', 0)
            ->when(
                trim($this->invoiceSearch) !== '',
                fn (Builder $query) => $this->applyStudentSearch(
                    $query,
                    $this->invoiceSearch
                )
            )
            ->when(
                $this->outstandingFilter === 'unpaid',
                fn (Builder $query) => $query->where('amount_paid', '<=', 0)
            )
            ->when(
                $this->outstandingFilter === 'partial',
                fn (Builder $query) => $query->where('amount_paid', '>', 0)
            )
            ->orderByDesc('due_at')
            ->get();

        return response()->streamDownload(function () use ($invoices) {
            $output = fopen('php://output', 'w');

            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'Invoice Number',
                'Student',
                'Admission Number',
                'Academic Session',
                'Term',
                'Amount Due',
                'Amount Paid',
                'Balance',
                'Currency',
                'Due Date',
                'Payment Status',
            ]);

            foreach ($invoices as $invoice) {
                $student = $invoice->student;

                fputcsv($output, [
                    $invoice->invoice_number,
                    $student
                        ? trim(
                            ($student->first_name ?? '') . ' ' .
                            ($student->last_name ?? '')
                        )
                        : '',
                    $student?->admission_number ?? '',
                    $invoice->academicSession?->name ?? '',
                    $invoice->term?->name ?? '',
                    $invoice->amount_due,
                    $invoice->amount_paid,
                    $invoice->balance,
                    $invoice->currency,
                    $invoice->due_at,
                    (float) $invoice->amount_paid <= 0
                        ? 'Unpaid'
                        : 'Partially Paid',
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $successfulStatuses = $this->successfulStatuses();

        // Payment report
        $filteredPayments = $this->applyPaymentDateFilters(
            $this->paymentsQuery()
        )
            ->when(
                $this->statusFilter !== 'all',
                fn (Builder $query) => $query->where(
                    'status',
                    $this->statusFilter
                )
            )
            ->when(
                trim($this->search) !== '',
                fn (Builder $query) => $this->applyStudentSearch(
                    $query,
                    $this->search
                )
            );

        $payments = (clone $filteredPayments)
            ->latest('created_at')
            ->paginate(15, ['*'], 'paymentsPage');

        // Collection summary
        $summaryQuery = $this->applyPaymentDateFilters(
            Payment::query()->where('school_id', $schoolId)
        );

        $summary = (clone $summaryQuery)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw(
                "SUM(CASE WHEN LOWER(status) = 'completed' THEN amount ELSE 0 END) as collected_amount"
            )
            ->selectRaw(
                "SUM(CASE WHEN LOWER(status) = 'completed' THEN 1 ELSE 0 END) as successful_count"
            )
            ->selectRaw(
                "SUM(CASE WHEN LOWER(status) <> 'completed' THEN 1 ELSE 0 END) as other_count"
            )
            ->first();

        $currencyTotals = (clone $summaryQuery)
            ->whereRaw('LOWER(status) = ?', ['completed'])
            ->selectRaw(
                'currency, SUM(amount) as total_amount, COUNT(*) as payment_count'
            )
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        $statusBreakdown = (clone $summaryQuery)
            ->selectRaw('LOWER(status) as payment_status, COUNT(*) as total_count')
            ->groupBy(DB::raw('LOWER(status)'))
            ->orderBy('payment_status')
            ->get();

        // Outstanding invoices: includes students who paid nothing.
        $outstandingQuery = $this->applyInvoiceDateFilters(
            $this->invoicesQuery()
        )
            ->where('balance', '>', 0)
            ->when(
                trim($this->invoiceSearch) !== '',
                fn (Builder $query) => $this->applyStudentSearch(
                    $query,
                    $this->invoiceSearch
                )
            )
            ->when(
                $this->outstandingFilter === 'unpaid',
                fn (Builder $query) => $query->where('amount_paid', '<=', 0)
            )
            ->when(
                $this->outstandingFilter === 'partial',
                fn (Builder $query) => $query->where('amount_paid', '>', 0)
            );

        $outstandingInvoices = (clone $outstandingQuery)
            ->orderBy('due_at')
            ->orderByDesc('id')
            ->paginate(15, ['*'], 'outstandingPage');

        // Outstanding summary across the selected invoice date range.
        $outstandingSummary = (clone $outstandingQuery)
            ->reorder()
            ->selectRaw('COUNT(*) as invoice_count')
            ->selectRaw(
                'COALESCE(SUM(balance), 0) as outstanding_amount'
            )
            ->selectRaw(
                'SUM(CASE WHEN amount_paid <= 0 THEN 1 ELSE 0 END) as unpaid_count'
            )
            ->selectRaw(
                'SUM(CASE WHEN amount_paid > 0 AND balance > 0 THEN 1 ELSE 0 END) as partial_count'
            )
            ->first();

        $overdueQuery = $this->applyInvoiceDateFilters(
            Invoice::query()->where('school_id', $schoolId)
        )
            ->where('balance', '>', 0)
            ->whereNotNull('due_at')
            ->whereDate('due_at', '<', now()->toDateString());

        $overdueSummary = (clone $overdueQuery)
            ->selectRaw('COUNT(*) as invoice_count')
            ->selectRaw(
                'COALESCE(SUM(balance), 0) as overdue_amount'
            )
            ->first();

        // Currency totals are separated to avoid adding different currencies.
        $outstandingCurrencyTotals = (clone $outstandingQuery)
            ->reorder()
            ->selectRaw(
                'currency, SUM(balance) as outstanding_amount, COUNT(*) as invoice_count'
            )
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        return view('livewire.treasury.financial-reports', [
            'payments' => $payments,
            'summary' => $summary,
            'currencyTotals' => $currencyTotals,
            'statusBreakdown' => $statusBreakdown,
            'successfulStatuses' => $successfulStatuses,

            'outstandingInvoices' => $outstandingInvoices,
            'outstandingSummary' => $outstandingSummary,
            'overdueSummary' => $overdueSummary,
            'outstandingCurrencyTotals' => $outstandingCurrencyTotals,
        ])->layout('components.layouts.treasury', [
            'title' => 'Financial Reports',
        ]);
    }
}
