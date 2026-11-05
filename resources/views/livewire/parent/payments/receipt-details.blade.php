
<div class="container-fluid py-4">

    {{-- Page heading --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Payment Receipt</h3>
            <p class="text-muted mb-0">
                View and print your child's verified school payment receipt.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('parent.receipts.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Receipts
            </a>

            <button type="button"
                    onclick="window.print()"
                    class="btn btn-primary">
                <i class="bi bi-printer me-1"></i>
                Print Receipt
            </button>
        </div>
    </div>

    {{-- Receipt document --}}
    <div class="card border-0 shadow-sm receipt-card">
        <div class="card-body p-4 p-lg-5">

            {{-- School header --}}
            <div class="row align-items-center gy-3 mb-4">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        @if ($receipt->school?->logo)
                            <img
                                src="{{ asset('storage/' . $receipt->school->logo) }}"
                                alt="School Logo"
                                class="receipt-logo"
                            >
                        @else
                            <div class="receipt-logo-placeholder">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                        @endif

                        <div>
                            <h4 class="fw-bold mb-1">
                                {{ $receipt->school?->name ?? 'School' }}
                            </h4>

                            @if ($receipt->school?->address)
                                <p class="text-muted small mb-1">
                                    {{ $receipt->school->address }}
                                </p>
                            @endif

                            @if ($receipt->school?->email)
                                <p class="text-muted small mb-0">
                                    {{ $receipt->school->email }}
                                </p>
                            @endif

                            @if ($receipt->school?->phone)
                                <p class="text-muted small mb-0">
                                    {{ $receipt->school->phone }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4 text-md-end">
                    <span class="badge text-bg-success px-3 py-2">
                        <i class="bi bi-check-circle me-1"></i>
                        PAYMENT VERIFIED
                    </span>

                    <h5 class="fw-bold mt-3 mb-1">OFFICIAL RECEIPT</h5>

                    <div class="text-muted small">
                        Receipt No:
                        <span class="fw-semibold text-dark">
                            {{ $receipt->receipt_number }}
                        </span>
                    </div>

                    <div class="text-muted small">
                        Date:
                        {{ $receipt->issued_at
                            ? \Illuminate\Support\Carbon::parse($receipt->issued_at)->format('d M Y, h:i A')
                            : 'N/A' }}
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Student and invoice information --}}
            <div class="row g-4 mb-4">

                <div class="col-md-6">
                    <div class="receipt-section h-100">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">
                            Student Information
                        </h6>

                        <div class="mb-2">
                            <span class="text-muted small d-block">
                                Student Name
                            </span>

                            <span class="fw-semibold">
                                @if ($receipt->payment?->student)
                                    {{ trim(
                                        ($receipt->payment->student->first_name ?? '') . ' ' .
                                        ($receipt->payment->student->last_name ?? '')
                                    ) ?: 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>

                        <div class="mb-2">
                            <span class="text-muted small d-block">
                                Student ID
                            </span>

                            <span class="fw-semibold">
                                {{ $receipt->payment?->student_id ?? 'N/A' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-muted small d-block">
                                School
                            </span>

                            <span class="fw-semibold">
                                {{ $receipt->school?->name ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="receipt-section h-100">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">
                            Payment Information
                        </h6>

                        <div class="mb-2">
                            <span class="text-muted small d-block">
                                Invoice Number
                            </span>

                            <span class="fw-semibold">
                                {{ $receipt->invoice?->invoice_number ?? 'N/A' }}
                            </span>
                        </div>

                        <div class="mb-2">
                            <span class="text-muted small d-block">
                                Payment Reference
                            </span>

                            <span class="fw-semibold">
                                {{ $receipt->payment?->reference ?? 'N/A' }}
                            </span>
                        </div>

                        <div class="mb-2">
                            <span class="text-muted small d-block">
                                Payment Method
                            </span>

                            <span class="fw-semibold">
                                {{ str($receipt->payment?->provider ?? 'N/A')
                                    ->replace('_', ' ')
                                    ->title() }}
                            </span>
                        </div>

                        <div>
                            <span class="text-muted small d-block">
                                Payment Date
                            </span>

                            <span class="fw-semibold">
                                {{ $receipt->payment?->paid_at
                                    ? \Illuminate\Support\Carbon::parse($receipt->payment->paid_at)->format('d M Y, h:i A')
                                    : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Amount summary --}}
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    School Fee Payment
                                </div>

                                <div class="text-muted small">
                                    Invoice:
                                    {{ $receipt->invoice?->invoice_number ?? 'N/A' }}
                                </div>
                            </td>

                            <td class="text-end fw-semibold">
                                {{ $receipt->payment?->currency ?? $receipt->invoice?->currency ?? 'NGN' }}
                                {{ number_format((float) ($receipt->payment?->amount ?? 0), 2) }}
                            </td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr class="table-light">
                            <th class="text-end">
                                Total Amount Paid
                            </th>

                            <th class="text-end text-success fs-5">
                                {{ $receipt->payment?->currency ?? $receipt->invoice?->currency ?? 'NGN' }}
                                {{ number_format((float) ($receipt->payment?->amount ?? 0), 2) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Payment confirmation --}}
            <div class="alert alert-success d-flex align-items-start gap-3">
                <i class="bi bi-patch-check-fill fs-3"></i>

                <div>
                    <div class="fw-bold">Payment Successfully Verified</div>

                    <div class="small">
                        This receipt confirms that the school has verified
                        the payment recorded above.
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center pt-4 mt-4 border-top">
                <h6 class="fw-bold mb-1">
                    Thank you for your payment.
                </h6>

                <p class="text-muted small mb-1">
                    Please keep this receipt for your records.
                </p>

                <p class="text-muted small mb-0">
                    Receipt: {{ $receipt->receipt_number }}
                </p>
            </div>

        </div>
    </div>
    <style>
    .receipt-card {
        border-radius: 14px;
        overflow: hidden;
    }

    .receipt-logo,
    .receipt-logo-placeholder {
        width: 76px;
        height: 76px;
        object-fit: contain;
        border-radius: 12px;
        flex-shrink: 0;
    }

    .receipt-logo-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #4338ca;
        font-size: 2rem;
    }

    .receipt-section {
        padding: 20px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
    }

    @media print {
        body {
            background: #fff !important;
        }

        body * {
            visibility: hidden;
        }

        .receipt-card,
        .receipt-card * {
            visibility: visible;
        }

        .receipt-card {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            border: none !important;
            box-shadow: none !important;
        }

        .receipt-card .card-body {
            padding: 0 !important;
        }

        .btn,
        a.btn,
        button {
            display: none !important;
        }

        .receipt-section {
            break-inside: avoid;
        }

        @page {
            size: A4;
            margin: 12mm;
        }
    }
</style>
</div>


