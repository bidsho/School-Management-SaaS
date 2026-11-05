
<div class="container-fluid py-4">

    <style>
        .detail-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
        }

        .invoice-banner {
            background: linear-gradient(135deg, #172554, #2563eb);
            color: white;
            padding: 28px;
            border-radius: 16px;
        }

        .amount-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            height: 100%;
        }

        .detail-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 5px;
        }

        .bank-detail {
            padding: 14px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .bank-detail:last-child {
            border-bottom: 0;
        }

        .bank-value {
            overflow-wrap: anywhere;
            font-weight: 600;
        }

        @media print {
            @page {
                size: A4;
                margin: 12mm;
            }

            body * {
                visibility: hidden;
            }

            #invoice-detail-print,
            #invoice-detail-print * {
                visibility: visible;
            }

            #invoice-detail-print {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

            .no-print,
            nav,
            aside,
            header,
            footer {
                display: none !important;
            }

            .invoice-banner {
                background: #fff !important;
                color: #111827 !important;
                border: 1px solid #d1d5db;
            }

            .invoice-banner * {
                color: #111827 !important;
            }

            .detail-card,
            .amount-box {
                box-shadow: none !important;
                break-inside: avoid;
            }
        }
    </style>

    {{-- Navigation and actions --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 no-print">
        <div>
            <a
                href="{{ route('student.invoices.index') }}"
                class="text-decoration-none text-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to My Invoices
            </a>

            <h3 class="fw-bold mt-2 mb-1">Invoice Details</h3>

            <p class="text-secondary mb-0">
                Review your invoice and payment instructions.
            </p>
        </div>

        <button
            type="button"
            onclick="window.print()"
            class="btn btn-outline-primary"
        >
            <i class="bi bi-printer me-2"></i>
            Print Invoice
        </button>
    </div>

    <div id="invoice-detail-print">

        {{-- Invoice heading --}}
        <div class="invoice-banner mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">

                <div>
                    <div class="mb-2">
                        <i class="bi bi-receipt me-2"></i>
                        SCHOOL FEE INVOICE
                    </div>

                    <h2 class="fw-bold mb-2">
                        {{ $invoice->invoice_number }}
                    </h2>

                    <div>
                        Issued:
                        {{ $invoice->issued_at?->format('d M Y') ?? 'N/A' }}
                    </div>

                    <div class="mt-1">
                        Due date:
                        {{ $invoice->due_at?->format('d M Y') ?? 'Not specified' }}
                    </div>
                </div>

                <div>
                    <span class="badge {{ $statusClass }} fs-6 px-3 py-2">
                        {{ $status }}
                    </span>
                </div>

            </div>
        </div>

        {{-- Student and academic information --}}
        <div class="detail-card mb-4">
            <div class="p-4 border-bottom">
                <h5 class="fw-bold mb-0">Student Information</h5>
            </div>

            <div class="p-4">
                <div class="row g-4">

                    <div class="col-12 col-md-6">
                        <div class="detail-label">Student Name</div>

                        <div class="fw-semibold">
                            {{ $invoice->student?->first_name ?? '' }}
                            {{ $invoice->student?->middle_name ?? '' }}
                            {{ $invoice->student?->last_name ?? '' }}

                            @if (
                                ! $invoice->student?->first_name
                                && ! $invoice->student?->middle_name
                                && ! $invoice->student?->last_name
                            )
                                Student
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="detail-label">Admission Number</div>

                        <div class="fw-semibold">
                            {{ $invoice->student?->admission_number ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="detail-label">Academic Session</div>

                        <div class="fw-semibold">
                            {{ $invoice->academicSession?->name ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="detail-label">Term</div>

                        <div class="fw-semibold">
                            {{ $invoice->term?->name ?? 'N/A' }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Invoice items --}}
        <div class="detail-card mb-4">

            <div class="p-4 border-bottom">
                <h5 class="fw-bold mb-1">Fee Breakdown</h5>

                <p class="text-secondary small mb-0">
                    Individual charges included in this invoice.
                </p>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Description</th>
                            <th class="py-3 text-center">Quantity</th>
                            <th class="py-3 text-end">Unit Amount</th>
                            <th class="py-3 text-end pe-4">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($invoice->items as $item)

                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold">
                                        {{ $item->description }}
                                    </div>

                                    @if ($item->feeStructure)
                                        <div class="text-secondary small">
                                            {{ $item->feeStructure->name ?? '' }}
                                        </div>
                                    @endif
                                </td>

                                <td class="text-center py-3">
                                    {{ $item->quantity }}
                                </td>

                                <td class="text-end py-3">
                                    {{ $invoice->currency ?? 'NGN' }}
                                    {{ number_format((float) $item->unit_amount, 2) }}
                                </td>

                                <td class="text-end py-3 pe-4 fw-semibold">
                                    {{ $invoice->currency ?? 'NGN' }}
                                    {{ number_format((float) $item->total_amount, 2) }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">
                                    No invoice items were found.
                                </td>
                            </tr>

                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- Totals --}}
            <div class="p-4 border-top">
                <div class="row justify-content-end">
                    <div class="col-12 col-md-6 col-lg-5">

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">Subtotal</span>

                            <span class="fw-semibold">
                                {{ $invoice->currency ?? 'NGN' }}
                                {{ number_format((float) $invoice->subtotal, 2) }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">Discount</span>

                            <span class="fw-semibold">
                                {{ $invoice->currency ?? 'NGN' }}
                                {{ number_format((float) $invoice->discount, 2) }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">Total Amount Due</span>

                            <span class="fw-bold">
                                {{ $invoice->currency ?? 'NGN' }}
                                {{ number_format((float) $invoice->amount_due, 2) }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">Amount Paid</span>

                            <span class="fw-bold text-success">
                                {{ $invoice->currency ?? 'NGN' }}
                                {{ number_format((float) $invoice->amount_paid, 2) }}
                            </span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Outstanding Balance</span>

                            <span class="fs-4 fw-bold {{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $invoice->currency ?? 'NGN' }}
                                {{ number_format($balance, 2) }}
                            </span>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- Payment instructions --}}
        @if ($isCancelled)

            <div class="alert alert-secondary">
                <i class="bi bi-x-circle me-2"></i>
                This invoice has been cancelled. Please contact your school accounts office if you have questions.
            </div>

        @elseif ($balance <= 0)

            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>

                <strong>Invoice fully paid.</strong>
                There is no outstanding balance on this invoice.
            </div>

        @else

            <div class="detail-card mb-4">

                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-credit-card me-2 text-primary"></i>
                        Pay Your Invoice
                    </h5>

                    <p class="text-secondary small mb-0">
                        Pay the full outstanding balance shown below using your school's payment instructions.
                    </p>
                </div>

                <div class="p-4">

                    <div class="amount-box mb-4">
                        <div class="text-secondary small mb-2">
                            Full Outstanding Balance to Pay
                        </div>

                        <h2 class="fw-bold text-primary mb-2">
                            {{ $invoice->currency ?? 'NGN' }}
                            {{ number_format($balance, 2) }}
                        </h2>

                        <div class="text-secondary small">
                            Invoice: {{ $invoice->invoice_number }}
                        </div>
                    </div>

                    @if (
                        $invoice->bank_name
                        || $invoice->bank_account_name
                        || $invoice->bank_account_number
                        || $invoice->bank_payment_instructions
                    )

                        <h6 class="fw-bold mb-3">Bank Transfer Instructions</h6>

                        @if ($invoice->bank_name)
                            <div class="bank-detail">
                                <div class="detail-label">Bank Name</div>
                                <div class="bank-value">{{ $invoice->bank_name }}</div>
                            </div>
                        @endif

                        @if ($invoice->bank_account_name)
                            <div class="bank-detail">
                                <div class="detail-label">Account Name</div>
                                <div class="bank-value">{{ $invoice->bank_account_name }}</div>
                            </div>
                        @endif

                        @if ($invoice->bank_account_number)
                            <div class="bank-detail">
                                <div class="detail-label">Account Number</div>

                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <span class="bank-value" id="invoice-bank-account">
                                        {{ $invoice->bank_account_number }}
                                    </span>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary no-print"
                                        onclick="navigator.clipboard.writeText(document.getElementById('invoice-bank-account').innerText.trim()).then(() => alert('Account number copied.')).catch(() => alert('Please copy the account number manually.'))"
                                    >
                                        <i class="bi bi-copy me-1"></i>
                                        Copy Account
                                    </button>
                                </div>
                            </div>
                        @endif

                        @if ($invoice->bank_payment_instructions)
                            <div class="bank-detail">
                                <div class="detail-label">Payment Instructions</div>

                                <div class="bank-value" style="white-space: pre-line;">{{ $invoice->bank_payment_instructions }}</div>
                            </div>
                        @endif

                        <div class="alert alert-info mt-4 mb-0">
                            <i class="bi bi-info-circle me-2"></i>

                            Use your invoice number
                            <strong>{{ $invoice->invoice_number }}</strong>
                            as your payment reference if instructed by your school.
                            Keep your transfer receipt and contact the school accounts office to confirm your payment.
                        </div>

                    @else

                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>

                            Payment instructions have not been added to this invoice.
                            Please contact your school accounts office before making a payment.
                        </div>

                    @endif

                </div>
            </div>

        @endif

        <div class="text-center text-secondary small mt-4">
            Please retain this invoice for your records.
        </div>

    </div>

</div>
