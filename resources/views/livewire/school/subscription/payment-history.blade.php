
<div>
    <style>
        .payment-history {
            color: #172033;
        }

        .payment-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .payment-header h3 {
            font-size: 23px;
            font-weight: 750;
            margin-bottom: 5px;
            color: #172033;
        }

        .payment-header p {
            color: #7b8497;
            font-size: 13px;
            margin: 0;
        }

        .payment-stat-card {
            background: #fff;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: 20px;
            height: 100%;
            box-shadow: 0 3px 15px rgba(20, 35, 60, .035);
        }

        .payment-stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .payment-stat-icon {
            width: 43px;
            height: 43px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 19px;
        }

        .payment-stat-label {
            font-size: 12px;
            font-weight: 600;
            color: #8992a5;
            margin-bottom: 7px;
        }

        .payment-stat-value {
            font-size: 25px;
            font-weight: 750;
            color: #172033;
            letter-spacing: -.5px;
        }

        .payment-stat-description {
            font-size: 11px;
            color: #9aa2b1;
            margin-top: 6px;
        }

        .payment-panel {
            background: #fff;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            box-shadow: 0 3px 15px rgba(20, 35, 60, .035);
            overflow: hidden;
        }

        .payment-panel-header {
            padding: 20px;
            border-bottom: 1px solid #edf0f5;
        }

        .payment-panel-header h5 {
            font-size: 16px;
            font-weight: 750;
            margin-bottom: 5px;
        }

        .payment-panel-header p {
            font-size: 12px;
            color: #929bad;
            margin: 0;
        }

        .payment-filter {
            background: #f8f9fc;
            border: 1px solid #e9edf3;
            border-radius: 9px;
            min-height: 41px;
            font-size: 12px;
            box-shadow: none !important;
        }

        .payment-filter:focus {
            border-color: #536dfe;
            background: #fff;
        }

        .payment-table {
            margin: 0;
            white-space: nowrap;
        }

        .payment-table thead th {
            background: #fafbfe;
            color: #8b95a7;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            padding: 15px 18px;
            border-bottom: 1px solid #edf0f5;
        }

        .payment-table tbody td {
            padding: 17px 18px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f2f6;
            font-size: 12px;
        }

        .payment-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .payment-reference {
            font-size: 12px;
            font-weight: 700;
            color: #28344b;
        }

        .payment-subtext {
            display: block;
            color: #929bad;
            font-size: 10px;
            margin-top: 4px;
        }

        .payment-plan-name {
            font-size: 12px;
            font-weight: 650;
            color: #27344a;
        }

        .payment-amount {
            font-weight: 750;
            color: #172033;
            font-size: 13px;
        }

        .payment-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 30px;
            padding: 6px 10px;
            font-size: 10px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .payment-status.successful {
            color: #13834b;
            background: #e9f9f0;
        }

        .payment-status.pending {
            color: #b7791f;
            background: #fff7e5;
        }

        .payment-status.failed {
            color: #d14343;
            background: #fff0f0;
        }

        .payment-view-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e9edf3;
            background: #fff;
            color: #536dfe;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .payment-view-btn:hover {
            background: #536dfe;
            color: #fff;
            border-color: #536dfe;
        }

        .payment-empty {
            padding: 55px 20px;
            text-align: center;
        }

        .payment-empty-icon {
            width: 65px;
            height: 65px;
            border-radius: 18px;
            background: #f0f3ff;
            color: #536dfe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 27px;
            margin: 0 auto 15px;
        }

        .payment-empty h6 {
            font-weight: 750;
            color: #27344a;
        }

        .payment-empty p {
            color: #929bad;
            font-size: 12px;
        }

        .payment-details-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .55);
            z-index: 1055;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .payment-details-modal {
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            overflow-y: auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.18);
        }

        .payment-details-header {
            padding: 22px;
            border-bottom: 1px solid #edf0f5;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .payment-details-body {
            padding: 22px;
        }

        .payment-detail-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f2f6;
            font-size: 12px;
        }

        .payment-detail-row span:first-child {
            color: #8992a5;
        }

        .payment-detail-row strong {
            color: #27344a;
            text-align: right;
            overflow-wrap: anywhere;
        }

        .payment-details-total {
            background: #f6f8ff;
            border-radius: 12px;
            padding: 18px;
            text-align: center;
            margin-bottom: 18px;
        }

        .payment-details-total small {
            color: #8992a5;
            font-size: 11px;
        }

        .payment-details-total h3 {
            color: #283dce;
            font-weight: 800;
            margin: 7px 0 0;
        }

        @media(max-width: 767px) {
            .payment-header h3 {
                font-size: 20px;
            }

            .payment-stat-value {
                font-size: 21px;
            }

            .payment-panel-header {
                padding: 15px;
            }
        }
    </style>

    <div class="payment-history">

        {{-- PAGE HEADER --}}
        <div class="payment-header">
            <div>
                <h3>Subscription Payment History</h3>
                <p>
                    Manage and review your school's application subscription payments.
                </p>
            </div>

            <a href="{{ route('school.subscription.plans') }}"
               class="btn btn-primary btn-sm px-3 py-2">
                <i class="bi bi-arrow-up-circle me-1"></i>
                Manage Subscription
            </a>
        </div>

        {{-- STATISTICS --}}
        <div class="row g-3 mb-4">

            <div class="col-xl-3 col-md-6">
                <div class="payment-stat-card">
                    <div class="payment-stat-top">
                        <div class="payment-stat-icon"
                             style="background:#eaf8f0;color:#159957;">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <span class="badge bg-light text-success">Paid</span>
                    </div>

                    <div class="payment-stat-label">Total Amount Paid</div>

                    <div class="payment-stat-value">
                        ₦{{ number_format((float) $statistics['total_paid'], 2) }}
                    </div>

                    <div class="payment-stat-description">
                        Confirmed subscription payments
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="payment-stat-card">
                    <div class="payment-stat-top">
                        <div class="payment-stat-icon"
                             style="background:#eaf8f0;color:#159957;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>

                    <div class="payment-stat-label">Successful Payments</div>

                    <div class="payment-stat-value">
                        {{ number_format($statistics['successful']) }}
                    </div>

                    <div class="payment-stat-description">
                        Completed transactions
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="payment-stat-card">
                    <div class="payment-stat-top">
                        <div class="payment-stat-icon"
                             style="background:#fff5df;color:#d99a20;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>

                    <div class="payment-stat-label">Pending Payments</div>

                    <div class="payment-stat-value">
                        {{ number_format($statistics['pending']) }}
                    </div>

                    <div class="payment-stat-description">
                        Awaiting confirmation
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="payment-stat-card">
                    <div class="payment-stat-top">
                        <div class="payment-stat-icon"
                             style="background:#fff0f0;color:#dc4545;">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>

                    <div class="payment-stat-label">Failed Payments</div>

                    <div class="payment-stat-value">
                        {{ number_format($statistics['failed']) }}
                    </div>

                    <div class="payment-stat-description">
                        Unsuccessful transactions
                    </div>
                </div>
            </div>

        </div>

        {{-- PAYMENT HISTORY TABLE --}}
        <div class="payment-panel">

            <div class="payment-panel-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5>Transaction History</h5>
                        <p>
                            {{ number_format($statistics['total_transactions']) }}
                            total subscription transactions
                        </p>
                    </div>

                    <span class="badge bg-light text-dark px-3 py-2">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        Secure Payment Records
                    </span>
                </div>
            </div>

            {{-- FILTERS --}}
            <div class="p-3 border-bottom">
                <div class="row g-2 align-items-end">

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small fw-semibold">
                            Search Transactions
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>

                            <input type="text"
                                   class="form-control payment-filter border-start-0"
                                   placeholder="Reference or plan name..."
                                   wire:model.live.debounce.400ms="search">
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-semibold">
                            Payment Status
                        </label>

                        <select class="form-select payment-filter"
                                wire:model.live="status">
                            <option value="all">All Statuses</option>
                            <option value="successful">Successful</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label small fw-semibold">
                            From Date
                        </label>

                        <input type="date"
                               class="form-control payment-filter"
                               wire:model.live="dateFrom">
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label small fw-semibold">
                            To Date
                        </label>

                        <input type="date"
                               class="form-control payment-filter"
                               wire:model.live="dateTo">
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <button type="button"
                                class="btn btn-outline-secondary btn-sm w-100 py-2"
                                wire:click="resetFilters">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset Filters
                        </button>
                    </div>

                </div>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">

                <table class="table payment-table align-middle">

                    <thead>
                        <tr>
                            <th>Transaction</th>
                            <th>Subscription Plan</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($payments as $payment)

                            <tr wire:key="subscription-payment-{{ $payment->id }}">

                                <td>
                                    <span class="payment-reference">
                                        {{ $payment->reference }}
                                    </span>

                                    <span class="payment-subtext">
                                        ID: {{ $payment->id }}
                                    </span>
                                </td>

                                <td>
                                    <span class="payment-plan-name">
                                        {{ $payment->plan?->name ?? 'Subscription Plan' }}
                                    </span>

                                    <span class="payment-subtext">
                                        {{ ucfirst($payment->plan?->interval ?? 'N/A') }}
                                    </span>
                                </td>

                                <td>
                                    <span class="payment-amount">
                                        ₦{{ number_format((float) $payment->amount, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="text-dark fw-semibold">
                                        {{ ucfirst($payment->channel ?? 'Paystack') }}
                                    </span>
                                </td>

                                <td>
                                    <span class="payment-status {{ $payment->status }}">
                                        <i class="bi bi-circle-fill"
                                           style="font-size:6px;"></i>
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="text-dark fw-semibold">
                                        {{ $payment->paid_at
                                            ? $payment->paid_at->format('d M, Y')
                                            : $payment->created_at->format('d M, Y') }}
                                    </span>

                                    <span class="payment-subtext">
                                        {{ $payment->paid_at
                                            ? $payment->paid_at->format('h:i A')
                                            : $payment->created_at->format('h:i A') }}
                                    </span>
                                </td>

                                <td class="text-end">
                                    <button type="button"
                                            class="payment-view-btn"
                                            wire:click="viewPayment({{ $payment->id }})"
                                            title="View transaction details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7">
                                    <div class="payment-empty">

                                        <div class="payment-empty-icon">
                                            <i class="bi bi-receipt"></i>
                                        </div>

                                        <h6>No Payment Records Found</h6>

                                        <p>
                                            No subscription payments match your search or filters.
                                        </p>

                                        @if(
                                            filled($search) ||
                                            $status !== 'all' ||
                                            filled($dateFrom) ||
                                            filled($dateTo)
                                        )
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    wire:click="resetFilters">
                                                Clear Filters
                                            </button>
                                        @else
                                            <a href="{{ route('school.subscription.plans') }}"
                                               class="btn btn-sm btn-primary">
                                                View Subscription Plans
                                            </a>
                                        @endif

                                    </div>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            @if($payments->hasPages())
                <div class="p-3 border-top">
                    {{ $payments->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- PAYMENT DETAILS MODAL --}}
    @if($showDetails && $selectedPayment)

        <div class="payment-details-overlay"
             wire:click.self="closeDetails">

            <div class="payment-details-modal">

                <div class="payment-details-header">
                    <div>
                        <h5 class="fw-bold mb-1">Payment Details</h5>
                        <small class="text-muted">
                            Subscription transaction information
                        </small>
                    </div>

                    <button type="button"
                            class="btn-close"
                            wire:click="closeDetails"></button>
                </div>

                <div class="payment-details-body">

                    <div class="payment-details-total">
                        <small>Transaction Amount</small>

                        <h3>
                            ₦{{ number_format((float) $selectedPayment->amount, 2) }}
                        </h3>

                        <div class="mt-2">
                            <span class="payment-status {{ $selectedPayment->status }}">
                                {{ ucfirst($selectedPayment->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="payment-detail-row">
                        <span>Payment Reference</span>
                        <strong>{{ $selectedPayment->reference }}</strong>
                    </div>

                    <div class="payment-detail-row">
                        <span>Transaction ID</span>
                        <strong>
                            {{ $selectedPayment->transaction_id ?? 'Not available' }}
                        </strong>
                    </div>

                    <div class="payment-detail-row">
                        <span>School</span>
                        <strong>{{ $school->name }}</strong>
                    </div>

                    <div class="payment-detail-row">
                        <span>Subscription Plan</span>
                        <strong>
                            {{ $selectedPayment->plan?->name ?? 'Subscription Plan' }}
                        </strong>
                    </div>

                    <div class="payment-detail-row">
                        <span>Billing Interval</span>
                        <strong>
                            {{ ucfirst($selectedPayment->plan?->interval ?? 'N/A') }}
                        </strong>
                    </div>

                    <div class="payment-detail-row">
                        <span>Payment Provider</span>
                        <strong>Paystack</strong>
                    </div>

                    <div class="payment-detail-row">
                        <span>Payment Channel</span>
                        <strong>
                            {{ ucfirst($selectedPayment->channel ?? 'N/A') }}
                        </strong>
                    </div>

                    <div class="payment-detail-row">
                        <span>Currency</span>
                        <strong>{{ $selectedPayment->currency }}</strong>
                    </div>

                    <div class="payment-detail-row">
                        <span>Payment Date</span>
                        <strong>
                            {{ $selectedPayment->paid_at
                                ? $selectedPayment->paid_at->format('d M Y, h:i A')
                                : 'Not confirmed' }}
                        </strong>
                    </div>

                    <div class="payment-detail-row">
                        <span>Record Created</span>
                        <strong>
                            {{ $selectedPayment->created_at->format('d M Y, h:i A') }}
                        </strong>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button"
                                class="btn btn-light btn-sm px-4"
                                wire:click="closeDetails">
                            Close
                        </button>
                    </div>

                </div>

            </div>

        </div>

    @endif

</div>
