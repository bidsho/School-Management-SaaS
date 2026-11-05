
<div class="container-fluid py-4 student-edit-page">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="text-muted small mb-2">
                School Management
                <i class="bi bi-chevron-right mx-1"></i>
                Students
                <i class="bi bi-chevron-right mx-1"></i>
                Edit Student
            </div>

            <h3 class="fw-bold mb-1">Edit Student</h3>
            <p class="text-muted mb-0">
                Update student information, parent details and account access.
            </p>
        </div>

        <a href="{{ route('school.students.index') }}"
           class="btn btn-light border rounded-3 px-3">
            <i class="bi bi-arrow-left me-2"></i> Back to Students
        </a>
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-3 shadow-sm">
            <div class="fw-bold mb-2">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Please correct the following errors:
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

                {{-- Personal Information --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 px-4 pt-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="student-section-icon">
                                <i class="bi bi-person-vcard"></i>
                            </div>

                            <div>
                                <h5 class="fw-bold mb-1">Personal Information</h5>
                                <p class="text-muted small mb-0">
                                    Update the student's basic details.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="first_name" class="form-label">
                                    First Name <span class="text-danger">*</span>
                                </label>

                                <input type="text" id="first_name"
                                       wire:model="first_name"
                                       class="form-control form-control-lg @error('first_name') is-invalid @enderror"
                                       placeholder="Enter first name">

                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="last_name" class="form-label">
                                    Last Name <span class="text-danger">*</span>
                                </label>

                                <input type="text" id="last_name"
                                       wire:model="last_name"
                                       class="form-control form-control-lg @error('last_name') is-invalid @enderror"
                                       placeholder="Enter last name">

                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="middle_name" class="form-label">
                                    Middle Name
                                </label>

                                <input type="text" id="middle_name"
                                       wire:model="middle_name"
                                       class="form-control form-control-lg @error('middle_name') is-invalid @enderror"
                                       placeholder="Enter middle name">

                                @error('middle_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-envelope"></i>
                                    </span>

                                    <input type="email" id="email"
                                           wire:model="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           placeholder="student@example.com">
                                </div>

                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-telephone"></i>
                                    </span>

                                    <input type="tel" id="phone"
                                           wire:model="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="Enter phone number">
                                </div>

                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label">
                                    Date of Birth
                                </label>

                                <input type="date" id="date_of_birth"
                                       wire:model="date_of_birth"
                                       max="{{ now()->subDay()->format('Y-m-d') }}"
                                       class="form-control form-control-lg @error('date_of_birth') is-invalid @enderror">

                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="gender" class="form-label">Gender</label>

                                <select id="gender" wire:model="gender"
                                        class="form-select form-select-lg @error('gender') is-invalid @enderror">
                                    <option value="">Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>

                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">
                                    Residential Address
                                </label>

                                <textarea id="address" wire:model="address" rows="3"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Enter the student's residential address"></textarea>

                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Admission Information --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 px-4 pt-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="student-section-icon admission-icon">
                                <i class="bi bi-mortarboard"></i>
                            </div>

                            <div>
                                <h5 class="fw-bold mb-1">Admission Information</h5>
                                <p class="text-muted small mb-0">
                                    Manage the student's admission number and status.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="admission_number" class="form-label">
                                    Admission Number <span class="text-danger">*</span>
                                </label>

                                <input type="text" id="admission_number"
                                       wire:model="admission_number"
                                       class="form-control form-control-lg @error('admission_number') is-invalid @enderror"
                                       placeholder="e.g. ADM2026001">

                                @error('admission_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="form-text">
                                    This number identifies the student in your school.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">
                                    Student Status <span class="text-danger">*</span>
                                </label>

                                <select id="status" wire:model="status"
                                        class="form-select form-select-lg @error('status') is-invalid @enderror">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>

                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="form-text">
                                    Inactive students remain in the system.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Parent / Guardian --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 px-4 pt-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="student-section-icon parent-icon">
                                <i class="bi bi-people"></i>
                            </div>

                            <div>
                                <h5 class="fw-bold mb-1">Parent / Guardian</h5>
                                <p class="text-muted small mb-0">
                                    Link this student to a parent registered at your school.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-md-7">
                                <label for="parentId" class="form-label">
                                    Select Parent / Guardian
                                </label>

                                <select id="parentId" wire:model="parentId"
                                        class="form-select form-select-lg @error('parentId') is-invalid @enderror">
                                    <option value="">No parent selected</option>

                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}">
                                            {{ $parent->first_name }}
                                            {{ $parent->last_name }}
                                            @if ($parent->email)
                                                — {{ $parent->email }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                @error('parentId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($parents->isEmpty())
                                    <div class="form-text text-warning">
                                        No parents have been registered for this school yet.
                                        Register a parent first if you want to link one.
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-5">
                                <label for="relationship" class="form-label">
                                    Relationship
                                </label>

                                <select id="relationship" wire:model="relationship"
                                        class="form-select form-select-lg @error('relationship') is-invalid @enderror">
                                    <option value="parent">Parent</option>
                                    <option value="mother">Mother</option>
                                    <option value="father">Father</option>
                                    <option value="guardian">Guardian</option>
                                    <option value="grandparent">Grandparent</option>
                                    <option value="other">Other</option>
                                </select>

                                @error('relationship')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-text">
                                    Selecting a parent links the records; it does not automatically
                                    create a parent login account.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Student Login Account --}}
                @if (empty($student->user_id))
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 px-4 pt-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="student-section-icon account-icon">
                                    <i class="bi bi-shield-lock"></i>
                                </div>

                                <div>
                                    <h5 class="fw-bold mb-1">Student Login Account</h5>
                                    <p class="text-muted small mb-0">
                                        Allow this student to access the school portal.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" id="createLoginAccount"
                                       wire:model="createLoginAccount"
                                       class="form-check-input"
                                       role="switch">

                                <label for="createLoginAccount" class="form-check-label fw-semibold">
                                    Create a login account for this student
                                </label>
                            </div>

                            @error('createLoginAccount')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror

                            <div class="form-text mt-2">
                                The student must have a valid email address. The Livewire component
                                must create the account, assign the Student role, and provide a
                                secure password setup or reset process.
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info border-0 rounded-4">
                        <i class="bi bi-person-check me-2"></i>
                        This student already has a login account.
                    </div>
                @endif

                {{-- Actions --}}
                <div class="d-flex flex-wrap justify-content-end gap-2 mb-4">
                    <a href="{{ route('school.students.index') }}"
                       class="btn btn-light border px-4 py-2 rounded-3">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-primary px-4 py-2 rounded-3"
                            wire:loading.attr="disabled"
                            wire:target="save">

                        <span wire:loading.remove wire:target="save">
                            <i class="bi bi-check-circle me-2"></i>
                            Save Changes
                        </span>

                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-2"
                                  aria-hidden="true"></span>
                            Saving...
                        </span>
                    </button>
                </div>

            </div>

            {{-- Right Sidebar --}}
            <div class="col-xl-4">

                {{-- Live Preview --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 student-preview-card">
                    <div class="card-body p-4 text-center">

                        <div class="student-avatar mx-auto mb-3">
                            {{ strtoupper(substr($first_name ?: 'S', 0, 1)) }}{{ strtoupper(substr($last_name ?: '', 0, 1)) }}
                        </div>

                        <h5 class="fw-bold mb-1">
                            {{ trim($first_name . ' ' . $last_name) ?: 'Student Name' }}
                        </h5>

                        <p class="text-muted small mb-3">
                            {{ $admission_number ?: 'Admission number not set' }}
                        </p>

                        @if ($status === 'active')
                            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i> Active Student
                            </span>
                        @else
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">
                                <i class="bi bi-pause-circle me-1"></i> Inactive Student
                            </span>
                        @endif

                        <hr class="my-4">

                        <div class="text-start">

                            <div class="preview-detail mb-3">
                                <div class="preview-icon">
                                    <i class="bi bi-envelope"></i>
                                </div>

                                <div class="overflow-hidden">
                                    <div class="text-muted small">Email Address</div>
                                    <div class="fw-semibold text-break">
                                        {{ $email ?: 'Not provided' }}
                                    </div>
                                </div>
                            </div>

                            <div class="preview-detail mb-3">
                                <div class="preview-icon">
                                    <i class="bi bi-telephone"></i>
                                </div>

                                <div class="overflow-hidden">
                                    <div class="text-muted small">Phone Number</div>
                                    <div class="fw-semibold text-break">
                                        {{ $phone ?: 'Not provided' }}
                                    </div>
                                </div>
                            </div>

                            <div class="preview-detail mb-3">
                                <div class="preview-icon">
                                    <i class="bi bi-calendar-event"></i>
                                </div>

                                <div>
                                    <div class="text-muted small">Date of Birth</div>
                                    <div class="fw-semibold">
                                        {{ $date_of_birth ? \Illuminate\Support\Carbon::parse($date_of_birth)->format('M d, Y') : 'Not provided' }}
                                    </div>
                                </div>
                            </div>

                            <div class="preview-detail">
                                <div class="preview-icon">
                                    <i class="bi bi-person-check"></i>
                                </div>

                                <div class="overflow-hidden">
                                    <div class="text-muted small">Parent / Guardian</div>

                                    <div class="fw-semibold text-break">
                                        @php
                                            $selectedParent = $parents->firstWhere('id', $parentId);
                                        @endphp

                                        {{ $selectedParent
                                            ? $selectedParent->first_name . ' ' . $selectedParent->last_name
                                            : 'Not selected' }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Help Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="help-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>

                            <div>
                                <h6 class="fw-bold mb-2">Student Record Security</h6>

                                <p class="text-muted small mb-0">
                                    Changes apply to the student record belonging to your school.
                                    Verify the admission number and contact information before saving.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Account Information --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="help-icon account-help-icon">
                                <i class="bi bi-info-circle"></i>
                            </div>

                            <div>
                                <h6 class="fw-bold mb-2">Account Information</h6>

                                <p class="text-muted small mb-0">
                                    Creating a student login requires a valid email address,
                                    a configured password-reset or invitation workflow, and
                                    the Student role in your permissions database.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <style>
        .student-edit-page {
            color: #273142;
        }

        .student-edit-page .card {
            transition: box-shadow .2s ease;
        }

        .student-edit-page .card:hover {
            box-shadow: 0 8px 26px rgba(30, 45, 70, .06) !important;
        }

        .student-edit-page .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .student-edit-page .form-control,
        .student-edit-page .form-select {
            border-color: #e0e5ec;
            border-radius: 10px;
            font-size: 14px;
        }

        .student-edit-page .form-control:focus,
        .student-edit-page .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .10);
        }

        .student-edit-page .input-group-text {
            border-color: #e0e5ec;
            border-radius: 10px 0 0 10px;
        }

        .student-edit-page .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }

        .student-section-icon {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #edf4ff;
            color: #0d6efd;
            border-radius: 14px;
            font-size: 21px;
        }

        .admission-icon {
            background: #eafaf2;
            color: #198754;
        }

        .parent-icon {
            background: #f2eefe;
            color: #6f42c1;
        }

        .account-icon {
            background: #fff3e6;
            color: #fd7e14;
        }

        .student-avatar {
            width: 94px;
            height: 94px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 28px;
            background: linear-gradient(135deg, #0d6efd, #6c9eff);
            color: #fff;
            font-size: 32px;
            font-weight: 700;
            box-shadow: 0 8px 22px rgba(13, 110, 253, .18);
        }

        .student-preview-card {
            overflow: hidden;
            position: relative;
        }

        .student-preview-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #0d6efd, #6c9eff);
        }

        .preview-detail {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .preview-icon,
        .help-icon {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f0f5ff;
            color: #0d6efd;
            font-size: 18px;
        }

        .help-icon {
            background: #eafaf2;
            color: #198754;
        }

        .account-help-icon {
            background: #fff3e6;
            color: #fd7e14;
        }

        .student-edit-page .btn {
            font-size: 14px;
            font-weight: 600;
        }

        @media (max-width: 576px) {
            .student-edit-page {
                padding-left: 12px;
                padding-right: 12px;
            }
        }
    </style>

</div>
