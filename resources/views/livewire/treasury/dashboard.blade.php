
<div>
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1">Treasury Dashboard</h3>
                <p class="text-muted mb-0">
                    Monitor school collections, pending payments and outstanding fees.
                </p>
            </div>

            <div class="text-muted small">
                <i class="bi bi-calendar3 me-1"></i>
                {{ now()->format('d M Y') }}
            </div>
        </div>

        {{-- Summary cards --}}
        <div class="row g-3 mb-4">

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-2">Total Collections</p>
                                <h4 class="fw-bold mb-2">Received</h4>
                                <small class="text-muted">By currency</small>
                            </div>
                            <span class="rounded-3 bg-success-subtle text-success p-3">
                                <i class="bi bi-wallet2 fs-4"></i>
                            </span>
                        </div>

                        @forelse ($collectionsByCurrency as $collection)
                            <div class="d-flex justify-content-between mt-3 gap-2">
                                <span class="text-muted">{{ $collection->currency }}</span>
                                <strong>
                                    {{ number_format((float) $collection->total, 2) }}
                                </strong>
                            </div>
                        @empty
                            <p class="text-muted mt-3 mb-0">No completed collections yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-2">Today's Collections</p>
                                <h4 class="fw-bold mb-2">Today</h4>
                                <small class="text-muted">By currency</small>
                            </div>
                            <span class="rounded-3 bg-primary-subtle text-primary p-3">
                                <i class="bi bi-calendar-check fs-4"></i>
                            </span>
                        </div>

                        @forelse ($todayByCurrency as $collection)
                            <div class="d-flex justify-content-between mt-3 gap-2">
                                <span class="text-muted">{{ $collection->currency }}</span>
                                <strong>
                                    {{ number_format((float) $collection->total, 2) }}
                                </strong>
                            </div>
                        @empty
                            <p class="text-muted mt-3 mb-0">No collections recorded today.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-2">Pending Payments</p>
                                <h3 class="fw-bold mb-2">{{ $pendingPayments }}</h3>
                                <small class="text-muted">Awaiting completion</small>
                            </div>
                            <span class="rounded-3 bg-warning-subtle text-warning p-3">
                                <i class="bi bi-hourglass-split fs-4"></i>
                            </span>
                        </div>

                        @forelse ($pendingByCurrency as $pending)
                            <div class="d-flex justify-content-between mt-3 gap-2">
                                <span class="text-muted">{{ $pending->currency }}</span>
                                <strong>
                                    {{ number_format((float) $pending->total, 2) }}
                                </strong>
                            </div>
                        @empty
                            <p class="text-muted mt-3 mb-0">No pending payments.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-2">Outstanding Fees</p>
                                <h4 class="fw-bold mb-2">{{ $unpaidInvoices }}</h4>
                                <small class="text-muted">Invoices with a balance</small>
                            </div>
                            <span class="rounded-3 bg-danger-subtle text-danger p-3">
                                <i class="bi bi-file-earmark-text fs-4"></i>
                            </span>
                        </div>

                        @forelse ($outstandingByCurrency as $outstanding)
                            <div class="d-flex justify-content-between mt-3 gap-2">
                                <span class="text-muted">{{ $outstanding->currency }}</span>
                                <strong>
                                    {{ number_format((float) $outstanding->total, 2) }}
                                </strong>
                            </div>
                        @empty
                            <p class="text-muted mt-3 mb-0">No outstanding balances.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Secondary statistics --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-info-subtle text-info p-3">
                            <i class="bi bi-receipt fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Invoices</div>
                            <h4 class="fw-bold mb-0">{{ $totalInvoices }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-success-subtle text-success p-3">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Completed Payments Verified</div>
                            <h4 class="fw-bold mb-0">{{ $verifiedPayments }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent payments --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1">Recent Payments</h5>
                    <small class="text-muted">Latest payment records for your school</small>
                </div>
                <span class="badge bg-light text-dark">
                    {{ $recentPayments->total() }} records
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">Reference</th>
                            <th>Invoice</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Verification</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($recentPayments as $payment)
                            <tr wire:key="payment-{{ $payment->id }}">
                                <td class="px-3">
                                    <div class="fw-semibold">
                                        {{ $payment->reference ?: '—' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $payment->provider ?: 'Manual payment' }}
                                    </small>
                                </td>

                                <td>
                                    {{ $payment->invoice?->invoice_number ?? '—' }}
                                </td>

                                <td class="text-nowrap fw-semibold">
                                    {{ $payment->currency }}
                                    {{ number_format((float) $payment->amount, 2) }}
                                </td>

                                <td>
                                    {{ $payment->paid_at?->format('d M Y, h:i A') ?? 'Not paid' }}
                                </td>

                                <td>
                                    @if ($payment->verified_at)
                                        <span class="badge bg-success-subtle text-success">
                                            Verified
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            Not verified
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $statusClass = match (strtolower($payment->status ?? '')) {
                                            'completed' => 'success',
                                            'pending' => 'warning',
                                            'failed', 'cancelled' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }}">
                                        {{ ucfirst($payment->status ?? 'Unknown') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-wallet2 fs-1 text-muted"></i>
                                    <h6 class="mt-3">No payment records yet</h6>
                                    <p class="text-muted mb-0">
                                        Payments will appear here when they are recorded.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($recentPayments->hasPages())
                <div class="card-footer bg-white">
                    {{ $recentPayments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
