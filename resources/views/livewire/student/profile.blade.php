<div class="container-fluid py-4">

    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Profile</h3>
            <p class="text-muted mb-0">
                View and update your personal information.
            </p>
        </div>

        <span class="badge bg-primary-subtle text-primary px-3 py-2">
            <i class="bi bi-person-check me-1"></i>
            Student Account
        </span>
    </div>

    {{-- Success message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please check the following fields:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        {{-- Student identity card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 text-center">

                    <div class="mb-3">
                        @if ($student->photo)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($student->photo) }}"
                                alt="Student photo"
                                class="rounded-circle border"
                                style="width: 120px; height: 120px; object-fit: cover;"
                            >
                        @else
                            <div class="rounded-circle bg-primary text-white d-inline-flex
                                        justify-content-center align-items-center fw-bold"
                                 style="width: 120px; height: 120px; font-size: 36px;">
                                {{ strtoupper(substr($student->first_name ?? 'S', 0, 1) . substr($student->last_name ?? 'T', 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <h4 class="fw-bold mb-1">
                        {{ trim(implode(' ', array_filter([
                            $student->first_name,
                            $student->middle_name,
                            $student->last_name,
                        ]))) }}
                    </h4>

                    <p class="text-muted mb-3">
                        {{ $student->admission_number ?: 'Admission number not assigned' }}
                    </p>

                    <div class="d-flex justify-content-center mb-4">
                        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                            {{ ucfirst($student->status ?? 'Student') }}
                        </span>
                    </div>

                    <hr>

                    <div class="text-start">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 bg-light p-2">
                                <i class="bi bi-envelope text-primary fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <small class="text-muted d-block">Email address</small>
                                <span class="fw-medium text-break">
                                    {{ $student->email ?: 'Not provided' }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 bg-light p-2">
                                <i class="bi bi-telephone text-primary fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Phone number</small>
                                <span class="fw-medium">
                                    {{ $student->phone ?: 'Not provided' }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-light p-2">
                                <i class="bi bi-journal-bookmark text-primary fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Enrolment records</small>
                                <span class="fw-bold">{{ $enrollmentCount }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Editable profile details --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>
                        Personal Information
                    </h5>
                    <p class="text-muted small mb-0">
                        Update your details and save your changes.
                    </p>
                </div>

                <div class="card-body p-4">
                    <form wire:submit="save">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="first_name" class="form-label fw-semibold">
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="first_name"
                                    type="text"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    wire:model="first_name"
                                    autocomplete="given-name"
                                >
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="middle_name" class="form-label fw-semibold">
                                    Middle Name
                                </label>
                                <input
                                    id="middle_name"
                                    type="text"
                                    class="form-control @error('middle_name') is-invalid @enderror"
                                    wire:model="middle_name"
                                >
                                @error('middle_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="last_name" class="form-label fw-semibold">
                                    Last Name <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="last_name"
                                    type="text"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    wire:model="last_name"
                                    autocomplete="family-name"
                                >
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="admission_number" class="form-label fw-semibold">
                                    Admission Number
                                </label>
                                <input
                                    id="admission_number"
                                    type="text"
                                    class="form-control bg-light"
                                    value="{{ $student->admission_number }}"
                                    readonly
                                >
                                <small class="text-muted">
                                    Contact your school administrator to change this.
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label fw-semibold">
                                    Date of Birth
                                </label>
                                <input
                                    id="date_of_birth"
                                    type="date"
                                    class="form-control @error('date_of_birth') is-invalid @enderror"
                                    wire:model="date_of_birth"
                                    max="{{ now()->toDateString() }}"
                                >
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="gender" class="form-label fw-semibold">
                                    Gender
                                </label>
                                <select
                                    id="gender"
                                    class="form-select @error('gender') is-invalid @enderror"
                                    wire:model="gender"
                                >
                                    <option value="">Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    Email Address
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    wire:model="email"
                                    autocomplete="email"
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">
                                    Phone Number
                                </label>
                                <input
                                    id="phone"
                                    type="text"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    wire:model="phone"
                                    autocomplete="tel"
                                >
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">
                                    Residential Address
                                </label>
                                <textarea
                                    id="address"
                                    rows="3"
                                    class="form-control @error('address') is-invalid @enderror"
                                    wire:model="address"
                                    autocomplete="street-address"
                                    placeholder="Enter your residential address"
                                ></textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <hr class="my-4">

                        <div class="d-flex flex-wrap justify-content-end gap-2">
                            <button
                                type="button"
                                class="btn btn-light border"
                                wire:click="$refresh"
                            >
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Refresh
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary px-4"
                                wire:loading.attr="disabled"
                                wire:target="save"
                            >
                                <span wire:loading.remove wire:target="save">
                                    <i class="bi bi-check2-circle me-1"></i>
                                    Save Changes
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Saving...
                                </span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            {{-- Account information --}}
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-shield-check text-success me-2"></i>
                        Account Information
                    </h6>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Student record ID</small>
                            <span class="fw-semibold">#{{ $student->id }}</span>
                        </div>

                        <div class="col-sm-6">
                            <small class="text-muted d-block">Profile last updated</small>
                            <span class="fw-semibold">
                                {{ $student->updated_at?->format('d M Y, h:i A') ?? 'Not available' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>