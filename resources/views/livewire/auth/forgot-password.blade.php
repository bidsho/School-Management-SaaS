
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ __('Forgot password') }} - {{ config('app.name', 'School Management SaaS') }}</title>

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

        .forgot-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }

        .forgot-card {
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            background: #fff;
            border: 1px solid rgba(226, 232, 240, .9);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .08);
        }

        .forgot-header {
            padding: 36px 36px 12px;
            text-align: center;
        }

        .brand-icon {
            width: 66px;
            height: 66px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: #eef2ff;
            color: var(--primary-color);
            font-size: 29px;
        }

        .forgot-header h1 {
            margin-bottom: 10px;
            font-size: 26px;
            font-weight: 750;
            letter-spacing: -.7px;
        }

        .forgot-header p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .forgot-body {
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

        .btn-submit {
            width: 100%;
            min-height: 49px;
            margin-top: 8px;
            border: none;
            border-radius: 10px;
            background: var(--primary-color);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            transition: background .2s, transform .2s;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            color: #fff;
            transform: translateY(-1px);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 24px;
            color: var(--text-muted);
            font-size: 13px;
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        .info-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 25px;
            padding: 14px;
            border-radius: 10px;
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 12px;
            line-height: 1.7;
        }

        .info-note i {
            margin-top: 2px;
            color: var(--primary-color);
            font-size: 16px;
        }

        .alert {
            border-radius: 10px;
            font-size: 13px;
        }

        .forgot-footer {
            padding: 16px 20px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .forgot-header {
                padding: 28px 22px 10px;
            }

            .forgot-body {
                padding: 22px;
            }

            .forgot-header h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>
    <main class="forgot-wrapper">
        <section class="forgot-card">

            <div class="forgot-header">
                <div class="brand-icon">
                    <i class="bi bi-envelope-lock"></i>
                </div>

                <h1>{{ __('Forgot your password?') }}</h1>

                <p>
                    {{ __('No problem. Enter the email address associated with your account and we will send you a password reset link.') }}
                </p>
            </div>

            <div class="forgot-body">

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
                    <div class="alert alert-danger" role="alert">
                        <div class="fw-semibold mb-1">
                            {{ __('Please check your email address.') }}
                        </div>

                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="form-label">
                            {{ __('Email address') }}
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="email@example.com"
                            autocomplete="email"
                            required
                            autofocus
                        >

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="btn btn-submit"
                        data-test="email-password-reset-link-button"
                    >
                        <i class="bi bi-send me-2"></i>
                        {{ __('Email password reset link') }}
                    </button>
                </form>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="back-link">
                        <i class="bi bi-arrow-left"></i>
                        {{ __('Return to login') }}
                    </a>
                </div>

                <div class="info-note">
                    <i class="bi bi-shield-check"></i>
                    <div>
                        <strong>{{ __('Your account security matters') }}</strong><br>
                        {{ __('Use the email address registered with your account. If the message does not arrive, check your spam folder.') }}
                    </div>
                </div>

            </div>

            <footer class="forgot-footer">
                &copy; {{ date('Y') }} {{ config('app.name', 'School Management SaaS') }}.
                {{ __('All rights reserved.') }}
            </footer>

        </section>
    </main>
</body>
</html>
