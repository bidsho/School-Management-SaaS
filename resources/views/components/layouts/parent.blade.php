<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Parent Dashboard' }} | School Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    @livewireStyles

    <style>
        :root {
            --parent-primary: #4f46e5;
            --parent-primary-dark: #3730a3;
            --parent-sidebar-width: 260px;
            --parent-bg: #f5f7fb;
            --parent-text: #172033;
            --parent-muted: #778198;
            --parent-border: #e9edf5;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--parent-bg);
            color: var(--parent-text);
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI",
                         Roboto, sans-serif;
            font-size: 14px;
        }

        a {
            text-decoration: none;
        }

        button,
        input {
            font: inherit;
        }

        .parent-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1045;
            width: var(--parent-sidebar-width);
            height: 100vh;
            overflow-y: auto;
            background: #fff;
            border-right: 1px solid var(--parent-border);
            transition: transform .25s ease;
        }

        .parent-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 82px;
            padding: 20px 24px;
            color: var(--parent-text);
            border-bottom: 1px solid var(--parent-border);
        }

        /* School logo */
        .parent-brand-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            overflow: hidden;
            color: #fff;
            font-size: 21px;
            background: linear-gradient(135deg, #6366f1, #4338ca);
            border-radius: 13px;
            box-shadow: 0 5px 14px rgba(79, 70, 229, .2);
        }

        .parent-brand-icon img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 3px;
            background: #fff;
        }

        .parent-brand-name {
            margin: 0;
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -.5px;
        }

        .parent-brand-caption {
            margin-top: 3px;
            color: var(--parent-muted);
            font-size: 11px;
        }

        .parent-sidebar-content {
            padding: 18px 14px 24px;
        }

        .parent-nav-heading {
            padding: 0 12px;
            margin: 20px 0 9px;
            color: #9aa3b5;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .parent-nav-link {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 12px 14px;
            margin-bottom: 5px;
            color: #68738a;
            font-weight: 600;
            border-radius: 10px;
            transition: color .2s ease, background .2s ease;
        }

        .parent-nav-link i {
            width: 19px;
            font-size: 17px;
            text-align: center;
        }

        .parent-nav-link:hover {
            color: var(--parent-primary);
            background: #f2f2ff;
        }

        .parent-nav-link.active {
            color: #fff;
            background: var(--parent-primary);
            box-shadow: 0 6px 15px rgba(79, 70, 229, .18);
        }

        .parent-sidebar-bottom {
            padding: 15px;
            margin-top: 28px;
            background: #f8f9ff;
            border: 1px solid #eceeff;
            border-radius: 13px;
        }

        .parent-sidebar-bottom i {
            color: var(--parent-primary);
        }

        .parent-sidebar-bottom p {
            margin: 8px 0 0;
            color: var(--parent-muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .parent-main {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: var(--parent-sidebar-width);
        }

        .parent-topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 76px;
            padding: 12px 30px;
            background: rgba(255, 255, 255, .97);
            border-bottom: 1px solid var(--parent-border);
            backdrop-filter: blur(12px);
        }

        .parent-page-title {
            margin: 0;
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -.4px;
        }

        .parent-page-subtitle {
            margin-top: 4px;
            color: var(--parent-muted);
            font-size: 12px;
        }

        .parent-icon-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 39px;
            height: 39px;
            color: #68738a;
            background: #fff;
            border: 1px solid var(--parent-border);
            border-radius: 11px;
            cursor: pointer;
        }

        .parent-icon-button:hover {
            color: var(--parent-primary);
            background: #f7f7ff;
        }

        .parent-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-left: 15px;
            margin-left: 5px;
            border-left: 1px solid var(--parent-border);
        }

        .parent-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 41px;
            height: 41px;
            color: #4338ca;
            font-size: 15px;
            font-weight: 800;
            background: #e9eaff;
            border-radius: 50%;
        }

        .parent-profile-name {
            max-width: 170px;
            overflow: hidden;
            font-size: 13px;
            font-weight: 750;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .parent-profile-role {
            margin-top: 3px;
            color: var(--parent-muted);
            font-size: 11px;
        }

        .parent-content {
            flex: 1;
            width: 100%;
            max-width: 1600px;
            padding: 28px 30px;
            margin: 0 auto;
        }

        .parent-content .alert {
            border: 0;
            border-radius: 12px;
        }

        .parent-footer {
            padding: 20px 30px;
            color: var(--parent-muted);
            font-size: 12px;
            background: #fff;
            border-top: 1px solid var(--parent-border);
        }

        .parent-footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .parent-overlay {
            display: none;
        }

        .parent-sidebar:focus-within {
            outline-offset: -2px;
        }

        @media (max-width: 991.98px) {
            .parent-sidebar {
                transform: translateX(-100%);
            }

            .parent-sidebar.show {
                transform: translateX(0);
            }

            .parent-overlay.show {
                position: fixed;
                inset: 0;
                z-index: 1040;
                display: block;
                background: rgba(15, 23, 42, .45);
            }

            .parent-main {
                margin-left: 0;
            }

            .parent-topbar {
                padding: 12px 20px;
            }

            .parent-content {
                padding: 22px 20px;
            }

            .parent-footer {
                padding: 18px 20px;
            }
        }

        @media (max-width: 575.98px) {
            .parent-topbar {
                min-height: 68px;
                gap: 8px;
                padding: 10px 14px;
            }

            .parent-page-title {
                font-size: 16px;
            }

            .parent-page-subtitle {
                font-size: 11px;
            }

            .parent-content {
                padding: 18px 14px;
            }

            .parent-profile {
                gap: 7px;
                padding-left: 8px;
                margin-left: 0;
            }

            .parent-avatar {
                width: 35px;
                height: 35px;
            }

            .parent-profile-name {
                max-width: 100px;
                font-size: 12px;
            }

            .parent-icon-button {
                width: 35px;
                height: 35px;
            }

            .parent-footer {
                padding: 16px 14px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    @php
        $currentUser = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | School Branding
        |--------------------------------------------------------------------------
        | Each parent sees the logo and name of their own school.
        */

        $schoolId = $currentUser?->school_id;

        $schoolSettings = $schoolId
            ? \App\Models\SchoolSetting::where('school_id', $schoolId)->first()
            : null;

        $schoolLogo = $schoolSettings?->logo;

        $schoolLogoExists = $schoolLogo
            && \Illuminate\Support\Facades\Storage::disk('public')->exists($schoolLogo);

        $schoolLogoUrl = $schoolLogoExists
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($schoolLogo)
            : null;

        $schoolName = $currentUser?->school?->name ?? 'School Portal';

        /*
        |--------------------------------------------------------------------------
        | Parent Profile
        |--------------------------------------------------------------------------
        */

        $displayName = $currentUser?->name ?? 'Parent';

        $initials = collect(explode(' ', trim($displayName)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');

        /*
        |--------------------------------------------------------------------------
        | Parent Navigation
        |--------------------------------------------------------------------------
        */

        $navigation = [
            [
                'heading' => 'OVERVIEW',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'icon' => 'bi-grid-1x2',
                        'url' => url('/parent/dashboard'),
                        'match' => 'parent/dashboard',
                        'available' => true,
                    ],
                ],
            ],
            [
                'heading' => 'MY FAMILY',
                'items' => [
                    [
                        'label' => 'My Children',
                        'icon' => 'bi-people',
                        'url' => url('/parent/children'),
                        'match' => 'parent/children*',
                        'available' => true,
                    ],
                    [
                        'label' => 'Attendance',
                        'icon' => 'bi-calendar2-check',
                        'url' => url('/parent/attendance'),
                        'match' => 'parent/attendance*',
                        'available' => true,
                    ],
                    [
                        'label' => 'Academic Results',
                        'icon' => 'bi-mortarboard',
                        'url' => url('/parent/results'),
                        'match' => 'parent/results*',
                        'available' => true,
                    ],
                    [
                        'label' => 'Timetable',
                        'icon' => 'bi-calendar3',
                        'url' => url('/parent/timetable'),
                        'match' => 'parent/timetable*',
                        'available' => true,
                    ],
                ],
            ],
            [
                'heading' => 'FINANCE',
                'items' => [
                    [
                        'label' => 'School Fees',
                        'icon' => 'bi-wallet2',
                        'url' => url('/parent/fees'),
                        'match' => 'parent/fees*',
                        'available' => true,
                    ],
                    [
                        'label' => 'Payment History',
                        'icon' => 'bi-credit-card',
                        'url' => url('/parent/payments'),
                        'match' => 'parent/payments*',
                        'available' => true,
                    ],
                    [
                        'label' => 'Receipts',
                        'icon' => 'bi-receipt',
                        'url' => url('/parent/receipts'),
                        'match' => 'parent/receipts*',
                        'available' => true,
                    ],
                ],
            ],
            [
                'heading' => 'COMMUNICATION',
                'items' => [
                    [
                        'label' => 'Announcements',
                        'icon' => 'bi-megaphone',
                        'url' => url('/parent/announcements'),
                        'match' => 'parent/announcements*',
                        'available' => true,
                    ],
                    [
                        'label' => 'My Profile',
                        'icon' => 'bi-person-circle',
                        'url' => url('/parent/profile'),
                        'match' => 'parent/profile*',
                        'available' => true,
                    ],
                ],
            ],
        ];
    @endphp

    {{-- Sidebar --}}
    <aside class="parent-sidebar" id="parentSidebar">

        {{-- School Logo and Name --}}
        <a href="{{ url('/parent/dashboard') }}" class="parent-brand">

            <div class="parent-brand-icon">
                @if ($schoolLogoExists)
                    <img
                        src="{{ $schoolLogoUrl }}"
                        alt="{{ $schoolName }} Logo"
                    >
                @else
                    <i class="bi bi-mortarboard-fill"></i>
                @endif
            </div>

            <div class="overflow-hidden">
                <h5 class="parent-brand-name text-truncate mb-0">
                    {{ $schoolName }}
                </h5>

                <div class="parent-brand-caption">
                    Parent &amp; Family Centre
                </div>
            </div>

        </a>

        <nav class="parent-sidebar-content" aria-label="Parent navigation">

            @foreach ($navigation as $group)

                <div class="parent-nav-heading">
                    {{ $group['heading'] }}
                </div>

                @foreach ($group['items'] as $item)

                    @if ($item['available'])

                        <a href="{{ $item['url'] }}"
                           class="parent-nav-link {{ request()->is($item['match']) ? 'active' : '' }}"
                           @if (request()->is($item['match'])) aria-current="page" @endif>

                            <i class="bi {{ $item['icon'] }}"></i>

                            <span>{{ $item['label'] }}</span>

                        </a>

                    @else

                        <span class="parent-nav-link opacity-50"
                              title="This page has not been built yet"
                              aria-disabled="true">

                            <i class="bi {{ $item['icon'] }}"></i>

                            <span>{{ $item['label'] }}</span>

                            <i class="bi bi-lock ms-auto small"></i>

                        </span>

                    @endif

                @endforeach

            @endforeach

            <div class="parent-sidebar-bottom">

                <i class="bi bi-shield-check fs-5"></i>

                <div class="fw-bold mt-2">
                    Your family matters
                </div>

                <p>
                    Access your children's school information securely from one place.
                </p>

            </div>

        </nav>

    </aside>

    <div class="parent-overlay" id="parentOverlay"></div>

    {{-- Main content area --}}
    <div class="parent-main">

        <header class="parent-topbar">

            <div class="d-flex align-items-center gap-3">

                <button type="button"
                        class="parent-icon-button d-lg-none"
                        id="parentMenuToggle"
                        aria-label="Toggle navigation"
                        aria-controls="parentSidebar"
                        aria-expanded="false">

                    <i class="bi bi-list fs-4"></i>

                </button>

                <div>
                    <h1 class="parent-page-title">
                        {{ $title ?? 'Parent Dashboard' }}
                    </h1>

                    <div class="parent-page-subtitle">
                        Welcome to your family school portal.
                    </div>
                </div>

            </div>

            <div class="d-flex align-items-center gap-2">

                <a href="{{ url('/parent/announcements') }}"
                   class="parent-icon-button"
                   aria-label="Announcements"
                   title="Announcements">

                    <i class="bi bi-bell"></i>

                </a>

                <div class="parent-profile">

                    <div class="parent-avatar">
                        {{ $initials ?: 'P' }}
                    </div>

                    <div class="d-none d-sm-block">

                        <div class="parent-profile-name">
                            {{ $displayName }}
                        </div>

                        <div class="parent-profile-role">
                            Parent Account
                        </div>

                    </div>

                    <div class="dropdown">

                        <button class="parent-icon-button border-0"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                aria-label="Account menu">

                            <i class="bi bi-chevron-down"></i>

                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2">

                            <li>
                                <a class="dropdown-item rounded-2 py-2"
                                   href="{{ url('/parent/profile') }}">

                                    <i class="bi bi-person me-2"></i>
                                    My Profile

                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <button type="submit"
                                            class="dropdown-item rounded-2 py-2 text-danger">

                                        <i class="bi bi-box-arrow-right me-2"></i>
                                        Sign Out

                                    </button>
                                </form>
                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </header>

        <main class="parent-content">

            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4"
                     role="alert">

                    <i class="bi bi-check-circle-fill me-2"></i>

                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close"></button>

                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4"
                     role="alert">

                    <i class="bi bi-exclamation-circle-fill me-2"></i>

                    {{ session('error') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close"></button>

                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-4" role="alert">

                    <div class="fw-bold mb-1">
                        Please check the following:
                    </div>

                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif

            {{ $slot }}

        </main>

        <footer class="parent-footer">

            <div class="parent-footer-inner">

                <span>
                    &copy; {{ date('Y') }} School Portal. All rights reserved.
                </span>

                <span>
                    <i class="bi bi-shield-lock me-1"></i>
                    Secure Parent Access
                </span>

            </div>

        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        (() => {

            function initializeParentLayout() {

                const sidebar = document.getElementById('parentSidebar');
                const overlay = document.getElementById('parentOverlay');
                const toggle = document.getElementById('parentMenuToggle');

                if (!sidebar || !overlay || !toggle) {
                    return;
                }

                function closeSidebar() {

                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');

                    toggle.setAttribute('aria-expanded', 'false');

                    document.body.style.overflow = '';
                }

                function openSidebar() {

                    sidebar.classList.add('show');
                    overlay.classList.add('show');

                    toggle.setAttribute('aria-expanded', 'true');

                    document.body.style.overflow = 'hidden';
                }

                toggle.addEventListener('click', function () {

                    if (sidebar.classList.contains('show')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }

                });

                overlay.addEventListener('click', closeSidebar);

                sidebar.querySelectorAll('a').forEach(function (link) {

                    link.addEventListener('click', function () {

                        if (window.innerWidth < 992) {
                            closeSidebar();
                        }

                    });

                });

                document.addEventListener('keydown', function (event) {

                    if (event.key === 'Escape') {
                        closeSidebar();
                    }

                });

                window.addEventListener('resize', function () {

                    if (window.innerWidth >= 992) {
                        closeSidebar();
                    }

                });

            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeParentLayout);
            } else {
                initializeParentLayout();
            }

        })();
    </script>

    @stack('scripts')

    @livewireScripts

</body>
</html>