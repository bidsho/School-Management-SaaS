
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ __('Confirm password') }} - {{ config('app.name', 'School Management SaaS') }}</title>

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

        .confirm-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }

        .confirm-card {
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            background: #fff;
            border: 1px solid rgba(226, 232, 240, .9);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .08);
        }

        .confirm-header {
            padding: 36px 36px 12px;
            text-align: center;
        }

        .security-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 66px;
            height: 66px;
            margin: 0 auto 20px;
            border-radius: 18px;
            background: #eef2ff;
            color: var(--primary-color);
            font-size: 29px;
        }

        .confirm-header h1 {
            margin-bottom: 10px;
            font-size: 25px;
            font-weight: 750;
            letter-spacing: -.6px;
        }

        .confirm-header p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .confirm-body {
            padding: 26px 36px 34px;
        }

        .form-label {
            margin-bottom: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 650;
        }

        .form-control {
            min-height: 49px;
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
            background: #fff;
            color: var(--text-muted);
        }

        .input-group .btn:hover {
            background: #f8fafc;
        }

        .btn-confirm {
            width: 100%;
            min-height: 49px;
            border: none;
            border-radius: 10px;
            background: var(--primary-color);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            transition: background .2s, transform .2s;
        }

        .btn-confirm:hover {
            background: var(--primary-hover);
            color: #fff;
            transform: translateY(-1px);
        }

        .security-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 24px;
            padding: 14px;
            border-radius: 10px;
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 12px;
            line-height: 1.7;
        }

        .security-note i {
            margin-top: 2px;
            color: var(--primary-color);
            font-size: 16px;
        }

        .section-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #94a3b8;
            font-size: 12px;
        }

        .section-divider::before,
        .section-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .alert {
            border-radius: 10px;
            font-size: 13px;
        }

        .confirm-footer {
            padding: 16px 20px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .confirm-header {
                padding: 28px 22px 10px;
            }

            .confirm-body {
                padding: 22px;
            }

            .confirm-header h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>
    <main class="confirm-wrapper">
        <section class="confirm-card">

            {{-- Page header --}}
            <div class="confirm-header">
                <div class="security-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>

                <h1>{{ __('Confirm your password') }}</h1>

                <p>
                    {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                </p>
            </div>

            <div class="confirm-body">

                {{-- Session status --}}
                @if (session('status'))
                    <div class="alert alert-success d-flex align-items-start gap-2"
                         role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-start gap-2"
                         role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>

                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Passkey confirmation --}}
                @if (class_exists(\Illuminate\Support\Facades\Route::class)
                    && \Illuminate\Support\Facades\Route::has('passkey.confirm-options')
                    && \Illuminate\Support\Facades\Route::has('passkey.confirm'))
                    <div class="passkey-section">
                        <div class="fw-semibold small text-center mb-3">
                            {{ __('Use your passkey') }}
                        </div>

                        <x-passkey-verify
                            options-route="passkey.confirm-options"
                            submit-route="passkey.confirm"
                            :label="__('Confirm with passkey')"
                            :loading-label="__('Confirming...')"
                            :separator="__('Or confirm with password')"
                        />
                    </div>

                    <div class="section-divider">
                        {{ __('Or confirm with password') }}
                    </div>
                @endif

                {{-- Password confirmation form --}}
                <form method="POST" action="{{ route('password.confirm.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="password" class="form-label">
                            {{ __('Password') }}
                        </label>

                        <div class="input-group">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="{{ __('Enter your current password') }}"
                                autocomplete="current-password"
                                required
                                autofocus
                            >

                            <button
                                class="btn"
                                type="button"
                                id="togglePassword"
                                aria-label="{{ __('Show password') }}"
                                title="{{ __('Show password') }}"
                            >
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>

                        @error('password')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="btn btn-confirm"
                        data-test="confirm-password-button"
                    >
                        <i class="bi bi-shield-check me-2"></i>
                        {{ __('Confirm password') }}
                    </button>
                </form>

                <div class="security-note">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong>{{ __('Why do I need to confirm?') }}</strong><br>
                        {{ __('Password confirmation helps protect sensitive actions and account settings, even when you are already logged in.') }}
                    </div>
                </div>
            </div>

            <footer class="confirm-footer">
                &copy; {{ date('Y') }} {{ config('app.name', 'School Management SaaS') }}.
                {{ __('All rights reserved.') }}
            </footer>

        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');

            if (!passwordInput || !toggleButton) {
                return;
            }

            toggleButton.addEventListener('click', function () {
                const showing = passwordInput.type === 'text';
                passwordInput.type = showing ? 'password' : 'text';

                const icon = toggleButton.querySelector('i');

                if (icon) {
                    icon.classList.toggle('bi-eye', showing);
                    icon.classList.toggle('bi-eye-slash', !showing);
                }

                toggleButton.setAttribute(
                    'aria-label',
                    showing ? 'Show password' : 'Hide password'
                );
            });
        });
    </script>
</body>
</html>
