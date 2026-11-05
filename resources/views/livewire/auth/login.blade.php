
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Log in') }} | {{ config('app.name', 'School Management') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #3157d5;
            --primary-dark: #2444b5;
            --ink: #17213a;
            --muted: #78839a;
            --border: #e6eaf2;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            color: var(--ink);
            background: #f4f7fc;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 35px 16px;
            background:
                radial-gradient(circle at 8% 10%, rgba(49,87,213,.09), transparent 30%),
                radial-gradient(circle at 95% 90%, rgba(14,165,233,.08), transparent 27%),
                #f4f7fc;
        }

        .login-shell {
            width: 100%;
            max-width: 1050px;
            display: grid;
            grid-template-columns: 0.95fr 1.05fr;
            overflow: hidden;
            border-radius: 25px;
            background: #fff;
            box-shadow: 0 25px 80px rgba(32, 49, 91, .12);
        }

        .brand-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 650px;
            padding: 44px;
            overflow: hidden;
            color: white;
            background: linear-gradient(145deg, #2443b2, #3157d5 55%, #5279f0);
        }

        .brand-panel::before,
        .brand-panel::after {
            content: "";
            position: absolute;
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 50%;
            pointer-events: none;
        }

        .brand-panel::before {
            width: 370px;
            height: 370px;
            right: -175px;
            top: 90px;
        }

        .brand-panel::after {
            width: 270px;
            height: 270px;
            right: -125px;
            top: 140px;
        }

        .brand-content,
        .brand-footer,
        .brand-logo {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            font-size: 19px;
            font-weight: 750;
            letter-spacing: -.4px;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 14px;
            background: rgba(255,255,255,.15);
            font-size: 23px;
        }

        .brand-content {
            margin-top: 75px;
            margin-bottom: 50px;
        }

        .brand-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
            padding: 8px 12px;
            border: 1px solid rgba(255,255,255,.22);
            border-radius: 50px;
            background: rgba(255,255,255,.12);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .4px;
        }

        .brand-content h1 {
            max-width: 390px;
            margin-bottom: 20px;
            font-size: clamp(30px, 3.5vw, 43px);
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -1.5px;
        }

        .brand-content p {
            max-width: 365px;
            margin-bottom: 30px;
            color: rgba(255,255,255,.8);
            font-size: 15px;
            line-height: 1.9;
        }

        .feature-list {
            display: grid;
            gap: 15px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,.94);
            font-size: 13px;
        }

        .feature-item i {
            width: 28px;
            height: 28px;
            display: grid;
            place-items: center;
            border-radius: 9px;
            background: rgba(255,255,255,.15);
        }

        .brand-footer {
            color: rgba(255,255,255,.68);
            font-size: 12px;
        }

        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 55px 60px;
        }

        .form-content {
            width: 100%;
            max-width: 390px;
        }

        .mobile-brand {
            display: none;
            margin-bottom: 35px;
        }

        .welcome-label {
            margin-bottom: 10px;
            color: var(--primary);
            font-size: 12px;
            font-weight: 750;
            letter-spacing: 1.6px;
            text-transform: uppercase;
        }

        .form-title {
            margin-bottom: 10px;
            color: var(--ink);
            font-size: 31px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .form-subtitle {
            margin-bottom: 30px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .field-label {
            display: block;
            margin-bottom: 9px;
            color: #303b54;
            font-size: 13px;
            font-weight: 700;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap > i {
            position: absolute;
            top: 50%;
            left: 16px;
            z-index: 2;
            color: #929db2;
            transform: translateY(-50%);
        }

        .form-control {
            min-height: 52px;
            padding: 13px 15px 13px 45px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #fbfcff;
            color: var(--ink);
            font-size: 14px;
            box-shadow: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }

        .form-control:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(49,87,213,.09);
        }

        .password-input { padding-right: 48px; }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 13px;
            z-index: 3;
            padding: 5px;
            border: 0;
            background: transparent;
            color: #8290a7;
            transform: translateY(-50%);
        }

        .password-toggle:hover { color: var(--primary); }

        .form-check-input {
            width: 16px;
            height: 16px;
            margin-top: 3px;
            border-color: #cbd3e1;
            cursor: pointer;
        }

        .form-check-input:checked {
            border-color: var(--primary);
            background-color: var(--primary);
        }

        .form-check-label {
            color: #69758b;
            font-size: 13px;
            cursor: pointer;
        }

        .forgot-link {
            color: var(--primary);
            font-size: 13px;
            font-weight: 650;
            text-decoration: none;
        }

        .forgot-link:hover { color: var(--primary-dark); }

        .btn-login {
            min-height: 52px;
            border: 0;
            border-radius: 12px;
            background: var(--primary);
            color: white;
            font-size: 14px;
            font-weight: 750;
            box-shadow: 0 9px 20px rgba(49,87,213,.2);
            transition: transform .2s, background .2s, box-shadow .2s;
        }

        .btn-login:hover {
            background: var(--primary-dark);
            color: white;
            box-shadow: 0 12px 25px rgba(49,87,213,.28);
            transform: translateY(-1px);
        }

        .security-note {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
            margin-top: 22px;
            color: #8b96aa;
            font-size: 11px;
        }

        .security-note i { color: #22a06b; }

        .signup-note {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #edf0f6;
            color: #7b869b;
            font-size: 13px;
            text-align: center;
        }

        .signup-note a {
            margin-left: 4px;
            color: var(--primary);
            font-weight: 750;
            text-decoration: none;
        }

        .signup-note a:hover { text-decoration: underline; }

        .alert {
            border-radius: 12px;
            font-size: 13px;
        }

        @media (max-width: 767.98px) {
            .login-page { padding: 18px 12px; }

            .login-shell {
                max-width: 470px;
                grid-template-columns: 1fr;
                border-radius: 21px;
            }

            .brand-panel { display: none; }

            .form-panel { padding: 38px 25px; }

            .mobile-brand {
                display: flex;
                align-items: center;
                gap: 11px;
            }

            .mobile-brand .logo-icon {
                background: #edf1ff;
                color: var(--primary);
                border-color: #e0e7ff;
            }

            .mobile-brand .brand-name {
                color: var(--ink);
                font-size: 16px;
                font-weight: 800;
            }

            .form-title { font-size: 28px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                transition: none !important;
            }
        }
    </style>
</head>

<body>
<div class="login-page">
    <div class="login-shell">

        <!-- Left branding panel -->
        <aside class="brand-panel">
            <a href="{{ url('/') }}" class="brand-logo text-decoration-none">
                <span class="logo-icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </span>
                <span>{{ config('app.name', 'EduManage') }}</span>
            </a>

            <div class="brand-content">
                <span class="brand-tag">
                    <i class="bi bi-stars"></i>
                    SMART SCHOOL MANAGEMENT
                </span>

                <h1>Everything your school needs, in one place.</h1>

                <p>
                    Manage students, teachers, classes, attendance and school
                    operations through one simple, secure platform.
                </p>

                <div class="feature-list">
                    <div class="feature-item">
                        <i class="bi bi-people-fill"></i>
                        <span>Student and staff management</span>
                    </div>

                    <div class="feature-item">
                        <i class="bi bi-calendar2-check-fill"></i>
                        <span>Attendance and academic tracking</span>
                    </div>

                    <div class="feature-item">
                        <i class="bi bi-shield-lock-fill"></i>
                        <span>Secure access for your school team</span>
                    </div>
                </div>
            </div>

            <div class="brand-footer">
                &copy; {{ date('Y') }} {{ config('app.name', 'EduManage') }}.
                Built for better learning.
            </div>
        </aside>

        <!-- Login form -->
        <main class="form-panel">
            <div class="form-content">

                <div class="mobile-brand">
                    <span class="logo-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <span class="brand-name">
                        {{ config('app.name', 'EduManage') }}
                    </span>
                </div>

                <div class="welcome-label">Welcome back</div>

                <h2 class="form-title">Sign in to your account</h2>

                <p class="form-subtitle">
                    Enter your details below to access your school dashboard.
                </p>

                <!-- Session status -->
                @if (session('status'))
                    <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Validation errors -->
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <div class="fw-bold mb-1">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            We couldn't sign you in.
                        </div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="field-label">
                            Email address
                        </label>

                        <div class="input-wrap">
                            <i class="bi bi-envelope"></i>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="you@school.com"
                                autocomplete="username"
                                required
                                autofocus
                            >
                        </div>

                        @error('email')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="password" class="field-label mb-0">
                                Password
                            </label>

                            @if (Route::has('password.request'))
                                <a class="forgot-link"
                                   href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <div class="input-wrap">
                            <i class="bi bi-lock"></i>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="form-control password-input @error('password') is-invalid @enderror"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                            >

                            <button type="button"
                                    class="password-toggle"
                                    id="togglePassword"
                                    aria-label="Show password"
                                    aria-pressed="false">
                                <i class="bi bi-eye" id="passwordIcon"></i>
                            </button>
                        </div>

                        @error('password')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center mb-4 mt-3">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="remember"
                                value="1"
                                id="remember"
                                @checked(old('remember'))
                            >
                            <label class="form-check-label ms-1" for="remember">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login w-100"
                            data-test="login-button">
                        <span>Sign in securely</span>
                        <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </form>

                <!-- Keep passkey login available -->
                <div class="mt-3">
                    <x-passkey-verify />
                </div>

                <div class="security-note">
                    <i class="bi bi-shield-check"></i>
                    Your account and information are protected.
                </div>

                @if (Route::has('register'))
                    <div class="signup-note">
                        Don't have an account?
                        <a href="{{ route('register') }}">Create an account</a>
                    </div>
                @endif

            </div>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');

        if (toggle && password && icon) {
            toggle.addEventListener('click', function () {
                const showing = password.type === 'password';

                password.type = showing ? 'text' : 'password';
                icon.className = showing ? 'bi bi-eye-slash' : 'bi bi-eye';
                toggle.setAttribute('aria-label', showing ? 'Hide password' : 'Show password');
                toggle.setAttribute('aria-pressed', String(showing));
            });
        }
    });
</script>
</body>
</html>
