
<div class="container-fluid py-4">

    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">Invoice Management</h3>
            <p class="text-muted mb-0">
                Manage school invoices, payments and outstanding balances.
            </p>
        </div>

        <a href="{{ route('treasury.fee-assignment') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Generate Invoices
        </a>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Total Invoices</div>
                    <h3 class="fw-bold mb-0">
                        {{ number_format($summary->total_count ?? 0) }}
                    </h3>
                    <div class="text-primary small mt-2">
                        All invoice records
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Total Amount Due</div>
                    <h3 class="fw-bold mb-0 text-primary">
                        {{ number_format((float) ($summary->total_due ?? 0), 2) }}
                    </h3>
                    <div class="text-muted small mt-2">Invoice totals</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Amount Collected</div>
                    <h3 class="fw-bold mb-0 text-success">
                        {{ number_format((float) ($summary->total_paid ?? 0), 2) }}
                    </h3>
                    <div class="text-muted small mt-2">Recorded payments</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Outstanding Balance</div>
                    <h3 class="fw-bold mb-0 text-danger">
                        {{ number_format((float) ($summary->total_balance ?? 0), 2) }}
                    </h3>
                    <div class="text-muted small mt-2">Unpaid balances</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Invoice details --}}
    @if($invoice)
        <div class="card border-0 shadow-sm mb-4" id="invoice-details">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Invoice Details</h5>

                <button type="button"
                        class="btn btn-sm btn-outline-secondary"
                        wire:click="closeInvoice">
                    Close
                </button>
            </div>

            <div class="card-body">

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="text-muted small">Invoice Number</div>
                        <div class="fw-bold">{{ $invoice->invoice_number }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">Student</div>
                        <div class="fw-bold">
                            {{ $invoice->student?->first_name }}
                            {{ $invoice->student?->middle_name }}
                            {{ $invoice->student?->last_name }}
                        </div>
                        <div class="small text-muted">
                            {{ $invoice->student?->admission_number }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">Academic Session</div>
                        <div>{{ $invoice->academicSession?->name ?? '—' }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">Term</div>
                        <div>{{ $invoice->term?->name ?? '—' }}</div>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end">Unit Amount</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoice->items as $item)
                                <tr>
                                    <td>{{ $item->description }}</td>
                                    <td class="text-end">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="text-end">
                                        {{ $invoice->currency }}
                                        {{ number_format((float) $item->unit_amount, 2) }}
                                    </td>
                                    <td class="text-end">
                                        {{ $invoice->currency }}
                                        {{ number_format((float) $item->total_amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        No invoice items found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end mb-4">
                    <div class="col-md-5 col-lg-4">
                        <div class="d-flex justify-content-between py-2">
                            <span>Subtotal</span>
                            <strong>
                                {{ $invoice->currency }}
                                {{ number_format((float) $invoice->subtotal, 2) }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between py-2">
                            <span>Discount</span>
                            <strong>
                                {{ $invoice->currency }}
                                {{ number_format((float) $invoice->discount, 2) }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between py-2 border-top">
                            <span>Total Due</span>
                            <strong>
                                {{ $invoice->currency }}
                                {{ number_format((float) $invoice->amount_due, 2) }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between py-2 text-success">
                            <span>Amount Paid</span>
                            <strong>
                                {{ $invoice->currency }}
                                {{ number_format((float) $invoice->amount_paid, 2) }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between py-2 border-top text-danger">
                            <span>Balance</span>
                            <strong>
                                {{ $invoice->currency }}
                                {{ number_format((float) $invoice->balance, 2) }}
                            </strong>
                        </div>
                    </div>
                </div>

                {{-- Bank details saved on this invoice --}}
                <div class="border rounded p-3 mb-3">
                    <h6 class="fw-bold mb-3">Payment Instructions</h6>

                    @if($invoice->bank_name
                        && $invoice->bank_account_name
                        && $invoice->bank_account_number)

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="small text-muted">Bank Name</div>
                                <div class="fw-semibold">{{ $invoice->bank_name }}</div>
                            </div>

                            <div class="col-md-4">
                                <div class="small text-muted">Account Name</div>
                                <div class="fw-semibold">{{ $invoice->bank_account_name }}</div>
                            </div>

                            <div class="col-md-4">
                                <div class="small text-muted">Account Number</div>
                                <div class="fw-semibold">{{ $invoice->bank_account_number }}</div>
                            </div>
                        </div>

                        @if($invoice->bank_payment_instructions)
                            <div class="mt-3 small">
                                {{ $invoice->bank_payment_instructions }}
                            </div>
                        @endif

                    @else
                        <div class="alert alert-warning mb-0">
                            Bank details were not saved on this invoice.
                            Check the school's bank settings.
                        </div>
                    @endif
                </div>

                <button type="button"
                        class="btn btn-dark"
                        onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>
                    Print Invoice
                </button>

            </div>
        </div>
    @endif

    {{-- Search and filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">

                <div class="col-12 col-lg-4">
                    <label class="form-label fw-semibold">Search Invoices</label>
                    <input type="search"
                           class="form-control"
                           placeholder="Invoice number, student or admission number..."
                           wire:model.live.debounce.400ms="search">
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label fw-semibold">Session</label>
                    <select class="form-select" wire:model.live="sessionId">
                        <option value="">All sessions</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}">
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label fw-semibold">Term</label>
                    <select class="form-select" wire:model.live="termId">
                        <option value="">All terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}">
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label fw-semibold">Status</label>
                    <select class="form-select" wire:model.live="status">
                        <option value="">All statuses</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="partial">Partial</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-2 d-flex align-items-end">
                    <button type="button"
                            class="btn btn-outline-secondary w-100"
                            wire:click="$set('search', '')">
                        Clear Search
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Invoice table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Invoices</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">Invoice</th>
                            <th>Student</th>
                            <th>Session / Term</th>
                            <th>Amount Due</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th class="text-end px-3">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($invoices as $row)
                            <tr wire:key="invoice-{{ $row->id }}">
                                <td class="px-3">
                                    <div class="fw-semibold">
                                        {{ $row->invoice_number }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $row->issued_at?->format('d M Y') ?? '—' }}
                                    </small>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $row->student?->first_name }}
                                        {{ $row->student?->last_name }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $row->student?->admission_number }}
                                    </small>
                                </td>

                                <td>
                                    <div>{{ $row->academicSession?->name ?? '—' }}</div>
                                    <small class="text-muted">
                                        {{ $row->term?->name ?? '—' }}
                                    </small>
                                </td>

                                <td>
                                    {{ $row->currency }}
                                    {{ number_format((float) $row->amount_due, 2) }}
                                </td>

                                <td class="text-success">
                                    {{ $row->currency }}
                                    {{ number_format((float) $row->amount_paid, 2) }}
                                </td>

                                <td class="fw-semibold text-danger">
                                    {{ $row->currency }}
                                    {{ number_format((float) $row->balance, 2) }}
                                </td>

                                <td>
                                    @php
                                        $statusClasses = [
                                            'paid' => 'success',
                                            'partial' => 'info',
                                            'unpaid' => 'warning',
                                            'overdue' => 'danger',
                                        ];

                                        $badgeClass = $statusClasses[$row->status] ?? 'secondary';
                                    @endphp

                                    <span class="badge text-bg-{{ $badgeClass }}">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                </td>

                                <td class="text-end px-3">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            wire:click="viewInvoice({{ $row->id }})">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted mb-2">
                                        No invoices found.
                                    </div>
                                    <a href="{{ route('treasury.fee-assignment') }}"
                                       class="btn btn-sm btn-primary">
                                        Generate Invoices
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($invoices->hasPages())
            <div class="card-footer bg-white">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

</div>
