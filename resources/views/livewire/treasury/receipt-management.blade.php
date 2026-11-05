
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">
        <div>
            <h3 class="fw-bold mb-1">Receipt Management</h3>
            <p class="text-secondary mb-0">
                View, search, and print receipts issued for your school.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('treasury.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Dashboard
            </a>

            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>
                Print Current Receipt
            </button>
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm no-print">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- SUMMARY CARDS --}}
    <div class="row g-3 mb-4 no-print">

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary small mb-2">Total Receipts</p>
                        <h3 class="fw-bold mb-0">
                            {{ number_format((int) ($counts->total_receipts ?? 0)) }}
                        </h3>
                    </div>
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary small mb-2">Issued This Month</p>
                        <h3 class="fw-bold mb-0">
                            {{ number_format((int) ($counts->receipts_this_month ?? 0)) }}
                        </h3>
                    </div>
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- RECEIPT LIST --}}
    <div class="card border-0 shadow-sm no-print">

        <div class="card-header bg-white border-0 p-3 p-md-4">
            <div class="d-flex flex-column gap-3">

                <div>
                    <h5 class="fw-bold mb-1">Issued Receipts</h5>
                    <p class="text-secondary small mb-0">
                        Search by receipt number, payment reference, or invoice number.
                    </p>
                </div>

                <div class="row g-2 align-items-end">

                    <div class="col-12 col-lg-5">
                        <label class="form-label small fw-semibold" for="receiptSearch">
                            Search
                        </label>
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-secondary"></i>
                            <input
                                id="receiptSearch"
                                type="search"
                                class="form-control ps-5"
                                placeholder="Receipt, payment, or invoice reference..."
                                wire:model.live.debounce.300ms="search"
                            >
                        </div>
                    </div>

                    <div class="col-6 col-lg-2">
                        <label class="form-label small fw-semibold" for="dateFrom">
                            Issued From
                        </label>
                        <input
                            id="dateFrom"
                            type="date"
                            class="form-control"
                            wire:model.live="dateFrom"
                        >
                    </div>

                    <div class="col-6 col-lg-2">
                        <label class="form-label small fw-semibold" for="dateTo">
                            Issued To
                        </label>
                        <input
                            id="dateTo"
                            type="date"
                            class="form-control"
                            wire:model.live="dateTo"
                        >
                    </div>

                    <div class="col-12 col-lg-3">
                        <button
                            type="button"
                            class="btn btn-outline-secondary w-100"
                            wire:click="clearFilters"
                        >
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Clear Filters
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-3">Receipt Number</th>
                            <th class="py-3">Student</th>
                            <th class="py-3">Invoice</th>
                            <th class="py-3">Payment Reference</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Issued Date</th>
                            <th class="text-end px-3 py-3">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($receipts as $receipt)
                            <tr wire:key="receipt-row-{{ $receipt->id }}">

                                <td class="px-3">
                                    <div class="fw-bold text-primary text-break">
                                        {{ $receipt->receipt_number }}
                                    </div>
                                    <small class="text-secondary">
                                        ID #{{ $receipt->id }}
                                    </small>
                                </td>

                                <td>
                                    @php
                                        $student = $receipt->payment?->student;
                                        $studentName = $student?->name
                                            ?? trim(($student?->first_name ?? '') . ' ' . ($student?->last_name ?? ''));
                                    @endphp

                                    <span class="fw-semibold">
                                        {{ $studentName !== '' ? $studentName : 'Student #' . ($receipt->payment?->student_id ?? 'N/A') }}
                                    </span>
                                </td>

                                <td>
                                    {{ $receipt->invoice?->invoice_number
                                        ?? 'Invoice #' . ($receipt->invoice_id ?? 'N/A') }}
                                </td>

                                <td>
                                    <span class="text-break">
                                        {{ $receipt->payment?->reference ?? 'N/A' }}
                                    </span>
                                    @if ($receipt->payment?->provider_reference)
                                        <div class="text-secondary small text-break">
                                            Provider: {{ $receipt->payment->provider_reference }}
                                        </div>
                                    @endif
                                </td>

                                <td class="fw-bold text-nowrap">
                                    {{ $receipt->payment?->currency ?? $receipt->invoice?->currency ?? '' }}
                                    {{ number_format((float) ($receipt->payment?->amount ?? 0), 2) }}
                                </td>

                                <td class="text-nowrap">
                                    {{ $receipt->issued_at?->format('d M Y') ?? 'N/A' }}
                                    <div class="text-secondary small">
                                        {{ $receipt->issued_at?->format('h:i A') }}
                                    </div>
                                </td>

                                <td class="text-end px-3">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        wire:click="viewReceipt({{ $receipt->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="viewReceipt({{ $receipt->id }})"
                                    >
                                        <i class="bi bi-eye me-1"></i>
                                        View / Print
                                    </button>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-receipt text-secondary"
                                       style="font-size: 2.5rem;"></i>
                                    <h6 class="fw-bold mt-3">No receipts found</h6>
                                    <p class="text-secondary small mb-0">
                                        Receipts will appear here after payments are verified.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        @if ($receipts->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>

    {{-- RECEIPT DETAILS AND PRINTABLE RECEIPT --}}
    @if ($selectedReceipt)
        <div class="receipt-details mt-4" wire:key="receipt-detail-{{ $selectedReceipt->id }}">

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 no-print">
                <div>
                    <h5 class="fw-bold mb-1">Receipt Preview</h5>
                    <p class="text-secondary small mb-0">
                        Review the receipt before printing.
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            wire:click="closeReceipt">
                        Close Preview
                    </button>

                    <button type="button"
                            class="btn btn-primary"
                            onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>
                        Print Receipt
                    </button>
                </div>
            </div>

            <div class="receipt-print-area bg-white border rounded-3 shadow-sm p-4 p-md-5">

                {{-- PRINT HEADER --}}
                <div class="text-center border-bottom pb-4 mb-4">
                    <div class="mb-3">
                        <i class="bi bi-mortarboard-fill text-primary"
                           style="font-size: 2.5rem;"></i>
                    </div>

                    <h3 class="fw-bold mb-1">
                        {{ config('app.name', 'School Management System') }}
                    </h3>

                    <p class="text-secondary mb-2">OFFICIAL PAYMENT RECEIPT</p>

                    <div class="d-inline-block border rounded-pill px-3 py-2">
                        <span class="text-secondary small">Receipt No.</span>
                        <strong class="ms-1">{{ $selectedReceipt->receipt_number }}</strong>
                    </div>
                </div>

                @php
                    $payment = $selectedReceipt->payment;
                    $invoice = $selectedReceipt->invoice;
                    $student = $payment?->student;

                    $previewStudentName = $student?->name
                        ?? trim(($student?->first_name ?? '') . ' ' . ($student?->last_name ?? ''));

                    $receiptAmount = (float) ($payment?->amount ?? 0);
                    $receiptCurrency = $payment?->currency ?? $invoice?->currency ?? '';
                @endphp

                {{-- RECEIPT INFORMATION --}}
                <div class="row g-4 mb-4">

                    <div class="col-12 col-md-6">
                        <h6 class="text-secondary text-uppercase small fw-bold mb-3">
                            Received From
                        </h6>

                        <div class="fw-bold fs-5">
                            {{ $previewStudentName !== ''
                                ? $previewStudentName
                                : 'Student #' . ($payment?->student_id ?? 'N/A') }}
                        </div>

                        <div class="text-secondary mt-1">
                            Student ID: {{ $payment?->student_id ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <h6 class="text-secondary text-uppercase small fw-bold mb-3">
                            Receipt Information
                        </h6>

                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <span class="text-secondary">Receipt Number</span>
                            <strong class="text-end">{{ $selectedReceipt->receipt_number }}</strong>
                        </div>

                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <span class="text-secondary">Date Issued</span>
                            <strong class="text-end">
                                {{ $selectedReceipt->issued_at?->format('d M Y, h:i A') ?? 'N/A' }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between gap-3">
                            <span class="text-secondary">Invoice Number</span>
                            <strong class="text-end">
                                {{ $invoice?->invoice_number ?? 'N/A' }}
                            </strong>
                        </div>
                    </div>

                </div>

                {{-- PAYMENT SUMMARY --}}
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Payment Reference</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    School fee payment
                                    @if ($invoice?->invoice_number)
                                        <div class="text-secondary small">
                                            Invoice: {{ $invoice->invoice_number }}
                                        </div>
                                    @endif
                                </td>

                                <td class="text-break">
                                    {{ $payment?->reference ?? 'N/A' }}
                                </td>

                                <td class="text-end fw-bold text-nowrap">
                                    {{ $receiptCurrency }}
                                    {{ number_format($receiptAmount, 2) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Total Received</th>
                                <th class="text-end fs-5 text-nowrap">
                                    {{ $receiptCurrency }}
                                    {{ number_format($receiptAmount, 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- PAYMENT STATUS --}}
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="border rounded-3 p-3">
                            <div class="text-secondary small mb-1">Payment Status</div>
                            <strong class="text-success">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                {{ ucfirst($payment?->status ?? 'Unknown') }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="border rounded-3 p-3">
                            <div class="text-secondary small mb-1">Payment Date</div>
                            <strong>
                                {{ $payment?->paid_at?->format('d M Y, h:i A')
                                    ?? $payment?->created_at?->format('d M Y, h:i A')
                                    ?? 'N/A' }}
                            </strong>
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="border-top pt-4 mt-4 text-center">
                    <h6 class="fw-bold mb-2">Thank you for your payment.</h6>
                    <p class="text-secondary small mb-3">
                        Please keep this receipt for your records.
                    </p>
                    <div class="text-secondary small">
                        This receipt was generated by {{ config('app.name', 'School Management System') }}.
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- PRINT STYLES --}}
    <style>
        @media print {
            @page {
                size: A4;
                margin: 12mm;
            }

            body {
                background: #fff !important;
                color: #000 !important;
                font-size: 12px;
            }

            body * {
                visibility: hidden !important;
            }

            .receipt-print-area,
            .receipt-print-area * {
                visibility: visible !important;
            }

            .receipt-print-area {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                color: #000 !important;
                background: #fff !important;
            }

            .no-print,
            .no-print * {
                display: none !important;
            }

            .receipt-print-area .text-secondary {
                color: #555 !important;
            }

            .receipt-print-area .table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .receipt-print-area th,
            .receipt-print-area td {
                border: 1px solid #ccc !important;
                padding: 8px !important;
            }

            .receipt-print-area .bg-primary,
            .receipt-print-area .bg-success,
            .receipt-print-area .table-light {
                background: #f5f5f5 !important;
                color: #000 !important;
            }

            .receipt-details {
                margin: 0 !important;
            }

            a[href]::after {
                content: none !important;
            }
        }
    </style>

</div>
