<div class="student-dashboard">

    @php
        $fullName = trim(collect([
            $student->first_name,
            $student->middle_name,
            $student->last_name,
        ])->filter()->implode(' '));

        $initials = collect([
            $student->first_name,
            $student->last_name,
        ])->filter()
            ->map(fn ($name) => strtoupper(substr($name, 0, 1)))
            ->implode('');

        $status = $student->status ?? 'active';

        $statusColors = [
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'danger',
            'graduated' => 'primary',
        ];

        $statusColor = $statusColors[strtolower($status)] ?? 'secondary';
    @endphp

    <style>
        .student-dashboard {
            --primary: #5b50e8;
            --primary-dark: #3930a8;
            --secondary: #8b5cf6;
            --muted: #7c879b;
            color: #1e293b;
        }

        /* Welcome banner */
        .student-dashboard .welcome-banner {
            position: relative;
            overflow: hidden;
            min-height: 230px;
            padding: clamp(24px, 4vw, 42px);
            border-radius: 25px;
            color: #fff;
            background:
                radial-gradient(circle at 86% 18%, rgba(255,255,255,.18), transparent 22%),
                radial-gradient(circle at 72% 110%, rgba(168,139,255,.35), transparent 28%),
                linear-gradient(118deg, #24206f 0%, #5145cd 52%, #8b5cf6 100%);
            box-shadow: 0 18px 42px rgba(67,56,202,.20);
        }

        .student-dashboard .welcome-banner::before,
        .student-dashboard .welcome-banner::after {
            content: "";
            position: absolute;
            border: 30px solid rgba(255,255,255,.08);
            border-radius: 50%;
            pointer-events: none;
        }

        .student-dashboard .welcome-banner::before {
            width: 280px;
            height: 280px;
            right: -70px;
            top: -140px;
        }

        .student-dashboard .welcome-banner::after {
            width: 180px;
            height: 180px;
            right: 120px;
            bottom: -125px;
        }

        .student-dashboard .welcome-content {
            position: relative;
            z-index: 1;
        }

        .student-dashboard .welcome-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 13px;
            border-radius: 30px;
            background: rgba(255,255,255,.15);
            color: #fff;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .4px;
        }

        .student-dashboard .welcome-title {
            margin: 18px 0 8px;
            font-size: clamp(1.8rem, 3.2vw, 2.55rem);
            font-weight: 800;
            letter-spacing: -1.1px;
        }

        .student-dashboard .welcome-description {
            max-width: 560px;
            color: rgba(255,255,255,.82);
            font-size: .96rem;
            line-height: 1.7;
        }

        .student-dashboard .student-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 1.25rem;
            font-weight: 800;
        }

        .student-dashboard .welcome-avatar {
            width: 78px;
            height: 78px;
            border-radius: 24px;
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.25);
            color: #fff;
            font-size: 1.65rem;
        }

        .student-dashboard .admission-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 15px;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 13px;
            background: rgba(255,255,255,.12);
            color: #fff;
            font-size: .85rem;
        }

        /* Section headings */
        .student-dashboard .section-heading {
            font-size: 1.18rem;
            font-weight: 800;
            letter-spacing: -.4px;
        }

        .student-dashboard .section-subtitle {
            color: var(--muted);
            font-size: .88rem;
            line-height: 1.55;
        }

        /* Dashboard cards */
        .student-dashboard .dashboard-card {
            height: 100%;
            border: 1px solid rgba(226,232,240,.9);
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 8px 28px rgba(30,41,59,.045);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .student-dashboard .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 36px rgba(30,41,59,.085);
        }

        .student-dashboard .stat-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 17px;
            font-size: 1.4rem;
        }

        .student-dashboard .icon-purple {
            background: #eeebff;
            color: #6554d9;
        }

        .student-dashboard .icon-green {
            background: #e4f8ee;
            color: #15945a;
        }

        .student-dashboard .icon-orange {
            background: #fff0df;
            color: #db790c;
        }

        .student-dashboard .icon-blue {
            background: #e2f2ff;
            color: #1877c9;
        }

        .student-dashboard .icon-red {
            background: #fff0f0;
            color: #dc3545;
        }

        .student-dashboard .stat-label {
            color: var(--muted);
            font-size: .84rem;
            font-weight: 600;
        }

        .student-dashboard .stat-value {
            margin: 13px 0 5px;
            color: #172033;
            font-size: clamp(1.8rem, 2.2vw, 2.15rem);
            font-weight: 800;
            letter-spacing: -1.2px;
            line-height: 1.2;
        }

        .student-dashboard .stat-caption {
            color: #94a3b8;
            font-size: .78rem;
        }

        /* Chart */
        .student-dashboard .chart-card {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 100% 0%, rgba(99,102,241,.055), transparent 34%),
                #fff;
        }

        .student-dashboard .chart-container {
            position: relative;
            width: 100%;
            height: 340px;
            padding: 8px 2px 0;
        }

        .student-dashboard .chart-heading-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            color: #5145cd;
            background: linear-gradient(135deg, #eeecff, #f4efff);
            font-size: 1.2rem;
        }

        .student-dashboard .chart-legend-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid #ecebff;
            border-radius: 999px;
            background: #faf9ff;
            color: #5b50c8;
            font-size: .76rem;
            font-weight: 700;
        }

        .student-dashboard .chart-legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #6759e8;
            box-shadow: 0 0 0 4px rgba(103,89,232,.12);
        }

        .student-dashboard .performance-summary-card {
            background:
                radial-gradient(circle at 100% 0%, rgba(139,92,246,.09), transparent 35%),
                #fff;
        }

        .student-dashboard .performance-score {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 17px 0;
            border-bottom: 1px solid #f0f1f7;
        }

        .student-dashboard .performance-score:last-of-type {
            border-bottom: 0;
        }

        .student-dashboard .performance-score-value {
            color: #202744;
            font-size: 1.65rem;
            font-weight: 850;
            letter-spacing: -1px;
        }

        /* Profile */
        .student-dashboard .profile-card {
            overflow: hidden;
        }

        .student-dashboard .profile-top {
            padding: 24px;
            background: linear-gradient(120deg, #f8faff, #f3f0ff);
            border-bottom: 1px solid #edf0f7;
        }

        .student-dashboard .profile-detail {
            padding: 16px 0;
            border-bottom: 1px solid #f0f2f6;
        }

        .student-dashboard .profile-detail:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .student-dashboard .detail-label {
            margin-bottom: 5px;
            color: #8792a5;
            font-size: .76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .65px;
        }

        .student-dashboard .detail-value {
            overflow-wrap: anywhere;
            color: #263247;
            font-size: .91rem;
            font-weight: 650;
        }

        .student-dashboard .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 11px;
            border-radius: 30px;
            font-size: .76rem;
            font-weight: 700;
        }

        .student-dashboard .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Enrollment */
        .student-dashboard .enrollment-box {
            padding: 20px;
            border: 1px solid #e9e6ff;
            border-radius: 16px;
            background:
                radial-gradient(circle at 100% 0%, rgba(139,92,246,.10), transparent 38%),
                linear-gradient(135deg, #f8f7ff, #f5f9ff);
        }

        .student-dashboard .enrollment-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .student-dashboard .enrollment-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 11px;
            background: #fff;
            color: #5145cd;
        }

        /* Payments */
        .student-dashboard .payment-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f2f6;
            transition: background .18s ease;
        }

        .student-dashboard .payment-row:last-child {
            border-bottom: 0;
        }

        .student-dashboard .payment-row:hover {
            background: #fbfcff;
        }

        .student-dashboard .payment-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            border-radius: 12px;
            background: #e4f8ee;
            color: #15945a;
            font-size: 17px;
        }

        /* Portal shortcuts */
        .student-dashboard .portal-item {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 82px;
            height: 100%;
            padding: 16px;
            border: 1px solid #e9edf5;
            border-radius: 16px;
            background: #fff;
            color: inherit;
            box-shadow: 0 4px 14px rgba(30,41,59,.025);
            transition: all .2s ease;
            text-decoration: none;
        }

        .student-dashboard a.portal-item:hover {
            border-color: #c9c4ff;
            transform: translateY(-3px);
            background: linear-gradient(135deg, #fff, #faf9ff);
            box-shadow: 0 12px 24px rgba(79,70,229,.09);
            color: var(--primary);
        }

        .student-dashboard .portal-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            border-radius: 13px;
            background: #f1efff;
            color: #635bdb;
            font-size: 1.2rem;
        }

        .student-dashboard .portal-item h6 {
            margin-bottom: 5px;
            font-size: .9rem;
            font-weight: 750;
        }

        .student-dashboard .portal-item p {
            margin-bottom: 0;
            color: #8a94a6;
            font-size: .78rem;
        }

        /* Empty states */
        .student-dashboard .empty-state {
            padding: 35px 15px;
            border: 1px dashed #e1e5ef;
            border-radius: 16px;
            background: #fbfcff;
            text-align: center;
            color: #94a3b8;
        }

        .student-dashboard .empty-state i {
            display: block;
            margin-bottom: 10px;
            font-size: 36px;
        }

        /* Responsive */
        @media (max-width: 575.98px) {
            .student-dashboard .welcome-banner {
                min-height: auto;
                padding: 23px;
                border-radius: 19px;
            }

            .student-dashboard .welcome-avatar {
                width: 62px;
                height: 62px;
                border-radius: 19px;
            }

            .student-dashboard .stat-value {
                font-size: 1.7rem;
            }

            .student-dashboard .chart-container {
                height: 270px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .student-dashboard .dashboard-card,
            .student-dashboard .portal-item {
                transition: none;
            }

            .student-dashboard .dashboard-card:hover,
            .student-dashboard a.portal-item:hover {
                transform: none;
            }
        }
    </style>

    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4"
             role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Welcome Banner --}}
    <section class="welcome-banner mb-4">
        <div class="welcome-content">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-4">

                <div class="d-flex align-items-start gap-3">
                    <div class="student-avatar welcome-avatar">
                        {{ $initials ?: 'ST' }}
                    </div>

                    <div>
                        <span class="welcome-label">
                            <i class="bi bi-stars"></i>
                            STUDENT PORTAL
                        </span>

                        <h1 class="welcome-title">
                            Welcome back, {{ $student->first_name }}!
                        </h1>

                        <p class="welcome-description mb-3">
                            Your learning journey continues here. Keep track of
                            your academic records and stay focused on your goals.
                        </p>

                        <div class="admission-chip">
                            <i class="bi bi-person-vcard"></i>
                            <span>
                                Admission No:
                                <strong>{{ $student->admission_number ?: 'Not assigned' }}</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="badge rounded-pill bg-white text-primary px-3 py-2">
                        <i class="bi bi-mortarboard-fill me-1"></i>
                        My Academics
                    </span>
                </div>

            </div>
        </div>
    </section>

    {{-- Academic Overview --}}
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
        <div>
            <h5 class="section-heading mb-1">Academic Overview</h5>
            <p class="section-subtitle mb-0">
                A quick look at your school records.
            </p>
        </div>

        <span class="text-secondary small">
            <i class="bi bi-shield-check me-1 text-success"></i>
            Personal student account
        </span>
    </div>

    {{-- Statistics --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="stat-label">Attendance Records</span>
                        <span class="stat-icon icon-purple">
                            <i class="bi bi-calendar2-check"></i>
                        </span>
                    </div>

                    <div class="stat-value">
                        {{ number_format($student->attendance_records_count) }}
                    </div>

                    <div class="stat-caption">
                        <i class="bi bi-journal-check me-1"></i>
                        Recorded attendance entries
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="stat-label">Exam Scores</span>
                        <span class="stat-icon icon-green">
                            <i class="bi bi-mortarboard"></i>
                        </span>
                    </div>

                    <div class="stat-value">
                        {{ number_format($student->exam_scores_count) }}
                    </div>

                    <div class="stat-caption">
                        <i class="bi bi-clipboard-check me-1"></i>
                        Assessment records
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="stat-label">Fee Invoices</span>
                        <span class="stat-icon icon-orange">
                            <i class="bi bi-receipt"></i>
                        </span>
                    </div>

                    <div class="stat-value">
                        {{ number_format($student->invoices_count) }}
                    </div>

                    <div class="stat-caption">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        Invoice records
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="stat-label">Payment Records</span>
                        <span class="stat-icon icon-blue">
                            <i class="bi bi-wallet2"></i>
                        </span>
                    </div>

                    <div class="stat-value">
                        {{ number_format($student->payments_count) }}
                    </div>

                    <div class="stat-caption">
                        <i class="bi bi-check2-circle me-1"></i>
                        Recorded payment entries
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Academic Performance Chart --}}
    <div class="row g-4 mb-4">

        <div class="col-12 col-xl-8">
            <div class="dashboard-card chart-card">
                <div class="card-body p-4">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">

                        <div class="d-flex align-items-center gap-3">
                            <span class="chart-heading-icon">
                                <i class="bi bi-activity"></i>
                            </span>

                            <div>
                                <h5 class="section-heading mb-1">
                                    Academic Performance
                                </h5>

                                <p class="section-subtitle mb-0">
                                    A visual overview of your latest examination scores.
                                </p>
                            </div>
                        </div>

                        <span class="chart-legend-pill">
                            <span class="chart-legend-dot"></span>
                            Recent exam scores
                        </span>

                    </div>

                    @if (count($chartScores) > 0)
                        <div wire:ignore class="chart-container">
                            <canvas id="studentPerformanceChart"></canvas>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-bar-chart-line"></i>

                            <h6 class="fw-bold text-dark">
                                No examination data yet
                            </h6>

                            <p class="small mb-0">
                                Your performance chart will appear when your
                                examination scores are published.
                            </p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- Performance Summary --}}
        <div class="col-12 col-xl-4">
            <div class="dashboard-card performance-summary-card">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="stat-icon icon-purple">
                            <i class="bi bi-trophy"></i>
                        </span>

                        <div>
                            <h5 class="fw-bold mb-1">Performance Summary</h5>
                            <p class="section-subtitle mb-0">
                                Recent exam results
                            </p>
                        </div>
                    </div>

                    <div class="performance-score">
                        <div>
                            <div class="stat-label mb-2">Average Score</div>
                            <div class="performance-score-value">
                                {{ number_format($averageScore, 2) }}%
                            </div>
                        </div>

                        <span class="stat-icon icon-purple">
                            <i class="bi bi-graph-up-arrow"></i>
                        </span>
                    </div>

                    <div class="performance-score">
                        <div>
                            <div class="stat-label mb-2">Highest Score</div>
                            <div class="performance-score-value text-success">
                                {{ number_format($highestScore, 2) }}%
                            </div>
                        </div>

                        <span class="stat-icon icon-green">
                            <i class="bi bi-award"></i>
                        </span>
                    </div>

                    <div class="performance-score">
                        <div>
                            <div class="stat-label mb-2">Recent Assessments</div>
                            <div class="performance-score-value">
                                {{ count($chartScores) }}
                            </div>
                        </div>

                        <span class="stat-icon icon-orange">
                            <i class="bi bi-journal-check"></i>
                        </span>
                    </div>

                    @if (Route::has('student.exam-results'))
                        <a href="{{ route('student.exam-results') }}"
                           class="btn btn-outline-primary w-100 rounded-3 mt-3">
                            View All Results
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    @endif

                </div>
            </div>
        </div>

    </div>

    {{-- Enrollment and Profile --}}
    <div class="row g-4 mb-4">

        {{-- Current Enrollment --}}
        <div class="col-12 col-xl-5">
            <div class="dashboard-card">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="stat-icon icon-blue">
                            <i class="bi bi-mortarboard"></i>
                        </span>

                        <div>
                            <h5 class="fw-bold mb-1">Current Enrollment</h5>
                            <p class="section-subtitle mb-0">
                                Your current academic placement
                            </p>
                        </div>
                    </div>

                    @if ($currentEnrollment)
                        <div class="enrollment-box">

                            <div class="enrollment-item mb-4">
                                <div class="enrollment-icon">
                                    <i class="bi bi-building"></i>
                                </div>

                                <div>
                                    <div class="detail-label">Class</div>
                                    <div class="detail-value">
                                        {{ $currentEnrollment->classRoom?->name ?? 'Not assigned' }}
                                    </div>
                                </div>
                            </div>

                            <div class="enrollment-item mb-4">
                                <div class="enrollment-icon">
                                    <i class="bi bi-calendar3"></i>
                                </div>

                                <div>
                                    <div class="detail-label">Academic Session</div>
                                    <div class="detail-value">
                                        {{ $currentEnrollment->academicSession?->name ?? 'Not assigned' }}
                                    </div>
                                </div>
                            </div>

                            <div class="enrollment-item">
                                <div class="enrollment-icon">
                                    <i class="bi bi-calendar-check"></i>
                                </div>

                                <div>
                                    <div class="detail-label">Term</div>
                                    <div class="detail-value">
                                        {{ $currentEnrollment->term?->name ?? 'Not assigned' }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-calendar-x"></i>

                            <h6 class="fw-bold text-dark">
                                No active enrollment
                            </h6>

                            <p class="small mb-0">
                                Contact your school administrator for enrollment information.
                            </p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- Student Profile --}}
        <div class="col-12 col-xl-7">
            <div class="dashboard-card profile-card">

                <div class="profile-top d-flex align-items-center gap-3">
                    <div class="student-avatar">
                        {{ $initials ?: 'ST' }}
                    </div>

                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1">My Student Profile</h5>
                        <p class="text-secondary small mb-0">
                            Your personal student information
                        </p>
                    </div>

                    <span class="status-pill bg-{{ $statusColor }}-subtle text-{{ $statusColor }}">
                        <span class="status-dot"></span>
                        {{ ucfirst($status) }}
                    </span>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3">

                        <div class="col-12 col-md-6">
                            <div class="profile-detail">
                                <div class="detail-label">
                                    <i class="bi bi-person me-1"></i>
                                    Full Name
                                </div>

                                <div class="detail-value">
                                    {{ $fullName ?: 'Not provided' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="profile-detail">
                                <div class="detail-label">
                                    <i class="bi bi-person-vcard me-1"></i>
                                    Admission Number
                                </div>

                                <div class="detail-value">
                                    {{ $student->admission_number ?: 'Not assigned' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="profile-detail">
                                <div class="detail-label">
                                    <i class="bi bi-envelope me-1"></i>
                                    Email Address
                                </div>

                                <div class="detail-value">
                                    {{ $student->email ?: 'Not provided' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="profile-detail">
                                <div class="detail-label">
                                    <i class="bi bi-patch-check me-1"></i>
                                    Account Status
                                </div>

                                <div class="detail-value">
                                    {{ ucfirst($status) }}
                                </div>
                            </div>
                        </div>

                    </div>

                    @if (Route::has('student.profile'))
                        <a href="{{ route('student.profile') }}"
                           class="btn btn-light border rounded-3 mt-3">
                            <i class="bi bi-person-gear me-2"></i>
                            Manage Profile
                        </a>
                    @endif

                </div>
            </div>
        </div>

    </div>

    {{-- Invoice and Payment Activity --}}
    <div class="row g-4 mb-4">

        {{-- Latest Invoice --}}
        <div class="col-12 col-lg-5">
            <div class="dashboard-card">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="section-heading mb-1">Latest Invoice</h5>
                            <p class="section-subtitle mb-0">
                                Your most recent school fee invoice
                            </p>
                        </div>

                        <span class="stat-icon icon-orange">
                            <i class="bi bi-receipt"></i>
                        </span>
                    </div>

                    @if ($latestInvoice)
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon icon-orange">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>

                            <div class="flex-grow-1">
                                <div class="fw-bold">
                                    Latest fee invoice
                                </div>

                                <small class="text-secondary">
                                    Issued:
                                    {{ $latestInvoice->issued_at
                                        ? \Illuminate\Support\Carbon::parse($latestInvoice->issued_at)->format('d M Y')
                                        : 'Date unavailable' }}
                                </small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                                {{ ucfirst($latestInvoice->status ?? 'Available') }}
                            </span>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-receipt"></i>

                            <h6 class="fw-bold text-dark">
                                No invoices available
                            </h6>

                            <p class="small mb-0">
                                Your school fee invoices will appear here.
                            </p>
                        </div>
                    @endif

                    @if (Route::has('student.invoices.index'))
                        <a href="{{ route('student.invoices.index') }}"
                           class="btn btn-outline-primary w-100 rounded-3 mt-3">
                            View My Invoices
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    @endif

                </div>
            </div>
        </div>

        {{-- Recent Payments --}}
        <div class="col-12 col-lg-7">
            <div class="dashboard-card">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="section-heading mb-1">Recent Payments</h5>
                            <p class="section-subtitle mb-0">
                                Your latest payment records
                            </p>
                        </div>

                        <span class="stat-icon icon-green">
                            <i class="bi bi-wallet2"></i>
                        </span>
                    </div>

                    @forelse ($recentPayments as $payment)
                        <div class="payment-row">
                            <div class="d-flex align-items-center gap-3">
                                <div class="payment-icon">
                                    <i class="bi bi-check-lg"></i>
                                </div>

                                <div>
                                    <div class="fw-bold small">
                                        School Fee Payment
                                    </div>

                                    <small class="text-secondary">
                                        {{ $payment->paid_at
                                            ? \Illuminate\Support\Carbon::parse($payment->paid_at)->format('d M Y')
                                            : 'Date unavailable' }}
                                    </small>
                                </div>
                            </div>

                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                                Recorded
                            </span>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="bi bi-wallet"></i>

                            <h6 class="fw-bold text-dark">
                                No payment records
                            </h6>

                            <p class="small mb-0">
                                Your payment history will appear here.
                            </p>
                        </div>
                    @endforelse

                    @if (Route::has('student.payment-history'))
                        <a href="{{ route('student.payment-history') }}"
                           class="btn btn-outline-primary w-100 rounded-3 mt-3">
                            View Payment History
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    @endif

                </div>
            </div>
        </div>

    </div>

    {{-- Student Portal Shortcuts --}}
    <div class="mb-3">
        <h5 class="section-heading mb-1">Explore Your Portal</h5>
        <p class="section-subtitle mb-0">
            Your academic tools, organized in one place.
        </p>
    </div>

    <div class="row g-3 mb-4">

        @if (Route::has('student.attendance'))
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('student.attendance') }}" class="portal-item">
                    <div class="portal-icon">
                        <i class="bi bi-calendar2-week"></i>
                    </div>

                    <div>
                        <h6>My Attendance</h6>
                        <p>Review attendance records</p>
                    </div>
                </a>
            </div>
        @endif

        @if (Route::has('student.exam-results'))
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('student.exam-results') }}" class="portal-item">
                    <div class="portal-icon" style="background:#e5f8ed;color:#15945a;">
                        <i class="bi bi-award"></i>
                    </div>

                    <div>
                        <h6>Exam Results</h6>
                        <p>View your assessments</p>
                    </div>
                </a>
            </div>
        @endif

        @if (Route::has('student.class-timetable'))
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('student.class-timetable') }}" class="portal-item">
                    <div class="portal-icon" style="background:#fff0df;color:#db790c;">
                        <i class="bi bi-calendar3"></i>
                    </div>

                    <div>
                        <h6>Class Timetable</h6>
                        <p>View your class schedule</p>
                    </div>
                </a>
            </div>
        @endif

        @if (Route::has('student.invoices.index'))
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('student.invoices.index') }}" class="portal-item">
                    <div class="portal-icon" style="background:#e2f2ff;color:#1877c9;">
                        <i class="bi bi-credit-card"></i>
                    </div>

                    <div>
                        <h6>School Fees</h6>
                        <p>Review fees and payments</p>
                    </div>
                </a>
            </div>
        @endif

    </div>

    {{-- Footer --}}
    <div class="text-center py-3">
        <p class="text-secondary small mb-0">
            <i class="bi bi-shield-lock me-1"></i>
            Your student information is displayed within your authenticated portal.
        </p>
    </div>

    {{-- Academic Performance Chart --}}
    @if (count($chartScores) > 0)
        <script>
            (() => {
                const labels = @js($chartLabels);
                const scores = @js($chartScores);
                const canvasId = 'studentPerformanceChart';

                const crosshairPlugin = {
                    id: 'studentChartCrosshair',

                    afterDraw(chart) {
                        if (!chart.tooltip?._active?.length) return;

                        const ctx = chart.ctx;
                        const x = chart.tooltip._active[0].element.x;

                        ctx.save();
                        ctx.beginPath();
                        ctx.setLineDash([4, 5]);
                        ctx.moveTo(x, chart.chartArea.top);
                        ctx.lineTo(x, chart.chartArea.bottom);
                        ctx.lineWidth = 1;
                        ctx.strokeStyle = 'rgba(91,80,232,.30)';
                        ctx.stroke();
                        ctx.restore();
                    }
                };

                function drawStudentChart() {
                    const canvas = document.getElementById(canvasId);

                    if (!canvas || typeof Chart === 'undefined') {
                        return;
                    }

                    const oldChart = Chart.getChart(canvas);

                    if (oldChart) {
                        oldChart.destroy();
                    }

                    const ctx = canvas.getContext('2d');

                    const gradient = ctx.createLinearGradient(0, 0, 0, 340);

                    gradient.addColorStop(0, 'rgba(99,91,232,.27)');
                    gradient.addColorStop(.55, 'rgba(139,92,246,.09)');
                    gradient.addColorStop(1, 'rgba(139,92,246,0)');

                    new Chart(ctx, {
                        type: 'line',

                        data: {
                            labels: labels,

                            datasets: [{
                                label: 'Exam score',
                                data: scores,

                                borderColor: '#6559e8',
                                backgroundColor: gradient,

                                borderWidth: 3.5,
                                fill: true,
                                tension: .42,
                                cubicInterpolationMode: 'monotone',

                                pointRadius: 4,
                                pointHoverRadius: 7,
                                pointHitRadius: 18,

                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#6559e8',
                                pointBorderWidth: 3,

                                pointHoverBackgroundColor: '#6559e8',
                                pointHoverBorderColor: '#ffffff',
                                pointHoverBorderWidth: 3
                            }]
                        },

                        plugins: [crosshairPlugin],

                        options: {
                            responsive: true,
                            maintainAspectRatio: false,

                            animation: {
                                duration: 1100,
                                easing: 'easeOutQuart'
                            },

                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },

                            layout: {
                                padding: {
                                    top: 12,
                                    right: 12,
                                    bottom: 0,
                                    left: 4
                                }
                            },

                            plugins: {
                                legend: {
                                    display: false
                                },

                                tooltip: {
                                    backgroundColor: '#201d4f',
                                    titleColor: '#ffffff',
                                    bodyColor: '#e9e7ff',

                                    titleFont: {
                                        family: 'Inter, sans-serif',
                                        size: 12,
                                        weight: '700'
                                    },

                                    bodyFont: {
                                        family: 'Inter, sans-serif',
                                        size: 12,
                                        weight: '600'
                                    },

                                    padding: 14,
                                    cornerRadius: 12,
                                    displayColors: false,
                                    caretSize: 6,

                                    callbacks: {
                                        title(items) {
                                            return items[0]?.label ?? 'Exam result';
                                        },

                                        label(context) {
                                            return `Score: ${Number(context.parsed.y).toFixed(1)}%`;
                                        }
                                    }
                                }
                            },

                            scales: {
                                y: {
                                    min: 0,
                                    max: 100,

                                    border: {
                                        display: false
                                    },

                                    grid: {
                                        color: 'rgba(148,163,184,.15)',
                                        drawTicks: false
                                    },

                                    ticks: {
                                        stepSize: 20,
                                        padding: 12,
                                        color: '#929bb0',

                                        font: {
                                            family: 'Inter, sans-serif',
                                            size: 11,
                                            weight: '500'
                                        },

                                        callback(value) {
                                            return `${value}%`;
                                        }
                                    }
                                },

                                x: {
                                    border: {
                                        display: false
                                    },

                                    grid: {
                                        display: false
                                    },

                                    ticks: {
                                        color: '#929bb0',
                                        padding: 12,
                                        maxRotation: 0,
                                        autoSkip: true,
                                        maxTicksLimit: 8,

                                        font: {
                                            family: 'Inter, sans-serif',
                                            size: 11,
                                            weight: '600'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                function loadChartLibrary() {
                    if (typeof Chart !== 'undefined') {
                        drawStudentChart();
                        return;
                    }

                    let script = document.querySelector(
                        'script[data-student-chart-library]'
                    );

                    if (script) {
                        script.addEventListener(
                            'load',
                            drawStudentChart,
                            { once: true }
                        );

                        return;
                    }

                    script = document.createElement('script');

                    script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js';
                    script.dataset.studentChartLibrary = 'true';
                    script.onload = drawStudentChart;

                    document.head.appendChild(script);
                }

                if (document.readyState === 'loading') {
                    document.addEventListener(
                        'DOMContentLoaded',
                        loadChartLibrary,
                        { once: true }
                    );
                } else {
                    loadChartLibrary();
                }
            })();
        </script>
    @endif

</div>