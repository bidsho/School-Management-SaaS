<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Student Dashboard' }} | School Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    @livewireStyles

    <style>
        :root {
            --student-primary: #3457d5;
            --student-primary-dark: #263fa8;
            --student-sidebar-width: 260px;
            --student-bg: #f5f7fb;
            --student-text: #1e293b;
            --student-muted: #64748b;
            --student-border: #e8edf5;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--student-bg);
            color: var(--student-text);
            font-family: Inter, -apple-system, BlinkMacSystemFont,
                         "Segoe UI", sans-serif;
            font-size: 14px;
        }

        a {
            text-decoration: none;
        }

        /* Sidebar */
        .student-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--student-sidebar-width);
            background: #fff;
            border-right: 1px solid var(--student-border);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .25s ease;
        }

        .student-brand {
            min-height: 78px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 22px;
            border-bottom: 1px solid var(--student-border);
            color: var(--student-text);
        }

        /* School logo */
        .student-brand-icon {
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 13px;
            display: grid;
            place-items: center;
            overflow: hidden;
            background: var(--student-primary);
            color: white;
            font-size: 22px;
        }

        .student-brand-icon img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: contain;
            padding: 3px;
            background: #fff;
        }

        .student-brand-details {
            min-width: 0;
            overflow: hidden;
        }

        .student-brand-name {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -.4px;
        }

        .student-brand-caption {
            color: var(--student-muted);
            font-size: 11px;
            margin-top: 2px;
        }

        .student-sidebar-body {
            padding: 25px 14px;
            overflow-y: auto;
            flex: 1;
        }

        .student-nav-label {
            color: #94a3b8;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
            padding: 0 12px;
            margin: 0 0 11px;
        }

        .student-nav-link {
            display: flex;
            align-items: center;
            gap: 13px;
            min-height: 45px;
            padding: 11px 13px;
            margin-bottom: 5px;
            border-radius: 10px;
            color: #64748b;
            font-weight: 600;
            transition: background .2s, color .2s;
        }

        .student-nav-link i {
            font-size: 18px;
            width: 21px;
            text-align: center;
        }

        .student-nav-link:hover {
            color: var(--student-primary);
            background: #f0f4ff;
        }

        .student-nav-link.active {
            color: var(--student-primary);
            background: #edf2ff;
            font-weight: 700;
        }

        .student-sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--student-border);
        }

        .student-help-card {
            background: #f5f7ff;
            border: 1px solid #e7ecff;
            border-radius: 13px;
            padding: 15px;
        }

        .student-help-card .help-icon {
            color: var(--student-primary);
            font-size: 22px;
        }

        /* Main content */
        .student-main {
            min-height: 100vh;
            margin-left: var(--student-sidebar-width);
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .student-topbar {
            min-height: 78px;
            background: #fff;
            border-bottom: 1px solid var(--student-border);
            padding: 14px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .student-search {
            position: relative;
            width: min(360px, 100%);
        }

        .student-search i {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .student-search input {
            width: 100%;
            border: 1px solid #edf0f6;
            background: #f8faff;
            border-radius: 10px;
            padding: 11px 14px 11px 40px;
            outline: none;
            font-size: 13px;
        }

        .student-search input:focus {
            border-color: #a5b8ff;
            background: #fff;
        }

        .student-topbar-right {
            display: flex;
            align-items: center;
            gap: 19px;
        }

        .student-icon-button {
            width: 39px;
            height: 39px;
            border: 1px solid var(--student-border);
            background: #fff;
            color: #64748b;
            border-radius: 11px;
            display: grid;
            place-items: center;
            font-size: 18px;
        }

        .student-icon-button:hover {
            background: #f5f7ff;
            color: var(--student-primary);
        }

        /* User profile */
        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: #e8edff;
            color: var(--student-primary);
            font-size: 15px;
            font-weight: 800;
        }

        .student-user-name {
            font-size: 13px;
            font-weight: 700;
        }

        .student-user-role {
            font-size: 11px;
            color: var(--student-muted);
            margin-top: 2px;
        }

        /* Page content */
        .student-content {
            padding: 30px;
            flex: 1;
        }

        .student-page-footer {
            padding: 18px 30px;
            color: var(--student-muted);
            border-top: 1px solid var(--student-border);
            background: #fff;
            font-size: 12px;
        }

        /* Mobile sidebar overlay */
        .student-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .45);
            z-index: 1030;
        }

        /* Tablet */
        @media (max-width: 991.98px) {
            .student-sidebar {
                transform: translateX(-100%);
            }

            .student-sidebar.show {
                transform: translateX(0);
            }

            .student-overlay.show {
                display: block;
            }

            .student-main {
                margin-left: 0;
            }

            .student-topbar {
                padding: 14px 20px;
            }

            .student-content {
                padding: 24px 20px;
            }

            .student-page-footer {
                padding: 18px 20px;
            }

            .student-search {
                width: 230px;
            }
        }

        /* Mobile */
        @media (max-width: 575.98px) {
            .student-topbar {
                gap: 10px;
                min-height: 68px;
                padding: 12px 15px;
            }

            .student-search {
                display: none;
            }

            .student-topbar-right {
                gap: 10px;
            }

            .student-content {
                padding: 20px 15px;
            }

            .student-user-details {
                display: none;
            }
        }
    </style>
</head>

<body>

@php
    $currentUser = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | School Logo
    |--------------------------------------------------------------------------
    | Get the authenticated student's school settings.
    | The logo is stored on Laravel's public storage disk.
    */

    $schoolSettings = $currentUser?->school_id
        ? \App\Models\SchoolSetting::where(
            'school_id',
            $currentUser->school_id
        )->first()
        : null;

    $schoolLogo = $schoolSettings?->logo;

    $schoolLogoExists = $schoolLogo
        && \Illuminate\Support\Facades\Storage::disk('public')
            ->exists($schoolLogo);

    $schoolLogoUrl = $schoolLogoExists
        ? \Illuminate\Support\Facades\Storage::disk('public')
            ->url($schoolLogo)
        : null;

    $schoolName = $currentUser?->school?->name ?? 'School Portal';

    /*
    |--------------------------------------------------------------------------
    | Student Initials
    |--------------------------------------------------------------------------
    */

    $studentInitials = collect(
        explode(' ', trim($currentUser->name ?? 'Student'))
    )
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');
@endphp


{{-- Mobile sidebar overlay --}}
<div class="student-overlay"
     id="studentOverlay"
     onclick="toggleStudentSidebar(false)">
</div>


{{-- Sidebar --}}
<aside class="student-sidebar" id="studentSidebar">

    {{-- School Brand and Logo --}}
    <a href="{{ route('student.dashboard') }}" class="student-brand">

        <div class="student-brand-icon">

            @if ($schoolLogoExists)
                <img src="{{ $schoolLogoUrl }}"
                     alt="{{ $schoolName }} Logo">
            @else
                <i class="bi bi-mortarboard-fill"></i>
            @endif

        </div>

        <div class="student-brand-details">

            <div class="student-brand-name text-truncate"
                 title="{{ $schoolName }}">
                {{ $schoolName }}
            </div>

            <div class="student-brand-caption">
                Student learning space
            </div>

        </div>

    </a>


    {{-- Sidebar Navigation --}}
    <div class="student-sidebar-body">

        <div class="student-nav-label">
            MAIN MENU
        </div>

        <a href="{{ route('student.dashboard') }}"
           class="student-nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">

            <i class="bi bi-grid-1x2"></i>
            <span>Dashboard</span>

        </a>


        {{-- Academics --}}
        <div class="student-nav-label mt-4">
            MY ACADEMICS
        </div>

        <a href="{{ route('student.attendance') }}"
           class="student-nav-link {{ request()->routeIs('student.attendance') ? 'active' : '' }}">

            <i class="bi bi-calendar2-check"></i>
            <span>My Attendance</span>

        </a>

        <a href="{{ route('student.exam-results') }}"
           class="student-nav-link {{ request()->routeIs('student.exam-results') ? 'active' : '' }}">

            <i class="bi bi-journal-check"></i>
            <span>Exam Results</span>

        </a>

        <a href="{{ route('student.class-timetable') }}"
           class="student-nav-link {{ request()->routeIs('student.class-timetable') ? 'active' : '' }}">

            <i class="bi bi-calendar-week"></i>
            <span>Class Timetable</span>

        </a>

        <a href="{{ route('student.class-teachers') }}"
           class="student-nav-link {{ request()->routeIs('student.class-teachers') ? 'active' : '' }}">

            <i class="bi bi-people"></i>
            <span>My Class & Teachers</span>

        </a>


        {{-- Finance --}}
        <div class="student-nav-label mt-4">
            FINANCE
        </div>

        <a href="{{ route('student.invoices.index') }}"
           class="student-nav-link {{ request()->routeIs('student.invoices.*') ? 'active' : '' }}">

            <i class="bi bi-receipt"></i>
            <span>My Invoices</span>

        </a>

        <a href="{{ route('student.payment-history') }}"
           class="student-nav-link {{ request()->routeIs('student.payment-history') ? 'active' : '' }}">

            <i class="bi bi-clock-history"></i>
            <span>Payment History</span>

        </a>


        {{-- Account --}}
        <div class="student-nav-label mt-4">
            ACCOUNT
        </div>

        <a href="{{ route('student.profile') }}"
           class="student-nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}">

            <i class="bi bi-person-circle"></i>
            <span>My Profile</span>

        </a>

    </div>


    {{-- Sidebar Footer --}}
    <div class="student-sidebar-footer">

        <div class="student-help-card">

            <div class="d-flex align-items-center gap-2 mb-2">

                <i class="bi bi-headset help-icon"></i>

                <strong class="small">
                    Need assistance?
                </strong>

            </div>

            <p class="text-secondary small mb-0">
                Contact your school administrator for help with your account.
            </p>

        </div>

    </div>

</aside>


{{-- Main Content --}}
<div class="student-main">

    {{-- Topbar --}}
    <header class="student-topbar">

        <div class="d-flex align-items-center gap-3 flex-grow-1">

            {{-- Mobile Menu Button --}}
            <button type="button"
                    class="student-icon-button d-lg-none"
                    aria-label="Open navigation"
                    onclick="toggleStudentSidebar()">

                <i class="bi bi-list"></i>

            </button>

            {{-- Search --}}
            <div class="student-search">

                <i class="bi bi-search"></i>

                <input type="search"
                       placeholder="Search your portal..."
                       aria-label="Search your portal">

            </div>

        </div>


        <div class="student-topbar-right">

            {{-- Notifications --}}
            <div class="dropdown">

                <button class="student-icon-button position-relative"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        aria-label="Notifications">

                    <i class="bi bi-bell"></i>

                </button>

                <div class="dropdown-menu dropdown-menu-end border-0 shadow-sm p-3"
                     style="width: 260px;">

                    <strong>Notifications</strong>

                    <p class="text-secondary small mt-2 mb-0">
                        You are all caught up.
                    </p>

                </div>

            </div>


            {{-- User Profile Dropdown --}}
            <div class="dropdown">

                <button class="btn p-0 border-0 bg-transparent d-flex align-items-center gap-2"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                    <div class="student-avatar">
                        {{ $studentInitials ?: 'S' }}
                    </div>

                    <div class="student-user-details text-start">

                        <div class="student-user-name">
                            {{ $currentUser->name }}
                        </div>

                        <div class="student-user-role">
                            Student
                        </div>

                    </div>

                    <i class="bi bi-chevron-down small text-secondary"></i>

                </button>


                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-3">

                    <li>

                        <span class="dropdown-item-text small text-secondary">
                            {{ $currentUser->email }}
                        </span>

                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>

                        <form method="POST" action="{{ route('logout') }}">

                            @csrf

                            <button type="submit"
                                    class="dropdown-item text-danger">

                                <i class="bi bi-box-arrow-right me-2"></i>
                                Sign out

                            </button>

                        </form>

                    </li>

                </ul>

            </div>

        </div>

    </header>


    {{-- Page Content --}}
    <main class="student-content">

        @if (session()->has('success'))

            <div class="alert alert-success alert-dismissible fade show rounded-3"
                 role="alert">

                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                </button>

            </div>

        @endif

        {{ $slot }}

    </main>


    {{-- Footer --}}
    <footer class="student-page-footer d-flex flex-wrap justify-content-between gap-2">

        <span>
            &copy; {{ date('Y') }} School Portal. All rights reserved.
        </span>

        <span>
            Built for learning and academic success.
        </span>

    </footer>

</div>


{{-- Bootstrap --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


{{-- Sidebar Script --}}
<script>

    function toggleStudentSidebar(forceState) {

        const sidebar = document.getElementById('studentSidebar');
        const overlay = document.getElementById('studentOverlay');

        if (!sidebar || !overlay) return;

        const shouldOpen = typeof forceState === 'boolean'
            ? forceState
            : !sidebar.classList.contains('show');

        sidebar.classList.toggle('show', shouldOpen);
        overlay.classList.toggle('show', shouldOpen);

    }


    document.querySelectorAll('#studentSidebar a').forEach(link => {

        link.addEventListener('click', () => {

            if (window.innerWidth < 992) {
                toggleStudentSidebar(false);
            }

        });

    });

</script>

@livewireScripts

</body>
</html>