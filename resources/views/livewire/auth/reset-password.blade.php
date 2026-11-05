
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ __('Reset password') }} - {{ config('app.name', 'School Management SaaS') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --text-dark: #172033;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI",
                Roboto, sans-serif;
            background: #f1f5f9;
            color: var(--text-dark);
        }

        .reset-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }

        .reset-card {
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            background: #fff;
            border: 1px solid rgba(226, 232, 240, .9);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .08);
        }

        .reset-header {
            padding: 34px 36px 12px;
            text-align: center;
        }

        .brand-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            border-radius: 18px;
            background: #eef2ff;
            color: var(--primary-color);
            font-size: 28px;
        }

        .reset-header h1 {
            margin-bottom: 10px;
            font-size: 26px;
            font-weight: 750;
            letter-spacing: -.7px;
        }

        .reset-header p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .reset-body {
            padding: 26px 36px 34px;
        }

        .form-label {
            margin-bottom: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 650;
        }

        .form-control {
            min-height: 48px;
            padding: 12px 14px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 14px;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .12);
        }

        .input-group .form-control {
            border-radius: 10px 0 0 10px;
        }

        .input-group .btn {
            min-width: 48px;
            border: 1px solid var(--border-color);
            border-left: 0;
            border-radius: 0 10px 10px 0;
            color: #64748b;
            background: #fff;
        }

        .input-group .btn:hover {
            background: #f8fafc;
        }

        .btn-reset {
            min-height: 49px;
            width: 100%;
            margin-top: 8px;
            border: none;
            border-radius: 10px;
            background: var(--primary-color);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            transition: background .2s, transform .2s;
        }

        .btn-reset:hover {
            background: var(--primary-hover);
            color: #fff;
            transform: translateY(-1px);
        }

        .security-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 22px;
            padding: 13px;
            border-radius: 10px;
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .security-note i {
            margin-top: 1px;
            color: var(--primary-color);
            font-size: 16px;
        }

        .reset-footer {
            padding: 16px 20px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
        }

        .alert {
            border-radius: 10px;
            font-size: 13px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-top: 22px;
            color: var(--text-muted);
            font-size: 13px;
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        @media (max-width: 480px) {
            .reset-header {
                padding: 28px 22px 10px;
            }

            .reset-body {
                padding: 22px;
            }

            .reset-header h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>
    <main class="reset-wrapper">
        <section class="reset-card">

            <div class="reset-header">
                <div class="brand-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>

                <h1>{{ __('Create a new password') }}</h1>

                <p>
                    {{ __('Choose a strong password to secure your account. Enter it below and confirm it to continue.') }}
                </p>
            </div>

            <div class="reset-body">

                {{-- Session status --}}
                @if (session('status'))
                    <div class="alert alert-success d-flex align-items-start gap-2"
                         role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                {{-- General validation errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <div class="fw-semibold mb-1">
                            {{ __('Please correct the following errors:') }}
                        </div>

                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    {{-- Password reset token --}}
                    <input
                        type="hidden"
                        name="token"
                        value="{{ request()->route('token') }}"
                    >

                    {{-- Email address --}}
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            {{ __('Email address') }}
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', request('email')) }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="you@example.com"
                            autocomplete="email"
                            required
                            autofocus
                        >

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- New password --}}
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            {{ __('New password') }}
                        </label>

                        <div class="input-group">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="{{ __('Enter a new password') }}"
                                autocomplete="new-password"
                                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                                required
                            >

                            <button
                                class="btn"
                                type="button"
                                data-toggle-password="password"
                                aria-label="{{ __('Show password') }}"
                                title="{{ __('Show password') }}"
                            >
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>

                        @error('password')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            {{ __('Use a strong password that you have not used elsewhere.') }}
                        </div>
                    </div>

                    {{-- Confirm password --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            {{ __('Confirm new password') }}
                        </label>

                        <div class="input-group">
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="{{ __('Re-enter your new password') }}"
                                autocomplete="new-password"
                                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                                required
                            >

                            <button
                                class="btn"
                                type="button"
                                data-toggle-password="password_confirmation"
                                aria-label="{{ __('Show password') }}"
                                title="{{ __('Show password') }}"
                            >
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="btn btn-reset"
                        data-test="reset-password-button"
                    >
                        <i class="bi bi-shield-check me-2"></i>
                        {{ __('Reset password') }}
                    </button>
                </form>

                {{-- Back to login --}}
                @if (Route::has('login'))
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="back-link">
                            <i class="bi bi-arrow-left"></i>
                            {{ __('Back to login') }}
                        </a>
                    </div>
                @endif

                <div class="security-note">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong>{{ __('Security tip') }}</strong><br>
                        {{ __('Never share your password with anyone. A secure password helps protect your school account and personal information.') }}
                    </div>
                </div>
            </div>

            <footer class="reset-footer">
                &copy; {{ date('Y') }} {{ config('app.name', 'School Management SaaS') }}.
                {{ __('All rights reserved.') }}
            </footer>

        </section>
    </main>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                const inputId = this.getAttribute('data-toggle-password');
                const input = document.getElementById(inputId);
                const icon = this.querySelector('i');

                if (!input || !icon) {
                    return;
                }

                const showingPassword = input.type === 'text';

                input.type = showingPassword ? 'password' : 'text';

                icon.classList.toggle('bi-eye', showingPassword);
                icon.classList.toggle('bi-eye-slash', !showingPassword);

                this.setAttribute(
                    'aria-label',
                    showingPassword ? 'Show password' : 'Hide password'
                );
            });
        });
    </script>
</body>
</html>
