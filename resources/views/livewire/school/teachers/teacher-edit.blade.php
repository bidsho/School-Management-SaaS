
<div class="container-fluid py-4 teacher-edit-page">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="teacher-header-icon">
                <i class="bi bi-person-gear"></i>
            </div>

            <div>
                <h3 class="fw-bold mb-1 text-dark">Edit Teacher</h3>
                <p class="text-secondary mb-0">
                    Update staff information and employment details.
                </p>
            </div>
        </div>

        <a href="{{ route('school.teachers.index') }}"
           class="btn btn-light border rounded-3 px-3 py-2">
            <i class="bi bi-arrow-left me-2"></i> Back to Teachers
        </a>
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm"
             role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Summary --}}
    @if ($errors->any())
        <div class="alert alert-danger rounded-3 border-0 shadow-sm">
            <div class="fw-semibold mb-1">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Please correct the following errors.
            </div>
            <ul class="mb-0 ps-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="save">

        <div class="row g-4">

            {{-- Main Form --}}
            <div class="col-xl-8">

                <div class="card teacher-form-card border-0 shadow-sm rounded-4">

                    <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="section-icon">
                                <i class="bi bi-person-vcard"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Personal Information</h5>
                                <small class="text-secondary">
                                    Basic details of the teaching staff member
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">

                            {{-- Staff Number --}}
                            <div class="col-md-6">
                                <label for="staff_number" class="form-label fw-semibold">
                                    Staff Number <span class="text-danger">*</span>
                                </label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-hash"></i>
                                    </span>
                                    <input
                                        id="staff_number"
                                        type="text"
                                        class="form-control @error('staff_number') is-invalid @enderror"
                                        wire:model="staff_number"
                                        placeholder="e.g. TCH001"
                                        required
                                    >
                                </div>
                                @error('staff_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- First Name --}}
                            <div class="col-md-6">
                                <label for="first_name" class="form-label fw-semibold">
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input
                                        id="first_name"
                                        type="text"
                                        class="form-control @error('first_name') is-invalid @enderror"
                                        wire:model="first_name"
                                        placeholder="Enter first name"
                                        required
                                    >
                                </div>
                                @error('first_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Middle Name --}}
                            <div class="col-md-6">
                                <label for="middle_name" class="form-label fw-semibold">
                                    Middle Name
                                </label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input
                                        id="middle_name"
                                        type="text"
                                        class="form-control @error('middle_name') is-invalid @enderror"
                                        wire:model="middle_name"
                                        placeholder="Enter middle name (optional)"
                                    >
                                </div>
                                @error('middle_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Last Name --}}
                            <div class="col-md-6">
                                <label for="last_name" class="form-label fw-semibold">
                                    Last Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input
                                        id="last_name"
                                        type="text"
                                        class="form-control @error('last_name') is-invalid @enderror"
                                        wire:model="last_name"
                                        placeholder="Enter last name"
                                        required
                                    >
                                </div>
                                @error('last_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">
                                    Phone Number
                                </label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-telephone"></i>
                                    </span>
                                    <input
                                        id="phone"
                                        type="tel"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        wire:model="phone"
                                        placeholder="Enter phone number"
                                    >
                                </div>
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Qualification --}}
                            <div class="col-md-6">
                                <label for="qualification" class="form-label fw-semibold">
                                    Qualification
                                </label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-mortarboard"></i>
                                    </span>
                                    <input
                                        id="qualification"
                                        type="text"
                                        class="form-control @error('qualification') is-invalid @enderror"
                                        wire:model="qualification"
                                        placeholder="e.g. B.Ed, B.Sc, M.Ed"
                                    >
                                </div>
                                @error('qualification')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">
                                    Residential Address
                                </label>
                                <textarea
                                    id="address"
                                    rows="3"
                                    class="form-control custom-textarea @error('address') is-invalid @enderror"
                                    wire:model="address"
                                    placeholder="Enter the teacher's residential address"
                                ></textarea>
                                @error('address')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Employment Details --}}
                <div class="card teacher-form-card border-0 shadow-sm rounded-4 mt-4">

                    <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="section-icon employment-icon">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Employment Details</h5>
                                <small class="text-secondary">
                                    Manage employment status and hiring date
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label for="employment_status" class="form-label fw-semibold">
                                    Employment Status <span class="text-danger">*</span>
                                </label>
                                <select
                                    id="employment_status"
                                    class="form-select custom-select @error('employment_status') is-invalid @enderror"
                                    wire:model="employment_status"
                                    required
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="on_leave">On Leave</option>
                                    <option value="terminated">Terminated</option>
                                </select>
                                @error('employment_status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hire Date --}}
                            <div class="col-md-6">
                                <label for="hired_at" class="form-label fw-semibold">
                                    Date Hired
                                </label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-calendar-event"></i>
                                    </span>
                                    <input
                                        id="hired_at"
                                        type="date"
                                        class="form-control @error('hired_at') is-invalid @enderror"
                                        wire:model="hired_at"
                                    >
                                </div>
                                @error('hired_at')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="d-flex flex-wrap justify-content-end gap-2 mt-4 mb-4">
                    <a href="{{ route('school.teachers.index') }}"
                       class="btn btn-light border rounded-3 px-4 py-2">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-primary rounded-3 px-4 py-2"
                            wire:loading.attr="disabled"
                            wire:target="save">

                        <span wire:loading.remove wire:target="save">
                            <i class="bi bi-check-circle me-2"></i>
                            Save Changes
                        </span>

                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-2"
                                  aria-hidden="true"></span>
                            Saving Changes...
                        </span>
                    </button>
                </div>

            </div>

            {{-- Right Side Information --}}
            <div class="col-xl-4">

                <div class="card teacher-profile-card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-center">

                        <div class="teacher-avatar mx-auto mb-3">
                            <i class="bi bi-person-fill"></i>
                        </div>

                        <h5 class="fw-bold mb-1">
                            {{ trim($first_name . ' ' . $middle_name . ' ' . $last_name) ?: 'Teacher Profile' }}
                        </h5>

                        <p class="text-secondary small mb-3">
                            {{ $staff_number ?: 'No staff number assigned' }}
                        </p>

                        @php
                            $statusStyles = [
                                'active' => 'success',
                                'inactive' => 'secondary',
                                'on_leave' => 'warning',
                                'terminated' => 'danger',
                            ];

                            $statusClass = $statusStyles[$employment_status] ?? 'secondary';
                        @endphp

                        <span class="badge rounded-pill bg-{{ $statusClass }}-subtle text-{{ $statusClass }} px-3 py-2">
                            {{ ucwords(str_replace('_', ' ', $employment_status)) }}
                        </span>

                        <hr class="my-4">

                        <div class="text-start">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="info-icon">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div>
                                    <small class="text-secondary d-block">Phone Number</small>
                                    <span class="fw-semibold">
                                        {{ $phone ?: 'Not provided' }}
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="info-icon">
                                    <i class="bi bi-mortarboard"></i>
                                </div>
                                <div>
                                    <small class="text-secondary d-block">Qualification</small>
                                    <span class="fw-semibold">
                                        {{ $qualification ?: 'Not provided' }}
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="info-icon">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div>
                                    <small class="text-secondary d-block">Date Hired</small>
                                    <span class="fw-semibold">
                                        {{ $hired_at ?: 'Not provided' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Helpful Note --}}
                <div class="card border-0 rounded-4 help-card">
                    <div class="card-body p-4">
                        <div class="d-flex gap-3">
                            <div class="help-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-2">Keep Information Accurate</h6>
                                <p class="text-secondary small mb-0">
                                    Make sure the staff number and personal
                                    information are correct before saving.
                                    Changes apply to this school's teacher record.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>
    <style>
    .teacher-edit-page {
        color: #172033;
    }

    .teacher-header-icon {
        width: 54px;
        height: 54px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: #e9efff;
        color: #3457d5;
        font-size: 25px;
    }

    .teacher-form-card,
    .teacher-profile-card {
        overflow: hidden;
        transition: box-shadow .2s ease;
    }

    .teacher-form-card:hover,
    .teacher-profile-card:hover {
        box-shadow: 0 10px 30px rgba(26, 39, 70, .07) !important;
    }

    .section-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: #edf2ff;
        color: #3457d5;
        font-size: 20px;
        flex-shrink: 0;
    }

    .employment-icon {
        background: #e5f7f0;
        color: #16865d;
    }

    .teacher-edit-page .form-label {
        color: #344054;
        font-size: .9rem;
        margin-bottom: .55rem;
    }

    .custom-input-group .input-group-text {
        background: #f8faff;
        border-color: #dfe5ef;
        color: #718096;
        min-width: 45px;
        justify-content: center;
    }

    .custom-input-group .form-control,
    .custom-textarea,
    .custom-select {
        min-height: 46px;
        border-color: #dfe5ef;
        border-radius: 0 10px 10px 0;
        font-size: .92rem;
        box-shadow: none;
    }

    .custom-input-group .form-control:focus,
    .custom-textarea:focus,
    .custom-select:focus {
        border-color: #7793f5;
        box-shadow: 0 0 0 .2rem rgba(52, 87, 213, .09);
    }

    .custom-input-group .form-control.is-invalid {
        border-color: #dc3545;
    }

    .custom-textarea {
        border-radius: 10px;
        resize: vertical;
    }

    .custom-select {
        border-radius: 10px;
    }

    .teacher-avatar {
        width: 88px;
        height: 88px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: linear-gradient(135deg, #e7edff, #f3eaff);
        color: #405bd5;
        font-size: 43px;
    }

    .info-icon {
        width: 37px;
        height: 37px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 11px;
        background: #f0f4ff;
        color: #405bd5;
    }

    .help-card {
        background: #f7f9ff;
        border: 1px solid #e6ebfb !important;
    }

    .help-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 12px;
        background: #e5f7ef;
        color: #16865d;
        font-size: 21px;
    }

    .teacher-edit-page .btn-primary {
        background: #3457d5;
        border-color: #3457d5;
    }

    .teacher-edit-page .btn-primary:hover {
        background: #2948bb;
        border-color: #2948bb;
    }

    @media (max-width: 575.98px) {
        .teacher-edit-page {
            padding-left: 12px;
            padding-right: 12px;
        }

        .teacher-edit-page .card-body {
            padding: 20px !important;
        }
    }
</style>
</div>


