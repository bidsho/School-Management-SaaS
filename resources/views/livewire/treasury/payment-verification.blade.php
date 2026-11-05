
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1">Payment Verification</h2>
            <p class="text-muted mb-0">
                Review parent-submitted bank transfers, verify payments, and issue receipts.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge text-bg-warning px-3 py-2">
                Pending: {{ $counts->pending_count ?? 0 }}
            </span>
            <span class="badge text-bg-success px-3 py-2">
                Completed: {{ $counts->completed_count ?? 0 }}
            </span>
            <span class="badge text-bg-danger px-3 py-2">
                Rejected: {{ $counts->rejected_count ?? 0 }}
            </span>
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- VALIDATION ERRORS --}}
    @if ($errors->has('payment'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ $errors->first('payment') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- SUMMARY CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning-subtle text-warning p-3">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Awaiting Verification</div>
                        <div class="fs-3 fw-bold">
                            {{ $counts->pending_count ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success-subtle text-success p-3">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Verified Payments</div>
                        <div class="fs-3 fw-bold">
                            {{ $counts->completed_count ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger-subtle text-danger p-3">
                        <i class="bi bi-x-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Rejected Payments</div>
                        <div class="fs-3 fw-bold">
                            {{ $counts->rejected_count ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PAYMENT TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-3 p-md-4">
            <div class="row g-3 align-items-center">
                <div class="col-lg-5">
                    <h5 class="fw-bold mb-1">Payments</h5>
                    <p class="text-muted small mb-0">
                        Review payment submissions for your school.
                    </p>
                </div>

                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="search"
                               class="form-control border-start-0"
                               placeholder="Search reference, invoice or student..."
                               wire:model.live.debounce.400ms="search">
                    </div>
                </div>

                <div class="col-lg-3">
                    <select class="form-select" wire:model.live="status">
                        <option value="pending">Pending Verification</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                        <option value="all">All Payments</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Payment</th>
                            <th>Student</th>
                            <th>Invoice</th>
                            <th>Method / Transfer Reference</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date Submitted</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($payments as $payment)
                            @php
                                $details = $payment->metadata ?? [];

                                $statusClass = match (strtolower($payment->status)) {
                                    'completed', 'success', 'successful', 'paid'
                                        => 'text-bg-success',
                                    'rejected', 'failed'
                                        => 'text-bg-danger',
                                    default
                                        => 'text-bg-warning',
                                };
                            @endphp

                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold">
                                        {{ $payment->reference }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $payment->provider === 'manual_bank_transfer'
                                            ? 'Manual bank transfer'
                                            : ($payment->provider ?: 'Manual payment') }}
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $payment->student->full_name
                                            ?? trim(($payment->student->first_name ?? '') . ' ' . ($payment->student->last_name ?? ''))
                                            ?: 'Student' }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $payment->student->admission_number ?? '' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $payment->invoice->invoice_number ?? '—' }}
                                    </div>
                                </td>

                                <td>
                                    @if ($payment->provider === 'manual_bank_transfer')
                                        <span class="badge text-bg-info mb-1">
                                            <i class="bi bi-bank me-1"></i> Bank Transfer
                                        </span>

                                        <div class="small fw-semibold">
                                            {{ $payment->provider_reference ?: 'No transfer reference' }}
                                        </div>

                                        @if (!empty($details['sender_name']))
                                            <div class="small text-muted">
                                                Sender: {{ $details['sender_name'] }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="small text-muted">
                                            {{ $payment->provider ?: 'Manual payment' }}
                                        </span>
                                        @if ($payment->provider_reference)
                                            <div class="small text-muted">
                                                Ref: {{ $payment->provider_reference }}
                                            </div>
                                        @endif
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-bold">
                                        ₦{{ number_format((float) $payment->amount, 2) }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $payment->currency }}
                                    </div>
                                </td>

                                <td>
                                    <span class="badge rounded-pill {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                    </span>
                                </td>

                                <td>
                                    <div>
                                        {{ $payment->created_at?->format('d M Y') ?? '—' }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $payment->created_at?->format('h:i A') ?? '' }}
                                    </div>
                                </td>

                                <td class="text-end pe-4">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            wire:click="viewPayment({{ $payment->id }})"
                                            wire:loading.attr="disabled">
                                        <i class="bi bi-eye me-1"></i> View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                    </div>
                                    <h6 class="fw-bold">No payments found</h6>
                                    <p class="text-muted small mb-0">
                                        Try another status filter or search term.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($payments->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    {{-- PAYMENT DETAILS MODAL --}}
    @if ($selectedPayment)
        @php
            $details = $selectedPayment->metadata ?? [];

            $selectedStatusClass = match (strtolower($selectedPayment->status)) {
                'completed', 'success', 'successful', 'paid'
                    => 'text-bg-success',
                'rejected', 'failed'
                    => 'text-bg-danger',
                default
                    => 'text-bg-warning',
            };
        @endphp

        <div class="modal d-block"
             tabindex="-1"
             role="dialog"
             aria-modal="true"
             style="background: rgba(0,0,0,.55);"
             wire:key="payment-details-{{ $selectedPayment->id }}">

            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">

                    {{-- MODAL HEADER --}}
                    <div class="modal-header px-4 py-3">
                        <div>
                            <h5 class="modal-title fw-bold mb-1">
                                Payment Details
                            </h5>
                            <div class="small text-muted">
                                {{ $selectedPayment->reference }}
                            </div>
                        </div>

                        <button type="button"
                                class="btn-close"
                                wire:click="closePaymentDetails"
                                aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">

                        {{-- PAYMENT AMOUNT --}}
                        <div class="rounded-3 bg-light p-4 mb-4 text-center">
                            <div class="text-muted small mb-1">Payment Amount</div>
                            <div class="display-6 fw-bold">
                                ₦{{ number_format((float) $selectedPayment->amount, 2) }}
                            </div>
                            <div class="mt-2">
                                <span class="badge rounded-pill {{ $selectedStatusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $selectedPayment->status)) }}
                                </span>
                            </div>
                        </div>

                        {{-- GENERAL INFORMATION --}}
                        <h6 class="fw-bold mb-3">Payment Information</h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="small text-muted mb-1">Payment Reference</div>
                                    <div class="fw-semibold">
                                        {{ $selectedPayment->reference }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="small text-muted mb-1">Payment Method</div>
                                    <div class="fw-semibold">
                                        {{ $selectedPayment->provider === 'manual_bank_transfer'
                                            ? 'Manual Bank Transfer'
                                            : ($selectedPayment->provider ?: 'Manual Payment') }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="small text-muted mb-1">Invoice Number</div>
                                    <div class="fw-semibold">
                                        {{ $selectedPayment->invoice->invoice_number ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="small text-muted mb-1">Currency</div>
                                    <div class="fw-semibold">
                                        {{ $selectedPayment->currency }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="small text-muted mb-1">Submitted At</div>
                                    <div class="fw-semibold">
                                        {{ $selectedPayment->created_at?->format('d M Y, h:i A') ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="small text-muted mb-1">Verified At</div>
                                    <div class="fw-semibold">
                                        {{ $selectedPayment->verified_at?->format('d M Y, h:i A') ?? 'Not verified' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- STUDENT INFORMATION --}}
                        <h6 class="fw-bold mb-3">Student Information</h6>

                        <div class="border rounded-3 p-3 mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="small text-muted mb-1">Student Name</div>
                                    <div class="fw-semibold">
                                        {{ $selectedPayment->student->full_name
                                            ?? trim(($selectedPayment->student->first_name ?? '') . ' ' . ($selectedPayment->student->last_name ?? ''))
                                            ?: 'Student' }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="small text-muted mb-1">Admission Number</div>
                                    <div class="fw-semibold">
                                        {{ $selectedPayment->student->admission_number ?? '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- MANUAL BANK TRANSFER DETAILS --}}
                        @if ($selectedPayment->provider === 'manual_bank_transfer')
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-bank me-2 text-primary"></i>
                                Parent Bank Transfer Submission
                            </h6>

                            <div class="alert alert-info">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="small text-muted mb-1">
                                            Bank Transfer Reference
                                        </div>
                                        <div class="fw-bold">
                                            {{ $selectedPayment->provider_reference ?: '—' }}
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="small text-muted mb-1">
                                            Sending Account Name
                                        </div>
                                        <div class="fw-bold">
                                            {{ $details['sender_name'] ?? '—' }}
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="small text-muted mb-1">Transfer Date</div>
                                        <div class="fw-semibold">
                                            {{ $details['transfer_date'] ?? '—' }}
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="small text-muted mb-1">Submission Date</div>
                                        <div class="fw-semibold">
                                            {{ $details['submitted_at'] ?? $selectedPayment->created_at?->toDateTimeString() ?? '—' }}
                                        </div>
                                    </div>

                                    @if (!empty($details['parent_notes']))
                                        <div class="col-12">
                                            <div class="small text-muted mb-1">Parent's Notes</div>
                                            <div>{{ $details['parent_notes'] }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="alert alert-warning small">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Verify that the funds have actually arrived in the school's bank account.
                                The parent's transfer reference is not proof of receipt.
                            </div>
                        @endif

                        {{-- INVOICE BALANCE --}}
                        @if ($selectedPayment->invoice)
                            <h6 class="fw-bold mb-3">Invoice Balance</h6>

                            <div class="border rounded-3 p-3 mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Invoice Total</span>
                                    <span class="fw-semibold">
                                        ₦{{ number_format((float) $selectedPayment->invoice->amount_due, 2) }}
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Amount Paid</span>
                                    <span class="fw-semibold text-success">
                                        ₦{{ number_format((float) $selectedPayment->invoice->amount_paid, 2) }}
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between border-top pt-3">
                                    <span class="fw-bold">Outstanding Balance</span>
                                    <span class="fw-bold text-danger">
                                        ₦{{ number_format(max(0, (float) $selectedPayment->invoice->balance), 2) }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        {{-- REJECTION REASON --}}
                        @if ($selectedPayment->status === 'rejected')
                            <div class="alert alert-danger">
                                <div class="fw-bold mb-1">
                                    <i class="bi bi-x-circle me-2"></i>
                                    Rejection Reason
                                </div>
                                {{ data_get($selectedPayment->metadata, 'rejection_reason', 'No reason recorded.') }}
                            </div>
                        @endif

                        {{-- RECEIPT --}}
                        @if ($selectedPayment->status === 'completed' && $selectedPayment->receipt)
                            <div class="alert alert-success">
                                <div class="fw-bold mb-1">
                                    <i class="bi bi-receipt me-2"></i>
                                    Receipt Issued
                                </div>
                                <div>
                                    Receipt Number:
                                    <strong>{{ $selectedPayment->receipt->receipt_number }}</strong>
                                </div>
                                <div class="small mt-1">
                                    Issued:
                                    {{ $selectedPayment->receipt->issued_at?->format('d M Y, h:i A') ?? '—' }}
                                </div>
                            </div>
                        @endif

                        {{-- ACTION VALIDATION ERRORS --}}
                        @if ($errors->has('payment'))
                            <div class="alert alert-danger">
                                {{ $errors->first('payment') }}
                            </div>
                        @endif

                        {{-- REJECTION FORM --}}
                        @if ($selectedPayment->status === 'pending')
                            <div class="border rounded-3 p-3 mt-4">
                                <h6 class="fw-bold mb-3">Reject Payment</h6>

                                <label for="rejectionReason" class="form-label">
                                    Reason for rejection
                                </label>

                                <textarea id="rejectionReason"
                                          rows="3"
                                          class="form-control @error('rejectionReason') is-invalid @enderror"
                                          wire:model="rejectionReason"
                                          maxlength="500"
                                          placeholder="Explain why this transfer cannot be approved..."
                                          required></textarea>

                                @error('rejectionReason')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <div class="small text-muted mt-2">
                                    Rejection does not change the invoice balance.
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- MODAL FOOTER --}}
                    <div class="modal-footer px-4 py-3">
                        <button type="button"
                                class="btn btn-light"
                                wire:click="closePaymentDetails">
                            Close
                        </button>

                        @if ($selectedPayment->status === 'pending')
                            <button type="button"
                                    class="btn btn-danger"
                                    wire:click="rejectPayment({{ $selectedPayment->id }})"
                                    wire:confirm="Are you sure you want to reject this payment?"
                                    wire:loading.attr="disabled"
                                    wire:target="rejectPayment">
                                <span wire:loading.remove wire:target="rejectPayment">
                                    <i class="bi bi-x-circle me-1"></i> Reject Payment
                                </span>
                                <span wire:loading wire:target="rejectPayment">
                                    Rejecting...
                                </span>
                            </button>

                            <button type="button"
                                    class="btn btn-success"
                                    wire:click="verifyPayment({{ $selectedPayment->id }})"
                                    wire:confirm="Confirm that the funds have arrived in the school's bank account. Approve this payment?"
                                    wire:loading.attr="disabled"
                                    wire:target="verifyPayment">
                                <span wire:loading.remove wire:target="verifyPayment">
                                    <i class="bi bi-check-circle me-1"></i> Verify & Issue Receipt
                                </span>
                                <span wire:loading wire:target="verifyPayment">
                                    Verifying...
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
