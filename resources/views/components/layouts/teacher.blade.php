
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Teacher Portal' }} | {{ config('app.name', 'School Management SaaS') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @livewireStyles

    <style>
        :root {
            --teacher-primary: #4f46e5;
            --teacher-primary-dark: #3730a3;
            --teacher-sidebar-width: 265px;
            --teacher-header-height: 72px;
            --teacher-bg: #f5f7fb;
            --teacher-text: #1f2937;
            --teacher-muted: #64748b;
            --teacher-border: #e8edf4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--teacher-bg);
            color: var(--teacher-text);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }

        a {
            text-decoration: none;
        }

        /* Sidebar */

        .teacher-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--teacher-sidebar-width);
            background: #fff;
            border-right: 1px solid var(--teacher-border);
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: transform .25s ease;
        }

        /* School Branding */

        .teacher-brand {
            min-height: var(--teacher-header-height);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 23px;
            border-bottom: 1px solid var(--teacher-border);
            color: var(--teacher-text);
            flex-shrink: 0;
        }

        .teacher-brand-icon {
            height: 44px;
            width: 44px;
            min-width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(135deg, #6366f1, #4338ca);
            font-size: 21px;
        }

        .teacher-brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 3px;
            background: #fff;
        }

        .teacher-brand-name {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: -.4px;
            line-height: 1.3;
            color: #111827;
            overflow-wrap: anywhere;
        }

        .teacher-brand-subtitle {
            display: block;
            color: var(--teacher-muted);
            font-size: 10px;
            font-weight: 600;
            margin-top: 3px;
            letter-spacing: .5px;
        }

        /* Sidebar Navigation */

        .teacher-sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 22px 14px;
        }

        .teacher-nav-label {
            padding: 0 12px;
            margin: 17px 0 9px;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.1px;
            text-transform: uppercase;
        }

        .teacher-nav-label:first-child {
            margin-top: 0;
        }

        .teacher-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 13px;
            margin-bottom: 4px;
            border-radius: 10px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            transition: all .2s ease;
        }

        .teacher-nav-link i {
            font-size: 17px;
            width: 21px;
            text-align: center;
        }

        .teacher-nav-link:hover {
            color: var(--teacher-primary);
            background: #f1f2ff;
        }

        .teacher-nav-link.active {
            color: var(--teacher-primary);
            background: #eef2ff;
            font-weight: 700;
        }

        /* Account Footer */

        .teacher-sidebar-footer {
            padding: 15px;
            border-top: 1px solid var(--teacher-border);
        }

        .teacher-account-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 12px;
            background: #f8fafc;
        }

        .teacher-avatar {
            width: 39px;
            height: 39px;
            min-width: 39px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #fff;
            background: var(--teacher-primary);
            font-size: 13px;
            font-weight: 800;
        }

        .teacher-account-name {
            overflow: hidden;
            color: var(--teacher-text);
            font-size: 12px;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .teacher-account-role {
            color: var(--teacher-muted);
            font-size: 10px;
            margin-top: 3px;
        }

        /* Main Content */

        .teacher-main {
            min-height: 100vh;
            margin-left: var(--teacher-sidebar-width);
        }

        .teacher-topbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            height: var(--teacher-header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 0 30px;
            background: rgba(255, 255, 255, .96);
            border-bottom: 1px solid var(--teacher-border);
            backdrop-filter: blur(12px);
        }

        .teacher-topbar-title {
            font-size: 16px;
            font-weight: 800;
            margin: 0;
        }

        .teacher-topbar-subtitle {
            color: var(--teacher-muted);
            font-size: 11px;
            margin-top: 3px;
        }

        .teacher-topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .teacher-icon-button {
            width: 39px;
            height: 39px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--teacher-border);
            border-radius: 11px;
            color: #475569;
            background: #fff;
            font-size: 17px;
        }

        .teacher-icon-button:hover {
            color: var(--teacher-primary);
            background: #f8fafc;
        }

        .teacher-page-content {
            padding: 26px 30px;
            min-height: calc(100vh - var(--teacher-header-height));
        }

        .teacher-mobile-toggle {
            display: none;
        }

        .teacher-sidebar-overlay {
            display: none;
        }

        .teacher-logout-button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 13px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: #dc2626;
            font-size: 13px;
            font-weight: 600;
            text-align: left;
        }

        .teacher-logout-button:hover {
            background: #fef2f2;
        }

        .teacher-logout-button i {
            width: 21px;
            font-size: 17px;
            text-align: center;
        }

        .teacher-content-footer {
            padding: 20px 0 0;
            margin-top: 25px;
            border-top: 1px solid var(--teacher-border);
            color: var(--teacher-muted);
            font-size: 11px;
        }

        /* Responsive */

        @media (max-width: 991.98px) {
            .teacher-sidebar {
                transform: translateX(-100%);
            }

            .teacher-sidebar.is-open {
                transform: translateX(0);
            }

            .teacher-sidebar-overlay.is-open {
                display: block;
                position: fixed;
                inset: 0;
                z-index: 1030;
                background: rgba(15, 23, 42, .45);
            }

            .teacher-main {
                margin-left: 0;
            }

            .teacher-mobile-toggle {
                display: inline-flex;
            }

            .teacher-topbar {
                padding: 0 20px;
            }

            .teacher-page-content {
                padding: 22px 20px;
            }
        }

        @media (max-width: 575.98px) {
            .teacher-topbar {
                height: 64px;
                padding: 0 14px;
            }

            .teacher-topbar-title {
                font-size: 14px;
            }

            .teacher-topbar-subtitle {
                display: none;
            }

            .teacher-page-content {
                padding: 17px 13px;
            }

            .teacher-topbar-actions {
                gap: 7px;
            }
        }
    </style>
</head>

<body>

    @php
        $teacherUser = auth()->user();

        $teacherName = $teacherUser?->name ?? 'Teacher';

        $teacherInitials = collect(explode(' ', trim($teacherName)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');

        $currentRoute = request()->route()?->getName() ?? '';

        /*
        |--------------------------------------------------------------------------
        | School Logo
        |--------------------------------------------------------------------------
        | Get the logo using the authenticated teacher's school_id.
        | This ensures each teacher sees their own school's logo.
        */

        $schoolSettings = $teacherUser?->school_id
            ? \App\Models\SchoolSetting::where('school_id', $teacherUser->school_id)->first()
            : null;

        $schoolLogo = $schoolSettings?->logo;

        $schoolName = $teacherUser?->school?->name ?? 'School Portal';

        $schoolLogoUrl = $schoolLogo
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($schoolLogo)
            : null;

        $hasSchoolLogo = $schoolLogo
            && \Illuminate\Support\Facades\Storage::disk('public')->exists($schoolLogo);
    @endphp

    {{-- Mobile Overlay --}}

    <div
        class="teacher-sidebar-overlay"
        id="teacherSidebarOverlay"
        onclick="toggleTeacherSidebar(false)"
    ></div>

    {{-- Sidebar --}}

    <aside class="teacher-sidebar" id="teacherSidebar">

        {{-- School Brand --}}

        <a href="{{ route('teacher.dashboard') }}" class="teacher-brand">

            <div class="teacher-brand-icon">

                @if ($hasSchoolLogo)
                    <img
                        src="{{ $schoolLogoUrl }}"
                        alt="{{ $schoolName }} Logo"
                    >
                @else
                    <i class="bi bi-mortarboard-fill"></i>
                @endif

            </div>

            <div class="overflow-hidden">

                <div class="teacher-brand-name">
                    {{ $schoolName }}
                </div>

                <span class="teacher-brand-subtitle">
                    TEACHER WORKSPACE
                </span>

            </div>

        </a>

        {{-- Navigation --}}

        <div class="teacher-sidebar-content">

            <div class="teacher-nav-label">Workspace</div>

            <a href="{{ route('teacher.dashboard') }}"
               class="teacher-nav-link {{ $currentRoute === 'teacher.dashboard' ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <div class="teacher-nav-label">Teaching</div>

            <a href="{{ route('teacher.classes') }}"
               class="teacher-nav-link {{ request()->routeIs('teacher.classes') ? 'active' : '' }}">
                <i class="bi bi-easel2"></i>
                <span>My Classes & Students</span>
            </a>

            <a href="{{ route('teacher.timetable') }}"
               class="teacher-nav-link {{ request()->routeIs('teacher.timetable') ? 'active' : '' }}">
                <i class="bi bi-calendar-week"></i>
                <span>My Timetable</span>
            </a>

            <div class="teacher-nav-label">Academic</div>

            <a href="{{ route('teacher.attendance') }}"
               class="teacher-nav-link {{ request()->routeIs('teacher.attendance') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Take Attendance</span>
            </a>

            <a href="{{ route('teacher.enter-scores') }}"
               class="teacher-nav-link {{ request()->routeIs('teacher.enter-scores') ? 'active' : '' }}">
                <i class="bi bi-journal-check"></i>
                <span>Enter Scores</span>
            </a>

            <a href="{{ route('teacher.promotions.recommendations') }}"
               class="teacher-nav-link {{ request()->routeIs('teacher.promotions.recommendations') ? 'active' : '' }}">
                <i class="bi bi-arrow-up-square"></i>
                <span>Promotion Recommendations</span>
            </a>

            <div class="teacher-nav-label">Account</div>

            {{-- Add your teacher profile route here when available. --}}

        </div>

        {{-- Account Footer --}}

        <div class="teacher-sidebar-footer">

            <div class="teacher-account-card mb-2">

                <div class="teacher-avatar">
                    {{ $teacherInitials ?: 'T' }}
                </div>

                <div class="flex-grow-1 overflow-hidden">

                    <div class="teacher-account-name">
                        {{ $teacherName }}
                    </div>

                    <div class="teacher-account-role">
                        Teacher Account
                    </div>

                </div>

            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="teacher-logout-button">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
                </button>

            </form>

        </div>

    </aside>

    {{-- Main Content --}}

    <main class="teacher-main">

        {{-- Topbar --}}

        <header class="teacher-topbar">

            <div class="d-flex align-items-center gap-3">

                <button
                    type="button"
                    class="teacher-icon-button teacher-mobile-toggle"
                    onclick="toggleTeacherSidebar()"
                    aria-label="Toggle navigation"
                >
                    <i class="bi bi-list"></i>
                </button>

                <div>

                    <h1 class="teacher-topbar-title">
                        {{ $title ?? 'Teacher Dashboard' }}
                    </h1>

                    <div class="teacher-topbar-subtitle">
                        Your teaching workspace
                    </div>

                </div>

            </div>

            <div class="teacher-topbar-actions">

                <div class="d-none d-sm-block text-end">

                    <div class="fw-bold small">
                        {{ $teacherName }}
                    </div>

                    <div class="text-muted" style="font-size: 11px;">
                        Teacher
                    </div>

                </div>

                <div class="teacher-avatar">
                    {{ $teacherInitials ?: 'T' }}
                </div>

            </div>

        </header>

        {{-- Livewire Page Content --}}

        <section class="teacher-page-content">

            {{ $slot }}

            <footer class="teacher-content-footer d-flex flex-wrap justify-content-between gap-2">

                <span>
                    &copy; {{ now()->year }}
                    {{ config('app.name', 'School Management SaaS') }}
                </span>

                <span>
                    <i class="bi bi-shield-check me-1"></i>
                    Teacher Portal
                </span>

            </footer>

        </section>

    </main>

    {{-- Bootstrap JS --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleTeacherSidebar(forceState) {
            const sidebar = document.getElementById('teacherSidebar');
            const overlay = document.getElementById('teacherSidebarOverlay');

            if (!sidebar || !overlay) {
                return;
            }

            const shouldOpen = typeof forceState === 'boolean'
                ? forceState
                : !sidebar.classList.contains('is-open');

            sidebar.classList.toggle('is-open', shouldOpen);
            overlay.classList.toggle('is-open', shouldOpen);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                toggleTeacherSidebar(false);
            }
        });

        document.querySelectorAll('#teacherSidebar a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) {
                    toggleTeacherSidebar(false);
                }
            });
        });
    </script>

    @livewireScripts

</body>
</html>
