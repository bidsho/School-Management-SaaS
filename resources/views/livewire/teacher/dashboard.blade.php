
<div class="container-fluid teacher-dashboard py-4 px-3 px-lg-4">

    @php
        $fullName = trim(
            ($teacher->first_name ?? '') . ' ' .
            ($teacher->middle_name ?? '') . ' ' .
            ($teacher->last_name ?? '')
        );

        $initials = strtoupper(
            substr($teacher->first_name ?? '', 0, 1) .
            substr($teacher->last_name ?? '', 0, 1)
        );

        $employmentStatus = strtolower($teacher->employment_status ?? '');

        $statusClasses = match ($employmentStatus) {
            'active' => 'td-status-active',
            'inactive' => 'td-status-inactive',
            'on_leave' => 'td-status-leave',
            'terminated' => 'td-status-terminated',
            default => 'td-status-unknown',
        };

        $statusLabel = match ($employmentStatus) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'on_leave' => 'On Leave',
            'terminated' => 'Terminated',
            default => 'Not Set',
        };
    @endphp

    {{-- Page Heading --}}
    <div class="td-page-heading mb-4">
        <div>
            <div class="td-eyebrow">
                <span class="td-eyebrow-dot"></span>
                TEACHER WORKSPACE
            </div>
            <h1 class="td-page-title">Dashboard</h1>
            <p class="td-page-subtitle">
                Your teaching activities, schedule and professional overview.
            </p>
        </div>

        <div class="td-date-pill">
            <i class="bi bi-calendar3"></i>
            <span>{{ now()->format('D, d M Y') }}</span>
        </div>
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4"
             role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Welcome Banner --}}
    <section class="td-hero mb-4">
        <div class="td-hero-pattern"></div>

        <div class="td-hero-content">
            <div class="td-hero-copy">
                <span class="td-hero-badge">
                    <i class="bi bi-mortarboard-fill"></i>
                    TEACHER PORTAL
                </span>

                <h2>
                    Welcome back,
                    <span>{{ $teacher->first_name }}!</span>
                </h2>

                <p>
                    Every lesson makes a difference. Here's an overview of your
                    teaching activities and professional workspace.
                </p>

                <div class="td-hero-meta">
                    <span>
                        <i class="bi bi-person-check-fill"></i>
                        {{ $statusLabel }} Teacher
                    </span>
                    <span class="td-hero-meta-divider"></span>
                    <span>
                        <i class="bi bi-clock"></i>
                        {{ now()->format('l') }}
                    </span>
                </div>
            </div>

            <div class="td-hero-avatar-wrap">
                <div class="td-hero-avatar">
                    {{ $initials ?: 'T' }}
                </div>
                <span class="td-avatar-online" title="Signed in"></span>
                <div class="td-hero-avatar-caption">
                    <i class="bi bi-patch-check-fill"></i>
                    Teacher Account
                </div>
            </div>
        </div>

        <div class="td-hero-bottom">
            <div>
                <i class="bi bi-shield-check me-2"></i>
                Your teaching workspace is ready
            </div>
            <span class="td-secure-label">
                <span></span> Secure session
            </span>
        </div>
    </section>

    {{-- Statistics --}}
    <div class="td-section-heading mb-3">
        <div>
            <h5>At a glance</h5>
            <p>Your teaching activity summary</p>
        </div>
        <span class="td-section-icon">
            <i class="bi bi-bar-chart-line"></i>
        </span>
    </div>

    <div class="row g-3 mb-4">

        {{-- Teaching Assignments --}}
        <div class="col-sm-6 col-xl-4">
            <div class="td-stat-card h-100">
                <div class="td-stat-top">
                    <div class="td-stat-icon td-icon-purple">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <span class="td-stat-tag">Teaching</span>
                </div>

                <div class="td-stat-value">
                    {{ number_format($stats['teaching_assignments']) }}
                </div>
                <div class="td-stat-title">Teaching Assignments</div>
                <p class="td-stat-description">
                    Your assigned teaching responsibilities.
                </p>

                <div class="td-stat-footer">
                    <span class="td-stat-footer-icon td-footer-purple">
                        <i class="bi bi-book-half"></i>
                    </span>
                    <span>Assigned responsibilities</span>
                </div>
            </div>
        </div>

        {{-- Timetable --}}
        <div class="col-sm-6 col-xl-4">
            <div class="td-stat-card h-100">
                <div class="td-stat-top">
                    <div class="td-stat-icon td-icon-green">
                        <i class="bi bi-calendar2-week-fill"></i>
                    </div>
                    <span class="td-stat-tag">Schedule</span>
                </div>

                <div class="td-stat-value">
                    {{ number_format($stats['scheduled_lessons']) }}
                </div>
                <div class="td-stat-title">Scheduled Lessons</div>
                <p class="td-stat-description">
                    Lessons recorded in your timetable.
                </p>

                <div class="td-stat-footer">
                    <span class="td-stat-footer-icon td-footer-green">
                        <i class="bi bi-calendar-check"></i>
                    </span>
                    <span>Recorded timetable entries</span>
                </div>
            </div>
        </div>

        {{-- Assignments Created --}}
        <div class="col-sm-6 col-xl-4">
            <div class="td-stat-card h-100">
                <div class="td-stat-top">
                    <div class="td-stat-icon td-icon-orange">
                        <i class="bi bi-clipboard2-check-fill"></i>
                    </div>
                    <span class="td-stat-tag">Classwork</span>
                </div>

                <div class="td-stat-value">
                    {{ number_format($stats['assignments_created']) }}
                </div>
                <div class="td-stat-title">Assignments Created</div>
                <p class="td-stat-description">
                    Learning tasks you have created for students.
                </p>

                <div class="td-stat-footer">
                    <span class="td-stat-footer-icon td-footer-orange">
                        <i class="bi bi-pencil-square"></i>
                    </span>
                    <span>Learning activities</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Main Dashboard Content --}}
    <div class="row g-4">

        {{-- Teacher Profile --}}
        <div class="col-xl-7">
            <section class="td-panel h-100">
                <div class="td-panel-header">
                    <div class="td-panel-heading">
                        <div class="td-panel-icon td-panel-icon-purple">
                            <i class="bi bi-person-vcard-fill"></i>
                        </div>
                        <div>
                            <h5>My Teaching Profile</h5>
                            <p>Your professional and employment information</p>
                        </div>
                    </div>

                    <span class="td-profile-status {{ $statusClasses }}">
                        <span></span>
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="td-profile-intro">
                    <div class="td-profile-avatar">
                        {{ $initials ?: 'T' }}
                    </div>
                    <div class="td-profile-intro-text">
                        <h4>{{ $fullName ?: 'Teacher' }}</h4>
                        <p>
                            <i class="bi bi-envelope me-1"></i>
                            {{ $user->email }}
                        </p>
                        <span>
                            <i class="bi bi-person-badge me-1"></i>
                            Staff ID: {{ $teacher->staff_number ?: 'Not assigned' }}
                        </span>
                    </div>
                </div>

                <div class="td-profile-divider"></div>

                <div class="td-details-grid">
                    <div class="td-detail-item">
                        <div class="td-detail-icon td-detail-blue">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div class="td-detail-content">
                            <span>Phone Number</span>
                            <strong>{{ $teacher->phone ?: 'Not provided' }}</strong>
                        </div>
                    </div>

                    <div class="td-detail-item">
                        <div class="td-detail-icon td-detail-purple">
                            <i class="bi bi-mortarboard"></i>
                        </div>
                        <div class="td-detail-content">
                            <span>Qualification</span>
                            <strong>{{ $teacher->qualification ?: 'Not provided' }}</strong>
                        </div>
                    </div>

                    <div class="td-detail-item">
                        <div class="td-detail-icon td-detail-green">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="td-detail-content">
                            <span>Date Hired</span>
                            <strong>
                                {{ $teacher->hired_at?->format('d M, Y') ?? 'Not provided' }}
                            </strong>
                        </div>
                    </div>

                    <div class="td-detail-item">
                        <div class="td-detail-icon td-detail-orange">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div class="td-detail-content">
                            <span>Employment Status</span>
                            <strong>{{ $statusLabel }}</strong>
                        </div>
                    </div>

                    <div class="td-detail-item td-detail-full">
                        <div class="td-detail-icon td-detail-red">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="td-detail-content">
                            <span>Residential Address</span>
                            <strong>{{ $teacher->address ?: 'No address provided' }}</strong>
                        </div>
                    </div>

                    <div class="td-detail-item td-detail-full">
                        <div class="td-detail-icon td-detail-blue">
                            <i class="bi bi-envelope-check"></i>
                        </div>
                        <div class="td-detail-content">
                            <span>Login Email Address</span>
                            <strong class="td-email-value">{{ $user->email }}</strong>
                        </div>
                    </div>
                </div>

                <div class="td-profile-footer">
                    <div class="td-verified-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <strong>Profile information</strong>
                        <p>Your employment details are displayed from your teacher record.</p>
                    </div>
                </div>
            </section>
        </div>

        {{-- Right Column --}}
        <div class="col-xl-5">

            {{-- Teaching Overview --}}
            <section class="td-panel mb-4">
                <div class="td-panel-header">
                    <div class="td-panel-heading">
                        <div class="td-panel-icon td-panel-icon-green">
                            <i class="bi bi-grid-1x2-fill"></i>
                        </div>
                        <div>
                            <h5>Teaching Overview</h5>
                            <p>A quick look at your activity</p>
                        </div>
                    </div>
                </div>

                <div class="td-activity-list">
                    <div class="td-activity-item">
                        <div class="td-activity-icon td-icon-purple">
                            <i class="bi bi-journal-bookmark"></i>
                        </div>
                        <div class="td-activity-copy">
                            <h6>Teaching Responsibilities</h6>
                            <p>Your current recorded teaching assignments.</p>
                        </div>
                        <div class="td-activity-count">
                            {{ number_format($stats['teaching_assignments']) }}
                        </div>
                    </div>

                    <div class="td-activity-item">
                        <div class="td-activity-icon td-icon-green">
                            <i class="bi bi-calendar-week"></i>
                        </div>
                        <div class="td-activity-copy">
                            <h6>Class Timetable</h6>
                            <p>Your recorded scheduled lessons.</p>
                        </div>
                        <div class="td-activity-count">
                            {{ number_format($stats['scheduled_lessons']) }}
                        </div>
                    </div>

                    <div class="td-activity-item">
                        <div class="td-activity-icon td-icon-orange">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <div class="td-activity-copy">
                            <h6>Class Assignments</h6>
                            <p>Learning tasks you have created.</p>
                        </div>
                        <div class="td-activity-count">
                            {{ number_format($stats['assignments_created']) }}
                        </div>
                    </div>
                </div>
            </section>

            {{-- Account Security --}}
            <section class="td-security-card">
                <div class="td-security-top">
                    <div class="td-security-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <span class="td-security-badge">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Signed In
                    </span>
                </div>

                <h5>Your Account Is Connected</h5>
                <p>
                    You are signed in to your teacher workspace. Keep your login
                    credentials private and sign out when using a shared computer.
                </p>

                <div class="td-security-account">
                    <div class="td-security-account-icon">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div>
                        <span>Signed in as</span>
                        <strong>{{ $user->name }}</strong>
                    </div>
                </div>
            </section>

        </div>
    </div>

    {{-- Footer --}}
    <footer class="td-dashboard-footer">
        <div class="td-footer-brand">
            <span class="td-footer-brand-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </span>
            <span>Teacher Portal</span>
        </div>
        <span>
            <i class="bi bi-heart-fill me-1"></i>
            Supporting better learning
        </span>
        <span>&copy; {{ now()->format('Y') }}</span>
    </footer>
    <style>
    .teacher-dashboard {
        --td-ink: #172033;
        --td-muted: #778198;
        --td-line: #e9edf5;
        --td-purple: #6857e8;
        --td-green: #159b72;
        --td-orange: #e99532;
        color: var(--td-ink);
        font-size: 14px;
    }

    .teacher-dashboard * {
        box-sizing: border-box;
    }

    .td-page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }

    .td-eyebrow {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6857e8;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.6px;
        margin-bottom: 7px;
    }

    .td-eyebrow-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #6857e8;
        box-shadow: 0 0 0 4px rgba(104, 87, 232, .10);
    }

    .td-page-title {
        font-size: clamp(25px, 3vw, 31px);
        font-weight: 800;
        letter-spacing: -1.1px;
        margin: 0 0 5px;
    }

    .td-page-subtitle {
        margin: 0;
        color: var(--td-muted);
        font-size: 13px;
        line-height: 1.7;
    }

    .td-date-pill {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        border: 1px solid var(--td-line);
        border-radius: 13px;
        padding: 11px 15px;
        background: #fff;
        color: #566079;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(24, 35, 68, .03);
    }

    .td-date-pill i {
        color: var(--td-purple);
        font-size: 15px;
    }

    /* Welcome banner */
    .td-hero {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        color: #fff;
        background: linear-gradient(116deg, #26224f 0%, #41358b 53%, #6557d9 100%);
        box-shadow: 0 15px 38px rgba(60, 49, 139, .18);
    }

    .td-hero-pattern {
        position: absolute;
        width: 360px;
        height: 360px;
        right: 6%;
        top: -210px;
        border: 1px solid rgba(255,255,255,.11);
        border-radius: 50%;
        box-shadow:
            0 0 0 35px rgba(255,255,255,.035),
            0 0 0 70px rgba(255,255,255,.025);
        pointer-events: none;
    }

    .td-hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
        padding: 34px 38px 30px;
    }

    .td-hero-copy {
        max-width: 610px;
    }

    .td-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 11px;
        border: 1px solid rgba(255,255,255,.19);
        border-radius: 8px;
        background: rgba(255,255,255,.09);
        color: #e6e2ff;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.1px;
    }

    .td-hero-copy h2 {
        margin: 20px 0 12px;
        font-size: clamp(25px, 3.2vw, 37px);
        line-height: 1.2;
        font-weight: 800;
        letter-spacing: -1.1px;
    }

    .td-hero-copy h2 span {
        color: #d4ccff;
    }

    .td-hero-copy > p {
        max-width: 525px;
        margin: 0;
        color: #d8d6f1;
        font-size: 13px;
        line-height: 1.9;
    }

    .td-hero-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 13px;
        margin-top: 21px;
        color: #eeecff;
        font-size: 11px;
        font-weight: 600;
    }

    .td-hero-meta span:not(.td-hero-meta-divider) {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .td-hero-meta-divider {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #a9a0f3;
    }

    .td-hero-avatar-wrap {
        position: relative;
        flex: 0 0 155px;
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 12px;
        margin-right: 18px;
    }

    .td-hero-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 118px;
        height: 118px;
        border: 5px solid rgba(255,255,255,.20);
        border-radius: 36px;
        background: linear-gradient(145deg, #f1edff, #c8c0ff);
        color: #41358b;
        font-size: 36px;
        font-weight: 800;
        box-shadow: 0 12px 25px rgba(19, 15, 64, .20);
    }

    .td-avatar-online {
        position: absolute;
        top: 87px;
        right: 24px;
        width: 15px;
        height: 15px;
        border: 3px solid #49408e;
        border-radius: 50%;
        background: #3ce1a5;
    }

    .td-hero-avatar-caption {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #e6e2ff;
        font-size: 11px;
        font-weight: 700;
    }

    .td-hero-avatar-caption i {
        color: #79e8c1;
    }

    .td-hero-bottom {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 14px 38px;
        border-top: 1px solid rgba(255,255,255,.12);
        background: rgba(15, 12, 52, .15);
        color: #e5e2ff;
        font-size: 11px;
    }

    .td-secure-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .td-secure-label > span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #5be1ae;
    }

    /* Section heading */
    .td-section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .td-section-heading h5 {
        margin: 0 0 5px;
        font-size: 16px;
        font-weight: 800;
        letter-spacing: -.3px;
    }

    .td-section-heading p {
        margin: 0;
        color: var(--td-muted);
        font-size: 12px;
    }

    .td-section-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: #f0edff;
        color: var(--td-purple);
        font-size: 17px;
    }

    /* Statistics */
    .td-stat-card {
        position: relative;
        padding: 23px;
        overflow: hidden;
        border: 1px solid var(--td-line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 5px 22px rgba(27, 39, 76, .035);
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }

    .td-stat-card:hover {
        transform: translateY(-4px);
        border-color: #dcd7ff;
        box-shadow: 0 13px 30px rgba(40, 42, 90, .09);
    }

    .td-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 22px;
    }

    .td-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 49px;
        height: 49px;
        border-radius: 15px;
        font-size: 21px;
    }

    .td-icon-purple {
        background: #f0edff;
        color: #6857e8;
    }

    .td-icon-green {
        background: #e4f8f0;
        color: #159b72;
    }

    .td-icon-orange {
        background: #fff1df;
        color: #dc8b22;
    }

    .td-stat-tag {
        padding: 6px 10px;
        border: 1px solid #edf0f6;
        border-radius: 8px;
        background: #f9faff;
        color: #7b8499;
        font-size: 10px;
        font-weight: 700;
    }

    .td-stat-value {
        margin-bottom: 4px;
        font-size: 34px;
        line-height: 1.2;
        font-weight: 800;
        letter-spacing: -1.4px;
        color: #202943;
    }

    .td-stat-title {
        color: #38425b;
        font-size: 12px;
        font-weight: 800;
    }

    .td-stat-description {
        min-height: 35px;
        margin: 9px 0 18px;
        color: var(--td-muted);
        font-size: 11px;
        line-height: 1.7;
    }

    .td-stat-footer {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-top: 13px;
        border-top: 1px solid #f0f2f7;
        color: #8a92a5;
        font-size: 10px;
    }

    .td-stat-footer-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 23px;
        height: 23px;
        border-radius: 7px;
    }

    .td-footer-purple {
        background: #f0edff;
        color: #6857e8;
    }

    .td-footer-green {
        background: #e4f8f0;
        color: #159b72;
    }

    .td-footer-orange {
        background: #fff1df;
        color: #dc8b22;
    }

    /* Panels */
    .td-panel {
        overflow: hidden;
        border: 1px solid var(--td-line);
        border-radius: 19px;
        background: #fff;
        box-shadow: 0 5px 24px rgba(27, 39, 76, .035);
    }

    .td-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding: 23px 24px;
        border-bottom: 1px solid #f0f2f7;
    }

    .td-panel-heading {
        display: flex;
        align-items: center;
        gap: 13px;
    }

    .td-panel-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 43px;
        height: 43px;
        flex: 0 0 43px;
        border-radius: 13px;
        font-size: 18px;
    }

    .td-panel-icon-purple {
        background: #f0edff;
        color: #6857e8;
    }

    .td-panel-icon-green {
        background: #e4f8f0;
        color: #159b72;
    }

    .td-panel-heading h5 {
        margin: 0 0 5px;
        font-size: 14px;
        font-weight: 800;
    }

    .td-panel-heading p {
        margin: 0;
        color: var(--td-muted);
        font-size: 11px;
        line-height: 1.6;
    }

    .td-profile-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 10px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .td-profile-status > span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .td-status-active {
        background: #e4f8f0;
        color: #12845f;
    }

    .td-status-inactive {
        background: #eef0f5;
        color: #667085;
    }

    .td-status-leave {
        background: #fff1d8;
        color: #a86b0c;
    }

    .td-status-terminated {
        background: #ffe8e8;
        color: #c03939;
    }

    .td-status-unknown {
        background: #f1f2f6;
        color: #737c91;
    }

    /* Profile */
    .td-profile-intro {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 25px 24px 22px;
    }

    .td-profile-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 66px;
        width: 66px;
        height: 66px;
        border: 1px solid #e1dcff;
        border-radius: 20px;
        background: linear-gradient(145deg, #f0edff, #e0dcff);
        color: #5746cf;
        font-size: 22px;
        font-weight: 800;
    }

    .td-profile-intro-text {
        min-width: 0;
    }

    .td-profile-intro-text h4 {
        margin: 0 0 6px;
        color: #202943;
        font-size: 17px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .td-profile-intro-text p {
        margin: 0 0 7px;
        color: #6e7890;
        font-size: 11px;
        overflow-wrap: anywhere;
    }

    .td-profile-intro-text > span {
        color: #9299aa;
        font-size: 10px;
        font-weight: 600;
    }

    .td-profile-divider {
        height: 1px;
        margin: 0 24px;
        background: #edf0f6;
    }

    .td-details-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 23px 18px;
        padding: 25px 24px;
    }

    .td-detail-item {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        min-width: 0;
    }

    .td-detail-full {
        grid-column: 1 / -1;
    }

    .td-detail-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 35px;
        width: 35px;
        height: 35px;
        border-radius: 11px;
        font-size: 14px;
    }

    .td-detail-blue {
        background: #e8f3ff;
        color: #3279d5;
    }

    .td-detail-purple {
        background: #f0edff;
        color: #6857e8;
    }

    .td-detail-green {
        background: #e4f8f0;
        color: #159b72;
    }

    .td-detail-orange {
        background: #fff1df;
        color: #dc8b22;
    }

    .td-detail-red {
        background: #ffeded;
        color: #d85c65;
    }

    .td-detail-content {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
        padding-top: 2px;
    }

    .td-detail-content > span {
        color: #8b93a7;
        font-size: 10px;
        font-weight: 600;
    }

    .td-detail-content strong {
        color: #313b54;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    .td-email-value {
        color: #6251d7 !important;
    }

    .td-profile-footer {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 17px 24px;
        border-top: 1px solid #edf0f6;
        background: #fafaff;
    }

    .td-verified-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 32px;
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: #e5f8f0;
        color: #159b72;
        font-size: 15px;
    }

    .td-profile-footer strong {
        display: block;
        margin: 1px 0 4px;
        color: #39425b;
        font-size: 11px;
        font-weight: 800;
    }

    .td-profile-footer p {
        margin: 0;
        color: #838ca1;
        font-size: 10px;
        line-height: 1.7;
    }

    /* Teaching overview */
    .td-activity-list {
        padding: 6px 24px 13px;
    }

    .td-activity-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 17px 0;
        border-bottom: 1px solid #f0f2f7;
    }

    .td-activity-item:last-child {
        border-bottom: 0;
    }

    .td-activity-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        border-radius: 13px;
        font-size: 17px;
    }

    .td-activity-copy {
        flex: 1;
        min-width: 0;
    }

    .td-activity-copy h6 {
        margin: 0 0 5px;
        color: #303a53;
        font-size: 11px;
        font-weight: 800;
    }

    .td-activity-copy p {
        margin: 0;
        color: #8a92a5;
        font-size: 10px;
        line-height: 1.6;
    }

    .td-activity-count {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 35px;
        height: 32px;
        padding: 0 8px;
        border: 1px solid #eceef5;
        border-radius: 9px;
        background: #fafbfe;
        color: #303a53;
        font-size: 12px;
        font-weight: 800;
    }

    /* Security card */
    .td-security-card {
        padding: 24px;
        border: 1px solid #e6e2ff;
        border-radius: 19px;
        background: linear-gradient(145deg, #f8f7ff 0%, #fff 90%);
        box-shadow: 0 5px 24px rgba(27, 39, 76, .025);
    }

    .td-security-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
    }

    .td-security-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 47px;
        height: 47px;
        border: 1px solid #e1dcff;
        border-radius: 15px;
        background: #efecff;
        color: #6857e8;
        font-size: 21px;
    }

    .td-security-badge {
        display: inline-flex;
        align-items: center;
        padding: 7px 10px;
        border: 1px solid #d8f1e5;
        border-radius: 8px;
        background: #edfaf3;
        color: #13845d;
        font-size: 10px;
        font-weight: 800;
    }

    .td-security-card h5 {
        margin: 0 0 10px;
        color: #252e49;
        font-size: 15px;
        font-weight: 800;
    }

    .td-security-card > p {
        margin: 0;
        color: #7b849a;
        font-size: 11px;
        line-height: 1.9;
    }

    .td-security-account {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-top: 20px;
        padding: 13px;
        border: 1px solid #ece9ff;
        border-radius: 12px;
        background: rgba(255,255,255,.8);
    }

    .td-security-account-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 36px;
        width: 36px;
        height: 36px;
        border-radius: 11px;
        background: #f0edff;
        color: #6857e8;
        font-size: 16px;
    }

    .td-security-account > div:last-child {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 0;
    }

    .td-security-account span {
        color: #9299aa;
        font-size: 10px;
    }

    .td-security-account strong {
        color: #39425b;
        font-size: 11px;
        overflow-wrap: anywhere;
    }

    /* Footer */
    .td-dashboard-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 13px;
        margin-top: 28px;
        padding: 18px 2px 6px;
        border-top: 1px solid var(--td-line);
        color: #9299aa;
        font-size: 10px;
    }

    .td-footer-brand {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #566079;
        font-weight: 800;
    }

    .td-footer-brand-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 27px;
        height: 27px;
        border-radius: 8px;
        background: #f0edff;
        color: #6857e8;
    }

    .td-dashboard-footer > span i {
        color: #d87583;
        font-size: 9px;
    }

    @media (max-width: 767.98px) {
        .td-hero-content {
            padding: 27px 23px 24px;
        }

        .td-hero-avatar-wrap {
            display: none;
        }

        .td-hero-copy h2 {
            margin-top: 17px;
        }

        .td-hero-bottom {
            padding: 13px 23px;
        }

        .td-panel-header {
            padding: 20px;
        }

        .td-profile-intro {
            padding: 22px 20px;
        }

        .td-profile-divider {
            margin: 0 20px;
        }

        .td-details-grid {
            gap: 20px 13px;
            padding: 22px 20px;
        }

        .td-profile-footer {
            padding: 16px 20px;
        }

        .td-activity-list {
            padding-right: 20px;
            padding-left: 20px;
        }

        .td-security-card {
            padding: 21px;
        }
    }

    @media (max-width: 479.98px) {
        .td-details-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .td-detail-full {
            grid-column: auto;
        }

        .td-hero-meta {
            gap: 9px;
        }

        .td-dashboard-footer {
            justify-content: flex-start;
        }

        .td-dashboard-footer > span:last-child {
            margin-left: auto;
        }

        .td-stat-card {
            padding: 19px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .td-stat-card {
            transition: none;
        }
    }
</style>


</div>

