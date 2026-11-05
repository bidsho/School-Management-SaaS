
<div class="parent-dashboard container-fluid py-4">

    @php
        $invoiceChartData = [
            max(0, $totalInvoices - $outstandingCount),
            $outstandingCount,
        ];

        $childChartLabels = $children->map(function ($child) {
            return trim(
                ($child->first_name ?? '') . ' ' .
                ($child->last_name ?? '')
            );
        })->values();

        $childAttendanceData = $children->pluck('attendance_records_count')->values();
        $childExamData = $children->pluck('exam_scores_count')->values();

        $totalAttendanceRecords = $children->sum('attendance_records_count');
        $totalExamScores = $children->sum('exam_scores_count');
    @endphp

    <style>
        .parent-dashboard {
            --parent-primary: #5546cf;
            --parent-dark: #30277e;
            --parent-muted: #8490a4;
            --parent-border: #edf0f6;
            color: #202940;
        }

        .parent-dashboard .welcome-banner {
            position: relative;
            overflow: hidden;
            padding: 32px;
            border-radius: 24px;
            color: white;
            background:
                radial-gradient(circle at 90% 10%, rgba(255,255,255,.18), transparent 25%),
                radial-gradient(circle at 75% 120%, rgba(192,132,252,.35), transparent 35%),
                linear-gradient(120deg, #28216d, #5145cd 58%, #8061e8);
            box-shadow: 0 15px 40px rgba(81,69,205,.17);
        }

        .parent-dashboard .welcome-banner::before,
        .parent-dashboard .welcome-banner::after {
            content: "";
            position: absolute;
            border: 24px solid rgba(255,255,255,.07);
            border-radius: 50%;
            pointer-events: none;
        }

        .parent-dashboard .welcome-banner::before {
            width: 240px;
            height: 240px;
            right: -65px;
            top: -130px;
        }

        .parent-dashboard .welcome-banner::after {
            width: 150px;
            height: 150px;
            right: 170px;
            bottom: -115px;
        }

        .parent-dashboard .welcome-content {
            position: relative;
            z-index: 1;
        }

        .parent-dashboard .welcome-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 13px;
            border-radius: 30px;
            background: rgba(255,255,255,.15);
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .parent-dashboard .welcome-title {
            margin: 16px 0 8px;
            font-size: clamp(1.65rem, 3vw, 2.3rem);
            font-weight: 800;
            letter-spacing: -.8px;
        }

        .parent-dashboard .welcome-description {
            max-width: 580px;
            color: rgba(255,255,255,.8);
            font-size: .92rem;
            line-height: 1.7;
        }

        .parent-dashboard .dashboard-card {
            height: 100%;
            border: 1px solid var(--parent-border);
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 6px 25px rgba(20,35,60,.045);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .parent-dashboard .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 13px 30px rgba(20,35,60,.075);
        }

        .parent-dashboard .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            flex-shrink: 0;
            border-radius: 16px;
            font-size: 1.35rem;
        }

        .parent-dashboard .stat-value {
            color: #202940;
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1.25;
        }

        .parent-dashboard .muted-label {
            color: var(--parent-muted);
            font-size: .83rem;
        }

        .parent-dashboard .section-title {
            font-size: 1.08rem;
            font-weight: 800;
            letter-spacing: -.3px;
        }

        .parent-dashboard .section-description {
            color: var(--parent-muted);
            font-size: .83rem;
        }

        .parent-dashboard .chart-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            border-radius: 14px;
            background: #eeebff;
            color: var(--parent-primary);
            font-size: 1.2rem;
        }

        .parent-dashboard .chart-container {
            position: relative;
            width: 100%;
            height: 290px;
        }

        .parent-dashboard .chart-container.doughnut-container {
            height: 260px;
            max-width: 330px;
            margin: auto;
        }

        .parent-dashboard .chart-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px 20px;
            margin-top: 14px;
        }

        .parent-dashboard .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #667085;
            font-size: .78rem;
            font-weight: 600;
        }

        .parent-dashboard .legend-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
        }

        .parent-dashboard .child-card {
            padding: 19px;
            border: 1px solid var(--parent-border);
            border-radius: 17px;
            background: #fff;
            transition: all .2s ease;
        }

        .parent-dashboard .child-card:hover {
            border-color: #d9d4ff;
            background: #fcfbff;
            transform: translateY(-2px);
        }

        .parent-dashboard .child-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 55px;
            height: 55px;
            flex-shrink: 0;
            border-radius: 17px;
            background: linear-gradient(135deg, #e8e6ff, #dce8ff);
            color: #5145cd;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .parent-dashboard .child-metric {
            padding: 12px;
            border-radius: 12px;
            background: #f8f9fc;
        }

        .parent-dashboard .child-metric-value {
            font-size: 1.15rem;
            font-weight: 800;
        }

        .parent-dashboard .table > :not(caption) > * > * {
            padding: 15px 13px;
            vertical-align: middle;
        }

        .parent-dashboard .dashboard-table thead th {
            color: #778196;
            font-size: .76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            white-space: nowrap;
        }

        .parent-dashboard .dashboard-link {
            color: var(--parent-primary);
            font-size: .85rem;
            font-weight: 700;
            text-decoration: none;
        }

        .parent-dashboard .dashboard-link:hover {
            color: var(--parent-dark);
        }

        .parent-dashboard .empty-state {
            padding: 35px 15px;
            border: 1px dashed #e1e5ef;
            border-radius: 16px;
            background: #fbfcff;
            text-align: center;
            color: #94a3b8;
        }

        .parent-dashboard .empty-state i {
            display: block;
            margin-bottom: 12px;
            font-size: 36px;
        }

        .parent-dashboard .balance-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 9px 0;
            border-bottom: 1px solid #f0f1f5;
        }

        .parent-dashboard .balance-row:last-child {
            border-bottom: 0;
        }

        @media (max-width: 575.98px) {
            .parent-dashboard .welcome-banner {
                padding: 23px;
                border-radius: 19px;
            }

            .parent-dashboard .dashboard-card .card-body {
                padding: 17px !important;
            }

            .parent-dashboard .chart-container {
                height: 250px;
            }

            .parent-dashboard .chart-container.doughnut-container {
                height: 235px;
            }

            .parent-dashboard .dashboard-table {
                font-size: .83rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .parent-dashboard .dashboard-card,
            .parent-dashboard .child-card {
                transition: none;
            }

            .parent-dashboard .dashboard-card:hover,
            .parent-dashboard .child-card:hover {
                transform: none;
            }
        }
    </style>

    {{-- Welcome Banner --}}
    <div class="welcome-banner mb-4">
        <div class="welcome-content">

            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">

                <div>
                    <span class="welcome-label">
                        <i class="bi bi-stars"></i>
                        PARENT PORTAL
                    </span>

                    <h2 class="welcome-title">
                        Welcome back,
                        {{ $parent?->first_name ?? auth()->user()->name }}!
                    </h2>

                    <p class="welcome-description mb-0">
                        Stay connected with your children's education.
                        Monitor school activities, follow their progress
                        and keep track of outstanding school fees.
                    </p>
                </div>

                <div class="text-md-end">
                    <span class="badge rounded-pill bg-white text-primary px-3 py-2">
                        <i class="bi bi-shield-check me-1"></i>
                        Parent Account
                    </span>

                    <div class="small text-white-50 mt-3">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ now()->format('D, d M Y') }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Parent Profile Notice --}}
    @if (!$parent)
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            Your parent profile has not been linked to this account yet.
            Please contact your school administrator.
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 g-xl-4 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card card">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="muted-label fw-semibold">
                            My Children
                        </span>

                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>

                    <div class="stat-value mb-1">
                        {{ number_format($totalChildren) }}
                    </div>

                    <div class="muted-label">
                        Linked student profiles
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card card">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="muted-label fw-semibold">
                            Total Invoices
                        </span>

                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="bi bi-receipt"></i>
                        </div>
                    </div>

                    <div class="stat-value mb-1">
                        {{ number_format($totalInvoices) }}
                    </div>

                    <div class="muted-label">
                        For your linked children
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card card">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="muted-label fw-semibold">
                            Outstanding Invoices
                        </span>

                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>

                    <div class="stat-value mb-1">
                        {{ number_format($outstandingCount) }}
                    </div>

                    <div class="muted-label">
                        Invoices with a balance
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card card">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="muted-label fw-semibold">
                            Outstanding Balances
                        </span>

                        <div class="stat-icon bg-danger-subtle text-danger">
    ₦
</div>
                    </div>

                    @forelse ($outstandingByCurrency as $balance)
                        <div class="balance-row">
                            <span class="small text-secondary">
                                {{ $balance->currency }}
                            </span>

                            <span class="fw-bold">
                                {{ number_format((float) $balance->total, 2) }}
                            </span>
                        </div>
                    @empty
                        <div class="stat-value mb-1">
                            0.00
                        </div>

                        <div class="muted-label">
                            No outstanding balances
                        </div>
                    @endforelse

                </div>
            </div>
        </div>

    </div>

    {{-- Charts --}}
    <div class="row g-4 mb-4">

        {{-- Invoice Pie Chart --}}
        <div class="col-12 col-xl-5">
            <div class="dashboard-card card h-100">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="chart-icon">
                            <i class="bi bi-pie-chart"></i>
                        </div>

                        <div>
                            <h5 class="section-title mb-1">
                                Invoice Overview
                            </h5>

                            <p class="section-description mb-0">
                                Paid and outstanding invoices
                            </p>
                        </div>
                    </div>

                    @if ($totalInvoices > 0)

                        <div wire:ignore class="chart-container doughnut-container">
                            <canvas id="parentInvoiceChart"></canvas>
                        </div>

                        <div class="chart-legend">
                            <span class="legend-item">
                                <span class="legend-dot" style="background:#16a36a;"></span>
                                Paid invoices
                            </span>

                            <span class="legend-item">
                                <span class="legend-dot" style="background:#f5a623;"></span>
                                Outstanding invoices
                            </span>
                        </div>

                    @else

                        <div class="empty-state mt-3">
                            <i class="bi bi-pie-chart"></i>

                            <h6 class="fw-bold text-dark">
                                No invoices available
                            </h6>

                            <p class="small mb-0">
                                Invoice statistics will appear when your children have invoices.
                            </p>
                        </div>

                    @endif

                </div>
            </div>
        </div>

        {{-- Children Performance Bar Chart --}}
        <div class="col-12 col-xl-7">
            <div class="dashboard-card card h-100">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="chart-icon">
                            <i class="bi bi-bar-chart-line"></i>
                        </div>

                        <div>
                            <h5 class="section-title mb-1">
                                Children's Activity
                            </h5>

                            <p class="section-description mb-0">
                                Attendance and exam records by child
                            </p>
                        </div>
                    </div>

                    @if ($totalChildren > 0)

                        <div wire:ignore class="chart-container">
                            <canvas id="parentChildrenChart"></canvas>
                        </div>

                    @else

                        <div class="empty-state mt-3">
                            <i class="bi bi-bar-chart"></i>

                            <h6 class="fw-bold text-dark">
                                No children linked
                            </h6>

                            <p class="small mb-0">
                                Your children's activity will appear here once their profiles are linked.
                            </p>
                        </div>

                    @endif

                </div>
            </div>
        </div>

    </div>

    {{-- My Children --}}
    <div class="dashboard-card card mb-4">

        <div class="card-body p-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h5 class="section-title mb-1">
                        My Children
                    </h5>

                    <p class="section-description mb-0">
                        Students linked to your parent account
                    </p>
                </div>

                <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                    {{ $totalChildren }} {{ \Illuminate\Support\Str::plural('Child', $totalChildren) }}
                </span>
            </div>

            <div class="row g-3">

                @forelse ($children as $child)
                    <div class="col-12 col-lg-6"
                         wire:key="parent-child-{{ $child->id }}">

                        <div class="child-card h-100">

                            <div class="d-flex align-items-center gap-3 mb-3">

                                <div class="child-avatar">
                                    {{ strtoupper(substr($child->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($child->last_name ?? '', 0, 1)) }}
                                </div>

                                <div class="flex-grow-1 min-width-0">

                                    <h6 class="fw-bold mb-1">
                                        {{ trim(($child->first_name ?? '') . ' ' . ($child->middle_name ?? '') . ' ' . ($child->last_name ?? '')) }}
                                    </h6>

                                    <div class="small text-secondary">
                                        Admission No:
                                        {{ $child->admission_number ?: 'Not assigned' }}
                                    </div>

                                </div>

                                <span class="badge rounded-pill
                                    {{ ($child->status ?? '') === 'active'
                                        ? 'bg-success-subtle text-success'
                                        : 'bg-secondary-subtle text-secondary' }}">
                                    {{ ucfirst($child->status ?? 'Unknown') }}
                                </span>

                            </div>

                            <div class="row g-2">

                                <div class="col-4">
                                    <div class="child-metric">
                                        <div class="muted-label mb-1">
                                            Invoices
                                        </div>

                                        <div class="child-metric-value text-primary">
                                            {{ number_format($child->invoices_count) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-4">
                                    <div class="child-metric">
                                        <div class="muted-label mb-1">
                                            Attendance
                                        </div>

                                        <div class="child-metric-value text-success">
                                            {{ number_format($child->attendance_records_count) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-4">
                                    <div class="child-metric">
                                        <div class="muted-label mb-1">
                                            Exams
                                        </div>

                                        <div class="child-metric-value text-info">
                                            {{ number_format($child->exam_scores_count) }}
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                @empty

                    <div class="col-12">
                        <div class="empty-state">
                            <i class="bi bi-people"></i>

                            <h6 class="fw-bold text-dark">
                                No linked children yet
                            </h6>

                            <p class="small mb-0">
                                Contact your school administrator to link your children's student profiles.
                            </p>
                        </div>
                    </div>

                @endforelse

            </div>

        </div>
    </div>

    {{-- Recent Fee Invoices --}}
    <div class="dashboard-card card mb-4">

        <div class="card-body p-0">

            <div class="p-4">
                <h5 class="section-title mb-1">
                    Recent Fee Invoices
                </h5>

                <p class="section-description mb-0">
                    The latest invoices for your children
                </p>
            </div>

            <div class="table-responsive">

                <table class="table table-hover dashboard-table mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Invoice</th>
                            <th>Student</th>
                            <th>Amount Due</th>
                            <th>Balance</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($recentInvoices as $invoice)
                            <tr wire:key="parent-invoice-{{ $invoice->id }}">

                                <td class="ps-4">
                                    <span class="fw-bold">
                                        {{ $invoice->invoice_number }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $invoice->student?->first_name }}
                                        {{ $invoice->student?->last_name }}
                                    </div>
                                </td>

                                <td>
                                    {{ $invoice->currency }}
                                    {{ number_format((float) $invoice->amount_due, 2) }}
                                </td>

                                <td>
                                    <span class="fw-bold">
                                        {{ $invoice->currency }}
                                        {{ number_format((float) $invoice->balance, 2) }}
                                    </span>
                                </td>

                                <td class="text-secondary small">
                                    {{ $invoice->created_at?->format('d M Y') ?? '—' }}
                                </td>

                                <td>
                                    @if ((float) $invoice->balance <= 0)
                                        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Paid
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis px-3 py-2">
                                            <i class="bi bi-clock-history me-1"></i>
                                            Outstanding
                                        </span>
                                    @endif
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center py-5">

                                    <i class="bi bi-receipt text-secondary fs-2"></i>

                                    <h6 class="fw-bold mt-3 mb-1">
                                        No invoices available
                                    </h6>

                                    <p class="text-secondary small mb-0">
                                        Invoices for your linked children will appear here.
                                    </p>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

    {{-- Footer --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pb-3">
        <span class="small text-secondary">
            <i class="bi bi-shield-check me-1"></i>
            Your children's school information is private to your parent account.
        </span>

        <span class="small text-secondary">
            Updated {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>

    {{-- Chart.js --}}
    <script>
        (() => {
            const invoiceData = @js($invoiceChartData);
            const childLabels = @js($childChartLabels);
            const attendanceData = @js($childAttendanceData);
            const examData = @js($childExamData);

            function initializeParentCharts() {
                if (typeof Chart === 'undefined') {
                    return;
                }

                // Invoice Doughnut Chart
                const invoiceCanvas = document.getElementById('parentInvoiceChart');

                if (invoiceCanvas) {
                    const existing = Chart.getChart(invoiceCanvas);

                    if (existing) {
                        existing.destroy();
                    }

                    const total = invoiceData.reduce(
                        (sum, value) => sum + Number(value),
                        0
                    );

                    if (total > 0) {
                        new Chart(invoiceCanvas, {
                            type: 'doughnut',

                            data: {
                                labels: [
                                    'Paid Invoices',
                                    'Outstanding Invoices'
                                ],

                                datasets: [{
                                    data: invoiceData,

                                    backgroundColor: [
                                        '#16a36a',
                                        '#f5a623'
                                    ],

                                    borderColor: '#ffffff',
                                    borderWidth: 4,
                                    borderRadius: 5,
                                    spacing: 3,
                                    hoverOffset: 9
                                }]
                            },

                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '72%',

                                animation: {
                                    duration: 1100,
                                    easing: 'easeOutQuart'
                                },

                                plugins: {
                                    legend: {
                                        display: false
                                    },

                                    tooltip: {
                                        backgroundColor: '#202044',
                                        titleColor: '#ffffff',
                                        bodyColor: '#e8e8ff',
                                        padding: 13,
                                        cornerRadius: 10,

                                        callbacks: {
                                            label(context) {
                                                const value = context.parsed;
                                                const percentage = total
                                                    ? ((value / total) * 100).toFixed(1)
                                                    : 0;

                                                return ` ${context.label}: ${value} (${percentage}%)`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }

                // Children Activity Bar Chart
                const childrenCanvas = document.getElementById('parentChildrenChart');

                if (childrenCanvas) {
                    const existing = Chart.getChart(childrenCanvas);

                    if (existing) {
                        existing.destroy();
                    }

                    new Chart(childrenCanvas, {
                        type: 'bar',

                        data: {
                            labels: childLabels,

                            datasets: [
                                {
                                    label: 'Attendance Records',
                                    data: attendanceData,

                                    backgroundColor: 'rgba(81,69,205,.85)',
                                    borderColor: '#5145cd',
                                    borderWidth: 1,
                                    borderRadius: 7,
                                    borderSkipped: false
                                },
                                {
                                    label: 'Exam Scores',
                                    data: examData,

                                    backgroundColor: 'rgba(22,163,106,.85)',
                                    borderColor: '#16a36a',
                                    borderWidth: 1,
                                    borderRadius: 7,
                                    borderSkipped: false
                                }
                            ]
                        },

                        options: {
                            responsive: true,
                            maintainAspectRatio: false,

                            animation: {
                                duration: 1200,
                                easing: 'easeOutQuart'
                            },

                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },

                            plugins: {
                                legend: {
                                    position: 'bottom',

                                    labels: {
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        padding: 20,
                                        color: '#667085',
                                        font: {
                                            size: 11,
                                            weight: '600'
                                        }
                                    }
                                },

                                tooltip: {
                                    backgroundColor: '#202044',
                                    titleColor: '#ffffff',
                                    bodyColor: '#e8e8ff',
                                    padding: 13,
                                    cornerRadius: 10
                                }
                            },

                            scales: {
                                y: {
                                    beginAtZero: true,

                                    ticks: {
                                        precision: 0,
                                        color: '#8792a5'
                                    },

                                    grid: {
                                        color: 'rgba(148,163,184,.15)'
                                    }
                                },

                                x: {
                                    ticks: {
                                        color: '#8792a5',
                                        font: {
                                            size: 10,
                                            weight: '600'
                                        }
                                    },

                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }
            }

            function loadParentChartLibrary() {
                if (typeof Chart !== 'undefined') {
                    initializeParentCharts();
                    return;
                }

                let script = document.querySelector(
                    'script[data-parent-chart-library]'
                );

                if (script) {
                    script.addEventListener(
                        'load',
                        initializeParentCharts,
                        { once: true }
                    );

                    return;
                }

                script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js';
                script.dataset.parentChartLibrary = 'true';
                script.onload = initializeParentCharts;

                document.head.appendChild(script);
            }

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    loadParentChartLibrary,
                    { once: true }
                );
            } else {
                loadParentChartLibrary();
            }
        })();
    </script>

</div>
