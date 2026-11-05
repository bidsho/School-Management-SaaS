
<div class="container-fluid py-4">

    {{-- Page header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-building me-2 text-primary"></i>
                School Profile & Payment Settings
            </h3>
            <p class="text-muted mb-0">
                Manage your school's branding, contact details, and invoice payment information.
            </p>
        </div>

        <span class="badge bg-primary-subtle text-primary border px-3 py-2">
            <i class="bi bi-shield-check me-1"></i>
            School Settings
        </span>
    </div>

    {{-- Success message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation summary --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm" role="alert">
            <div class="fw-semibold mb-2">
                Please correct the following errors:
            </div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="save">
        <div class="row g-4">

            {{-- Main settings --}}
            <div class="col-xl-8">

                {{-- School profile --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-building me-2 text-primary"></i>
                            School Profile
                        </h5>
                        <p class="text-muted small mb-0">
                            The information displayed on your school's documents and invoices.
                        </p>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-4">

                            {{-- Logo upload --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="logo">
                                    School Logo
                                </label>

                                <div class="d-flex flex-wrap align-items-center gap-4">
                                    <div class="school-logo-preview">
                                        @if ($logo)
                                            <img
                                                src="{{ $logo->temporaryUrl() }}"
                                                alt="New school logo preview"
                                            >
                                        @elseif ($currentLogoUrl)
                                            <img
                                                src="{{ $currentLogoUrl }}"
                                                alt="Current school logo"
                                            >
                                        @else
                                            <div class="school-logo-placeholder">
                                                <i class="bi bi-image"></i>
                                                <span>No logo</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-grow-1">
                                        <input
                                            id="logo"
                                            type="file"
                                            class="form-control @error('logo') is-invalid @enderror"
                                            wire:model="logo"
                                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        >

                                        @error('logo')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        <div class="form-text">
                                            JPG, JPEG, PNG, or WebP. Maximum file size: 2 MB.
                                        </div>

                                        <div wire:loading wire:target="logo"
                                             class="small text-primary mt-2">
                                            <span class="spinner-border spinner-border-sm me-1"></span>
                                            Uploading image...
                                        </div>

                                        @if ($logo)
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-secondary mt-2"
                                                wire:click="$set('logo', null)"
                                            >
                                                <i class="bi bi-x-circle me-1"></i>
                                                Cancel new logo
                                            </button>
                                        @elseif ($currentLogoUrl)
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger mt-2"
                                                wire:click="removeLogo"
                                                wire:confirm="Are you sure you want to remove the school logo?"
                                                wire:loading.attr="disabled"
                                                wire:target="removeLogo"
                                            >
                                                <i class="bi bi-trash me-1"></i>
                                                Remove logo
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- School name --}}
                            <div class="col-12">
                                <label for="schoolName" class="form-label fw-semibold">
                                    School Name <span class="text-danger">*</span>
                                </label>

                                <input
                                    id="schoolName"
                                    type="text"
                                    class="form-control @error('schoolName') is-invalid @enderror"
                                    wire:model="schoolName"
                                    placeholder="Enter your school's registered name"
                                    maxlength="255"
                                >

                                @error('schoolName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    School Email
                                </label>

                                <input
                                    id="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    wire:model="email"
                                    placeholder="school@example.com"
                                    autocomplete="email"
                                >

                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">
                                    Phone Number
                                </label>

                                <input
                                    id="phone"
                                    type="tel"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    wire:model="phone"
                                    placeholder="e.g. +234 800 000 0000"
                                    autocomplete="tel"
                                >

                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">
                                    School Address
                                </label>

                                <textarea
                                    id="address"
                                    rows="3"
                                    maxlength="1000"
                                    class="form-control @error('address') is-invalid @enderror"
                                    wire:model="address"
                                    placeholder="Enter the school's full address"
                                ></textarea>

                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Bank details --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-bank me-2 text-primary"></i>
                            Bank Account Information
                        </h5>
                        <p class="text-muted small mb-0">
                            These details can be used on school invoices for parent payments.
                        </p>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="bank_name" class="form-label fw-semibold">
                                    Bank Name <span class="text-danger">*</span>
                                </label>

                                <input
                                    id="bank_name"
                                    type="text"
                                    class="form-control @error('bank_name') is-invalid @enderror"
                                    wire:model="bank_name"
                                    placeholder="e.g. Access Bank"
                                    maxlength="150"
                                >

                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="bank_account_name" class="form-label fw-semibold">
                                    Account Name <span class="text-danger">*</span>
                                </label>

                                <input
                                    id="bank_account_name"
                                    type="text"
                                    class="form-control @error('bank_account_name') is-invalid @enderror"
                                    wire:model="bank_account_name"
                                    placeholder="Registered account name"
                                    maxlength="150"
                                >

                                @error('bank_account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="bank_account_number" class="form-label fw-semibold">
                                    Account Number <span class="text-danger">*</span>
                                </label>

                                <input
                                    id="bank_account_number"
                                    type="text"
                                    inputmode="numeric"
                                    class="form-control @error('bank_account_number') is-invalid @enderror"
                                    wire:model="bank_account_number"
                                    placeholder="Enter account number"
                                    maxlength="30"
                                    autocomplete="off"
                                >

                                @error('bank_account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="form-text">
                                    Verify the account number before saving.
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="bank_payment_instructions"
                                       class="form-label fw-semibold">
                                    Payment Instructions
                                </label>

                                <textarea
                                    id="bank_payment_instructions"
                                    rows="4"
                                    maxlength="2000"
                                    class="form-control @error('bank_payment_instructions') is-invalid @enderror"
                                    wire:model="bank_payment_instructions"
                                    placeholder="Tell parents how to make payments and what reference to include."
                                ></textarea>

                                @error('bank_payment_instructions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Save button --}}
                <div class="d-flex flex-wrap justify-content-end gap-2">
                    <button
                        type="submit"
                        class="btn btn-primary btn-lg px-4"
                        wire:loading.attr="disabled"
                        wire:target="save,logo"
                        @disabled($errors->has('logo'))
                    >
                        <span wire:loading.remove wire:target="save">
                            <i class="bi bi-save me-2"></i>
                            Save School Settings
                        </span>

                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-2"
                                  aria-hidden="true"></span>
                            Saving settings...
                        </span>
                    </button>
                </div>

            </div>

            {{-- Sidebar previews --}}
            <div class="col-xl-4">

                {{-- School identity preview --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-eye me-2 text-primary"></i>
                            School Identity Preview
                        </h6>
                    </div>

                    <div class="card-body p-4 text-center">
                        <div class="school-logo-preview school-logo-preview-large mx-auto mb-3">
                            @if ($logo)
                                <img src="{{ $logo->temporaryUrl() }}"
                                     alt="School logo preview">
                            @elseif ($currentLogoUrl)
                                <img src="{{ $currentLogoUrl }}"
                                     alt="School logo">
                            @else
                                <div class="school-logo-placeholder">
                                    <i class="bi bi-building"></i>
                                    <span>School</span>
                                </div>
                            @endif
                        </div>

                        <h5 class="fw-bold mb-2">
                            {{ $schoolName ?: 'Your School Name' }}
                        </h5>

                        @if ($address)
                            <p class="small text-muted mb-3">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $address }}
                            </p>
                        @endif

                        @if ($email)
                            <div class="small text-muted mb-2">
                                <i class="bi bi-envelope me-2"></i>{{ $email }}
                            </div>
                        @endif

                        @if ($phone)
                            <div class="small text-muted">
                                <i class="bi bi-telephone me-2"></i>{{ $phone }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Invoice preview --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-receipt me-2 text-primary"></i>
                            Invoice Payment Preview
                        </h6>
                    </div>

                    <div class="card-body p-4">
                        <div class="border rounded-3 p-3 bg-light">

                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="school-logo-preview school-logo-preview-small">
                                    @if ($logo)
                                        <img src="{{ $logo->temporaryUrl() }}"
                                             alt="School logo">
                                    @elseif ($currentLogoUrl)
                                        <img src="{{ $currentLogoUrl }}"
                                             alt="School logo">
                                    @else
                                        <div class="school-logo-placeholder">
                                            <i class="bi bi-building"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="fw-bold text-break">
                                        {{ $schoolName ?: 'Your School Name' }}
                                    </div>
                                    <div class="small text-muted">
                                        Payment Information
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="small text-muted mb-1">Bank</div>
                            <div class="fw-semibold mb-3">
                                {{ $bank_name ?: 'Your bank name' }}
                            </div>

                            <div class="small text-muted mb-1">Account Name</div>
                            <div class="fw-semibold mb-3">
                                {{ $bank_account_name ?: 'Your account name' }}
                            </div>

                            <div class="small text-muted mb-1">Account Number</div>
                            <div class="fw-bold font-monospace mb-3">
                                {{ $bank_account_number ?: 'Your account number' }}
                            </div>

                            @if ($bank_payment_instructions)
                                <div class="small text-muted mb-1">
                                    Payment Instructions
                                </div>
                                <div class="small text-break">
                                    {{ $bank_payment_instructions }}
                                </div>
                            @endif

                            @if ($phone || $email)
                                <hr>
                                <div class="small text-muted">
                                    Contact the school if you need help with your payment.
                                </div>
                            @endif
                        </div>

                        <p class="small text-muted mt-3 mb-0">
                            This is a preview. Your invoice Blade template must also
                            read these saved settings to display them on actual invoices.
                        </p>
                    </div>
                </div>

                {{-- Security reminder --}}
                <div class="alert alert-warning border-0 rounded-4">
                    <h6 class="fw-bold">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Security Reminder
                    </h6>
                    <p class="small mb-0">
                        Only authorized school administrators should manage these settings.
                        Verify your school's bank details before distributing invoices.
                    </p>
                </div>

            </div>
        </div>
    </form>

    <style>
        .school-logo-preview {
            width: 110px;
            height: 110px;
            flex-shrink: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #ced4da;
            border-radius: 1rem;
            background: #f8f9fa;
        }

        .school-logo-preview img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: .35rem;
        }

        .school-logo-preview-large {
            width: 130px;
            height: 130px;
            border-style: solid;
            background: #fff;
        }

        .school-logo-preview-small {
            width: 58px;
            height: 58px;
            border-radius: .65rem;
        }

        .school-logo-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .3rem;
            color: #6c757d;
            font-size: .75rem;
        }

        .school-logo-placeholder i {
            font-size: 1.8rem;
        }

        .school-logo-preview-small .school-logo-placeholder i {
            font-size: 1.3rem;
        }

        .min-w-0 {
            min-width: 0;
        }
    </style>

</div>
