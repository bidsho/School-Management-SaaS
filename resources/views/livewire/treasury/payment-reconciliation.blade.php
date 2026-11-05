
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Payment Reconciliation</h3>
            <p class="text-secondary mb-0">Compare recorded bank transactions with school payments.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#transactionForm">
            <i class="bi bi-plus-circle me-1"></i> Record Transaction
        </button>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">Total Transactions</div>
                    <div class="fs-3 fw-bold">{{ number_format($stats->total ?? 0) }}</div>
                    <i class="bi bi-arrow-left-right text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">Matched</div>
                    <div class="fs-3 fw-bold text-success">{{ number_format($stats->matched ?? 0) }}</div>
                    <i class="bi bi-check-circle text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">Unmatched</div>
                    <div class="fs-3 fw-bold text-warning">{{ number_format($stats->unmatched ?? 0) }}</div>
                    <i class="bi bi-exclamation-circle text-warning"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">Unmatched Value</div>
                    <div class="fs-3 fw-bold">
                        ₦{{ number_format((float) ($stats->unmatched_amount ?? 0), 2) }}
                    </div>
                    <i class="bi bi-wallet2 text-secondary"></i>
                    <div class="small text-secondary mt-1">Display assumes NGN.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="collapse mb-4" id="transactionForm">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Record Bank Statement Transaction</h5>
            </div>
            <div class="card-body">
                <form wire:submit="saveTransaction">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Transaction Date</label>
                            <input type="date" class="form-control"
                                   wire:model="transactionDate" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control"
                                   wire:model="description"
                                   placeholder="e.g. Bank transfer received" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bank Reference</label>
                            <input type="text" class="form-control"
                                   wire:model="reference"
                                   placeholder="Statement transaction reference">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control"
                                   wire:model="amount" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Currency</label>
                            <select class="form-select" wire:model="currency" required>
                                <option value="NGN">NGN — Nigerian Naira</option>
                                <option value="USD">USD — US Dollar</option>
                                <option value="GBP">GBP — British Pound</option>
                                <option value="EUR">EUR — Euro</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes (optional)</label>
                            <textarea class="form-control" wire:model="notes" rows="2"
                                      placeholder="Statement details or internal notes"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"
                                    wire:loading.attr="disabled"
                                    wire:target="saveTransaction">
                                <span wire:loading.remove wire:target="saveTransaction">
                                    Save Transaction
                                </span>
                                <span wire:loading wire:target="saveTransaction">
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($selectedTransaction)
        <div class="card border-primary shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <strong>Match Bank Transaction</strong>
                <button type="button" class="btn btn-sm btn-light"
                        wire:click="cancelMatching">Cancel</button>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="small text-secondary">Transaction</div>
                        <strong>{{ $selectedTransaction->description }}</strong>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-secondary">Reference</div>
                        {{ $selectedTransaction->reference ?: 'Not provided' }}
                    </div>
                    <div class="col-md-4">
                        <div class="small text-secondary">Amount</div>
                        <strong>
                            {{ $selectedTransaction->currency }}
                            {{ number_format((float) $selectedTransaction->amount, 2) }}
                        </strong>
                    </div>
                </div>

                <label class="form-label">Search payment reference</label>
                <input type="text" class="form-control mb-3"
                       wire:model.live.debounce.300ms="paymentSearch"
                       placeholder="Enter school payment or provider reference">

                <label class="form-label">Select matching payment</label>
                <select class="form-select mb-3" wire:model="selectedPaymentId">
                    <option value="">Choose a matching payment...</option>
                    @foreach ($candidatePayments as $payment)
                        <option value="{{ $payment->id }}">
                            {{ $payment->reference ?: $payment->provider_reference ?: 'Payment #'.$payment->id }}
                            — {{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}
                            — {{ $payment->student?->name ?? 'Student #'.$payment->student_id }}
                            — {{ ucfirst($payment->status) }}
                        </option>
                    @endforeach
                </select>

                @if ($candidatePayments->isEmpty())
                    <div class="alert alert-warning mb-3">
                        No unlinked payments with the same amount and currency were found.
                        Check the bank entry and payment records before proceeding.
                    </div>
                @endif

                <button class="btn btn-success" wire:click="matchTransaction"
                        wire:loading.attr="disabled"
                        @disabled($candidatePayments->isEmpty())>
                    <i class="bi bi-link-45deg me-1"></i> Confirm Match
                </button>

                <p class="small text-secondary mt-3 mb-0">
                    Confirm only after checking the bank statement. This action does not
                    change the payment status or create a receipt.
                </p>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Bank Transactions</h5>
        </div>

        <div class="card-body border-bottom">
            <div class="row g-2">
                <div class="col-lg-4">
                    <input type="search" class="form-control" wire:model.live.debounce.300ms="search"
                           placeholder="Search description or reference...">
                </div>
                <div class="col-sm-6 col-lg-2">
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="all">All statuses</option>
                        <option value="unmatched">Unmatched</option>
                        <option value="matched">Matched</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <input type="date" class="form-control" wire:model.live="dateFrom"
                           aria-label="From date">
                </div>
                <div class="col-sm-6 col-lg-2">
                    <input type="date" class="form-control" wire:model.live="dateTo"
                           aria-label="To date">
                </div>
                <div class="col-sm-6 col-lg-2">
                    <button type="button" class="btn btn-outline-secondary w-100"
                            wire:click="clearFilters">Clear Filters</button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Transaction</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Matched Payment</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr wire:key="bank-transaction-{{ $transaction->id }}">
                            <td>{{ $transaction->transaction_date?->format('d M Y') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $transaction->description }}</div>
                                @if ($transaction->notes)
                                    <div class="small text-secondary">{{ $transaction->notes }}</div>
                                @endif
                            </td>
                            <td>{{ $transaction->reference ?: '—' }}</td>
                            <td class="fw-semibold">
                                {{ $transaction->currency }}
                                {{ number_format((float) $transaction->amount, 2) }}
                            </td>
                            <td>
                                @if ($transaction->status === 'matched')
                                    <span class="badge text-bg-success">Matched</span>
                                @else
                                    <span class="badge text-bg-warning">Unmatched</span>
                                @endif
                            </td>
                            <td>
                                @if ($transaction->payment)
                                    <div class="fw-semibold">
                                        {{ $transaction->payment->reference
                                            ?: $transaction->payment->provider_reference
                                            ?: 'Payment #'.$transaction->payment->id }}
                                    </div>
                                    <div class="small text-secondary">
                                        {{ $transaction->matched_at?->format('d M Y, h:i A') }}
                                    </div>
                                @else
                                    <span class="text-secondary">Not matched</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                @if ($transaction->status !== 'matched')
                                    <button class="btn btn-sm btn-outline-primary"
                                            wire:click="selectTransaction({{ $transaction->id }})">
                                        Match
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-danger"
                                            wire:click="undoMatch({{ $transaction->id }})"
                                            wire:confirm="Reverse this reconciliation match?">
                                        Undo Match
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox fs-2 text-secondary"></i>
                                <div class="fw-semibold mt-2">No transactions found</div>
                                <div class="text-secondary small">
                                    Record a bank transaction to start reconciling payments.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transactions->hasPages())
            <div class="card-footer bg-white">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
