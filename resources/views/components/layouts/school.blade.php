
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'School Dashboard' }} | {{ config('app.name', 'School SaaS') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    @livewireStyles

    <style>
        :root {
            --school-primary: #4f46e5;
            --school-primary-dark: #3730a3;
            --school-sidebar-width: 265px;
            --school-bg: #f5f7fb;
            --school-text: #1f2937;
            --school-muted: #64748b;
            --school-border: #e9edf4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--school-bg);
            color: var(--school-text);
            font-family: Inter, -apple-system, BlinkMacSystemFont,
                "Segoe UI", sans-serif;
            font-size: 14px;
        }

        a {
            text-decoration: none;
        }

        /* Sidebar */

        .school-sidebar {
            width: var(--school-sidebar-width);
            background: #fff;
            border-right: 1px solid var(--school-border);
            padding: 24px 16px;
            z-index: 1045;
        }

        .school-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 10px 25px;
            border-bottom: 1px solid var(--school-border);
        }

        .school-brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--school-primary);
            color: #fff;
            font-size: 22px;
            flex-shrink: 0;
        }

        .school-brand-name {
            font-size: 16px;
            font-weight: 800;
            color: #111827;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }

        .school-brand-caption {
            font-size: 11px;
            color: var(--school-muted);
            margin-top: 3px;
        }

        .school-nav-label {
            padding: 23px 12px 9px;
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .school-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            margin: 4px 0;
            border-radius: 10px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            transition: all .2s ease;
        }

        .school-nav-link i {
            font-size: 18px;
            flex-shrink: 0;
        }

        .school-nav-link:hover {
            background: #f5f6ff;
            color: var(--school-primary);
        }

        .school-nav-link.active {
            color: #fff;
            background: var(--school-primary);
            box-shadow: 0 5px 14px rgba(79, 70, 229, .18);
        }

        .school-nav-link.active:hover {
            color: #fff;
            background: var(--school-primary-dark);
        }

        .school-sidebar-bottom {
            margin-top: 28px;
            padding: 16px;
            background: #f8f9ff;
            border: 1px solid #e8eaff;
            border-radius: 14px;
        }

        /* Main content */

        .school-main {
            min-height: 100vh;
            margin-left: var(--school-sidebar-width);
        }

        .school-topbar {
            min-height: 78px;
            background: rgba(255, 255, 255, .96);
            border-bottom: 1px solid var(--school-border);
            padding: 14px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .school-content {
            padding: 30px;
            max-width: 1800px;
            margin: 0 auto;
        }

        .school-search {
            position: relative;
            width: min(300px, 30vw);
        }

        .school-search i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .school-search input {
            width: 100%;
            border: 1px solid var(--school-border);
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px 12px 10px 37px;
            font-size: 13px;
        }

        .school-search input:focus {
            outline: none;
            border-color: #a5b4fc;
            background: #fff;
        }

        .school-user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #e0e7ff;
            color: var(--school-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            flex-shrink: 0;
        }

        .school-user-name {
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
        }

        .school-user-role {
            color: var(--school-muted);
            font-size: 11px;
            margin-top: 2px;
        }

        .school-footer {
            padding: 20px 30px;
            color: #94a3b8;
            font-size: 12px;
        }

        .school-mobile-brand {
            display: none;
        }

        /* Responsive sidebar */

        @media (max-width: 991.98px) {
            .school-sidebar {
                width: 280px;
            }

            .school-main {
                margin-left: 0;
            }

            .school-topbar {
                padding: 14px 20px;
            }

            .school-content {
                padding: 22px 20px;
            }

            .school-mobile-brand {
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 800;
            }
        }

        @media (max-width: 575.98px) {
            .school-topbar {
                padding: 12px 14px;
                min-height: 68px;
            }

            .school-content {
                padding: 18px 14px;
            }

            .school-search {
                display: none;
            }

            .school-user-details {
                display: none;
            }

            .school-footer {
                padding: 18px 14px;
            }
        }
    </style>
</head>

<body>

@php
    $currentSchool = request()->attributes->get('currentSchool');
    $currentUser = auth()->user();

    // Get the current school's settings
    $schoolSettings = $currentSchool
        ? \App\Models\SchoolSetting::where('school_id', $currentSchool->id)->first()
        : null;

    $schoolLogo = $schoolSettings?->logo;

    $userInitials = collect(explode(' ', trim($currentUser?->name ?? 'User')))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');
@endphp

{{-- Sidebar --}}

<aside class="school-sidebar offcanvas-lg offcanvas-start position-fixed top-0 bottom-0 overflow-y-auto"
       tabindex="-1"
       id="schoolSidebar"
       aria-labelledby="schoolSidebarLabel">

    <div class="offcanvas-header d-lg-none">
        <h5 class="offcanvas-title" id="schoolSidebarLabel">School Menu</h5>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
                data-bs-target="#schoolSidebar"
                aria-label="Close"></button>
    </div>

    <div class="offcanvas-body d-block p-0">

        {{-- School Branding --}}

        <a href="{{ route('school.dashboard') }}" class="school-brand">

           @if ($schoolLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($schoolLogo))
    <img
        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($schoolLogo) }}"
        alt="{{ $currentSchool?->name ?? 'School' }} Logo"
        class="school-brand-icon"
        style="object-fit: contain; background: #fff; padding: 3px;"
    >
@else
    <div class="school-brand-icon">
        <i class="bi bi-mortarboard-fill"></i>
    </div>
@endif

            <div>
                <div class="school-brand-name">
                    {{ $currentSchool?->name ?? 'School Portal' }}
                </div>

                <div class="school-brand-caption">MANAGEMENT PORTAL</div>
            </div>

        </a>

        {{-- Main Menu --}}

        <div class="school-nav-label">Main Menu</div>

        <a href="{{ route('school.dashboard') }}"
           class="school-nav-link {{ request()->routeIs('school.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        {{-- Parents --}}

        @can('view parents')
            <a href="{{ route('school.parents.index') }}"
               class="school-nav-link {{ request()->routeIs('school.parents.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Parents</span>
            </a>
        @endcan

        {{-- Students --}}

        @can('view students')
            <a href="{{ route('school.students.index') }}"
               class="school-nav-link {{ request()->routeIs('school.students.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Students</span>
            </a>
        @endcan

        {{-- Enrollments --}}

        @can('manage admissions')
            <a href="{{ route('school.enrollments.index') }}"
               class="school-nav-link {{ request()->routeIs('school.enrollments.*') ? 'active' : '' }}">
                <i class="bi bi-person-vcard-fill"></i>
                <span>Enrollments</span>
            </a>
        @endcan

        {{-- Treasury --}}

        @can('record payments')
            @if (\Illuminate\Support\Facades\Route::has('school.treasuries.index'))
                <a href="{{ route('school.treasuries.index') }}"
                   class="school-nav-link {{ request()->routeIs('school.treasuries.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i>
                    <span>Treasury</span>
                </a>
            @endif
        @endcan

        {{-- Bank Details --}}

        @can('manage school settings')
            <a href="{{ route('school.settings.bank-details') }}"
               class="school-nav-link {{ request()->routeIs('school.settings.bank-details') ? 'active' : '' }}">
                <i class="bi bi-bank"></i>
                <span>Bank Details</span>
            </a>
        @endcan


        {{-- School Management --}}

        <div class="school-nav-label">School Management</div>

        {{-- Teachers --}}

        @can('view teachers')
            <a href="{{ route('school.teachers.index') }}"
               class="school-nav-link {{ request()->routeIs('school.teachers.*') ? 'active' : '' }}">
                <i class="bi bi-person-workspace"></i>
                <span>Teachers</span>
            </a>
        @endcan

        {{-- Principals and Receptionists --}}

        @role('School Admin|Principal')

            <a href="{{ route('school.principals.index') }}"
               class="school-nav-link {{ request()->routeIs('school.principals.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Manage Principals</span>
            </a>

            <a href="{{ route('school.receptionists.index') }}"
               class="school-nav-link {{ request()->routeIs('school.receptionists.*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i>
                <span>Manage Receptionists</span>
            </a>

        @endrole

        {{-- Classes --}}

        @can('manage classes')

            <a href="{{ route('school.classes.index') }}"
               class="school-nav-link {{ request()->routeIs('school.classes.*') ? 'active' : '' }}">
                <i class="bi bi-door-open"></i>
                <span>Classes</span>
            </a>

            <a href="{{ route('school.sections.index') }}"
               class="school-nav-link {{ request()->routeIs('school.sections.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i>
                <span>Sections</span>
            </a>

        @endcan

        {{-- Academic Sessions --}}

        @can('manage academic sessions')

            <a href="{{ route('school.academic-sessions.index') }}"
               class="school-nav-link {{ request()->routeIs('school.academic-sessions.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i>
                <span>Academic Sessions</span>
            </a>

            <a href="{{ route('school.terms.index') }}"
               class="school-nav-link {{ request()->routeIs('school.terms.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-week"></i>
                <span>Terms</span>
            </a>

        @endcan

        {{-- Subjects --}}

        @can('manage subjects')
            <a href="{{ route('school.subjects.index') }}"
               class="school-nav-link {{ request()->routeIs('school.subjects.*') ? 'active' : '' }}">
                <i class="bi bi-book-half"></i>
                <span>Subjects</span>
            </a>
        @endcan

        {{-- Teacher Assignments --}}

        @can('manage teachers')
            <a href="{{ route('school.teacher-assignments.index') }}"
               class="school-nav-link {{ request()->routeIs('school.teacher-assignments.*') ? 'active' : '' }}">
                <i class="bi bi-person-workspace"></i>
                <span>Teacher Assignments</span>
            </a>
        @endcan

        {{-- Class Head Teachers --}}

        @can('manage teachers')
            <a href="{{ route('school.class-head-teachers.index') }}"
               class="school-nav-link {{ request()->routeIs('school.class-head-teachers.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Class Head Teachers</span>
            </a>
        @endcan

        {{-- Results and Promotions --}}

        @role('School Admin|Principal')

            <a href="{{ route('school.exams.results-approval.index') }}"
               class="school-nav-link {{ request()->routeIs('school.exams.results-approval.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-check"></i>
                <span>Results Approval</span>
            </a>

            <a href="{{ route('school.promotions.review.index') }}"
               class="school-nav-link {{ request()->routeIs('school.promotions.review.*') ? 'active' : '' }}">
                <i class="bi bi-mortarboard"></i>
                <span>Promotion Review</span>
            </a>

            <a href="{{ route('school.promotions.execution.index') }}"
               class="school-nav-link {{ request()->routeIs('school.promotions.execution.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i>
                <span>Promotion Execution</span>
            </a>

        @endrole

        {{-- Attendance --}}

        @can('view attendance')
            <a href="{{ route('school.attendance.index') }}"
               class="school-nav-link {{ request()->routeIs('school.attendance.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Attendance</span>
            </a>
        @endcan

        {{-- Attendance Submissions --}}

        @if (\Illuminate\Support\Facades\Route::has('school-admin.attendance-submissions'))
            @role('School Admin|Principal')
                <a href="{{ route('school-admin.attendance-submissions') }}"
                   class="school-nav-link {{ request()->routeIs('school-admin.attendance-submissions') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Attendance Submissions</span>
                </a>
            @endrole
        @endif

        {{-- Examinations --}}

        @can('view exams')
            <a href="{{ route('school.exams.index') }}"
               class="school-nav-link {{ request()->routeIs('school.exams.*') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark"></i>
                <span>Examinations</span>
            </a>
        @endcan

        {{-- School Fees --}}

        @can('record payments')
            <a href="{{ route('school.payments.index') }}"
               class="school-nav-link {{ request()->routeIs('school.payments.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>School Fees / Payments</span>
            </a>
        @endcan

        {{-- Timetable and Fee Structures --}}

        @role('School Admin|Principal')

            <a href="{{ route('school.timetable.index') }}"
               class="school-nav-link {{ request()->routeIs('school.timetable.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-week"></i>
                <span>Timetable Management</span>
            </a>

            <a href="{{ route('school.fee-structures.index') }}"
               class="school-nav-link {{ request()->routeIs('school.fee-structures.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                <span>Fee Structures</span>
            </a>

        @endrole

        {{-- Subscription Management --}}

        @role('School Admin|Principal')

            <div class="school-nav-label">Subscription</div>

            <a href="{{ route('school.subscription.plans') }}"
               class="school-nav-link {{ request()->routeIs('school.subscription.plans') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i>
                <span>Subscription Plans</span>
            </a>

            <a href="{{ route('school.subscription.payment-history') }}"
               class="school-nav-link {{ request()->routeIs('school.subscription.payment-history') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>Payment History</span>
            </a>

        @endrole


        {{-- Account --}}

        <div class="school-nav-label">Account</div>

        @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
            <a href="{{ route('profile.edit') }}"
               class="school-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span>My Profile</span>
            </a>
        @endif

        {{-- Security Notice --}}

        <div class="school-sidebar-bottom">

            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-shield-check text-success fs-5"></i>
                <span class="fw-bold small">Secure School Portal</span>
            </div>

            <p class="text-secondary small mb-0">
                Your school workspace is protected by account and school access controls.
            </p>

        </div>

    </div>
</aside>


{{-- Main Content --}}

<div class="school-main">

    {{-- Topbar --}}

    <header class="school-topbar">

        <div class="d-flex align-items-center gap-3">

            <button class="btn btn-light border d-lg-none"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#schoolSidebar"
                    aria-controls="schoolSidebar"
                    aria-label="Open school navigation">

                <i class="bi bi-list fs-4"></i>

            </button>

            <div class="school-mobile-brand">
                <i class="bi bi-mortarboard-fill text-primary fs-4"></i>
                <span>School Portal</span>
            </div>

            <div class="school-search">

                <i class="bi bi-search"></i>

                <input type="search"
                       aria-label="Search"
                       placeholder="Search school portal..."
                       disabled
                       title="Search will be enabled in a future update">

            </div>

        </div>


        <div class="d-flex align-items-center gap-3">

            <div class="text-end school-user-details">

                <div class="school-user-name">
                    {{ $currentUser?->name ?? 'School User' }}
                </div>

                <div class="school-user-role">
                    School Workspace
                </div>

            </div>

            <div class="school-user-avatar"
                 title="{{ $currentUser?->name }}">
                {{ $userInitials ?: 'U' }}
            </div>

            @if (\Illuminate\Support\Facades\Route::has('logout'))

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                            class="btn btn-light border"
                            title="Log out"
                            aria-label="Log out">

                        <i class="bi bi-box-arrow-right"></i>

                    </button>
                </form>

            @endif

        </div>

    </header>


    {{-- Page Content --}}

    <main class="school-content">
        {{ $slot }}
    </main>


    {{-- Footer --}}

    <footer class="school-footer">

        <div class="d-flex flex-wrap justify-content-between gap-2">

            <span>
                &copy; {{ date('Y') }}
                {{ config('app.name', 'School Management SaaS') }}.
                All rights reserved.
            </span>

            <span>
                {{ $currentSchool?->name ?? 'School Portal' }}
            </span>

        </div>

    </footer>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@livewireScripts

</body>
</html>
