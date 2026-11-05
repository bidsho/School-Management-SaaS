
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ __('Two-factor authentication') }} - {{ config('app.name', 'School Management SaaS') }}</title>

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

        [x-cloak] {
            display: none !important;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI",
                Roboto, sans-serif;
            background: #f1f5f9;
            color: var(--text-dark);
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }

        .auth-card {
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            background: #fff;
            border: 1px solid rgba(226, 232, 240, .9);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .08);
        }

        .auth-header {
            padding: 36px 36px 12px;
            text-align: center;
        }

        .auth-icon {
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

        .auth-header h1 {
            margin-bottom: 10px;
            font-size: 25px;
            font-weight: 750;
            letter-spacing: -.6px;
        }

        .auth-header p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .auth-body {
            padding: 26px 36px 34px;
        }

        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 9px;
            margin: 28px 0;
        }

        .otp-input {
            width: 48px;
            height: 56px;
            padding: 0;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            outline: none;
            background: #fff;
            color: var(--text-dark);
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            transition: border-color .2s, box-shadow .2s;
        }

        .otp-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .12);
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

        .btn-auth {
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

        .btn-auth:hover {
            background: var(--primary-hover);
            color: #fff;
            transform: translateY(-1px);
        }

        .toggle-section {
            margin-top: 25px;
            color: var(--text-muted);
            font-size: 13px;
            text-align: center;
        }

        .toggle-link {
            display: inline-block;
            margin-top: 8px;
            padding: 0;
            border: none;
            background: transparent;
            color: var(--primary-color);
            font-size: 13px;
            font-weight: 650;
            text-decoration: underline;
            text-underline-offset: 3px;
            cursor: pointer;
        }

        .security-note {
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

        .security-note i {
            margin-top: 2px;
            color: var(--primary-color);
            font-size: 16px;
        }

        .alert {
            border-radius: 10px;
            font-size: 13px;
        }

        .auth-footer {
            padding: 16px 20px;
            border-top: 1px solid #f1f5f9;
            color: #94a3b8;
            font-size: 12px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .auth-header {
                padding: 28px 22px 10px;
            }

            .auth-body {
                padding: 22px;
            }

            .otp-inputs {
                gap: 6px;
            }

            .otp-input {
                width: 42px;
                height: 52px;
                font-size: 21px;
            }
        }
    </style>
</head>

<body>
    <main class="auth-wrapper">
        <section class="auth-card"
                 x-data="{
                    showRecoveryInput: @js($errors->has('recovery_code')),
                    code: '',
                    recovery_code: '',

                    init() {
                        this.$nextTick(() => this.focusActiveInput());
                    },

                    focusActiveInput() {
                        if (this.showRecoveryInput) {
                            this.$refs.recovery_code?.focus();
                        } else {
                            this.$refs.otp0?.focus();
                        }
                    },

                    toggleInput() {
                        this.showRecoveryInput = !this.showRecoveryInput;
                        this.code = '';
                        this.recovery_code = '';

                        this.$nextTick(() => this.focusActiveInput());
                    },

                    focusNext(index, event) {
                        const input = event.target;
                        input.value = input.value.replace(/[^0-9]/g, '').slice(-1);
                        this.updateCode();

                        if (input.value && index < 5) {
                            this.$refs['otp' + (index + 1)]?.focus();
                        }
                    },

                    handleKey(index, event) {
                        if (event.key === 'Backspace' && !event.target.value && index > 0) {
                            this.$refs['otp' + (index - 1)]?.focus();
                        }

                        if (event.key === 'ArrowLeft' && index > 0) {
                            event.preventDefault();
                            this.$refs['otp' + (index - 1)]?.focus();
                        }

                        if (event.key === 'ArrowRight' && index < 5) {
                            event.preventDefault();
                            this.$refs['otp' + (index + 1)]?.focus();
                        }
                    },

                    handlePaste(event) {
                        event.preventDefault();

                        const digits = event.clipboardData
                            .getData('text')
                            .replace(/[^0-9]/g, '')
                            .slice(0, 6);

                        for (let i = 0; i < 6; i++) {
                            if (this.$refs['otp' + i]) {
                                this.$refs['otp' + i].value = digits[i] || '';
                            }
                        }

                        this.updateCode();

                        const nextIndex = Math.min(digits.length, 5);
                        this.$refs['otp' + nextIndex]?.focus();
                    },

                    updateCode() {
                        this.code = Array.from({ length: 6 }, (_, i) =>
                            this.$refs['otp' + i]?.value || ''
                        ).join('');
                    }
                 }">

            {{-- Header --}}
            <div class="auth-header">
                <div class="auth-icon"
                     x-bind:class="showRecoveryInput ? 'bg-warning-subtle text-warning-emphasis' : ''">
                    <i class="bi"
                       x-bind:class="showRecoveryInput ? 'bi-key' : 'bi-shield-lock'"></i>
                </div>

                <div x-show="!showRecoveryInput">
                    <h1>{{ __('Authentication code') }}</h1>
                    <p>
                        {{ __('Enter the six-digit authentication code provided by your authenticator application.') }}
                    </p>
                </div>

                <div x-show="showRecoveryInput">
                    <h1>{{ __('Recovery code') }}</h1>
                    <p>
                        {{ __('Enter one of your emergency recovery codes to confirm access to your account.') }}
                    </p>
                </div>
            </div>

            <div class="auth-body">

                {{-- Validation messages --}}
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

                <form method="POST" action="{{ route('two-factor.login.store') }}">
                    @csrf

                    {{-- Six-digit authenticator code --}}
                    <div x-show="!showRecoveryInput">
                        <label class="form-label d-block text-center">
                            {{ __('6-digit verification code') }}
                        </label>

                        <div class="otp-inputs">
                            @for ($i = 0; $i < 6; $i++)
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    maxlength="1"
                                    class="otp-input"
                                    x-ref="otp{{ $i }}"
                                    aria-label="{{ __('Verification digit') }} {{ $i + 1 }}"
                                    autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                                    @input="focusNext({{ $i }}, $event)"
                                    @keydown="handleKey({{ $i }}, $event)"
                                    @paste="handlePaste($event)"
                                    required
                                >
                            @endfor
                        </div>

                        {{-- Combined code submitted to Fortify --}}
                        <input type="hidden" name="code" x-bind:value="code">

                        @error('code')
                            <div class="text-danger small text-center mb-3">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Recovery code --}}
                    <div x-show="showRecoveryInput" x-cloak>
                        <label for="recovery_code" class="form-label">
                            {{ __('Recovery code') }}
                        </label>

                        <input
                            id="recovery_code"
                            type="text"
                            name="recovery_code"
                            x-ref="recovery_code"
                            x-model="recovery_code"
                            x-bind:required="showRecoveryInput"
                            x-bind:disabled="!showRecoveryInput"
                            autocomplete="off"
                            class="form-control @error('recovery_code') is-invalid @enderror"
                            placeholder="{{ __('Enter your recovery code') }}"
                        >

                        @error('recovery_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text mt-2">
                            {{ __('Each recovery code can generally be used only once.') }}
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="btn btn-auth mt-2"
                        x-bind:disabled="!showRecoveryInput && code.length !== 6"
                    >
                        <i class="bi bi-shield-check me-2"></i>
                        {{ __('Continue') }}
                    </button>
                </form>

                {{-- Switch authentication method --}}
                <div class="toggle-section">
                    <span>{{ __('Having trouble?') }}</span>

                    <div>
                        <button
                            type="button"
                            class="toggle-link"
                            @click="toggleInput()"
                            x-show="!showRecoveryInput"
                        >
                            <i class="bi bi-key me-1"></i>
                            {{ __('Login using a recovery code') }}
                        </button>

                        <button
                            type="button"
                            class="toggle-link"
                            @click="toggleInput()"
                            x-show="showRecoveryInput"
                        >
                            <i class="bi bi-phone me-1"></i>
                            {{ __('Login using an authentication code') }}
                        </button>
                    </div>
                </div>

                <div class="security-note">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong>{{ __('Keep your account secure') }}</strong><br>
                        {{ __('Never share your authentication or recovery codes with anyone. If you lose access to your authenticator, use a valid recovery code.') }}
                    </div>
                </div>

            </div>

            <footer class="auth-footer">
                &copy; {{ date('Y') }} {{ config('app.name', 'School Management SaaS') }}.
                {{ __('All rights reserved.') }}
            </footer>
        </section>
    </main>
</body>
</html>
