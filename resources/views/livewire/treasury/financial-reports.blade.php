
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Financial Reports</h3>
            <p class="text-secondary mb-0">
                Review fee collections, unpaid invoices, outstanding balances, and payment activity.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2 report-actions">
            <button type="button" class="btn btn-outline-secondary"
                    onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print
            </button>

            <button type="button" class="btn btn-primary"
                    wire:click="exportCsv"
                    wire:loading.attr="disabled"
                    wire:target="exportCsv">
                <i class="bi bi-download me-1"></i>
                <span wire:loading.remove wire:target="exportCsv">Export Payments</span>
                <span wire:loading wire:target="exportCsv">Preparing...</span>
            </button>

            <button type="button" class="btn btn-outline-primary"
                    wire:click="exportOutstandingCsv"
                    wire:loading.attr="disabled"
                    wire:target="exportOutstandingCsv">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                <span wire:loading.remove wire:target="exportOutstandingCsv">
                    Export Outstanding
                </span>
                <span wire:loading wire:target="exportOutstandingCsv">
                    Preparing...
                </span>
            </button>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm">
        <i class="bi bi-info-circle me-2"></i>
        Collection totals include completed payments. Outstanding fees are calculated from
        invoices with a positive balance. Totals are grouped by currency so different
        currencies are never added together.
    </div>

    {{-- Shared date filters --}}
    <div class="card border-0 shadow-sm mb-4 report-filters">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">From date</label>
                    <input type="date" class="form-control" wire:model.live="dateFrom">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">To date</label>
                    <input type="date" class="form-control" wire:model.live="dateTo">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Payment status</label>
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="all">All statuses</option>
                        @foreach ($statusBreakdown as $status)
                            <option value="{{ $status->payment_status }}">
                                {{ ucfirst($status->payment_status ?: 'Unknown') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-secondary w-100"
                            wire:click="clearFilters">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Clear all filters
                    </button>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Search payments</label>
                    <input type="search" class="form-control"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search student name or admission number...">
                </div>
            </div>
        </div>
    </div>

    {{-- Payment summary --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small mb-2">Payments in Period</div>
                    <div class="fs-3 fw-bold">
                        {{ number_format((int) ($summary->total_count ?? 0)) }}
                    </div>
                    <div class="small text-secondary">Payment records</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small mb-2">Successful Payments</div>
                    <div class="fs-3 fw-bold text-success">
                        {{ number_format((int) ($summary->successful_count ?? 0)) }}
                    </div>
                    <div class="small text-secondary">Completed transactions</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small mb-2">Other Payment Statuses</div>
                    <div class="fs-3 fw-bold text-warning">
                        {{ number_format((int) ($summary->other_count ?? 0)) }}
                    </div>
                    <div class="small text-secondary">Pending, rejected, or other statuses</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Collections by currency --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Collections by Currency</h5>
        </div>

        <div class="card-body">
            @forelse ($currencyTotals as $currencyTotal)
                <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom py-3 gap-2">
                    <div>
                        <div class="fw-semibold">{{ $currencyTotal->currency }}</div>
                        <div class="small text-secondary">
                            {{ number_format((int) $currencyTotal->payment_count) }}
                            successful payments
                        </div>
                    </div>

                    <div class="fs-5 fw-bold text-success">
                        {{ $currencyTotal->currency }}
                        {{ number_format((float) $currencyTotal->total_amount, 2) }}
                    </div>
                </div>
            @empty
                <p class="text-secondary mb-0">
                    No successful payments were found for the selected period.
                </p>
            @endforelse
        </div>
    </div>

    {{-- Outstanding fee summary --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1">Outstanding Fees</h4>
            <p class="text-secondary mb-0">
                Students who have unpaid or partially paid invoices.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small mb-2">Outstanding Invoices</div>
                    <div class="fs-3 fw-bold">
                        {{ number_format((int) ($outstandingSummary->invoice_count ?? 0)) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small mb-2">Unpaid Invoices</div>
                    <div class="fs-3 fw-bold text-danger">
                        {{ number_format((int) ($outstandingSummary->unpaid_count ?? 0)) }}
                    </div>
                    <div class="small text-secondary">No payment recorded</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small mb-2">Partially Paid</div>
                    <div class="fs-3 fw-bold text-warning">
                        {{ number_format((int) ($outstandingSummary->partial_count ?? 0)) }}
                    </div>
                    <div class="small text-secondary">A balance remains</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small mb-2">Overdue Invoices</div>
                    <div class="fs-3 fw-bold text-danger">
                        {{ number_format((int) ($overdueSummary->invoice_count ?? 0)) }}
                    </div>
                    <div class="small text-secondary">Past their due date</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Outstanding balances by currency --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Outstanding Balances by Currency</h5>
        </div>

        <div class="card-body">
            @forelse ($outstandingCurrencyTotals as $total)
                <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom py-3 gap-2">
                    <div>
                        <div class="fw-semibold">{{ $total->currency }}</div>
                        <div class="small text-secondary">
                            {{ number_format((int) $total->invoice_count) }}
                            outstanding invoices
                        </div>
                    </div>

                    <div class="fs-5 fw-bold text-danger">
                        {{ $total->currency }}
                        {{ number_format((float) $total->outstanding_amount, 2) }}
                    </div>
                </div>
            @empty
                <p class="text-secondary mb-0">
                    No outstanding invoice balances were found for the selected period.
                </p>
            @endforelse
        </div>
    </div>

    {{-- Outstanding invoice filters --}}
    <div class="card border-0 shadow-sm mb-4 report-filters">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Search outstanding invoices</label>
                    <input type="search" class="form-control"
                           wire:model.live.debounce.300ms="invoiceSearch"
                           placeholder="Student name or admission number...">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Payment progress</label>
                    <select class="form-select" wire:model.live="outstandingFilter">
                        <option value="all">All outstanding invoices</option>
                        <option value="unpaid">Not paid at all</option>
                        <option value="partial">Partially paid</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Outstanding invoice table --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Students with Outstanding Fees</h5>
            <div class="small text-secondary mt-1">
                Only invoices with a remaining balance are listed.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Admission No.</th>
                        <th>Invoice</th>
                        <th>Due Date</th>
                        <th>Amount Due</th>
                        <th>Amount Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($outstandingInvoices as $invoice)
                        @php
                            $student = $invoice->student;
                            $paid = (float) ($invoice->amount_paid ?? 0);
                            $balance = (float) ($invoice->balance ?? 0);
                            $dueAt = $invoice->due_at
                                ? \Illuminate\Support\Carbon::parse($invoice->due_at)
                                : null;
                            $isOverdue = $dueAt
                                && $dueAt->toDateString() < now()->toDateString();
                        @endphp

                        <tr wire:key="outstanding-invoice-{{ $invoice->id }}">
                            <td>
                                <div class="fw-semibold">
                                    {{ $student
                                        ? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))
                                        : 'Unknown student' }}
                                </div>
                            </td>

                            <td>{{ $student?->admission_number ?? '—' }}</td>

                            <td>
                                {{ $invoice->invoice_number ?? 'Invoice #' . $invoice->id }}
                            </td>

                            <td>
                                @if ($dueAt)
                                    <span class="{{ $isOverdue ? 'text-danger fw-semibold' : '' }}">
                                        {{ $dueAt->format('d M Y') }}
                                    </span>
                                    @if ($isOverdue)
                                        <div class="small text-danger">Overdue</div>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>

                            <td class="text-nowrap">
                                {{ $invoice->currency }}
                                {{ number_format((float) $invoice->amount_due, 2) }}
                            </td>

                            <td class="text-nowrap text-success">
                                {{ $invoice->currency }}
                                {{ number_format($paid, 2) }}
                            </td>

                            <td class="text-nowrap fw-bold text-danger">
                                {{ $invoice->currency }}
                                {{ number_format($balance, 2) }}
                            </td>

                            <td>
                                @if ($paid <= 0)
                                    <span class="badge text-bg-danger">Unpaid</span>
                                @else
                                    <span class="badge text-bg-warning">Partially Paid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-check-circle fs-2 text-success"></i>
                                <p class="fw-semibold mt-2 mb-1">No outstanding invoices found</p>
                                <p class="text-secondary small mb-0">
                                    Try changing the date range or outstanding-fee filters.
                                    Students without an issued invoice will not appear in this list.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($outstandingInvoices->hasPages())
            <div class="card-footer bg-white">
                {{ $outstandingInvoices->links() }}
            </div>
        @endif
    </div>

    {{-- Payment status breakdown --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Payment Status Breakdown</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Status</th>
                        <th class="text-end">Number of Payments</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($statusBreakdown as $status)
                        <tr>
                            <td>
                                <span class="badge {{ in_array($status->payment_status, $successfulStatuses, true)
                                    ? 'text-bg-success'
                                    : 'text-bg-secondary' }}">
                                    {{ ucfirst($status->payment_status ?: 'Unknown') }}
                                </span>
                            </td>

                            <td class="text-end fw-semibold">
                                {{ number_format((int) $status->total_count) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-4 text-secondary">
                                No payment records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Payment details --}}
    <div class="card border-0 shadow-sm report-payments">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Payment Details</h5>
            <div class="small text-secondary mt-1">
                Payment records for this school and selected filters.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date Paid</th>
                        <th>Payment Reference</th>
                        <th>Student</th>
                        <th>Provider</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($payments as $payment)
                        <tr wire:key="financial-payment-{{ $payment->id }}">
                            <td>
                                {{ $payment->paid_at?->format('d M Y, h:i A') ?? 'Not paid' }}
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $payment->reference ?: 'Payment #' . $payment->id }}
                                </div>

                                @if ($payment->provider_reference)
                                    <div class="small text-secondary">
                                        {{ $payment->provider_reference }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                @if ($payment->student)
                                    {{ trim(($payment->student->first_name ?? '') . ' ' . ($payment->student->last_name ?? '')) ?: 'Student #' . $payment->student_id }}
                                @else
                                    {{ $payment->student_id ? 'Student #' . $payment->student_id : '—' }}
                                @endif
                            </td>

                            <td>{{ $payment->provider ?: '—' }}</td>

                            <td class="fw-semibold text-nowrap">
                                {{ $payment->currency }}
                                {{ number_format((float) $payment->amount, 2) }}
                            </td>

                            <td>
                                <span class="badge {{ in_array(strtolower($payment->status ?? ''), $successfulStatuses, true)
                                    ? 'text-bg-success'
                                    : 'text-bg-secondary' }}">
                                    {{ ucfirst($payment->status ?? 'Unknown') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox fs-2 text-secondary"></i>
                                <p class="fw-semibold mt-2 mb-1">No payment records found</p>
                                <p class="text-secondary small mb-0">
                                    Outstanding invoices can still appear above even when no payments exist.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
            <div class="card-footer bg-white">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <style>
        @media print {
            body {
                background: #fff !important;
                font-size: 11px !important;
            }

            .treasury-sidebar,
            .treasury-topbar,
            .treasury-overlay,
            .report-filters,
            .report-actions,
            .btn,
            .pagination {
                display: none !important;
            }

            .treasury-main {
                margin-left: 0 !important;
            }

            .treasury-content {
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                break-inside: avoid;
            }

            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>

</div>
