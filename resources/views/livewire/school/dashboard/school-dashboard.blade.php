
<div class="school-dashboard">

    @php
        $school = request()->attributes->get('currentSchool');

        $summaryCards = [
            [
                'title' => 'Total Students',
                'value' => $stats['totalStudents'],
                'note' => $stats['activeStudents'] . ' active students',
                'icon' => 'bi-mortarboard',
                'color' => 'primary',
            ],
            [
                'title' => 'Teachers',
                'value' => $stats['totalTeachers'],
                'note' => $stats['activeTeachers'] . ' active teachers',
                'icon' => 'bi-person-workspace',
                'color' => 'success',
            ],
            [
                'title' => 'Classes',
                'value' => $stats['totalClasses'],
                'note' => $stats['totalSections'] . ' sections',
                'icon' => 'bi-door-open',
                'color' => 'info',
            ],
            [
                'title' => 'Attendance Today',
                'value' => $stats['attendanceMarked'],
                'note' => $stats['attendanceRate'] . '% attendance rate',
                'icon' => 'bi-calendar-check',
                'color' => 'warning',
            ],
        ];

        $attendanceChartData = [
            $stats['presentToday'],
            $stats['absentToday'],
            $stats['lateToday'],
            $stats['excusedToday'],
        ];

        $populationChartData = [
            $stats['activeStudents'],
            $stats['inactiveStudents'],
            $stats['activeTeachers'],
            $stats['totalClasses'],
            $stats['totalSections'],
        ];
    @endphp

    <style>
        .school-dashboard {
            --school-primary: #5145cd;
            --school-primary-dark: #3730a3;
            --school-muted: #7b8496;
            --school-border: #edf0f5;
            color: #172033;
        }

        .school-dashboard .dashboard-hero {
            position: relative;
            overflow: hidden;
            padding: 30px;
            border-radius: 23px;
            color: #fff;
            background:
                radial-gradient(circle at 85% 15%, rgba(255,255,255,.17), transparent 25%),
                radial-gradient(circle at 70% 120%, rgba(167,139,250,.35), transparent 35%),
                linear-gradient(120deg, #25206f, #5145cd 55%, #8058e8);
            box-shadow: 0 15px 38px rgba(79,70,229,.18);
        }

        .school-dashboard .dashboard-hero::before,
        .school-dashboard .dashboard-hero::after {
            content: "";
            position: absolute;
            border: 25px solid rgba(255,255,255,.08);
            border-radius: 50%;
            pointer-events: none;
        }

        .school-dashboard .dashboard-hero::before {
            width: 240px;
            height: 240px;
            right: -60px;
            top: -120px;
        }

        .school-dashboard .dashboard-hero::after {
            width: 160px;
            height: 160px;
            right: 130px;
            bottom: -115px;
        }

        .school-dashboard .hero-content {
            position: relative;
            z-index: 1;
        }

        .school-dashboard .hero-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 13px;
            border-radius: 30px;
            background: rgba(255,255,255,.14);
            font-size: .77rem;
            font-weight: 700;
            letter-spacing: .4px;
        }

        .school-dashboard .hero-title {
            margin: 15px 0 7px;
            font-size: clamp(1.6rem, 3vw, 2.25rem);
            font-weight: 800;
            letter-spacing: -.8px;
        }

        .school-dashboard .hero-description {
            max-width: 550px;
            color: rgba(255,255,255,.8);
            font-size: .92rem;
            line-height: 1.7;
        }

        .school-dashboard .dashboard-card {
            height: 100%;
            border: 1px solid var(--school-border);
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 6px 24px rgba(20,35,60,.045);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .school-dashboard .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 13px 30px rgba(20,35,60,.075);
        }

        .school-dashboard .stat-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .school-dashboard .stat-value {
            font-size: clamp(1.7rem, 3vw, 2.15rem);
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1.3;
            color: #172033;
        }

        .school-dashboard .muted-label {
            color: var(--school-muted);
            font-size: .84rem;
        }

        .school-dashboard .section-title {
            font-size: 1.08rem;
            font-weight: 800;
            letter-spacing: -.3px;
        }

        .school-dashboard .section-description {
            color: var(--school-muted);
            font-size: .83rem;
        }

        .school-dashboard .table > :not(caption) > * > * {
            padding: .95rem .8rem;
            vertical-align: middle;
        }

        .school-dashboard .student-avatar {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #edf3ff;
            color: #3563d9;
            font-weight: 800;
            flex-shrink: 0;
        }

        .school-dashboard .quick-action {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            border: 1px solid var(--school-border);
            border-radius: 14px;
            text-decoration: none;
            color: #27344a;
            transition: all .2s ease;
        }

        .school-dashboard .quick-action:hover {
            background: #f6f8ff;
            border-color: #cdd9ff;
            transform: translateX(3px);
            color: var(--school-primary);
        }

        .school-dashboard .quick-action-icon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #eef3ff;
            color: #365fca;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .school-dashboard .attendance-progress {
            height: 10px;
            border-radius: 20px;
            background: #edf0f5;
            overflow: hidden;
        }

        .school-dashboard .attendance-progress .progress-bar {
            border-radius: 20px;
        }

        .school-dashboard .dashboard-link {
            font-size: .85rem;
            font-weight: 700;
            text-decoration: none;
            color: var(--school-primary);
        }

        .school-dashboard .dashboard-link:hover {
            color: var(--school-primary-dark);
        }

        .school-dashboard .chart-card {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 100% 0%, rgba(99,102,241,.045), transparent 35%),
                #fff;
        }

        .school-dashboard .chart-container {
            position: relative;
            width: 100%;
            height: 310px;
        }

        .school-dashboard .chart-container.doughnut-container {
            height: 275px;
            max-width: 340px;
            margin: auto;
        }

        .school-dashboard .chart-icon {
            width: 43px;
            height: 43px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: #eeebff;
            color: #5145cd;
            font-size: 1.2rem;
        }

        .school-dashboard .chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 17px;
            justify-content: center;
            margin-top: 12px;
        }

        .school-dashboard .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: .78rem;
            color: #667085;
            font-weight: 600;
        }

        .school-dashboard .legend-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
        }

        .school-dashboard .attendance-box {
            padding: 15px;
            border-radius: 14px;
        }

        .school-dashboard .empty-state {
            padding: 35px 15px;
            border: 1px dashed #e1e5ef;
            border-radius: 16px;
            background: #fbfcff;
            text-align: center;
            color: #94a3b8;
        }

        .school-dashboard .empty-state i {
            display: block;
            margin-bottom: 10px;
            font-size: 34px;
        }

        @media (max-width: 575.98px) {
            .school-dashboard .dashboard-hero {
                padding: 23px;
                border-radius: 19px;
            }

            .school-dashboard .dashboard-card .card-body {
                padding: 1rem !important;
            }

            .school-dashboard .table-responsive {
                font-size: .84rem;
            }

            .school-dashboard .chart-container {
                height: 260px;
            }

            .school-dashboard .chart-container.doughnut-container {
                height: 245px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .school-dashboard .dashboard-card,
            .school-dashboard .quick-action {
                transition: none;
            }

            .school-dashboard .dashboard-card:hover,
            .school-dashboard .quick-action:hover {
                transform: none;
            }
        }
    </style>

    {{-- Page Heading --}}
    <div class="dashboard-hero mb-4">
        <div class="hero-content">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">

                <div>
                    <span class="hero-label">
                        <i class="bi bi-stars"></i>
                        SCHOOL MANAGEMENT PORTAL
                    </span>

                    <h2 class="hero-title">
                        School Dashboard
                    </h2>

                    <p class="hero-description mb-0">
                        Welcome to your school management dashboard.
                        Monitor your students, teachers, attendance and
                        daily school activities from one place.
                    </p>
                </div>

                <div class="text-md-end">
                    <span class="badge rounded-pill bg-white text-primary px-3 py-2">
                        <i class="bi bi-shield-check me-1"></i>
                        School Portal
                    </span>

                    <div class="small mt-3 text-white-50">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ now()->format('D, d M Y') }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- School Information --}}
    <div class="dashboard-card card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center gap-3">

                <div class="stat-icon bg-primary-subtle text-primary"
                     style="width:60px;height:60px;">
                    <i class="bi bi-buildings fs-2"></i>
                </div>

                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-1">
                        {{ $school?->name ?? 'Your School' }}
                    </h5>

                    <div class="text-secondary small">
                        {{ $school?->email ?? 'School email not configured' }}
                    </div>

                    @if($school?->phone)
                        <div class="text-secondary small mt-1">
                            <i class="bi bi-telephone me-1"></i>
                            {{ $school->phone }}
                        </div>
                    @endif
                </div>

                <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                    <i class="bi bi-circle-fill me-1" style="font-size:7px;"></i>
                    {{ ucfirst($school?->status ?? 'Active') }}
                </span>

            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 g-xl-4 mb-4">

        @foreach($summaryCards as $card)
            <div class="col-12 col-sm-6 col-xl-3"
                 wire:key="summary-{{ $card['title'] }}">

                <div class="dashboard-card card h-100">
                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <span class="muted-label fw-semibold">
                                {{ $card['title'] }}
                            </span>

                            <div class="stat-icon bg-{{ $card['color'] }}-subtle text-{{ $card['color'] }}">
                                <i class="bi {{ $card['icon'] }}"></i>
                            </div>
                        </div>

                        <div class="stat-value mb-1">
                            {{ number_format($card['value']) }}
                        </div>

                        <div class="muted-label">
                            {{ $card['note'] }}
                        </div>

                    </div>
                </div>

            </div>
        @endforeach

    </div>

    {{-- Charts --}}
    <div class="row g-4 mb-4">

        {{-- Attendance Doughnut Chart --}}
        <div class="col-12 col-xl-5">
            <div class="dashboard-card chart-card h-100">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="chart-icon">
                            <i class="bi bi-pie-chart"></i>
                        </div>

                        <div>
                            <h5 class="section-title mb-1">
                                Attendance Breakdown
                            </h5>

                            <p class="section-description mb-0">
                                Today's attendance distribution
                            </p>
                        </div>
                    </div>

                    @if($stats['attendanceMarked'] > 0)

                        <div wire:ignore class="chart-container doughnut-container">
                            <canvas id="schoolAttendanceChart"></canvas>
                        </div>

                        <div class="chart-legend">
                            <span class="legend-item">
                                <span class="legend-dot" style="background:#16a36a;"></span>
                                Present
                            </span>

                            <span class="legend-item">
                                <span class="legend-dot" style="background:#ef5350;"></span>
                                Absent
                            </span>

                            <span class="legend-item">
                                <span class="legend-dot" style="background:#f5a623;"></span>
                                Late
                            </span>

                            <span class="legend-item">
                                <span class="legend-dot" style="background:#2699d6;"></span>
                                Excused
                            </span>
                        </div>

                    @else

                        <div class="empty-state mt-3">
                            <i class="bi bi-pie-chart"></i>

                            <h6 class="fw-bold text-dark">
                                No attendance data yet
                            </h6>

                            <p class="small mb-0">
                                Attendance chart will appear when attendance is marked.
                            </p>
                        </div>

                    @endif

                </div>
            </div>
        </div>

        {{-- Population Bar Chart --}}
        <div class="col-12 col-xl-7">
            <div class="dashboard-card chart-card h-100">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="chart-icon">
                            <i class="bi bi-bar-chart-line"></i>
                        </div>

                        <div>
                            <h5 class="section-title mb-1">
                                School Population
                            </h5>

                            <p class="section-description mb-0">
                                Students, teachers, classes and sections
                            </p>
                        </div>
                    </div>

                    <div wire:ignore class="chart-container">
                        <canvas id="schoolPopulationChart"></canvas>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- Attendance and People Overview --}}
    <div class="row g-4 mb-4">

        {{-- Today's Attendance --}}
        <div class="col-12 col-xl-7">
            <div class="dashboard-card card h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h5 class="section-title mb-1">
                                Today's Attendance
                            </h5>

                            <p class="text-secondary small mb-0">
                                Attendance records marked for today.
                            </p>
                        </div>

                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-calendar2-check"></i>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">

                        <div class="col-6 col-md-3">
                            <div class="attendance-box bg-success-subtle h-100">
                                <div class="small text-success mb-2">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Present
                                </div>

                                <div class="h3 fw-bold mb-0 text-success">
                                    {{ number_format($stats['presentToday']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="attendance-box bg-danger-subtle h-100">
                                <div class="small text-danger mb-2">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Absent
                                </div>

                                <div class="h3 fw-bold mb-0 text-danger">
                                    {{ number_format($stats['absentToday']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="attendance-box bg-warning-subtle h-100">
                                <div class="small text-warning-emphasis mb-2">
                                    <i class="bi bi-clock me-1"></i>
                                    Late
                                </div>

                                <div class="h3 fw-bold mb-0 text-warning-emphasis">
                                    {{ number_format($stats['lateToday']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="attendance-box bg-info-subtle h-100">
                                <div class="small text-info-emphasis mb-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Excused
                                </div>

                                <div class="h3 fw-bold mb-0 text-info-emphasis">
                                    {{ number_format($stats['excusedToday']) }}
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold small">
                            Present attendance rate
                        </span>

                        <span class="fw-bold">
                            {{ number_format($stats['attendanceRate'], 1) }}%
                        </span>
                    </div>

                    <div class="attendance-progress mb-3">
                        <div class="progress-bar bg-success"
                             role="progressbar"
                             style="width: {{ min(100, max(0, $stats['attendanceRate'])) }}%; height:100%;"
                             aria-valuenow="{{ $stats['attendanceRate'] }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between small text-secondary">
                        <span>
                            {{ number_format($stats['presentToday']) }} present
                        </span>

                        <span>
                            {{ number_format($stats['attendanceMarked']) }} records marked
                        </span>
                    </div>

                    @if(Route::has('school.attendance.index'))
                        <div class="mt-4">
                            <a href="{{ route('school.attendance.index') }}"
                               class="dashboard-link">
                                View attendance records
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- School Population Details --}}
        <div class="col-12 col-xl-5">
            <div class="dashboard-card card h-100">
                <div class="card-body p-4">

                    <h5 class="section-title mb-1">
                        Population Summary
                    </h5>

                    <p class="text-secondary small mb-4">
                        Overview of registered school members.
                    </p>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-secondary">
                            <i class="bi bi-mortarboard text-primary me-2"></i>
                            Total students
                        </span>

                        <span class="fw-bold">
                            {{ number_format($stats['totalStudents']) }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-secondary">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Active students
                        </span>

                        <span class="fw-bold text-success">
                            {{ number_format($stats['activeStudents']) }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="small text-secondary">
                            <i class="bi bi-person-dash text-secondary me-2"></i>
                            Inactive students
                        </span>

                        <span class="fw-bold">
                            {{ number_format($stats['inactiveStudents']) }}
                        </span>
                    </div>

                    <hr class="border-light my-3">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="small text-secondary">
                            <i class="bi bi-person-workspace text-success me-2"></i>
                            Total teachers
                        </span>

                        <span class="fw-bold">
                            {{ number_format($stats['totalTeachers']) }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="small text-secondary">
                            <i class="bi bi-person-check text-success me-2"></i>
                            Active teachers
                        </span>

                        <span class="fw-bold text-success">
                            {{ number_format($stats['activeTeachers']) }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="small text-secondary">
                            <i class="bi bi-door-open text-info me-2"></i>
                            Classes
                        </span>

                        <span class="fw-bold">
                            {{ number_format($stats['totalClasses']) }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small text-secondary">
                            <i class="bi bi-grid text-primary me-2"></i>
                            Sections
                        </span>

                        <span class="fw-bold">
                            {{ number_format($stats['totalSections']) }}
                        </span>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- Recent Registrations and Quick Actions --}}
    <div class="row g-4 mb-4">

        {{-- Recent Students --}}
        <div class="col-12 col-xl-7">
            <div class="dashboard-card card h-100">
                <div class="card-body p-0">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-4">
                        <div>
                            <h5 class="section-title mb-1">
                                Recent Students
                            </h5>

                            <p class="text-secondary small mb-0">
                                Latest student registrations.
                            </p>
                        </div>

                        @if(Route::has('school.students.index'))
                            <a href="{{ route('school.students.index') }}"
                               class="dashboard-link">
                                View all
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Student</th>
                                    <th>Admission No.</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($recentStudents as $student)
                                    <tr wire:key="recent-student-{{ $student->id }}">

                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">

                                                <div class="student-avatar">
                                                    {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($student->last_name ?? '', 0, 1)) }}
                                                </div>

                                                <div>
                                                    <div class="fw-semibold">
                                                        {{ trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: 'Unnamed student' }}
                                                    </div>

                                                    <div class="small text-secondary">
                                                        {{ $student->email ?: 'No email provided' }}
                                                    </div>
                                                </div>

                                            </div>
                                        </td>

                                        <td>
                                            <span class="small">
                                                {{ $student->admission_number ?: '—' }}
                                            </span>
                                        </td>

                                        <td>
                                            @if($student->status === 'active')
                                                <span class="badge rounded-pill bg-success-subtle text-success">
                                                    Active
                                                </span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary-subtle text-secondary">
                                                    {{ ucfirst($student->status ?? 'Unknown') }}
                                                </span>
                                            @endif
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <i class="bi bi-people fs-2 text-secondary"></i>

                                            <p class="fw-semibold mt-2 mb-1">
                                                No students yet
                                            </p>

                                            <p class="small text-secondary mb-0">
                                                Registered students will appear here.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-12 col-xl-5">
            <div class="dashboard-card card h-100">
                <div class="card-body p-4">

                    <h5 class="section-title mb-1">
                        Quick Actions
                    </h5>

                    <p class="text-secondary small mb-4">
                        Shortcuts to your daily school tasks.
                    </p>

                    <div class="d-grid gap-3">

                        @if(Route::has('school.students.create'))
                            <a href="{{ route('school.students.create') }}"
                               class="quick-action">

                                <span class="quick-action-icon">
                                    <i class="bi bi-person-plus"></i>
                                </span>

                                <span class="flex-grow-1">
                                    <span class="d-block fw-semibold">
                                        Register Student
                                    </span>

                                    <span class="d-block small text-secondary">
                                        Add a student to your school
                                    </span>
                                </span>

                                <i class="bi bi-chevron-right text-secondary"></i>
                            </a>

                        @elseif(Route::has('school.students.index'))
                            <a href="{{ route('school.students.index') }}"
                               class="quick-action">

                                <span class="quick-action-icon">
                                    <i class="bi bi-people"></i>
                                </span>

                                <span class="flex-grow-1">
                                    <span class="d-block fw-semibold">
                                        Manage Students
                                    </span>

                                    <span class="d-block small text-secondary">
                                        View registered students
                                    </span>
                                </span>

                                <i class="bi bi-chevron-right text-secondary"></i>
                            </a>
                        @endif

                        @if(Route::has('school.teachers.index'))
                            <a href="{{ route('school.teachers.index') }}"
                               class="quick-action">

                                <span class="quick-action-icon">
                                    <i class="bi bi-person-workspace"></i>
                                </span>

                                <span class="flex-grow-1">
                                    <span class="d-block fw-semibold">
                                        Manage Teachers
                                    </span>

                                    <span class="d-block small text-secondary">
                                        View your teaching staff
                                    </span>
                                </span>

                                <i class="bi bi-chevron-right text-secondary"></i>
                            </a>
                        @endif

                        @if(Route::has('school.classes.index'))
                            <a href="{{ route('school.classes.index') }}"
                               class="quick-action">

                                <span class="quick-action-icon">
                                    <i class="bi bi-door-open"></i>
                                </span>

                                <span class="flex-grow-1">
                                    <span class="d-block fw-semibold">
                                        Manage Classes
                                    </span>

                                    <span class="d-block small text-secondary">
                                        View classes and sections
                                    </span>
                                </span>

                                <i class="bi bi-chevron-right text-secondary"></i>
                            </a>
                        @endif

                        @if(Route::has('school.attendance.index'))
                            <a href="{{ route('school.attendance.index') }}"
                               class="quick-action">

                                <span class="quick-action-icon">
                                    <i class="bi bi-calendar-check"></i>
                                </span>

                                <span class="flex-grow-1">
                                    <span class="d-block fw-semibold">
                                        Attendance
                                    </span>

                                    <span class="d-block small text-secondary">
                                        Review attendance records
                                    </span>
                                </span>

                                <i class="bi bi-chevron-right text-secondary"></i>
                            </a>
                        @endif

                        @if(Route::has('school.subjects.index'))
                            <a href="{{ route('school.subjects.index') }}"
                               class="quick-action">

                                <span class="quick-action-icon">
                                    <i class="bi bi-journal-bookmark"></i>
                                </span>

                                <span class="flex-grow-1">
                                    <span class="d-block fw-semibold">
                                        Manage Subjects
                                    </span>

                                    <span class="d-block small text-secondary">
                                        View school subjects
                                    </span>
                                </span>

                                <i class="bi bi-chevron-right text-secondary"></i>
                            </a>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Latest Attendance Activity --}}
    <div class="dashboard-card card mb-4">
        <div class="card-body p-0">

            <div class="p-4">
                <h5 class="section-title mb-1">
                    Today's Attendance Activity
                </h5>

                <p class="text-secondary small mb-0">
                    Latest attendance records for your school.
                </p>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Student ID</th>
                            <th>Class ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($recentAttendance as $record)
                            <tr wire:key="attendance-record-{{ $record->id }}">

                                <td class="ps-4">
                                    <span class="fw-semibold">
                                        #{{ $record->student_id }}
                                    </span>
                                </td>

                                <td>
                                    {{ $record->class_room_id ?: '—' }}
                                </td>

                                <td>
                                    {{ $record->attendance_date
                                        ? \Illuminate\Support\Carbon::parse($record->attendance_date)->format('d M Y')
                                        : '—' }}
                                </td>

                                <td>
                                    @php
                                        $status = strtolower($record->status ?? 'unknown');

                                        $statusClass = match ($status) {
                                            'present' => 'success',
                                            'absent' => 'danger',
                                            'late' => 'warning',
                                            'excused' => 'info',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge rounded-pill bg-{{ $statusClass }}-subtle text-{{ $statusClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>

                                <td class="text-secondary small">
                                    {{ $record->remarks ?: '—' }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-calendar2-x fs-2 text-secondary"></i>

                                    <p class="fw-semibold mt-2 mb-1">
                                        No attendance records today
                                    </p>

                                    <p class="small text-secondary mb-0">
                                        Records will appear when attendance is marked.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            @if(Route::has('school.attendance.index'))
                <div class="p-3 text-center border-top">
                    <a href="{{ route('school.attendance.index') }}"
                       class="dashboard-link">
                        View all attendance
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            @endif

        </div>
    </div>

    {{-- Footer --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pb-3">
        <span class="small text-secondary">
            <i class="bi bi-info-circle me-1"></i>
            Dashboard statistics reflect the data currently recorded for your school.
        </span>

        <span class="small text-secondary">
            Updated {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>

    {{-- Chart.js --}}
    <script>
        (() => {
            const attendanceData = @js($attendanceChartData);
            const populationData = @js($populationChartData);

            function initializeSchoolCharts() {
                if (typeof Chart === 'undefined') {
                    return;
                }

                // Attendance Doughnut Chart
                const attendanceCanvas = document.getElementById('schoolAttendanceChart');

                if (attendanceCanvas) {
                    const existingAttendanceChart = Chart.getChart(attendanceCanvas);

                    if (existingAttendanceChart) {
                        existingAttendanceChart.destroy();
                    }

                    const totalAttendance = attendanceData.reduce(
                        (total, value) => total + Number(value),
                        0
                    );

                    if (totalAttendance > 0) {
                        new Chart(attendanceCanvas, {
                            type: 'doughnut',

                            data: {
                                labels: [
                                    'Present',
                                    'Absent',
                                    'Late',
                                    'Excused'
                                ],

                                datasets: [{
                                    data: attendanceData,

                                    backgroundColor: [
                                        '#16a36a',
                                        '#ef5350',
                                        '#f5a623',
                                        '#2699d6'
                                    ],

                                    borderColor: '#ffffff',
                                    borderWidth: 4,
                                    hoverOffset: 9,
                                    borderRadius: 5,
                                    spacing: 3
                                }]
                            },

                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '73%',

                                animation: {
                                    animateRotate: true,
                                    animateScale: true,
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
                                        displayColors: true,

                                        callbacks: {
                                            label(context) {
                                                const value = context.parsed;
                                                const percentage = totalAttendance
                                                    ? ((value / totalAttendance) * 100).toFixed(1)
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

                // School Population Bar Chart
                const populationCanvas = document.getElementById('schoolPopulationChart');

                if (populationCanvas) {
                    const existingPopulationChart = Chart.getChart(populationCanvas);

                    if (existingPopulationChart) {
                        existingPopulationChart.destroy();
                    }

                    new Chart(populationCanvas, {
                        type: 'bar',

                        data: {
                            labels: [
                                'Active Students',
                                'Inactive Students',
                                'Active Teachers',
                                'Classes',
                                'Sections'
                            ],

                            datasets: [{
                                label: 'Total Records',
                                data: populationData,

                                backgroundColor: [
                                    'rgba(81,69,205,.85)',
                                    'rgba(148,163,184,.8)',
                                    'rgba(22,163,106,.85)',
                                    'rgba(38,153,214,.85)',
                                    'rgba(245,166,35,.85)'
                                ],

                                borderColor: [
                                    '#5145cd',
                                    '#94a3b8',
                                    '#16a36a',
                                    '#2699d6',
                                    '#f5a623'
                                ],

                                borderWidth: 1,
                                borderRadius: 9,
                                borderSkipped: false,
                                barPercentage: .62,
                                categoryPercentage: .72
                            }]
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
                                    display: false
                                },

                                tooltip: {
                                    backgroundColor: '#202044',
                                    titleColor: '#ffffff',
                                    bodyColor: '#e8e8ff',
                                    padding: 13,
                                    cornerRadius: 10,
                                    displayColors: false,

                                    callbacks: {
                                        label(context) {
                                            return ` ${context.parsed.y.toLocaleString()} records`;
                                        }
                                    }
                                }
                            },

                            scales: {
                                y: {
                                    beginAtZero: true,

                                    border: {
                                        display: false
                                    },

                                    grid: {
                                        color: 'rgba(148,163,184,.15)',
                                        drawTicks: false
                                    },

                                    ticks: {
                                        precision: 0,
                                        padding: 10,
                                        color: '#8792a5'
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
                                        color: '#8792a5',
                                        maxRotation: 0,
                                        minRotation: 0,
                                        font: {
                                            size: 10,
                                            weight: '600'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            function loadChartLibrary() {
                if (typeof Chart !== 'undefined') {
                    initializeSchoolCharts();
                    return;
                }

                let script = document.querySelector(
                    'script[data-school-chart-library]'
                );

                if (script) {
                    script.addEventListener(
                        'load',
                        initializeSchoolCharts,
                        { once: true }
                    );

                    return;
                }

                script = document.createElement('script');

                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js';
                script.dataset.schoolChartLibrary = 'true';
                script.onload = initializeSchoolCharts;

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

</div>
