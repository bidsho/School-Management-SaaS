
<div class="container-fluid py-4">

    <style>
        .invoice-stat {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 22px;
            height: 100%;
        }

        .invoice-stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .invoice-table-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
        }

        .invoice-number {
            color: #1d4ed8;
            font-weight: 700;
            text-decoration: none;
        }

        .invoice-number:hover {
            text-decoration: underline;
        }

        .invoice-table th {
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
        }

        .invoice-table td {
            vertical-align: middle;
        }

        .invoice-action {
            white-space: nowrap;
        }
    </style>

    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Invoices</h3>
            <p class="text-secondary mb-0">
                View your school fees, payment history summary, and outstanding balances.
            </p>
        </div>

        <div class="text-secondary small">
            <i class="bi bi-receipt me-1"></i>
            {{ $invoiceCount }} invoice(s)
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-6 col-xl-4">
            <div class="invoice-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="invoice-stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-receipt"></i>
                    </div>

                    <div>
                        <div class="text-secondary small mb-1">
                            Total Invoiced
                        </div>

                        <h4 class="fw-bold mb-0">
                            ₦{{ number_format($totalInvoiced, 2) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="invoice-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="invoice-stat-icon bg-success-subtle text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <div>
                        <div class="text-secondary small mb-1">
                            Total Paid
                        </div>

                        <h4 class="fw-bold mb-0">
                            ₦{{ number_format($totalPaid, 2) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="invoice-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="invoice-stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-wallet2"></i>
                    </div>

                    <div>
                        <div class="text-secondary small mb-1">
                            Outstanding Balance
                        </div>

                        <h4 class="fw-bold mb-0 text-danger">
                            ₦{{ number_format($totalOutstanding, 2) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Invoice table --}}
    <div class="invoice-table-card">

        <div class="p-4 border-bottom">
            <h5 class="fw-bold mb-1">Invoice History</h5>
            <p class="text-secondary small mb-0">
                Select an invoice to view its details and payment instructions.
            </p>
        </div>

        @if ($invoices->isEmpty())

            <div class="text-center py-5 px-3">
                <div
                    class="mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center"
                    style="width:72px;height:72px;"
                >
                    <i class="bi bi-receipt text-secondary" style="font-size:30px;"></i>
                </div>

                <h5 class="fw-bold">No invoices found</h5>

                <p class="text-secondary mb-0">
                    Your school has not issued any invoices to you yet.
                </p>
            </div>

        @else

            <div class="table-responsive">
                <table class="table invoice-table table-hover mb-0">

                    <thead>
                        <tr>
                            <th class="px-4 py-3">Invoice</th>
                            <th class="py-3">Academic Session</th>
                            <th class="py-3">Term</th>
                            <th class="py-3">Amount Due</th>
                            <th class="py-3">Amount Paid</th>
                            <th class="py-3">Balance</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 pe-4">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($invoices as $invoice)

                            @php
                                $balance = max(
                                    0,
                                    (float) $invoice->amount_due
                                        - (float) $invoice->amount_paid
                                );

                                if ($balance <= 0) {
                                    $status = 'Paid';
                                    $statusClass = 'text-bg-success';
                                } elseif ((float) $invoice->amount_paid > 0) {
                                    $status = 'Partially Paid';
                                    $statusClass = 'text-bg-warning';
                                } elseif (in_array(strtolower($invoice->status ?? ''), ['cancelled', 'canceled', 'void'])) {
                                    $status = 'Cancelled';
                                    $statusClass = 'text-bg-secondary';
                                } elseif ($invoice->due_at && $invoice->due_at->isPast()) {
                                    $status = 'Overdue';
                                    $statusClass = 'text-bg-danger';
                                } else {
                                    $status = 'Unpaid';
                                    $statusClass = 'text-bg-primary';
                                }
                            @endphp

                            <tr>
                                <td class="px-4 py-3">
                                    <a
                                        href="{{ route('student.invoices.show', $invoice->id) }}"
                                        class="invoice-number"
                                    >
                                        {{ $invoice->invoice_number }}
                                    </a>

                                    <div class="text-secondary small mt-1">
                                        Issued:
                                        {{ $invoice->issued_at?->format('d M Y') ?? 'N/A' }}
                                    </div>
                                </td>

                                <td>
                                    {{ $invoice->academicSession?->name ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $invoice->term?->name ?? 'N/A' }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $invoice->currency ?? 'NGN' }}
                                    {{ number_format((float) $invoice->amount_due, 2) }}
                                </td>

                                <td class="text-success">
                                    {{ $invoice->currency ?? 'NGN' }}
                                    {{ number_format((float) $invoice->amount_paid, 2) }}
                                </td>

                                <td class="{{ $balance > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                    {{ $invoice->currency ?? 'NGN' }}
                                    {{ number_format($balance, 2) }}
                                </td>

                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </td>

                                <td class="pe-4 invoice-action">
                                    <a
                                        href="{{ route('student.invoices.show', $invoice->id) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="bi bi-eye me-1"></i>
                                        View Details
                                    </a>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="p-3 border-top">
                {{ $invoices->links() }}
            </div>

        @endif

    </div>

</div>
