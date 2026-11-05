
<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">Platform Settings</h3>
            <p class="text-secondary mb-0">
                Configure the global settings for your school management platform.
            </p>
        </div>

        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-shield-check me-1"></i>
            Super Admin
        </span>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Close"></button>
        </div>
    @endif

    <form wire:submit="save">
        <div class="row g-4">

            {{-- General settings --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-primary-subtle text-primary p-3">
                                <i class="bi bi-sliders fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">General Information</h5>
                                <p class="text-secondary small mb-0">
                                    Set the platform identity and contact details.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label for="platformName" class="form-label fw-semibold">
                                Platform Name
                            </label>
                            <input
                                id="platformName"
                                type="text"
                                class="form-control form-control-lg @error('platformName') is-invalid @enderror"
                                wire:model="platformName"
                                placeholder="School Management SaaS"
                            >
                            @error('platformName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="supportEmail" class="form-label fw-semibold">
                                    Support Email
                                </label>
                                <input
                                    id="supportEmail"
                                    type="email"
                                    class="form-control @error('supportEmail') is-invalid @enderror"
                                    wire:model="supportEmail"
                                    placeholder="support@example.com"
                                >
                                @error('supportEmail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="supportPhone" class="form-label fw-semibold">
                                    Support Phone
                                </label>
                                <input
                                    id="supportPhone"
                                    type="text"
                                    class="form-control @error('supportPhone') is-invalid @enderror"
                                    wire:model="supportPhone"
                                    placeholder="+234..."
                                >
                                @error('supportPhone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="defaultTimezone" class="form-label fw-semibold">
                                Default Timezone
                            </label>
                            <select
                                id="defaultTimezone"
                                class="form-select @error('defaultTimezone') is-invalid @enderror"
                                wire:model="defaultTimezone"
                            >
                                <option value="Africa/Lagos">Africa/Lagos (Nigeria)</option>
                                <option value="UTC">UTC</option>
                                <option value="Europe/London">Europe/London</option>
                                <option value="America/New_York">America/New_York</option>
                                <option value="Africa/Accra">Africa/Accra</option>
                                <option value="Africa/Nairobi">Africa/Nairobi</option>
                            </select>
                            @error('defaultTimezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                This is a platform default; individual schools may have their own timezone.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Registration settings --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-success-subtle text-success p-3">
                                <i class="bi bi-building-add fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">School Registration</h5>
                                <p class="text-secondary small mb-0">
                                    Control new school registration.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="form-check form-switch mb-3">
                            <input
                                id="schoolRegistrationEnabled"
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                wire:model="schoolRegistrationEnabled"
                            >
                            <label
                                class="form-check-label fw-semibold"
                                for="schoolRegistrationEnabled"
                            >
                                Allow school registration
                            </label>
                        </div>

                        @if ($schoolRegistrationEnabled)
                            <div class="alert alert-success small mb-0">
                                <i class="bi bi-check-circle me-1"></i>
                                School registration is enabled in the platform settings.
                            </div>
                        @else
                            <div class="alert alert-warning small mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                School registration is disabled in the platform settings.
                            </div>
                        @endif

                        <hr class="my-4">

                        <div class="small text-secondary">
                            <i class="bi bi-info-circle me-1"></i>
                            This setting stores your preference. It will only enforce registration restrictions after the school registration flow checks it.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Save --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="fw-bold mb-1">Save platform configuration</h6>
                            <p class="text-secondary small mb-0">
                                Changes are saved to the global platform settings table.
                            </p>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 py-2"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">
                                <i class="bi bi-save me-2"></i>Save Settings
                            </span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Saving...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
