
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Payments</h3>
            <p class="text-muted mb-0">
                View and monitor student fee payments for your school.
            </p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Payments</div>
                    <div class="fs-3 fw-bold">{{ number_format($totalPayments) }}</div>
                    <i class="bi bi-wallet2 text-primary"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Successful</div>
                    <div class="fs-3 fw-bold text-success">
                        {{ number_format($successfulPayments) }}
                    </div>
                    <i class="bi bi-check-circle text-success"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Pending</div>
                    <div class="fs-3 fw-bold text-warning">
                        {{ number_format($pendingPayments) }}
                    </div>
                    <i class="bi bi-clock-history text-warning"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Failed</div>
                    <div class="fs-3 fw-bold text-danger">
                        {{ number_format($failedPayments) }}
                    </div>
                    <i class="bi bi-x-circle text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-5">
                    <label class="form-label">Search payments</label>
                    <input type="search"
                           class="form-control"
                           wire:model.live.debounce.350ms="search"
                           placeholder="Reference, student, admission number or invoice ID">
                </div>

                <div class="col-sm-6 col-lg-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" wire:model.live="status">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>

                <div class="col-sm-6 col-lg-2">
                    <label class="form-label">Provider</label>
                    <select class="form-select" wire:model.live="provider">
                        <option value="">All providers</option>
                        @foreach ($providers as $paymentProvider)
                            <option value="{{ $paymentProvider }}">
                                {{ ucfirst($paymentProvider) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-6 col-lg-2">
                    <label class="form-label">Payment date</label>
                    <input type="date"
                           class="form-control"
                           wire:model.live="payment_date">
                </div>

                <div class="col-sm-6 col-lg-1">
                    <button type="button"
                            class="btn btn-outline-secondary w-100"
                            wire:click="clearFilters"
                            title="Clear filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="fw-semibold mb-0">Payment Records</h5>
                <span class="text-muted small">
                    {{ $payments->total() }} records found
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Payment Reference</th>
                        <th>Student</th>
                        <th>Invoice</th>
                        <th>Amount</th>
                        <th>Provider</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Verification</th>
                        <th>Receipt</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($payments as $payment)
                        <tr wire:key="payment-{{ $payment->id }}">
                            <td class="ps-4">
                                <div class="fw-semibold">
                                    {{ $payment->reference ?: '—' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $payment->provider_reference ?: 'No provider reference' }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ trim(
                                        ($payment->student?->first_name ?? '') . ' ' .
                                        ($payment->student?->last_name ?? '')
                                    ) ?: 'Student unavailable' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $payment->student?->admission_number ?? '' }}
                                </div>
                            </td>

                            <td>
                                {{ $payment->invoice_id ? '#' . $payment->invoice_id : '—' }}
                            </td>

                            <td class="fw-semibold text-nowrap">
                                {{ $payment->currency }}
                                {{ number_format((float) $payment->amount, 2) }}
                            </td>

                            <td>
                                {{ $payment->provider
                                    ? ucfirst($payment->provider)
                                    : '—' }}
                            </td>

                            <td class="text-nowrap">
                                <div>
                                    {{ $payment->created_at?->format('d M Y') ?? '—' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $payment->created_at?->format('h:i A') ?? '' }}
                                </div>
                            </td>

                            <td>
                                @php
                                    $paymentStatus = strtolower($payment->status ?? '');

                                    $paymentBadge = match ($paymentStatus) {
                                        'paid', 'successful', 'completed'
                                            => 'bg-success-subtle text-success',
                                        'pending', 'processing'
                                            => 'bg-warning-subtle text-warning-emphasis',
                                        'failed', 'cancelled'
                                            => 'bg-danger-subtle text-danger',
                                        'refunded'
                                            => 'bg-info-subtle text-info-emphasis',
                                        default
                                            => 'bg-secondary-subtle text-secondary',
                                    };
                                @endphp

                                <span class="badge {{ $paymentBadge }}">
                                    {{ ucfirst($paymentStatus ?: 'Unknown') }}
                                </span>
                            </td>

                            <td>
                                @if ($payment->verified_at)
                                    <span class="text-success small">
                                        <i class="bi bi-patch-check-fill me-1"></i>
                                        Verified
                                    </span>
                                    <div class="small text-muted">
                                        {{ $payment->verified_at->format('d M Y, h:i A') }}
                                    </div>
                                @else
                                    <span class="text-muted small">Not verified</span>
                                @endif
                            </td>

                            <td>
                                @if ($payment->receipt)
                                    <span class="badge bg-success-subtle text-success">
                                        Available
                                    </span>
                                @else
                                    <span class="text-muted small">No receipt</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-wallet fs-1 text-muted"></i>
                                <h6 class="fw-semibold mt-3">No payments found</h6>
                                <p class="text-muted mb-0">
                                    Try changing your search or filters.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
