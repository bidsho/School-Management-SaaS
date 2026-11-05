
<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between
                align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Add Parent</h3>
            <p class="text-muted mb-0">
                Create a parent profile and login account.
            </p>
        </div>

        <a href="{{ route('school.parents.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show"
             role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Warning Message --}}
    @if (session()->has('warning'))
        <div class="alert alert-warning alert-dismissible fade show"
             role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('warning') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Parent Form --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 pt-4 px-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10
                            text-primary p-3">
                    <i class="bi bi-person-plus fs-4"></i>
                </div>

                <div>
                    <h5 class="fw-bold mb-1">Parent Information</h5>
                    <p class="text-muted small mb-0">
                        Enter the parent's details below.
                    </p>
                </div>
            </div>
        </div>

        <div class="card-body p-4">

            <form wire:submit="save">

                {{-- Personal Information --}}
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-person me-2 text-primary"></i>
                    Personal Information
                </h6>

                <div class="row g-3">

                    {{-- First Name --}}
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">
                            First Name <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="first_name"
                            wire:model="first_name"
                            class="form-control @error('first_name') is-invalid @enderror"
                            placeholder="Enter first name"
                            maxlength="100"
                            autocomplete="given-name"
                            required
                        >

                        @error('first_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Last Name --}}
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">
                            Last Name <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="last_name"
                            wire:model="last_name"
                            class="form-control @error('last_name') is-invalid @enderror"
                            placeholder="Enter last name"
                            maxlength="100"
                            autocomplete="family-name"
                            required
                        >

                        @error('last_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                <hr class="my-4">

                {{-- Login Information --}}
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-shield-lock me-2 text-primary"></i>
                    Login Information
                </h6>

                <div class="row g-3">

                    {{-- Email --}}
                    <div class="col-12">
                        <label for="email" class="form-label">
                            Email Address
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="email"
                            id="email"
                            wire:model="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="parent@example.com"
                            maxlength="255"
                            autocomplete="email"
                            required
                        >

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            This email will be used to sign in. A password
                            setup link will be requested after the account
                            is created.
                        </div>
                    </div>

                </div>

                <div class="alert alert-info border-0 mt-3 mb-0">
                    <div class="d-flex gap-2">
                        <i class="bi bi-envelope-paper fs-5"></i>

                        <div>
                            <div class="fw-semibold">
                                Parent Account Setup
                            </div>

                            <small>
                                The parent will use their email address to
                                sign in after setting a password. Ensure
                                your application's email delivery is
                                configured correctly.
                            </small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Contact Information --}}
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-telephone me-2 text-primary"></i>
                    Contact Information
                </h6>

                <div class="row g-3">

                    {{-- Phone --}}
                    <div class="col-md-6">
                        <label for="phone" class="form-label">
                            Phone Number
                        </label>

                        <input
                            type="tel"
                            id="phone"
                            wire:model="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            placeholder="Enter phone number"
                            maxlength="30"
                            autocomplete="tel"
                        >

                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Occupation --}}
                    <div class="col-md-6">
                        <label for="occupation" class="form-label">
                            Occupation
                        </label>

                        <input
                            type="text"
                            id="occupation"
                            wire:model="occupation"
                            class="form-control @error('occupation') is-invalid @enderror"
                            placeholder="Enter occupation"
                            maxlength="150"
                        >

                        @error('occupation')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="col-12">
                        <label for="address" class="form-label">
                            Residential Address
                        </label>

                        <textarea
                            id="address"
                            wire:model="address"
                            rows="3"
                            maxlength="2000"
                            class="form-control @error('address') is-invalid @enderror"
                            placeholder="Enter residential address"
                            autocomplete="street-address"
                        ></textarea>

                        @error('address')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                {{-- Form Actions --}}
                <div class="d-flex flex-wrap justify-content-end
                            gap-2 mt-4 pt-3 border-top">

                    <a href="{{ route('school.parents.index') }}"
                       class="btn btn-light border">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary px-4"
                        wire:loading.attr="disabled"
                        wire:target="save"
                    >
                        <span wire:loading.remove wire:target="save">
                            <i class="bi bi-person-check me-1"></i>
                            Create Parent Account
                        </span>

                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm
                                         me-2"
                                  aria-hidden="true"></span>
                            Creating account...
                        </span>
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>
