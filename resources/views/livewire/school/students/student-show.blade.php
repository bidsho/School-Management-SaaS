
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Student Details</h3>
            <p class="text-muted mb-0">
                View the student's admission and personal information.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('school.students.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>

            <a href="{{ route('school.students.edit', $student->id) }}"
               class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Edit Student
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif
    

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary-subtle text-primary
                            d-flex align-items-center justify-content-center"
                     style="width: 76px; height: 76px; font-size: 28px;">
                    <i class="bi bi-person-fill"></i>
                </div>

                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-1">
                        {{ $student->first_name }}
                        {{ $student->middle_name }}
                        {{ $student->last_name }}
                    </h4>

                    <div class="text-muted">
                        Admission No: {{ $student->admission_number }}
                    </div>
                </div>

                <span class="badge rounded-pill
                    {{ $student->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                    {{ ucfirst($student->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-person-vcard text-primary me-2"></i>
                        Personal Information
                    </h5>
                </div>

                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted d-block">First Name</small>
                        <strong>{{ $student->first_name }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Middle Name</small>
                        <strong>{{ $student->middle_name ?: 'Not provided' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Last Name</small>
                        <strong>{{ $student->last_name }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Date of Birth</small>
                        <strong>
                            {{ $student->date_of_birth?->format('d M Y') ?? 'Not provided' }}
                        </strong>
                    </div>

                    <div>
                        <small class="text-muted d-block">Gender</small>
                        <strong>{{ $student->gender ? ucfirst($student->gender) : 'Not provided' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-telephone text-primary me-2"></i>
                        Contact Information
                    </h5>
                </div>

                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted d-block">Email Address</small>
                        <strong>{{ $student->email ?: 'Not provided' }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Phone Number</small>
                        <strong>{{ $student->phone ?: 'Not provided' }}</strong>
                    </div>

                    <div>
                        <small class="text-muted d-block">Home Address</small>
                        <strong>{{ $student->address ?: 'Not provided' }}</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
