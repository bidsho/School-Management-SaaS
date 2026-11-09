
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ $title ?? 'Platform Admin' }} |
        {{ config('app.name', 'EduManage') }}
    </title>

    {{-- Bootstrap 5 --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    {{-- Livewire styles --}}
    @livewireStyles

    <style>
        :root {
            --super-primary: #4f46e5;
            --super-primary-dark: #3730a3;
            --super-sidebar: #111827;
            --super-sidebar-muted: #9ca3af;
            --super-bg: #f5f7fb;
            --super-border: #e8ebf2;
            --super-text: #172033;
            --super-muted: #7b8498;
            --super-sidebar-width: 265px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--super-bg);
            color: var(--super-text);
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            font-size: 14px;
        }

        a {
            text-decoration: none;
        }

        /* =====================================
           SIDEBAR
        ===================================== */

        .super-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            width: var(--super-sidebar-width);
            height: 100vh;
            padding: 22px 16px;
            overflow-y: auto;
            background: var(--super-sidebar);
            color: white;
            transition: transform .25s ease;
        }

        .super-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 4px 9px 25px;
            color: white;
        }

        .super-brand:hover {
            color: white;
        }

        .super-brand-icon {
            display: grid;
            place-items: center;
            width: 45px;
            height: 45px;
            flex-shrink: 0;
            border-radius: 14px;
            background: linear-gradient(135deg, #6366f1, #818cf8);
            box-shadow: 0 7px 22px rgba(99, 102, 241, .25);
            font-size: 22px;
        }

        .super-brand-name {
            font-size: 16px;
            font-weight: 800;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }

        .super-brand-caption {
            margin-top: 4px;
            color: #a5b4fc;
            font-size: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .super-section-label {
            padding: 17px 12px 9px;
            color: #737f95;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .super-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 45px;
            margin: 3px 0;
            padding: 11px 13px;
            border: 1px solid transparent;
            border-radius: 10px;
            color: var(--super-sidebar-muted);
            font-size: 13px;
            font-weight: 600;
            transition: background .2s, color .2s;
        }

        .super-nav-link > i {
            width: 20px;
            font-size: 17px;
            text-align: center;
        }

        .super-nav-link:hover {
            background: #1f2937;
            color: white;
        }

        .super-nav-link.active {
            border-color: rgba(165, 180, 252, .1);
            background: linear-gradient(100deg, #4f46e5, #6366f1);
            color: white;
            box-shadow: 0 5px 14px rgba(79, 70, 229, .2);
        }

        .super-nav-link .nav-arrow {
            margin-left: auto;
            font-size: 11px;
            opacity: .65;
        }

        /* =====================================
           SIDEBAR PROFILE
        ===================================== */

        .super-sidebar-bottom {
            margin-top: auto;
            padding-top: 22px;
        }

        .super-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 17px 8px 3px;
            border-top: 1px solid #293244;
        }

        .super-avatar {
            display: grid;
            place-items: center;
            width: 39px;
            height: 39px;
            flex-shrink: 0;
            border-radius: 12px;
            background: #3730a3;
            color: white;
            font-size: 13px;
            font-weight: 800;
        }

        .super-profile-name {
            overflow: hidden;
            color: white;
            font-size: 12px;
            font-weight: 750;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .super-profile-role {
            margin-top: 4px;
            color: #a5b4fc;
            font-size: 11px;
        }

        /* =====================================
           MAIN CONTENT
        ===================================== */

        .super-main {
            min-height: 100vh;
            margin-left: var(--super-sidebar-width);
        }

        /* =====================================
           TOPBAR
        ===================================== */

        .super-topbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 78px;
            padding: 15px 32px;
            border-bottom: 1px solid var(--super-border);
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(12px);
        }

        .super-page-title {
            margin: 0;
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -.4px;
        }

        .super-breadcrumb {
            margin-top: 5px;
            color: var(--super-muted);
            font-size: 12px;
        }

        .super-breadcrumb span {
            color: var(--super-primary);
        }

        .super-topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .super-date {
            color: var(--super-muted);
            font-size: 12px;
        }

        .super-status {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 11px;
            border: 1px solid #d9f1e4;
            border-radius: 50px;
            background: #f0fdf4;
            color: #16834d;
            font-size: 11px;
            font-weight: 700;
        }

        .super-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
        }

        .super-content {
            padding: 30px 32px;
        }

        /* =====================================
           MOBILE MENU
        ===================================== */

        .super-menu-toggle {
            display: none;
            place-items: center;
            width: 40px;
            height: 40px;
            border: 1px solid var(--super-border);
            border-radius: 10px;
            background: white;
            color: var(--super-text);
            font-size: 20px;
        }

        .super-overlay {
            display: none;
        }

        /* =====================================
           LOGOUT
        ===================================== */

        .super-logout {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 11px 13px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: #fca5a5;
            font-size: 13px;
            font-weight: 600;
            text-align: left;
        }

        .super-logout:hover {
            background: rgba(239, 68, 68, .1);
            color: #fecaca;
        }

        /* =====================================
           RESPONSIVE
        ===================================== */

        @media (max-width: 991.98px) {
            .super-sidebar {
                transform: translateX(-100%);
            }

            .super-sidebar.show {
                transform: translateX(0);
            }

            .super-main {
                margin-left: 0;
            }

            .super-menu-toggle {
                display: inline-grid;
            }

            .super-overlay.show {
                position: fixed;
                inset: 0;
                z-index: 1030;
                display: block;
                background: rgba(15, 23, 42, .55);
            }

            .super-topbar {
                padding: 14px 20px;
            }

            .super-content {
                padding: 23px 20px;
            }
        }

        @media (max-width: 575.98px) {
            .super-topbar {
                padding: 13px;
            }

            .super-content {
                padding: 17px 13px;
            }

            .super-date {
                display: none;
            }

            .super-page-title {
                font-size: 16px;
            }

            .super-status {
                padding: 7px 9px;
            }
        }
    </style>

    @stack('styles')

</head>

<body>

@php
    $user = auth()->user();

    $initials = collect(explode(' ', trim($user?->name ?? 'Admin')))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');

    $navigation = [
        [
            'label' => 'Overview',
            'icon' => 'bi-grid-1x2-fill',
            'url' => '/platform/dashboard',
            'active' => request()->is('platform/dashboard'),
        ],
        [
            'label' => 'Schools',
            'icon' => 'bi-buildings',
            'url' => '/platform/schools',
            'active' => request()->is('platform/schools*'),
        ],
        [
            'label' => 'Subscriptions',
            'icon' => 'bi-credit-card-2-front',
            'url' => '/platform/subscriptions',
            'active' => request()->is('platform/subscriptions*'),
        ],
        [
            'label' => 'Plans & Pricing',
            'icon' => 'bi-tags',
            'url' => '/platform/plans',
            'active' => request()->is('platform/plans*'),
        ],
        [
            'label' => 'Platform Users',
            'icon' => 'bi-people',
            'url' => '/platform/users',
            'active' => request()->is('platform/users*'),
        ],
    ];
@endphp

{{-- MOBILE SIDEBAR OVERLAY --}}
<div class="super-overlay" id="superOverlay"></div>

{{-- =====================================
     SIDEBAR
===================================== --}}

<aside class="super-sidebar" id="superSidebar">

    {{-- BRAND --}}
    <a href="{{ url('/platform/dashboard') }}" class="super-brand">

        <span class="super-brand-icon">
            <i class="bi bi-mortarboard-fill"></i>
        </span>

        <span>
            <span class="super-brand-name d-block">
                {{ config('app.name', 'EduManage') }}
            </span>

            <span class="super-brand-caption">
                Platform control
            </span>
        </span>

    </a>

    {{-- PLATFORM NAVIGATION --}}
    <div class="super-section-label">
        Platform
    </div>

    @foreach ($navigation as $item)

        <a
            href="{{ url($item['url']) }}"
            class="super-nav-link {{ $item['active'] ? 'active' : '' }}"
            @if($item['active']) aria-current="page" @endif
        >
            <i class="bi {{ $item['icon'] }}"></i>

            <span>{{ $item['label'] }}</span>
        </a>

    @endforeach

    {{-- SYSTEM NAVIGATION --}}
    <div class="super-section-label">
        System
    </div>

    <a
        href="{{ url('/platform/activity') }}"
        class="super-nav-link {{ request()->is('platform/activity*') ? 'active' : '' }}"
    >
        <i class="bi bi-clock-history"></i>
        <span>Activity Logs</span>
    </a>

    <a
        href="{{ url('/platform/settings') }}"
        class="super-nav-link {{ request()->is('platform/settings*') ? 'active' : '' }}"
    >
        <i class="bi bi-gear"></i>
        <span>Platform Settings</span>
    </a>

    {{-- SIDEBAR BOTTOM --}}
    <div class="super-sidebar-bottom">

        <div class="super-section-label">
            Account
        </div>

        {{-- LOGOUT --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="super-logout">
                <i class="bi bi-box-arrow-left fs-5"></i>
                Sign out
            </button>
        </form>

        {{-- USER PROFILE --}}
        <div class="super-profile">

            <div class="super-avatar">
                {{ $initials ?: 'SA' }}
            </div>

            <div class="flex-grow-1 overflow-hidden">

                <div class="super-profile-name">
                    {{ $user?->name }}
                </div>

                <div class="super-profile-role">
                    <i class="bi bi-shield-check me-1"></i>
                    Super Administrator
                </div>

            </div>

            <i class="bi bi-three-dots-vertical text-secondary"></i>

        </div>

    </div>

</aside>

{{-- =====================================
     MAIN CONTENT
===================================== --}}

<div class="super-main">

    {{-- TOPBAR --}}
    <header class="super-topbar">

        <div class="d-flex align-items-center gap-3">

            {{-- MOBILE MENU BUTTON --}}
            <button
                type="button"
                class="super-menu-toggle"
                id="superMenuToggle"
                aria-label="Toggle navigation"
                aria-expanded="false"
                aria-controls="superSidebar"
            >
                <i class="bi bi-list"></i>
            </button>

            <div>

                <h1 class="super-page-title">
                    {{ $title ?? 'Platform Dashboard' }}
                </h1>

                <div class="super-breadcrumb">
                    Platform
                    <span class="mx-1">/</span>
                    {{ $title ?? 'Overview' }}
                </div>

            </div>

        </div>

        {{-- TOPBAR ACTIONS --}}
        <div class="super-topbar-actions">

            <span class="super-date">
                <i class="bi bi-calendar3 me-1"></i>
                {{ now()->format('D, d M Y') }}
            </span>

            <span class="super-status">
                <span class="super-status-dot"></span>
                Platform active
            </span>

            <div class="super-avatar">
                {{ $initials ?: 'SA' }}
            </div>

        </div>

    </header>

    {{-- PAGE CONTENT --}}
    <main class="super-content">

        {{ $slot }}

    </main>

</div>

{{-- =====================================
     JAVASCRIPT
===================================== --}}

<script>
    (function () {

        function initializeSuperLayout() {

            const sidebar = document.getElementById('superSidebar');
            const toggle = document.getElementById('superMenuToggle');
            const overlay = document.getElementById('superOverlay');

            if (!sidebar || !toggle || !overlay) {
                return;
            }

            // Prevent duplicate event listeners.
            if (toggle.dataset.initialized === 'true') {
                return;
            }

            toggle.dataset.initialized = 'true';

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

                const isOpen = sidebar.classList.contains('show');

                if (isOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }

            });

            overlay.addEventListener('click', closeSidebar);

            // Close sidebar when a navigation link is selected.
            sidebar.querySelectorAll('.super-nav-link').forEach(function (link) {

                link.addEventListener('click', function () {

                    if (window.innerWidth <= 991) {
                        closeSidebar();
                    }

                });

            });

            // Close with Escape key.
            document.addEventListener('keydown', function (event) {

                if (event.key === 'Escape') {
                    closeSidebar();
                }

            });

            // Close when screen becomes desktop size.
            window.addEventListener('resize', function () {

                if (window.innerWidth > 991) {
                    closeSidebar();
                }

            });

        }

        if (document.readyState === 'loading') {

            document.addEventListener(
                'DOMContentLoaded',
                initializeSuperLayout
            );

        } else {

            initializeSuperLayout();

        }

    })();
</script>

{{-- Additional page scripts --}}
@stack('scripts')

{{-- Bootstrap JavaScript --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Livewire scripts --}}
@livewireScripts

</body>
</html>
