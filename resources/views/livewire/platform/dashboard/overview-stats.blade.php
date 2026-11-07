
<div class="platform-overview">

    @php
        $cards = [
            [
                'label' => 'Total Schools',
                'value' => $stats['totalSchools'],
                'icon' => 'bi-buildings',
                'color' => 'primary',
                'description' => 'All registered schools',
                'footer' => 'Platform registrations',
            ],
            [
                'label' => 'Active Schools',
                'value' => $stats['activeSchools'],
                'icon' => 'bi-check-circle',
                'color' => 'success',
                'description' => 'Schools currently active',
                'footer' => 'Currently operational',
            ],
            [
                'label' => 'Total Users',
                'value' => $stats['totalUsers'],
                'icon' => 'bi-people',
                'color' => 'info',
                'description' => 'All registered accounts',
                'footer' => 'Across the platform',
            ],
            [
                'label' => 'Total Students',
                'value' => $stats['totalStudents'],
                'icon' => 'bi-mortarboard',
                'color' => 'warning',
                'description' => 'Registered students',
                'footer' => 'All schools combined',
            ],
            [
                'label' => 'Active Subscriptions',
                'value' => $stats['activeSubscriptions'],
                'icon' => 'bi-credit-card',
                'color' => 'purple',
                'description' => 'Currently active plans',
                'footer' => 'Subscription overview',
            ],
        ];
    @endphp

    {{-- STATISTICS CARDS --}}
    <div class="row g-4 mb-4">

        @foreach ($cards as $card)
            <div class="col-12 col-sm-6 col-xl">

                <div class="overview-stat-card h-100">

                    <div class="d-flex align-items-center justify-content-between mb-4">

                        <div class="stat-icon stat-icon-{{ $card['color'] }}">
                            <i class="bi {{ $card['icon'] }}"></i>
                        </div>

                        <span class="stat-indicator">
                            <i class="bi bi-graph-up-arrow"></i>
                        </span>

                    </div>

                    <div class="stat-label">
                        {{ $card['label'] }}
                    </div>

                    <div class="stat-value">
                        {{ number_format($card['value']) }}
                    </div>

                    <div class="stat-description">
                        {{ $card['description'] }}
                    </div>

                    <div class="stat-footer">
                        <span class="stat-footer-dot"></span>
                        {{ $card['footer'] }}
                    </div>

                </div>

            </div>
        @endforeach

    </div>


    {{-- PLATFORM GROWTH CHART --}}
    <div class="row g-4">

        <div class="col-12 col-xl-8">

            <div class="overview-panel h-100">

                <div class="panel-header">

                    <div>
                        <h5 class="panel-title">
                            Platform Growth
                        </h5>

                        <p class="panel-subtitle">
                            Registration and subscription activity over the last 6 months
                        </p>
                    </div>

                    <div class="panel-header-icon">
                        <i class="bi bi-bar-chart-line"></i>
                    </div>

                </div>

                <div class="chart-legend">

                    <span>
                        <i class="legend-dot legend-schools"></i>
                        Schools
                    </span>

                    <span>
                        <i class="legend-dot legend-users"></i>
                        Users
                    </span>

                    <span>
                        <i class="legend-dot legend-students"></i>
                        Students
                    </span>

                    <span>
                        <i class="legend-dot legend-subscriptions"></i>
                        Subscriptions
                    </span>

                </div>

                <div class="growth-chart-container" wire:ignore>
                    <canvas
                        id="platformGrowthChart"
                        aria-label="Platform growth over the last six months"
                        role="img"
                    ></canvas>
                </div>

            </div>

        </div>


        {{-- PLATFORM SUMMARY --}}
        <div class="col-12 col-xl-4">

            <div class="overview-panel h-100">

                <div class="panel-header">

                    <div>
                        <h5 class="panel-title">
                            Platform Summary
                        </h5>

                        <p class="panel-subtitle">
                            Current platform statistics
                        </p>
                    </div>

                    <div class="panel-header-icon">
                        <i class="bi bi-pie-chart"></i>
                    </div>

                </div>

                <div class="summary-list">

                    <div class="summary-item">
                        <div class="summary-icon summary-primary">
                            <i class="bi bi-buildings"></i>
                        </div>

                        <div class="summary-content">
                            <span>Total Schools</span>
                            <strong>{{ number_format($stats['totalSchools']) }}</strong>
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon summary-success">
                            <i class="bi bi-check-circle"></i>
                        </div>

                        <div class="summary-content">
                            <span>Active Schools</span>
                            <strong>{{ number_format($stats['activeSchools']) }}</strong>
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon summary-info">
                            <i class="bi bi-people"></i>
                        </div>

                        <div class="summary-content">
                            <span>Platform Users</span>
                            <strong>{{ number_format($stats['totalUsers']) }}</strong>
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon summary-warning">
                            <i class="bi bi-mortarboard"></i>
                        </div>

                        <div class="summary-content">
                            <span>Total Students</span>
                            <strong>{{ number_format($stats['totalStudents']) }}</strong>
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon summary-purple">
                            <i class="bi bi-credit-card"></i>
                        </div>

                        <div class="summary-content">
                            <span>Active Subscriptions</span>
                            <strong>{{ number_format($stats['activeSubscriptions']) }}</strong>
                        </div>
                    </div>

                </div>

                <div class="summary-note">
                    <i class="bi bi-shield-check"></i>
                    EduPilot platform overview
                </div>

            </div>

        </div>

    </div>
{{-- CHART.JS --}}
@assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
@endassets


@script
<script>
    const chartData = @js($chartData);

    const initializeGrowthChart = () => {
        const canvas = $wire.$el.querySelector('#platformGrowthChart');

        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        if (canvas._growthChart) {
            canvas._growthChart.destroy();
        }

        canvas._growthChart = new Chart(canvas, {
            type: 'bar',

            data: {
                labels: chartData.labels,

                datasets: [
                    {
                        label: 'Schools',
                        data: chartData.schools,
                        backgroundColor: '#6366f1',
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.7,
                        categoryPercentage: 0.7,
                    },
                    {
                        label: 'Users',
                        data: chartData.users,
                        backgroundColor: '#06b6d4',
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.7,
                        categoryPercentage: 0.7,
                    },
                    {
                        label: 'Students',
                        data: chartData.students,
                        backgroundColor: '#f59e0b',
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.7,
                        categoryPercentage: 0.7,
                    },
                    {
                        label: 'Subscriptions',
                        data: chartData.subscriptions,
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.7,
                        categoryPercentage: 0.7,
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    intersect: false,
                    mode: 'index',
                },

                plugins: {
                    legend: {
                        display: false,
                    },

                    tooltip: {
                        backgroundColor: '#111827',
                        titleColor: '#ffffff',
                        bodyColor: '#e5e7eb',
                        padding: 14,
                        cornerRadius: 10,
                        displayColors: true,
                    }
                },

                scales: {
                    x: {
                        grid: {
                            display: false,
                        },

                        border: {
                            display: false,
                        },

                        ticks: {
                            color: '#8490a5',
                            font: {
                                size: 11,
                            }
                        }
                    },

                    y: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0,
                            color: '#8490a5',
                            padding: 10,
                        },

                        grid: {
                            color: 'rgba(148, 163, 184, 0.13)',
                            drawBorder: false,
                        },

                        border: {
                            display: false,
                            dash: [4, 4],
                        }
                    }
                }
            }
        });
    };

    initializeGrowthChart();
</script>
@endscript


{{-- CUSTOM DESIGN --}}
<style>
    .platform-overview {
        --overview-text: #172033;
        --overview-muted: #8792a5;
        --overview-border: #edf0f5;
        color: var(--overview-text);
    }

    .overview-stat-card {
        background: #ffffff;
        border: 1px solid var(--overview-border);
        border-radius: 18px;
        padding: 23px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .035);
        transition: all .25s ease;
    }

    .overview-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(15, 23, 42, .075);
        border-color: #dce3ef;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .stat-icon-primary {
        color: #6366f1;
        background: #eef0ff;
    }

    .stat-icon-success {
        color: #10b981;
        background: #e7f9f1;
    }

    .stat-icon-info {
        color: #0891b2;
        background: #e5f8fc;
    }

    .stat-icon-warning {
        color: #d97706;
        background: #fff5df;
    }

    .stat-icon-purple {
        color: #9333ea;
        background: #f5eaff;
    }

    .stat-indicator {
        color: #10b981;
        background: #eafaf3;
        border-radius: 30px;
        padding: 7px 10px;
        font-size: 13px;
    }

    .stat-label {
        font-size: 13px;
        color: var(--overview-muted);
        font-weight: 600;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: clamp(25px, 2vw, 32px);
        font-weight: 800;
        letter-spacing: -1px;
        line-height: 1.2;
        margin-bottom: 8px;
        color: #182237;
    }

    .stat-description {
        color: #8792a5;
        font-size: 12px;
    }

    .stat-footer {
        border-top: 1px solid #f0f2f6;
        margin-top: 19px;
        padding-top: 14px;
        font-size: 11px;
        color: #8993a4;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .stat-footer-dot {
        width: 7px;
        height: 7px;
        background: #10b981;
        border-radius: 50%;
    }

    .overview-panel {
        background: #ffffff;
        border: 1px solid var(--overview-border);
        border-radius: 18px;
        padding: 25px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .035);
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 24px;
    }

    .panel-title {
        font-size: 16px;
        font-weight: 750;
        margin-bottom: 6px;
        color: #182237;
    }

    .panel-subtitle {
        color: #929bad;
        font-size: 12px;
        margin-bottom: 0;
    }

    .panel-header-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #f1f3ff;
        color: #6366f1;
        font-size: 19px;
    }

    .chart-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 17px;
        margin-bottom: 20px;
    }

    .chart-legend span {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #7e899d;
        font-size: 11px;
        font-weight: 600;
    }

    .legend-dot {
        width: 9px;
        height: 9px;
        border-radius: 3px;
    }

    .legend-schools {
        background: #6366f1;
    }

    .legend-users {
        background: #06b6d4;
    }

    .legend-students {
        background: #f59e0b;
    }

    .legend-subscriptions {
        background: #10b981;
    }

    .growth-chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .summary-list {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .summary-item {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 12px 0;
        border-bottom: 1px solid #f0f2f6;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }

    .summary-primary {
        color: #6366f1;
        background: #eef0ff;
    }

    .summary-success {
        color: #10b981;
        background: #e7f9f1;
    }

    .summary-info {
        color: #0891b2;
        background: #e5f8fc;
    }

    .summary-warning {
        color: #d97706;
        background: #fff5df;
    }

    .summary-purple {
        color: #9333ea;
        background: #f5eaff;
    }

    .summary-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .summary-content span {
        color: #8792a5;
        font-size: 12px;
    }

    .summary-content strong {
        font-size: 17px;
        color: #182237;
        font-weight: 750;
    }

    .summary-note {
        display: flex;
        align-items: center;
        gap: 9px;
        background: #f5f7ff;
        color: #6366a6;
        border-radius: 10px;
        padding: 13px;
        margin-top: 18px;
        font-size: 11px;
        font-weight: 600;
    }

    @media (max-width: 767px) {
        .overview-stat-card {
            padding: 19px;
        }

        .overview-panel {
            padding: 19px;
        }

        .growth-chart-container {
            height: 260px;
        }

        .chart-legend {
            gap: 12px;
        }
    }
</style>

</div>


