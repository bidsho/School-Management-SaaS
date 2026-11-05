
<div class="container-fluid py-4">

    <style>
        .payment-stat {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 22px;
            height: 100%;
        }

        .payment-stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            flex-shrink: 0;
        }

        .payment-panel {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
        }

        .payment-table th {
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
        }

        .payment-table td {
            vertical-align: middle;
        }

        .payment-reference {
            font-weight: 700;
            color: #1d4ed8;
            overflow-wrap: anywhere;
        }

        .payment-amount {
            font-weight: 700;
            white-space: nowrap;
        }

        .payment-status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }
    </style>

    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Payment History</h3>
            <p class="text-secondary mb-0">
                Review your school fee payments and their verification status.
            </p>
        </div>

        <a
            href="{{ route('student.invoices.index') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-receipt me-2"></i>
            My Invoices
        </a>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-6 col-xl-4">
            <div class="payment-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="payment-stat-icon bg-success-subtle text-success">
                        <i class="bi bi-wallet2"></i>
                    </div>

                    <div>
                        <div class="text-secondary small mb-1">
                            Total Successfully Paid
                        </div>

                        <h4 class="fw-bold mb-0">
                            ₦{{ number_format($totalPaid, 2) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="payment-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="payment-stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <div>
                        <div class="text-secondary small mb-1">
                            Successful Transactions
                        </div>

                        <h4 class="fw-bold mb-0">
                            {{ $successfulCount }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="payment-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="payment-stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <div>
                        <div class="text-secondary small mb-1">
                            Pending Payments
                        </div>

                        <h4 class="fw-bold mb-0">
                            {{ $pendingCount }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Payment history table --}}
    <div class="payment-panel">

        <div class="p-4 border-bottom">
            <h5 class="fw-bold mb-1">Transaction History</h5>

            <p class="text-secondary small mb-0">
                Your recorded payments, including pending and unsuccessful transactions.
            </p>
        </div>

        @if ($payments->isEmpty())

            <div class="text-center py-5 px-3">
                <div
                    class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3"
                    style="width:76px;height:76px;"
                >
                    <i class="bi bi-credit-card text-secondary" style="font-size:32px;"></i>
                </div>

                <h5 class="fw-bold">No payment history yet</h5>

                <p class="text-secondary mb-3">
                    Your payments will appear here after a payment record is created.
                </p>

                <a
                    href="{{ route('student.invoices.index') }}"
                    class="btn btn-outline-primary"
                >
                    View My Invoices
                </a>
            </div>

        @else

            <div class="table-responsive">
                <table class="table table-hover payment-table mb-0">

                    <thead>
                        <tr>
                            <th class="px-4 py-3">Reference</th>
                            <th class="py-3">Invoice</th>
                            <th class="py-3">Payment Method</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Payment Date</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 pe-4">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($payments as $payment)

                            @php
                                $paymentStatus = strtolower(
                                    trim((string) $payment->status)
                                );

                                $statusLabels = [
                                    'success' => 'Successful',
                                    'successful' => 'Successful',
                                    'completed' => 'Completed',
                                    'paid' => 'Paid',
                                    'verified' => 'Verified',
                                    'pending' => 'Pending',
                                    'processing' => 'Processing',
                                    'failed' => 'Failed',
                                    'cancelled' => 'Cancelled',
                                    'canceled' => 'Cancelled',
                                    'refunded' => 'Refunded',
                                ];

                                $statusClasses = [
                                    'success' => 'bg-success-subtle text-success',
                                    'successful' => 'bg-success-subtle text-success',
                                    'completed' => 'bg-success-subtle text-success',
                                    'paid' => 'bg-success-subtle text-success',
                                    'verified' => 'bg-success-subtle text-success',
                                    'pending' => 'bg-warning-subtle text-warning-emphasis',
                                    'processing' => 'bg-info-subtle text-info-emphasis',
                                    'failed' => 'bg-danger-subtle text-danger',
                                    'cancelled' => 'bg-secondary-subtle text-secondary',
                                    'canceled' => 'bg-secondary-subtle text-secondary',
                                    'refunded' => 'bg-secondary-subtle text-secondary',
                                ];

                                $displayStatus = $statusLabels[$paymentStatus]
                                    ?? ucfirst(str_replace('_', ' ', $paymentStatus ?: 'Unknown'));

                                $displayStatusClass = $statusClasses[$paymentStatus]
                                    ?? 'bg-secondary-subtle text-secondary';

                                $provider = $payment->provider
                                    ? ucfirst(str_replace('_', ' ', $payment->provider))
                                    : 'Not specified';
                            @endphp

                            <tr>
                                <td class="px-4 py-3">
                                    <div class="payment-reference">
                                        {{ $payment->reference ?: 'N/A' }}
                                    </div>

                                    @if ($payment->provider_reference)
                                        <div class="text-secondary small mt-1">
                                            Provider: {{ $payment->provider_reference }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    @if ($payment->invoice)
                                        <a
                                            href="{{ route('student.invoices.show', $payment->invoice->id) }}"
                                            class="text-decoration-none fw-semibold"
                                        >
                                            {{ $payment->invoice->invoice_number }}
                                        </a>
                                    @else
                                        <span class="text-secondary">N/A</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="text-secondary small">
                                        {{ $provider }}
                                    </span>
                                </td>

                                <td class="payment-amount">
                                    {{ $payment->currency ?? 'NGN' }}
                                    {{ number_format((float) $payment->amount, 2) }}
                                </td>

                                <td>
                                    <div>
                                        {{ $payment->paid_at?->format('d M Y') ?? 'Not paid yet' }}
                                    </div>

                                    @if ($payment->paid_at)
                                        <div class="text-secondary small">
                                            {{ $payment->paid_at->format('h:i A') }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <span class="payment-status {{ $displayStatusClass }}">
                                        {{ $displayStatus }}
                                    </span>

                                    @if ($payment->verified_at)
                                        <div class="text-success small mt-1">
                                            <i class="bi bi-shield-check me-1"></i>
                                            Verified
                                        </div>
                                    @endif
                                </td>

                                <td class="pe-4">
                                    @if ($payment->invoice)
                                        <a
                                            href="{{ route('student.invoices.show', $payment->invoice->id) }}"
                                            class="btn btn-sm btn-outline-primary text-nowrap"
                                        >
                                            <i class="bi bi-eye me-1"></i>
                                            Invoice
                                        </a>
                                    @else
                                        <span class="text-secondary small">No invoice</span>
                                    @endif
                                </td>
                            </tr>

                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="p-3 border-top">
                {{ $payments->links() }}
            </div>

        @endif

    </div>

    <div class="text-secondary small mt-3">
        <i class="bi bi-info-circle me-1"></i>
        If a payment is pending or its status looks incorrect, contact your school's accounts office.
    </div>

</div>
