
<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Outstanding Balances</h3>
            <p class="text-muted mb-0">
                Monitor unpaid fees, partial payments and outstanding student balances.
            </p>
        </div>

        <button type="button" class="btn btn-outline-dark" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Total Invoices</div>
                            <div class="fs-3 fw-bold">
                                {{ number_format($summary['invoices']) }}
                            </div>
                        </div>
                        <i class="bi bi-receipt fs-3 text-secondary"></i>
                    </div>
                    <small class="text-muted">All invoices for selected session and term</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Completely Unpaid</div>
                            <div class="fs-3 fw-bold text-danger">
                                {{ number_format($summary['unpaid_count']) }}
                            </div>
                        </div>
                        <i class="bi bi-person-exclamation fs-3 text-danger"></i>
                    </div>
                    <small class="text-muted">Invoices with no payment recorded</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Partially Paid</div>
                            <div class="fs-3 fw-bold text-warning">
                                {{ number_format($summary['partial_count']) }}
                            </div>
                        </div>
                        <i class="bi bi-wallet2 fs-3 text-warning"></i>
                    </div>
                    <small class="text-muted">Invoices with a remaining balance</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Overdue Invoices</div>
                            <div class="fs-3 fw-bold text-danger">
                                {{ number_format($summary['overdue_count']) }}
                            </div>
                        </div>
                        <i class="bi bi-calendar-x fs-3 text-danger"></i>
                    </div>
                    <small class="text-muted">Past due date with unpaid balance</small>
                </div>
            </div>
        </div>

    </div>

    {{-- Financial Summary --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Total Expected</div>
                    <div class="fs-4 fw-bold">
                        {{ number_format((float) $summary['total_expected'], 2) }}
                    </div>
                    <small class="text-muted">
                        Combined invoice values; currencies are not converted.
                    </small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Total Collected</div>
                    <div class="fs-4 fw-bold text-success">
                        {{ number_format((float) $summary['total_collected'], 2) }}
                    </div>
                    <small class="text-muted">
                        Amount recorded as paid on invoices.
                    </small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Total Outstanding</div>
                    <div class="fs-4 fw-bold text-danger">
                        {{ number_format((float) $summary['outstanding_total'], 2) }}
                    </div>
                    <small class="text-muted">
                        Overdue outstanding amount:
                        {{ number_format((float) $summary['overdue_total'], 2) }}
                    </small>
                </div>
            </div>
        </div>

    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-funnel me-2"></i>Search and Filter
            </h5>
        </div>

        <div class="card-body">
            <div class="row g-3">

                <div class="col-12 col-lg-4">
                    <label class="form-label fw-semibold">Search Student or Invoice</label>
                    <input
                        type="search"
                        class="form-control"
                        wire:model.live.debounce.350ms="search"
                        placeholder="Name, admission number or invoice..."
                    >
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-semibold">Academic Session</label>
                    <select class="form-select" wire:model.live="sessionFilter">
                        <option value="">All sessions</option>
                        @foreach ($sessions as $session)
                            <option value="{{ $session->id }}">
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label fw-semibold">Term</label>
                    <select class="form-select" wire:model.live="termFilter">
                        <option value="">All terms</option>
                        @foreach ($terms as $term)
                            <option value="{{ $term->id }}">
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-8 col-lg-3">
                    <label class="form-label fw-semibold">Payment Status</label>
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="outstanding">All Outstanding</option>
                        <option value="unpaid">Completely Unpaid</option>
                        <option value="partial">Partially Paid</option>
                        <option value="overdue">Overdue</option>
                        <option value="paid">Fully Paid</option>
                        <option value="all">All Invoices</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        wire:click="clearFilters"
                    >
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Clear Filters
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Student Fee Balances --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-1 fw-bold">Student Fee Balances</h5>
                    <small class="text-muted">
                        Students with unpaid, partially paid, overdue or settled invoices.
                    </small>
                </div>

                <span class="badge bg-light text-dark border">
                    {{ number_format($invoices->total()) }} invoices
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Invoice</th>
                            <th>Student</th>
                            <th>Session / Term</th>
                            <th>Amount Due</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($invoices as $invoice)
                            @php
                                $balance = (float) $invoice->balance;
                                $amountDue = (float) $invoice->amount_due;
                                $amountPaid = (float) ($invoice->amount_paid ?? 0);

                                $dueDate = $invoice->due_at;

                                $isOverdue = $balance > 0
                                    && $dueDate
                                    && $dueDate->toDateString() < now()->toDateString();

                                $student = $invoice->student;

                                $studentName = $student
                                    ? trim(
                                        $student->first_name . ' ' .
                                        ($student->middle_name
                                            ? $student->middle_name . ' '
                                            : '') .
                                        $student->last_name
                                    )
                                    : 'Student unavailable';
                            @endphp

                            <tr wire:key="outstanding-invoice-{{ $invoice->id }}">

                                <td class="ps-3">
                                    <span class="fw-semibold">
                                        {{ $invoice->invoice_number }}
                                    </span>
                                    <div class="small text-muted">
                                        #{{ $invoice->id }}
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $studentName }}</div>
                                    <div class="small text-muted">
                                        Admission No:
                                        {{ $student?->admission_number ?? '—' }}
                                    </div>
                                </td>

                                <td>
                                    <div>
                                        {{ $invoice->academicSession?->name ?? '—' }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $invoice->term?->name ?? '—' }}
                                    </div>
                                </td>

                                <td class="text-nowrap">
                                    {{ $invoice->currency }}
                                    {{ number_format($amountDue, 2) }}
                                </td>

                                <td class="text-nowrap text-success">
                                    {{ $invoice->currency }}
                                    {{ number_format($amountPaid, 2) }}
                                </td>

                                <td class="text-nowrap fw-bold {{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $invoice->currency }}
                                    {{ number_format(max(0, $balance), 2) }}
                                </td>

                                <td class="text-nowrap">
                                    {{ $dueDate ? $dueDate->format('d M Y') : '—' }}

                                    @if ($isOverdue)
                                        <div class="small text-danger fw-semibold">
                                            Past due
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    @if ($balance <= 0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i> Paid
                                        </span>
                                    @elseif ($isOverdue)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="bi bi-exclamation-circle me-1"></i> Overdue
                                        </span>
                                    @elseif ($amountPaid > 0)
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                            <i class="bi bi-clock-history me-1"></i> Part-paid
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border">
                                            <i class="bi bi-x-circle me-1"></i> Unpaid
                                        </span>
                                    @endif
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>

                                    <h6 class="fw-bold mt-3 mb-1">
                                        No invoices found
                                    </h6>

                                    <p class="text-muted small mb-0">
                                        Try changing your filters, or check whether fee invoices
                                        have been created for the selected students and term.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        @if ($invoices->hasPages())
            <div class="card-footer bg-white">
                {{ $invoices->links() }}
            </div>
        @endif

    </div>

    <div class="alert alert-info border-0 mt-4 mb-0">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Important:</strong>
        This report lists students who have invoices. Students without an invoice will not
        appear until their school fees are assigned and an invoice is generated.
        Amounts in different currencies are displayed as recorded and are not converted.
    </div>

    {{-- Print Styles --}}
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .container-fluid,
            .container-fluid * {
                visibility: visible;
            }

            .container-fluid {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 0 !important;
            }

            button,
            input,
            select,
            .pagination,
            .card-footer {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                break-inside: avoid;
            }

            .table-responsive {
                overflow: visible !important;
            }

            table {
                font-size: 10px;
                width: 100% !important;
            }

            th,
            td {
                padding: 5px !important;
            }
        }
    </style>

</div>
