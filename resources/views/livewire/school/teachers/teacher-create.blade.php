
<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Register Teacher</h3>
            <p class="text-muted mb-0">
                Add a new member of your school's teaching staff and create their login account.
            </p>
        </div>

        <a href="{{ route('school.teachers.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Teachers
        </a>
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- General Error Message --}}
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-2">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Please correct the errors below.
            </div>

            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="save">

        {{-- =====================================================
             PERSONAL INFORMATION
        ====================================================== --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-person-badge me-2 text-primary"></i>
                    Personal Information
                </h5>

                <small class="text-muted">
                    Enter the teacher's official staff details.
                </small>
            </div>

            <div class="card-body p-4">
                <div class="row g-3">

                    {{-- Staff Number --}}
                    <div class="col-md-4">
                        <label for="staff_number" class="form-label fw-semibold">
                            Staff Number <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="staff_number"
                            class="form-control @error('staff_number') is-invalid @enderror"
                            wire:model="staff_number"
                            placeholder="e.g. TCH-001"
                        >

                        @error('staff_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- First Name --}}
                    <div class="col-md-4">
                        <label for="first_name" class="form-label fw-semibold">
                            First Name <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="first_name"
                            class="form-control @error('first_name') is-invalid @enderror"
                            wire:model="first_name"
                            placeholder="First name"
                            autocomplete="given-name"
                        >

                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Middle Name --}}
                    <div class="col-md-4">
                        <label for="middle_name" class="form-label fw-semibold">
                            Middle Name
                        </label>

                        <input
                            type="text"
                            id="middle_name"
                            class="form-control @error('middle_name') is-invalid @enderror"
                            wire:model="middle_name"
                            placeholder="Middle name (optional)"
                        >

                        @error('middle_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Last Name --}}
                    <div class="col-md-4">
                        <label for="last_name" class="form-label fw-semibold">
                            Last Name <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="last_name"
                            class="form-control @error('last_name') is-invalid @enderror"
                            wire:model="last_name"
                            placeholder="Last name"
                            autocomplete="family-name"
                        >

                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="col-md-4">
                        <label for="phone" class="form-label fw-semibold">
                            Phone Number
                        </label>

                        <input
                            type="tel"
                            id="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            wire:model="phone"
                            placeholder="Teacher's phone number"
                            autocomplete="tel"
                        >

                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Qualification --}}
                    <div class="col-md-4">
                        <label for="qualification" class="form-label fw-semibold">
                            Qualification
                        </label>

                        <input
                            type="text"
                            id="qualification"
                            class="form-control @error('qualification') is-invalid @enderror"
                            wire:model="qualification"
                            placeholder="e.g. B.Ed, B.Sc, PGDE"
                        >

                        @error('qualification')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Employment Status --}}
                    <div class="col-md-6">
                        <label for="employment_status" class="form-label fw-semibold">
                            Employment Status <span class="text-danger">*</span>
                        </label>

                        <select
                            id="employment_status"
                            class="form-select @error('employment_status') is-invalid @enderror"
                            wire:model="employment_status"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="on_leave">On Leave</option>
                            <option value="terminated">Terminated</option>
                        </select>

                        @error('employment_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Date Hired --}}
                    <div class="col-md-6">
                        <label for="hired_at" class="form-label fw-semibold">
                            Date Hired
                        </label>

                        <input
                            type="date"
                            id="hired_at"
                            class="form-control @error('hired_at') is-invalid @enderror"
                            wire:model="hired_at"
                            max="{{ now()->toDateString() }}"
                        >

                        @error('hired_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="col-12">
                        <label for="address" class="form-label fw-semibold">
                            Residential Address
                        </label>

                        <textarea
                            id="address"
                            class="form-control @error('address') is-invalid @enderror"
                            wire:model="address"
                            rows="3"
                            placeholder="Enter the teacher's residential address"
                            autocomplete="street-address"
                        ></textarea>

                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- =====================================================
             TEACHER LOGIN ACCOUNT
        ====================================================== --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-1 fw-semibold">
                    <i class="bi bi-shield-lock me-2 text-primary"></i>
                    Teacher Login Account
                </h5>

                <small class="text-muted">
                    Create the account the teacher will use to sign in to the school portal.
                </small>
            </div>

            <div class="card-body p-4">
                <div class="alert alert-info d-flex align-items-start gap-2">
                    <i class="bi bi-info-circle-fill mt-1"></i>

                    <div>
                        <strong>Login details</strong>
                        <p class="mb-0 small">
                            Use a valid email address and a strong initial password.
                            The teacher will sign in through your existing login page.
                        </p>
                    </div>
                </div>

                <div class="row g-3">

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">
                            Login Email <span class="text-danger">*</span>
                        </label>

                        <input
                            type="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            wire:model="email"
                            placeholder="teacher@example.com"
                            autocomplete="email"
                        >

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <small class="text-muted">
                            This email must not already belong to another user account.
                        </small>
                    </div>

                    {{-- Password --}}
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold">
                            Initial Password <span class="text-danger">*</span>
                        </label>

                        <input
                            type="password"
                            id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            wire:model="password"
                            placeholder="Minimum 8 characters"
                            autocomplete="new-password"
                        >

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <small class="text-muted">
                            Choose a strong password and share it securely.
                        </small>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-semibold">
                            Confirm Password <span class="text-danger">*</span>
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            class="form-control"
                            wire:model="password_confirmation"
                            placeholder="Re-enter the password"
                            autocomplete="new-password"
                        >
                    </div>

                </div>
            </div>
        </div>

        {{-- =====================================================
             FORM ACTIONS
        ====================================================== --}}
        <div class="d-flex justify-content-end gap-2 flex-wrap mb-4">

            <a
                href="{{ route('school.teachers.index') }}"
                class="btn btn-light border"
            >
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
                    Register Teacher & Create Login
                </span>

                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Creating Account...
                </span>
            </button>

        </div>

    </form>
</div>
