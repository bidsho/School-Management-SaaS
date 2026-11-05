
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <a href="{{ route('parent.fees.index') }}"
               class="btn btn-sm btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-1"></i> Back to School Fees
            </a>

            <h2 class="h4 fw-bold mb-1">Invoice Details</h2>
            <p class="text-muted mb-0">
                Review the invoice and submit your bank transfer.
            </p>
        </div>

        <span class="badge rounded-pill px-3 py-2
            {{ (float) $invoice->balance <= 0 ? 'text-bg-success' : 'text-bg-warning' }}">
            {{ (float) $invoice->balance <= 0 ? 'Paid' : ucfirst(str_replace('_', ' ', $invoice->status ?? 'unpaid')) }}
        </span>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @error('bankDetails')
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ $message }}
        </div>
    @enderror

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
                        <div>
                            <p class="text-muted small mb-1">Invoice Number</p>
                            <h5 class="fw-bold mb-0">{{ $invoice->invoice_number }}</h5>
                        </div>

                        <div class="text-lg-end">
                            <p class="text-muted small mb-1">Currency</p>
                            <span class="fw-semibold">NGN — Nigerian Naira (₦)</span>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="rounded bg-light p-3 h-100">
                                <p class="text-muted small mb-1">Student</p>
                                <div class="fw-semibold">
                                    {{ $invoice->student->full_name
                                        ?? trim(($invoice->student->first_name ?? '') . ' ' . ($invoice->student->last_name ?? ''))
                                        ?: 'Student' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $invoice->student->admission_number ?? '' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="rounded bg-light p-3 h-100">
                                <p class="text-muted small mb-1">Academic Session / Term</p>
                                <div class="fw-semibold">
                                    {{ $invoice->academicSession->name ?? '—' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $invoice->term->name ?? '—' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <p class="text-muted small mb-1">Issue Date</p>
                            <div class="fw-semibold">
                                {{ $invoice->issued_at?->format('d M Y') ?? '—' }}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <p class="text-muted small mb-1">Due Date</p>
                            <div class="fw-semibold">
                                {{ $invoice->due_at?->format('d M Y') ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">Invoice Items</h6>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Amount</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoice->items as $item)
                                    <tr>
                                        <td>{{ $item->description }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">
                                            ₦{{ number_format((float) $item->unit_amount, 2) }}
                                        </td>
                                        <td class="text-end fw-semibold">
                                            ₦{{ number_format((float) $item->total_amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No invoice items found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-md-7 col-lg-6">
                            <div class="d-flex justify-content-between py-2">
                                <span class="text-muted">Subtotal</span>
                                <span>₦{{ number_format((float) $invoice->subtotal, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between py-2">
                                <span class="text-muted">Discount</span>
                                <span>₦{{ number_format((float) $invoice->discount, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between py-2 border-top">
                                <span class="fw-semibold">Amount Due</span>
                                <span class="fw-semibold">
                                    ₦{{ number_format((float) $invoice->amount_due, 2) }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between py-2">
                                <span class="text-muted">Amount Paid</span>
                                <span class="text-success">
                                    ₦{{ number_format((float) $invoice->amount_paid, 2) }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-1">
                                <span class="fw-bold">Outstanding Balance</span>
                                <span class="fs-4 fw-bold text-danger">
                                    ₦{{ number_format(max(0, (float) $invoice->balance), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if ((float) $invoice->balance > 0)
                        <div class="mt-4">
                            <button type="button"
                                    wire:click="openPaymentForm"
                                    class="btn btn-primary btn-lg"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="openPaymentForm">
                                    <i class="bi bi-bank me-2"></i> Make Payment
                                </span>
                                <span wire:loading wire:target="openPaymentForm">
                                    Loading...
                                </span>
                            </button>
                        </div>
                    @else
                        <div class="alert alert-success mt-4 mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            This invoice has no outstanding balance.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Payment Activity</h5>

                    @forelse ($invoice->payments as $payment)
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 border-bottom py-3">
                            <div>
                                <div class="fw-semibold">
                                    ₦{{ number_format((float) $payment->amount, 2) }}
                                </div>
                                <div class="small text-muted">
                                    Reference: {{ $payment->reference }}
                                </div>
                                @if ($payment->provider_reference)
                                    <div class="small text-muted">
                                        Transfer reference: {{ $payment->provider_reference }}
                                    </div>
                                @endif
                                <div class="small text-muted">
                                    {{ $payment->created_at?->format('d M Y, h:i A') }}
                                </div>
                            </div>

                            <span class="badge rounded-pill
                                {{ in_array(strtolower($payment->status), ['completed', 'success', 'successful', 'paid'])
                                    ? 'text-bg-success'
                                    : (strtolower($payment->status) === 'rejected'
                                        ? 'text-bg-danger'
                                        : 'text-bg-warning') }}">
                                {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">
                            No payments have been submitted for this invoice.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-bank fs-4 text-primary"></i>
                        <h5 class="fw-bold mb-0">Bank Transfer Details</h5>
                    </div>

                    <p class="small text-muted">
                        Transfer money to the school's account using the details provided for this invoice.
                    </p>

                    @if (filled($invoice->bank_name)
                        && filled($invoice->bank_account_name)
                        && filled($invoice->bank_account_number))
                        <div class="border rounded p-3 mb-3">
                            <p class="text-muted small mb-1">Bank Name</p>
                            <div class="fw-bold mb-3">{{ $invoice->bank_name }}</div>

                            <p class="text-muted small mb-1">Account Name</p>
                            <div class="fw-bold mb-3">{{ $invoice->bank_account_name }}</div>

                            <p class="text-muted small mb-1">Account Number</p>
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <span class="fw-bold fs-5">{{ $invoice->bank_account_number }}</span>
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        onclick="navigator.clipboard.writeText(@js($invoice->bank_account_number))">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </div>
                        </div>

                        @if ($invoice->bank_payment_instructions)
                            <h6 class="fw-semibold">Transfer Instructions</h6>
                            <p class="small text-muted mb-0">
                                {{ $invoice->bank_payment_instructions }}
                            </p>
                        @endif
                    @else
                        <div class="alert alert-warning mb-0">
                            The school has not configured bank details for this invoice yet.
                            Please contact the school before making a transfer.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold">
                        <i class="bi bi-shield-check text-success me-2"></i>
                        Payment Security
                    </h6>
                    <p class="small text-muted mb-0">
                        Your submission is recorded as pending until the school verifies the transfer.
                        Do not submit another payment for the same transfer.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if ($showPaymentForm)
        <div class="modal d-block" tabindex="-1" role="dialog"
             style="background: rgba(0,0,0,.55);"
             aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <form wire:submit="submitPayment">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title fw-bold">Submit Bank Transfer</h5>
                                <p class="text-muted small mb-0">
                                    Invoice {{ $invoice->invoice_number }}
                                </p>
                            </div>
                            <button type="button"
                                    class="btn-close"
                                    wire:click="$set('showPaymentForm', false)"
                                    aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4">
                            <div class="alert alert-info">
                                <div class="small">Outstanding balance</div>
                                <div class="fs-4 fw-bold">
                                    ₦{{ number_format(max(0, (float) $invoice->balance), 2) }}
                                </div>
                                <div class="small">
                                    Enter the amount you actually transferred. Partial payments are allowed.
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount Transferred (₦)</label>
                                    <input id="amount"
                                           type="number"
                                           step="0.01"
                                           min="0.01"
                                           max="{{ max(0, (float) $invoice->balance) }}"
                                           class="form-control @error('amount') is-invalid @enderror"
                                           wire:model="amount"
                                           required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="transferDate" class="form-label">Transfer Date</label>
                                    <input id="transferDate"
                                           type="date"
                                           max="{{ now()->toDateString() }}"
                                           class="form-control @error('transferDate') is-invalid @enderror"
                                           wire:model="transferDate"
                                           required>
                                    @error('transferDate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="providerReference" class="form-label">
                                        Bank Transfer Reference
                                    </label>
                                    <input id="providerReference"
                                           type="text"
                                           class="form-control @error('providerReference') is-invalid @enderror"
                                           wire:model="providerReference"
                                           maxlength="150"
                                           placeholder="Enter the transaction reference from your bank"
                                           required>
                                    @error('providerReference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="senderName" class="form-label">
                                        Name on Sending Bank Account
                                    </label>
                                    <input id="senderName"
                                           type="text"
                                           class="form-control @error('senderName') is-invalid @enderror"
                                           wire:model="senderName"
                                           maxlength="150"
                                           required>
                                    @error('senderName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label">
                                        Additional Information (Optional)
                                    </label>
                                    <textarea id="notes"
                                              rows="3"
                                              class="form-control @error('notes') is-invalid @enderror"
                                              wire:model="notes"
                                              maxlength="1000"
                                              placeholder="Any additional information to help the school identify your transfer"></textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="alert alert-warning mt-3 mb-0 small">
                                Only submit this form after you have made the bank transfer.
                                The invoice will remain outstanding until the school verifies your payment.
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-light"
                                    wire:click="$set('showPaymentForm', false)">
                                Cancel
                            </button>

                            <button type="submit"
                                    class="btn btn-primary"
                                    wire:loading.attr="disabled"
                                    wire:target="submitPayment">
                                <span wire:loading.remove wire:target="submitPayment">
                                    <i class="bi bi-send me-1"></i> Submit for Verification
                                </span>
                                <span wire:loading wire:target="submitPayment">
                                    Submitting...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
