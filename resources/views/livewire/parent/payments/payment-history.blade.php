
<div class="container-fluid py-4">

    {{-- Page header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">Payment History</h2>
            <p class="text-muted mb-0">
                Review payments made for your children's school fees
                and access your verified receipts.
            </p>
        </div>

        <a href="{{ route('parent.fees.index') }}"
           class="btn btn-outline-primary">
            <i class="bi bi-receipt me-1"></i>
            View Invoices
        </a>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Payment Records
                    </div>

                    <h3 class="fw-bold mb-0">
                        {{ $totalPayments }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Verified Payments
                    </div>

                    <h3 class="fw-bold text-success mb-0">
                        ₦{{ number_format((float) $verifiedAmount, 2) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Pending Payments
                    </div>

                    <h3 class="fw-bold text-warning mb-0">
                        ₦{{ number_format((float) $pendingAmount, 2) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-6">
                    <label for="studentId" class="form-label fw-semibold">
                        Filter by child
                    </label>

                    <select id="studentId"
                            class="form-select"
                            wire:model.live="studentId">

                        <option value="all">All children</option>

                        @foreach ($children as $child)
                            <option value="{{ $child->id }}">
                                {{ $child->first_name }}
                                {{ $child->last_name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label fw-semibold">
                        Payment status
                    </label>

                    <select id="status"
                            class="form-select"
                            wire:model.live="status">

                        <option value="all">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="successful">Successful</option>
                        <option value="success">Success</option>
                        <option value="paid">Paid</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="rejected">Rejected</option>

                    </select>
                </div>

            </div>
        </div>
    </div>

    {{-- Payment table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Payment Records</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="px-3">Reference</th>
                        <th>Child</th>
                        <th>Invoice</th>
                        <th>Amount</th>
                        <th>Date Paid</th>
                        <th>Status</th>
                        <th>Verification</th>
                        <th>Receipt</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($payments as $payment)

                        @php
                            $paymentStatus = strtolower(
                                $payment->status ?? 'pending'
                            );

                            $isVerified = $payment->verified_at !== null
                                && in_array($paymentStatus, [
                                    'successful',
                                    'success',
                                    'paid',
                                    'completed',
                                ], true);

                            $badgeClass = match ($paymentStatus) {
                                'successful', 'success', 'paid', 'completed'
                                    => 'success',

                                'pending', 'processing'
                                    => 'warning',

                                'failed', 'cancelled', 'rejected'
                                    => 'danger',

                                default => 'secondary',
                            };
                        @endphp

                        <tr wire:key="payment-{{ $payment->id }}">

                            {{-- Reference --}}
                            <td class="px-3">
                                <span class="fw-semibold">
                                    {{ $payment->reference }}
                                </span>
                            </td>

                            {{-- Child --}}
                            <td>
                                {{ $payment->student?->first_name }}
                                {{ $payment->student?->last_name }}
                            </td>

                            {{-- Invoice --}}
                            <td>
                                {{ $payment->invoice?->invoice_number ?? '—' }}
                            </td>

                            {{-- Amount --}}
                            <td class="fw-semibold">
                                {{ $payment->currency }}
                                {{ number_format((float) $payment->amount, 2) }}
                            </td>

                            {{-- Date paid --}}
                            <td>
                                {{ $payment->paid_at?->format('d M Y, h:i A') ?? '—' }}
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="badge text-bg-{{ $badgeClass }}">
                                    {{ ucfirst($paymentStatus) }}
                                </span>
                            </td>

                            {{-- Verification --}}
                            <td>
                                @if ($isVerified)
                                    <span class="text-success small">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Verified
                                    </span>

                                    <div class="text-muted small">
                                        {{ $payment->verified_at->format('d M Y') }}
                                    </div>
                                @elseif ($paymentStatus === 'pending')
                                    <span class="text-warning small">
                                        <i class="bi bi-clock me-1"></i>
                                        Awaiting verification
                                    </span>
                                @elseif ($paymentStatus === 'rejected')
                                    <span class="text-danger small">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Rejected
                                    </span>
                                @else
                                    <span class="text-muted small">
                                        Not verified
                                    </span>
                                @endif
                            </td>

                            {{-- Receipt --}}
                            <td>
                                @if ($isVerified && $payment->receipt)

                                    <a href="{{ route(
                                        'parent.receipts.show',
                                        $payment->receipt->id
                                    ) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-receipt me-1"></i>
                                        View Receipt
                                    </a>

                                @elseif ($paymentStatus === 'pending')

                                    <span class="text-muted small">
                                        Awaiting verification
                                    </span>

                                @elseif ($paymentStatus === 'rejected')

                                    <span class="text-danger small">
                                        Not issued
                                    </span>

                                @else

                                    <span class="text-muted small">
                                        Not available
                                    </span>

                                @endif
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="text-center py-5">

                                <i class="bi bi-wallet2 fs-1 text-muted"></i>

                                <h6 class="fw-bold mt-3">
                                    No payments found
                                </h6>

                                <p class="text-muted mb-0">
                                    Payment records will appear here when available.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

        {{-- Pagination --}}
        @if ($payments->hasPages())
            <div class="card-footer bg-white">
                {{ $payments->links() }}
            </div>
        @endif

    </div>

</div>
