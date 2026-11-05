
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Create Account | {{ config('app.name', 'EduManage') }}</title>

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

        .register-page {
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

        .register-shell {
            width: 100%;
            max-width: 1050px;
            display: grid;
            grid-template-columns: .95fr 1.05fr;
            overflow: hidden;
            border-radius: 25px;
            background: #fff;
            box-shadow: 0 25px 80px rgba(32,49,91,.12);
        }

        .brand-panel {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 720px;
            padding: 44px;
            position: relative;
            overflow: hidden;
            color: #fff;
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

        .brand-logo, .brand-content, .brand-footer {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            font-size: 19px;
            font-weight: 800;
            text-decoration: none;
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
            margin: 65px 0 45px;
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
            font-weight: 650;
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
            width: 29px;
            height: 29px;
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
            padding: 48px 60px;
        }

        .form-content {
            width: 100%;
            max-width: 390px;
        }

        .mobile-brand {
            display: none;
            margin-bottom: 30px;
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
            font-size: 31px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .form-subtitle {
            margin-bottom: 26px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .field-label {
            display: block;
            margin-bottom: 8px;
            color: #303b54;
            font-size: 13px;
            font-weight: 700;
        }

        .input-wrap { position: relative; }

        .input-wrap > i {
            position: absolute;
            top: 50%;
            left: 15px;
            z-index: 2;
            color: #929db2;
            transform: translateY(-50%);
        }

        .form-control {
            min-height: 49px;
            padding: 12px 45px;
            border: 1px solid var(--border);
            border-radius: 11px;
            background: #fbfcff;
            color: var(--ink);
            font-size: 14px;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(49,87,213,.09);
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            z-index: 3;
            padding: 5px;
            border: 0;
            background: transparent;
            color: #8290a7;
            transform: translateY(-50%);
        }

        .password-toggle:hover { color: var(--primary); }

        .btn-register {
            min-height: 51px;
            border: 0;
            border-radius: 12px;
            background: var(--primary);
            color: #fff;
            font-size: 14px;
            font-weight: 750;
            box-shadow: 0 9px 20px rgba(49,87,213,.2);
            transition: .2s ease;
        }

        .btn-register:hover {
            background: var(--primary-dark);
            color: #fff;
            box-shadow: 0 12px 25px rgba(49,87,213,.28);
            transform: translateY(-1px);
        }

        .password-help {
            margin-top: 7px;
            color: #8792a7;
            font-size: 11px;
        }

        .security-note {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
            margin-top: 20px;
            color: #8b96aa;
            font-size: 11px;
        }

        .security-note i { color: #22a06b; }

        .signin-note {
            margin-top: 25px;
            padding-top: 22px;
            border-top: 1px solid #edf0f6;
            color: #7b869b;
            font-size: 13px;
            text-align: center;
        }

        .signin-note a {
            margin-left: 4px;
            color: var(--primary);
            font-weight: 750;
            text-decoration: none;
        }

        .signin-note a:hover { text-decoration: underline; }

        .alert {
            border-radius: 11px;
            font-size: 13px;
        }

        @media (max-width: 767.98px) {
            .register-page { padding: 16px 12px; }

            .register-shell {
                max-width: 470px;
                grid-template-columns: 1fr;
                border-radius: 21px;
            }

            .brand-panel { display: none; }

            .form-panel { padding: 34px 24px; }

            .mobile-brand {
                display: flex;
                align-items: center;
                gap: 11px;
            }

            .mobile-brand .logo-icon {
                border-color: #e0e7ff;
                background: #edf1ff;
                color: var(--primary);
            }

            .mobile-brand .brand-name {
                color: var(--ink);
                font-size: 16px;
                font-weight: 800;
            }

            .form-title { font-size: 28px; }
        }
    </style>
</head>

<body>
<div class="register-page">
    <div class="register-shell">

        <!-- Branding panel -->
        <aside class="brand-panel">
            <a href="{{ url('/') }}" class="brand-logo">
                <span class="logo-icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </span>
                <span>{{ config('app.name', 'EduManage') }}</span>
            </a>

            <div class="brand-content">
                <span class="brand-tag">
                    <i class="bi bi-stars"></i>
                    YOUR SCHOOL, SIMPLIFIED
                </span>

                <h1>Build a smarter school community.</h1>

                <p>
                    Create your account and discover a better way to manage
                    school operations, people, learning and progress.
                </p>

                <div class="feature-list">
                    <div class="feature-item">
                        <i class="bi bi-people-fill"></i>
                        <span>Organize students and staff</span>
                    </div>

                    <div class="feature-item">
                        <i class="bi bi-calendar2-check-fill"></i>
                        <span>Manage everyday school activities</span>
                    </div>

                    <div class="feature-item">
                        <i class="bi bi-shield-lock-fill"></i>
                        <span>Keep your school information protected</span>
                    </div>
                </div>
            </div>

            <div class="brand-footer">
                &copy; {{ date('Y') }} {{ config('app.name', 'EduManage') }}.
                All rights reserved.
            </div>
        </aside>

        <!-- Registration form -->
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

                <div class="welcome-label">Get started today</div>

                <h2 class="form-title">Create your account</h2>

                <p class="form-subtitle">
                    Fill in your details below to get started.
                </p>

                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <div class="fw-bold mb-1">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Please correct the following:
                        </div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="field-label">Full name</label>

                        <div class="input-wrap">
                            <i class="bi bi-person"></i>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                placeholder="Enter your full name"
                                autocomplete="name"
                                required
                                autofocus
                            >
                        </div>

                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="field-label">Email address</label>

                        <div class="input-wrap">
                            <i class="bi bi-envelope"></i>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="you@example.com"
                                autocomplete="email"
                                required
                            >
                        </div>

                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="field-label">Password</label>

                        <div class="input-wrap">
                            <i class="bi bi-lock"></i>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Create a strong password"
                                autocomplete="new-password"
                                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                                required
                            >

                            <button type="button"
                                    class="password-toggle"
                                    data-toggle-password="password"
                                    aria-label="Show password"
                                    aria-pressed="false">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>

                        <div class="password-help">
                            Choose a strong password that you don't use elsewhere.
                        </div>

                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="field-label">
                            Confirm password
                        </label>

                        <div class="input-wrap">
                            <i class="bi bi-shield-lock"></i>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="form-control"
                                placeholder="Enter your password again"
                                autocomplete="new-password"
                                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                                required
                            >

                            <button type="button"
                                    class="password-toggle"
                                    data-toggle-password="password_confirmation"
                                    aria-label="Show password"
                                    aria-pressed="false">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                            class="btn btn-register w-100"
                            data-test="register-user-button">
                        Create account
                        <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="security-note">
                    <i class="bi bi-shield-check"></i>
                    Your information is handled securely.
                </div>

                <div class="signin-note">
                    Already have an account?
                    <a href="{{ route('login') }}">Sign in</a>
                </div>

            </div>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(
                    button.getAttribute('data-toggle-password')
                );

                const icon = button.querySelector('i');

                if (!input || !icon) return;

                const showing = input.type === 'password';

                input.type = showing ? 'text' : 'password';
                icon.className = showing ? 'bi bi-eye-slash' : 'bi bi-eye';

                button.setAttribute(
                    'aria-label',
                    showing ? 'Hide password' : 'Show password'
                );

                button.setAttribute('aria-pressed', String(showing));
            });
        });
    });
</script>
</body>
</html>