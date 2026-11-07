
<div class="container-fluid py-3">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

        <div>
            <h4 class="fw-bold mb-1">Register New School</h4>

            <p class="text-secondary mb-0">
                Add a school and create its first administrator account.
            </p>
        </div>

        <a href="{{ route('platform.schools.index') }}"
           class="btn btn-light border rounded-3 px-3">

            <i class="bi bi-arrow-left me-2"></i>
            Back to Schools

        </a>

    </div>


    {{-- CREATION ERROR --}}
    @if ($errors->has('creation'))
        <div class="alert alert-danger rounded-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ $errors->first('creation') }}
        </div>
    @endif


    {{-- SUCCESS MESSAGE --}}
    @if (session()->has('success'))
        <div class="alert alert-success rounded-3">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif


    {{-- WARNING MESSAGE --}}
    @if (session()->has('warning'))
        <div class="alert alert-warning rounded-3">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('warning') }}
        </div>
    @endif


    <form wire:submit="save">

        <div class="row g-4">

            {{-- MAIN FORM --}}
            <div class="col-12 col-xl-8">

                {{-- SCHOOL INFORMATION --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">

                    <div class="card-header bg-white border-0 p-4">

                        <div class="d-flex align-items-center gap-3">

                            <div class="rounded-3 bg-primary bg-opacity-10 text-primary
                                        d-flex align-items-center justify-content-center"
                                 style="width: 45px; height: 45px;">

                                <i class="bi bi-buildings fs-4"></i>

                            </div>

                            <div>
                                <h5 class="fw-bold mb-1">
                                    School Information
                                </h5>

                                <p class="text-secondary small mb-0">
                                    Enter the school's basic information.
                                </p>
                            </div>

                        </div>

                    </div>


                    <div class="card-body p-4">

                        <div class="row g-3">

                            {{-- SCHOOL NAME --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    School Name
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       class="form-control rounded-3 @error('name') is-invalid @enderror"
                                       wire:model="name"
                                       placeholder="Enter school name">

                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- SCHOOL EMAIL --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    School Email
                                </label>

                                <input type="email"
                                       class="form-control rounded-3 @error('email') is-invalid @enderror"
                                       wire:model="email"
                                       placeholder="info@school.com">

                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- PHONE --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Phone Number
                                </label>

                                <input type="text"
                                       class="form-control rounded-3 @error('phone') is-invalid @enderror"
                                       wire:model="phone"
                                       placeholder="+234...">

                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- TIMEZONE --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Timezone
                                </label>

                                <select class="form-select rounded-3 @error('timezone') is-invalid @enderror"
                                        wire:model="timezone">

                                    <option value="Africa/Lagos">
                                        Africa/Lagos (Nigeria)
                                    </option>

                                    <option value="Africa/Accra">
                                        Africa/Accra (Ghana)
                                    </option>

                                    <option value="Africa/Nairobi">
                                        Africa/Nairobi (Kenya)
                                    </option>

                                    <option value="Europe/London">
                                        Europe/London
                                    </option>

                                    <option value="America/New_York">
                                        America/New_York
                                    </option>

                                </select>

                                @error('timezone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- ADDRESS --}}
                            <div class="col-12">

                                <label class="form-label fw-semibold">
                                    School Address
                                </label>

                                <textarea class="form-control rounded-3 @error('address') is-invalid @enderror"
                                          wire:model="address"
                                          rows="3"
                                          placeholder="Enter the school's full address"></textarea>

                                @error('address')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- STATUS --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Initial Status
                                </label>

                                <select class="form-select rounded-3 @error('status') is-invalid @enderror"
                                        wire:model="status">

                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="suspended">Suspended</option>

                                </select>

                                @error('status')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>

                    </div>

                </div>


                {{-- FIRST SCHOOL ADMIN --}}
                <div class="card border-0 shadow-sm rounded-4">

                    <div class="card-header bg-white border-0 p-4">

                        <div class="d-flex align-items-center gap-3">

                            <div class="rounded-3 bg-success bg-opacity-10 text-success
                                        d-flex align-items-center justify-content-center"
                                 style="width: 45px; height: 45px;">

                                <i class="bi bi-person-badge fs-4"></i>

                            </div>

                            <div>
                                <h5 class="fw-bold mb-1">
                                    First School Administrator
                                </h5>

                                <p class="text-secondary small mb-0">
                                    Create the account that manages this school.
                                </p>
                            </div>

                        </div>

                    </div>


                    <div class="card-body p-4">

                        <div class="alert alert-info border-0 rounded-3 small">

                            <i class="bi bi-info-circle me-2"></i>

                            The administrator will only manage the school
                            registered here. A password setup link will be
                            sent to their email address.

                        </div>


                        <div class="row g-3">

                            {{-- ADMIN NAME --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Administrator Name
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       class="form-control rounded-3 @error('adminName') is-invalid @enderror"
                                       wire:model="adminName"
                                       placeholder="Enter full name">

                                @error('adminName')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            {{-- ADMIN EMAIL --}}
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Administrator Email
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="email"
                                       class="form-control rounded-3 @error('adminEmail') is-invalid @enderror"
                                       wire:model="adminEmail"
                                       placeholder="admin@school.com">

                                @error('adminEmail')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div class="form-text">
                                    This email must not already exist in the platform.
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- RIGHT SIDEBAR --}}
            <div class="col-12 col-xl-4">

                <div class="card border-0 shadow-sm rounded-4">

                    <div class="card-body p-4">

                        <h6 class="fw-bold mb-3">
                            Registration Summary
                        </h6>

                        <div class="d-flex align-items-center gap-3 mb-3">

                            <div class="bg-primary bg-opacity-10 text-primary
                                        rounded-3 d-flex align-items-center
                                        justify-content-center"
                                 style="width: 40px; height: 40px;">

                                <i class="bi bi-building"></i>

                            </div>

                            <div>
                                <div class="small text-secondary">
                                    School
                                </div>

                                <div class="fw-semibold">
                                    New school registration
                                </div>
                            </div>

                        </div>


                        <div class="d-flex align-items-center gap-3 mb-3">

                            <div class="bg-success bg-opacity-10 text-success
                                        rounded-3 d-flex align-items-center
                                        justify-content-center"
                                 style="width: 40px; height: 40px;">

                                <i class="bi bi-person-check"></i>

                            </div>

                            <div>
                                <div class="small text-secondary">
                                    Administrator
                                </div>

                                <div class="fw-semibold">
                                    School Admin
                                </div>
                            </div>

                        </div>


                        <div class="d-flex align-items-center gap-3 mb-4">

                            <div class="bg-warning bg-opacity-10 text-warning
                                        rounded-3 d-flex align-items-center
                                        justify-content-center"
                                 style="width: 40px; height: 40px;">

                                <i class="bi bi-envelope"></i>

                            </div>

                            <div>
                                <div class="small text-secondary">
                                    Account invitation
                                </div>

                                <div class="fw-semibold">
                                    Password setup email
                                </div>
                            </div>

                        </div>


                        <hr>


                        <div class="d-grid gap-2">

                            <button type="submit"
                                    class="btn btn-primary rounded-3 py-3 fw-semibold"
                                    wire:loading.attr="disabled"
                                    wire:target="save">

                                <span wire:loading.remove wire:target="save">

                                    <i class="bi bi-check-circle me-2"></i>

                                    Create School

                                </span>

                                <span wire:loading wire:target="save">

                                    <span class="spinner-border spinner-border-sm me-2"></span>

                                    Creating School...

                                </span>

                            </button>


                            <a href="{{ route('platform.schools.index') }}"
                               class="btn btn-light border rounded-3 py-2">

                                Cancel

                            </a>

                        </div>

                    </div>

                </div>


                <div class="alert alert-light border rounded-3 mt-3 small">

                    <i class="bi bi-shield-check text-success me-2"></i>

                    School registration and administrator creation are
                    processed together in a database transaction.

                </div>

            </div>

        </div>

    </form>

</div>
