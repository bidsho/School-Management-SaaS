<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ $title ?? 'Receptionist Dashboard' }} |
        {{ config('app.name', 'School Management') }}
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @livewireStyles

    <style>
        :root {
            --reception-primary: #2563eb;
            --reception-dark: #172554;
            --reception-bg: #f5f7fb;
            --reception-text: #334155;
            --reception-muted: #94a3b8;
            --reception-border: #e8edf4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--reception-bg);
            color: var(--reception-text);
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 14px;
        }

        .reception-layout {
            min-height: 100vh;
        }

        /* Sidebar */
        .reception-sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1040;
            background: #fff;
            border-right: 1px solid var(--reception-border);
            display: flex;
            flex-direction: column;
            transition: transform .3s ease;
        }

        .reception-brand {
            height: 76px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--reception-border);
            flex-shrink: 0;
            color: var(--reception-dark);
        }

        /* School logo container */
        .reception-brand-icon {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
            border-radius: 12px;
            background: #eff6ff;
            color: var(--reception-primary);
            font-size: 22px;
        }

        .reception-brand-icon img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 3px;
            background: #fff;
        }

        .reception-brand-details {
            min-width: 0;
            overflow: hidden;
        }

        .reception-brand h5 {
            font-size: 16px;
            font-weight: 800;
            margin: 0;
            color: var(--reception-dark);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .reception-brand small {
            font-size: 10px;
            color: var(--reception-muted);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .reception-sidebar-content {
            flex: 1;
            padding: 24px 14px;
            overflow-y: auto;
        }

        .reception-section-title {
            padding: 0 12px;
            margin: 15px 0 10px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
            color: #a0aec0;
            text-transform: uppercase;
        }

        .reception-nav-link {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 12px 14px;
            margin-bottom: 5px;
            border-radius: 9px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s ease;
        }

        .reception-nav-link i {
            font-size: 17px;
        }

        .reception-nav-link:hover {
            color: var(--reception-primary);
            background: #f1f5ff;
        }

        .reception-nav-link.active {
            color: #fff;
            background: var(--reception-primary);
            box-shadow: 0 5px 12px rgba(37, 99, 235, .18);
        }

        .reception-nav-link.disabled-link {
            opacity: .55;
            cursor: default;
        }

        .reception-sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--reception-border);
            flex-shrink: 0;
        }

        .reception-user-mini {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .reception-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #dbeafe;
            color: #1d4ed8;
            font-weight: 800;
            flex-shrink: 0;
        }

        .reception-user-mini strong {
            display: block;
            font-size: 12px;
            color: #1e293b;
        }

        .reception-user-mini small {
            font-size: 11px;
            color: var(--reception-muted);
        }

        /* Main content */
        .reception-main {
            margin-left: 260px;
            min-height: 100vh;
        }

        .reception-topbar {
            min-height: 76px;
            padding: 0 30px;
            background: #fff;
            border-bottom: 1px solid var(--reception-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .reception-page-heading h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            color: var(--reception-dark);
        }

        .reception-page-heading small {
            color: var(--reception-muted);
            font-size: 11px;
        }

        .reception-top-actions {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .reception-icon-button {
            width: 38px;
            height: 38px;
            border: 1px solid var(--reception-border);
            background: #fff;
            border-radius: 10px;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .reception-content {
            padding: 30px;
        }

        .reception-overlay,
        .reception-mobile-toggle {
            display: none;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .reception-sidebar {
                transform: translateX(-100%);
            }

            .reception-sidebar.show {
                transform: translateX(0);
            }

            .reception-main {
                margin-left: 0;
            }

            .reception-mobile-toggle {
                display: flex;
            }

            .reception-overlay.show {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, .45);
                z-index: 1030;
            }

            .reception-content {
                padding: 20px;
            }

            .reception-topbar {
                padding: 0 18px;
            }
        }

        @media (max-width: 575.98px) {
            .reception-topbar {
                min-height: 65px;
            }

            .reception-content {
                padding: 15px;
            }

            .reception-page-heading small {
                display: none;
            }
        }
    </style>
</head>

<body>

@php
    $currentUser = auth()->user();

    $initial = strtoupper(substr($currentUser?->name ?? 'R', 0, 1));

    /*
    |--------------------------------------------------------------------------
    | School Branding
    |--------------------------------------------------------------------------
    | The receptionist sees the logo belonging to their own school.
    */

    $schoolId = $currentUser?->school_id;

    $schoolSettings = $schoolId
        ? \App\Models\SchoolSetting::query()
            ->where('school_id', $schoolId)
            ->first()
        : null;

    $schoolLogo = $schoolSettings?->logo;

    $schoolLogoExists = $schoolLogo
        && \Illuminate\Support\Facades\Storage::disk('public')->exists($schoolLogo);

    $schoolLogoUrl = $schoolLogoExists
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($schoolLogo)
        : null;

    $schoolName = $currentUser?->school?->name ?? 'School Portal';
@endphp

<div class="reception-layout">

    {{-- Mobile sidebar overlay --}}
    <div
        class="reception-overlay"
        id="receptionOverlay"
        onclick="toggleReceptionSidebar()">
    </div>

    {{-- Sidebar --}}
    <aside class="reception-sidebar" id="receptionSidebar">

        {{-- School Logo and Branding --}}
        <a href="{{ route('receptionist.dashboard') }}"
           class="reception-brand text-decoration-none">

            <div class="reception-brand-icon">

                @if ($schoolLogoExists)
                    <img
                        src="{{ $schoolLogoUrl }}"
                        alt="{{ $schoolName }} Logo">
                @else
                    <i class="bi bi-buildings-fill"></i>
                @endif

            </div>

            <div class="reception-brand-details">
                <h5>{{ $schoolName }}</h5>
                <small>Reception Desk</small>
            </div>

        </a>

        <div class="reception-sidebar-content">

            {{-- Dashboard --}}
            <div class="reception-section-title">Main Menu</div>

            <a href="{{ route('receptionist.dashboard') }}"
               class="reception-nav-link {{ request()->routeIs('receptionist.dashboard') ? 'active' : '' }}">

                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            {{-- Front Office --}}
            <div class="reception-section-title">Front Office</div>

            {{-- Enrollments --}}
            @can('view enrollments')
                <a href="{{ route('receptionist.enrollments.index') }}"
                   class="reception-nav-link {{
                        request()->routeIs('receptionist.enrollments.index') ||
                        request()->routeIs('receptionist.enrollments.edit')
                            ? 'active' : ''
                   }}">

                    <i class="bi bi-people-fill"></i>
                    <span>Enrollments</span>
                </a>
            @endcan

            {{-- New Enrollment --}}
            @can('manage admissions')
                <a href="{{ route('receptionist.enrollments.create') }}"
                   class="reception-nav-link {{ request()->routeIs('receptionist.enrollments.create') ? 'active' : '' }}">

                    <i class="bi bi-person-plus-fill"></i>
                    <span>New Enrollment</span>
                </a>
            @endcan

            {{-- Parents --}}
            @can('view parents')
                <a href="{{ route('receptionist.parents.index') }}"
                   class="reception-nav-link {{
                        request()->routeIs('receptionist.parents.index') ||
                        request()->routeIs('receptionist.parents.edit')
                            ? 'active' : ''
                   }}">

                    <i class="bi bi-person-hearts"></i>
                    <span>Parents</span>
                </a>
            @endcan

            {{-- Add Parent --}}
            @can('create parents')
                <a href="{{ route('receptionist.parents.create') }}"
                   class="reception-nav-link {{ request()->routeIs('receptionist.parents.create') ? 'active' : '' }}">

                    <i class="bi bi-person-plus-fill"></i>
                    <span>Add Parent</span>
                </a>
            @endcan

            {{-- Student Management --}}
            <div class="reception-section-title">Student Management</div>

            {{-- View Students --}}
            @can('view students')
                <a href="{{ route('receptionist.students.index') }}"
                   class="reception-nav-link {{
                        request()->routeIs('receptionist.students.index') ||
                        request()->routeIs('receptionist.students.show') ||
                        request()->routeIs('receptionist.students.edit')
                            ? 'active'
                            : ''
                   }}">

                    <i class="bi bi-mortarboard-fill"></i>
                    <span>Students</span>
                </a>
            @endcan

            {{-- Add Student --}}
            @can('create students')
                <a href="{{ route('receptionist.students.create') }}"
                   class="reception-nav-link {{
                        request()->routeIs('receptionist.students.create') ? 'active' : ''
                   }}">

                    <i class="bi bi-person-plus-fill"></i>
                    <span>Add Student</span>
                </a>
            @endcan

            {{-- Other Front Office Modules --}}
            {{-- <span class="reception-nav-link disabled-link"
                  title="Visitor Management coming soon">
                <i class="bi bi-person-badge"></i>
                <span>Visitor Management</span>
            </span>

            <span class="reception-nav-link disabled-link"
                  title="Appointments coming soon">
                <i class="bi bi-calendar-check"></i>
                <span>Appointments</span>
            </span>

            <span class="reception-nav-link disabled-link"
                  title="Document Management coming soon">
                <i class="bi bi-folder2-open"></i>
                <span>Received Documents</span>
            </span> --}}

            {{-- Communication --}}
            {{-- <div class="reception-section-title">Communication</div>

            <span class="reception-nav-link disabled-link"
                  title="Announcements coming soon">
                <i class="bi bi-megaphone"></i>
                <span>Announcements</span>
            </span>

            <span class="reception-nav-link disabled-link"
                  title="Messages coming soon">
                <i class="bi bi-envelope"></i>
                <span>Messages</span>
            </span> --}}

            {{-- Account --}}
            {{-- <div class="reception-section-title">Account</div>

            <span class="reception-nav-link disabled-link"
                  title="Profile management coming soon">
                <i class="bi bi-person-circle"></i>
                <span>My Profile</span>
            </span> --}}

        </div>

        {{-- Sidebar Footer --}}
        <div class="reception-sidebar-footer">

            <div class="reception-user-mini">

                <div class="reception-avatar">
                    {{ $initial }}
                </div>

                <div class="flex-grow-1 overflow-hidden">
                    <strong class="text-truncate">
                        {{ $currentUser?->name ?? 'Receptionist' }}
                    </strong>

                    <small>Receptionist</small>
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                            class="btn btn-sm text-danger"
                            title="Logout"
                            aria-label="Logout">

                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </button>
                </form>

            </div>

        </div>

    </aside>

    {{-- Main --}}
    <main class="reception-main">

        {{-- Topbar --}}
        <header class="reception-topbar">

            <div class="d-flex align-items-center gap-3">

                <button type="button"
                        class="reception-icon-button reception-mobile-toggle"
                        onclick="toggleReceptionSidebar()"
                        aria-label="Toggle sidebar">

                    <i class="bi bi-list"></i>
                </button>

                <div class="reception-page-heading">
                    <h5>{{ $header ?? 'Receptionist Dashboard' }}</h5>

                    <small>
                        Welcome to your school front office
                    </small>
                </div>

            </div>

            {{-- Topbar Actions --}}
            <div class="reception-top-actions">

                <span class="text-muted small d-none d-md-inline">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ now()->format('D, d M Y') }}
                </span>

                <button class="reception-icon-button"
                        type="button"
                        title="Notifications">

                    <i class="bi bi-bell"></i>
                </button>

                <div class="reception-avatar">
                    {{ $initial }}
                </div>

            </div>

        </header>

        {{-- Livewire Page Content --}}
        <div class="reception-content">

            {{ $slot }}

        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleReceptionSidebar() {
        const sidebar = document.getElementById('receptionSidebar');
        const overlay = document.getElementById('receptionOverlay');

        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }
</script>

@livewireScripts

</body>
</html>