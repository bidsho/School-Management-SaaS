<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Treasury Dashboard' }} | {{ config('app.name', 'School Management') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @livewireStyles

    <style>
        :root {
            --treasury-sidebar: #14243a;
            --treasury-sidebar-hover: #213752;
            --treasury-primary: #4361ee;
            --treasury-bg: #f5f7fb;
            --treasury-text: #243247;
            --treasury-muted: #8290a5;
            --treasury-border: #e8edf4;
            --treasury-sidebar-width: 260px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--treasury-bg);
            color: var(--treasury-text);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }

        .treasury-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1040;
            width: var(--treasury-sidebar-width);
            background: var(--treasury-sidebar);
            color: #fff;
            display: flex;
            flex-direction: column;
            transition: width .25s ease, transform .25s ease;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .treasury-brand {
            min-height: 78px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 22px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            color: #fff;
            text-decoration: none;
            white-space: nowrap;
        }

        /* School logo container */
        .treasury-brand-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 12px;
            background: var(--treasury-primary);
            display: grid;
            place-items: center;
            flex-shrink: 0;
            overflow: hidden;
            font-size: 20px;
        }

        /* Actual school logo */
        .treasury-brand-icon img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: contain;
            padding: 3px;
            background: #fff;
        }

        .treasury-brand-text {
            min-width: 0;
            overflow: hidden;
        }

        .treasury-brand-name {
            display: block;
            max-width: 165px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 15px;
            font-weight: 800;
        }

        .treasury-brand-subtitle {
            font-size: 10px;
            color: #aab8ca;
            margin-top: 3px;
        }

        .treasury-nav {
            padding: 20px 12px;
            flex: 1;
        }

        .treasury-nav-label {
            padding: 12px 12px 8px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.3px;
            color: #8192a9;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .treasury-nav-link {
            display: flex;
            align-items: center;
            gap: 13px;
            min-height: 44px;
            margin-bottom: 5px;
            padding: 11px 13px;
            color: #c2ccda;
            text-decoration: none;
            border-radius: 9px;
            white-space: nowrap;
            transition: background .2s, color .2s;
        }

        .treasury-nav-link i {
            width: 20px;
            flex-shrink: 0;
            text-align: center;
            font-size: 17px;
        }

        .treasury-nav-link:hover {
            background: var(--treasury-sidebar-hover);
            color: #fff;
        }

        .treasury-nav-link.active {
            color: #fff;
            background: var(--treasury-primary);
            box-shadow: 0 5px 15px rgba(67,97,238,.2);
        }

        .treasury-sidebar-footer {
            padding: 15px 18px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .treasury-user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            background: #304967;
            color: #fff;
            font-weight: 700;
        }

        .treasury-main {
            min-height: 100vh;
            margin-left: var(--treasury-sidebar-width);
            transition: margin-left .25s ease;
        }

        .treasury-topbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            min-height: 76px;
            padding: 14px 28px;
            background: rgba(255,255,255,.96);
            border-bottom: 1px solid var(--treasury-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            backdrop-filter: blur(10px);
        }

        .treasury-page-title {
            font-size: 19px;
            font-weight: 800;
            margin: 0;
        }

        .treasury-breadcrumb {
            font-size: 12px;
            color: var(--treasury-muted);
            margin-top: 4px;
        }

        .treasury-content {
            padding: 28px;
        }

        .treasury-icon-button {
            width: 40px;
            height: 40px;
            border: 1px solid var(--treasury-border);
            background: #fff;
            color: var(--treasury-text);
            border-radius: 10px;
            display: inline-grid;
            place-items: center;
            text-decoration: none;
        }

        .treasury-icon-button:hover {
            background: #f0f3f9;
        }

        .treasury-sidebar-toggle {
            cursor: pointer;
        }

        .treasury-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1035;
            background: rgba(12,24,40,.55);
        }

        /* Collapsed desktop sidebar */
        .treasury-sidebar-collapsed .treasury-sidebar {
            width: 78px;
        }

        .treasury-sidebar-collapsed .treasury-main {
            margin-left: 78px;
        }

        .treasury-sidebar-collapsed .treasury-brand {
            padding-left: 19px;
            padding-right: 19px;
        }

        .treasury-sidebar-collapsed .treasury-brand-text,
        .treasury-sidebar-collapsed .treasury-nav-label,
        .treasury-sidebar-collapsed .treasury-nav-link span,
        .treasury-sidebar-collapsed .treasury-sidebar-footer .treasury-user-details,
        .treasury-sidebar-collapsed .treasury-sidebar-footer .treasury-logout-label {
            display: none;
        }

        .treasury-sidebar-collapsed .treasury-nav-link {
            justify-content: center;
            padding-left: 10px;
            padding-right: 10px;
        }

        .treasury-sidebar-collapsed .treasury-sidebar-footer {
            padding-left: 20px;
            padding-right: 20px;
        }

        .treasury-sidebar-collapsed .treasury-user-avatar {
            display: none;
        }

        /* Tablet and mobile */
        @media (max-width: 991.98px) {
            .treasury-sidebar {
                width: var(--treasury-sidebar-width);
                transform: translateX(-100%);
            }

            .treasury-mobile-open .treasury-sidebar {
                transform: translateX(0);
            }

            .treasury-mobile-open .treasury-overlay {
                display: block;
            }

            .treasury-main,
            .treasury-sidebar-collapsed .treasury-main {
                margin-left: 0;
            }

            .treasury-sidebar-collapsed .treasury-sidebar {
                width: var(--treasury-sidebar-width);
            }

            .treasury-sidebar-collapsed .treasury-brand-text,
            .treasury-sidebar-collapsed .treasury-nav-label,
            .treasury-sidebar-collapsed .treasury-nav-link span,
            .treasury-sidebar-collapsed .treasury-sidebar-footer .treasury-user-details,
            .treasury-sidebar-collapsed .treasury-sidebar-footer .treasury-logout-label {
                display: initial;
            }

            .treasury-sidebar-collapsed .treasury-nav-link {
                justify-content: flex-start;
                padding: 11px 13px;
            }

            .treasury-topbar {
                padding: 13px 18px;
            }

            .treasury-content {
                padding: 18px;
            }
        }

        @media (max-width: 575.98px) {
            .treasury-content {
                padding: 14px;
            }

            .treasury-topbar {
                min-height: 66px;
                padding: 10px 14px;
            }

            .treasury-page-title {
                font-size: 16px;
            }

            .treasury-breadcrumb {
                font-size: 11px;
            }
        }
    </style>
</head>

<body>

    @php
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | School Logo and School Name
        |--------------------------------------------------------------------------
        | The logo path is stored in school_settings.logo and the image
        | is served from Laravel's public storage disk.
        */

        $schoolSettings = $user?->school_id
            ? \App\Models\SchoolSetting::where('school_id', $user->school_id)->first()
            : null;

        $schoolLogo = $schoolSettings?->logo;

        $schoolLogoExists = $schoolLogo
            && \Illuminate\Support\Facades\Storage::disk('public')->exists($schoolLogo);

        $schoolLogoUrl = $schoolLogoExists
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($schoolLogo)
            : null;

        $schoolName = $user?->school?->name
            ?? config('app.name', 'School Management');

        /*
        |--------------------------------------------------------------------------
        | Treasury Navigation
        |--------------------------------------------------------------------------
        */

        $navigation = [
            [
                'label' => 'OVERVIEW',
                'items' => [
                    [
                        'route' => 'treasury.dashboard',
                        'label' => 'Dashboard',
                        'icon' => 'bi-grid-1x2',
                        'patterns' => ['treasury.dashboard'],
                    ],
                ],
            ],
            [
                'label' => 'FINANCIAL OPERATIONS',
                'items' => [
                    [
                        'route' => 'treasury.payments',
                        'label' => 'Payments',
                        'icon' => 'bi-credit-card',
                        'patterns' => ['treasury.payments'],
                    ],
                    [
                        'route' => 'treasury.receipts',
                        'label' => 'Receipts',
                        'icon' => 'bi-receipt',
                        'patterns' => ['treasury.receipts'],
                    ],
                    [
                        'route' => 'treasury.reconciliation',
                        'label' => 'Reconciliation',
                        'icon' => 'bi-arrow-left-right',
                        'patterns' => ['treasury.reconciliation'],
                    ],
                    [
                        'route' => 'treasury.reports',
                        'label' => 'Financial Reports',
                        'icon' => 'bi-file-earmark-bar-graph',
                        'patterns' => ['treasury.reports'],
                    ],
                    [
                        'route' => 'treasury.outstanding-balances',
                        'label' => 'Outstanding Balances',
                        'icon' => 'bi-wallet2',
                        'patterns' => ['treasury.outstanding-balances'],
                    ],
                    [
                        'route' => 'treasury.fee-assignment',
                        'label' => 'Fee Assignment',
                        'icon' => 'bi-file-earmark-plus',
                        'patterns' => ['treasury.fee-assignment'],
                    ],
                    [
                        'route' => 'treasury.invoices',
                        'label' => 'Invoice Management',
                        'icon' => 'bi-receipt',
                        'patterns' => ['treasury.invoices'],
                    ],
                ],
            ],
        ];
    @endphp


    <div id="treasuryApp">

        <div class="treasury-overlay" id="treasuryOverlay"></div>


        {{-- Sidebar --}}
        <aside class="treasury-sidebar">

            {{-- School Logo and Name --}}
            <a href="{{ Route::has('treasury.dashboard') ? route('treasury.dashboard') : '#' }}"
               class="treasury-brand">

                <span class="treasury-brand-icon">

                    @if ($schoolLogoExists)
                        <img src="{{ $schoolLogoUrl }}" alt="{{ $schoolName }} Logo">
                    @else
                        <i class="bi bi-bank"></i>
                    @endif

                </span>

                <span class="treasury-brand-text">

                    <span class="treasury-brand-name" title="{{ $schoolName }}">
                        {{ $schoolName }}
                    </span>

                    <span class="treasury-brand-subtitle d-block">
                        TREASURY PORTAL
                    </span>

                </span>

            </a>


            {{-- Navigation --}}
            <nav class="treasury-nav">

                @foreach ($navigation as $section)

                    <div class="treasury-nav-label">
                        {{ $section['label'] }}
                    </div>

                    @foreach ($section['items'] as $item)

                        @if (Route::has($item['route']))

                            <a href="{{ route($item['route']) }}"
                               class="treasury-nav-link {{ request()->routeIs(...$item['patterns']) ? 'active' : '' }}">

                                <i class="bi {{ $item['icon'] }}"></i>

                                <span>{{ $item['label'] }}</span>

                            </a>

                        @endif

                    @endforeach

                @endforeach

            </nav>


            {{-- Sidebar Footer --}}
            <div class="treasury-sidebar-footer">

                <div class="d-flex align-items-center gap-2 mb-3">

                    <div class="treasury-user-avatar">
                        {{ strtoupper(substr($user?->name ?? 'T', 0, 1)) }}
                    </div>

                    <div class="treasury-user-details overflow-hidden">

                        <div class="fw-semibold text-white text-truncate">
                            {{ $user?->name ?? 'Treasury User' }}
                        </div>

                        <div class="small text-white-50">
                            Treasury Officer
                        </div>

                    </div>

                </div>


                <div class="d-flex gap-2">

                    @if (Route::has('profile.edit'))

                        <a href="{{ route('profile.edit') }}"
                           class="treasury-nav-link flex-grow-1 mb-0">

                            <i class="bi bi-person-gear"></i>

                            <span>Profile</span>

                        </a>

                    @endif


                    <form method="POST" action="{{ route('logout') }}">

                        @csrf

                        <button type="submit"
                                class="treasury-nav-link border-0 bg-transparent">

                            <i class="bi bi-box-arrow-right"></i>

                            <span class="treasury-logout-label">
                                Logout
                            </span>

                        </button>

                    </form>

                </div>

            </div>

        </aside>


        {{-- Main Content --}}
        <main class="treasury-main">

            {{-- Topbar --}}
            <header class="treasury-topbar">

                <div class="d-flex align-items-center gap-3">

                    <button type="button"
                            id="treasurySidebarToggle"
                            class="treasury-icon-button treasury-sidebar-toggle"
                            aria-label="Toggle sidebar">

                        <i class="bi bi-list fs-5"></i>

                    </button>

                    <div>

                        <h1 class="treasury-page-title">
                            {{ $title ?? 'Treasury Dashboard' }}
                        </h1>

                        <div class="treasury-breadcrumb">

                            Home
                            <i class="bi bi-chevron-right mx-1"></i>

                            Treasury
                            <i class="bi bi-chevron-right mx-1"></i>

                            {{ $title ?? 'Dashboard' }}

                        </div>

                    </div>

                </div>


                <div class="d-flex align-items-center gap-2">

                    <span class="d-none d-md-inline text-secondary small">
                        {{ now()->format('D, d M Y') }}
                    </span>

                    <div class="treasury-user-avatar">
                        {{ strtoupper(substr($user?->name ?? 'T', 0, 1)) }}
                    </div>

                </div>

            </header>


            {{-- Page Content --}}
            <section class="treasury-content">

                {{ $slot }}

            </section>

        </main>

    </div>


    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    {{-- Sidebar Script --}}
    <script>
        (() => {

            const app = document.getElementById('treasuryApp');
            const toggle = document.getElementById('treasurySidebarToggle');
            const overlay = document.getElementById('treasuryOverlay');

            const storageKey = 'treasury-sidebar-collapsed';

            if (!app || !toggle || !overlay) return;

            const mobile = () => window.innerWidth < 992;


            // Restore saved desktop sidebar state.
            try {

                if (localStorage.getItem(storageKey) === 'true' && !mobile()) {
                    app.classList.add('treasury-sidebar-collapsed');
                }

            } catch (e) {}


            // Toggle sidebar.
            toggle.addEventListener('click', () => {

                if (mobile()) {

                    app.classList.toggle('treasury-mobile-open');

                } else {

                    app.classList.toggle('treasury-sidebar-collapsed');

                    try {

                        localStorage.setItem(
                            storageKey,
                            app.classList.contains('treasury-sidebar-collapsed') ? 'true' : 'false'
                        );

                    } catch (e) {}

                }

            });


            // Close mobile sidebar when overlay is clicked.
            overlay.addEventListener('click', () => {

                app.classList.remove('treasury-mobile-open');

            });


            // Close mobile sidebar after navigation.
            app.querySelectorAll('.treasury-nav-link').forEach(link => {

                link.addEventListener('click', () => {

                    if (mobile()) {
                        app.classList.remove('treasury-mobile-open');
                    }

                });

            });


            // Reset mobile state when returning to desktop.
            window.addEventListener('resize', () => {

                if (!mobile()) {
                    app.classList.remove('treasury-mobile-open');
                }

            });

        })();
    </script>


    @livewireScripts

</body>
</html>