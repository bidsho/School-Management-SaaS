
<div>
    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <a href="{{ route('parent.children.index') }}"
               class="text-decoration-none text-secondary small">
                <i class="bi bi-arrow-left me-1"></i>
                Back to My Children
            </a>

            <h4 class="fw-bold mt-2 mb-1">Student Profile</h4>
            <p class="text-secondary mb-0">
                Personal details and school admission information.
            </p>
        </div>

        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
            <i class="bi bi-shield-check me-1"></i>
            Parent access
        </span>
    </div>

    {{-- Student identity card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div style="height: 6px; background: linear-gradient(90deg, #4f46e5, #818cf8);"></div>

        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-4">
                @if ($student->photo)
                    <img
                        src="{{ asset('storage/' . $student->photo) }}"
                        alt="{{ $student->first_name }}'s photo"
                        class="rounded-circle flex-shrink-0 border"
                        style="width: 110px; height: 110px; object-fit: cover;"
                    >
                @else
                    <div
                        class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                        style="width: 110px; height: 110px; background: #eeeeff; color: #4338ca; font-size: 34px; font-weight: 800;"
                    >
                        {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($student->last_name ?? '', 0, 1)) }}
                    </div>
                @endif

                <div class="text-center text-sm-start flex-grow-1 min-w-0">
                    <h3 class="fw-bold mb-2 text-break">
                        {{ trim(($student->first_name ?? '') . ' ' . ($student->middle_name ?? '') . ' ' . ($student->last_name ?? '')) }}
                    </h3>

                    <div class="text-secondary mb-3">
                        <i class="bi bi-person-vcard me-2"></i>
                        {{ $student->admission_number ?: 'Admission number not assigned' }}
                    </div>

                    @php
                        $status = strtolower((string) ($student->status ?? 'unknown'));

                        $statusClass = match ($status) {
                            'active' => 'bg-success-subtle text-success',
                            'inactive', 'suspended' => 'bg-danger-subtle text-danger',
                            default => 'bg-secondary-subtle text-secondary',
                        };
                    @endphp

                    <span class="badge rounded-pill {{ $statusClass }} px-3 py-2 text-capitalize">
                        {{ $student->status ?: 'Status not specified' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Personal information --}}
        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>
                        Personal Information
                    </h5>
                    <p class="text-secondary small mb-0">
                        The personal details recorded by the school.
                    </p>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="text-secondary small mb-1">First name</div>
                            <div class="fw-semibold">
                                {{ $student->first_name ?: 'Not provided' }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="text-secondary small mb-1">Middle name</div>
                            <div class="fw-semibold">
                                {{ $student->middle_name ?: 'Not provided' }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="text-secondary small mb-1">Last name</div>
                            <div class="fw-semibold">
                                {{ $student->last_name ?: 'Not provided' }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="text-secondary small mb-1">Date of birth</div>
                            <div class="fw-semibold">
                                {{ $student->date_of_birth?->format('d M Y') ?? 'Not provided' }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="text-secondary small mb-1">Gender</div>
                            <div class="fw-semibold text-capitalize">
                                {{ $student->gender ?: 'Not provided' }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="text-secondary small mb-1">Admission number</div>
                            <div class="fw-semibold">
                                {{ $student->admission_number ?: 'Not assigned' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact information --}}
        <div class="col-12 col-xl-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-telephone text-primary me-2"></i>
                        Contact Information
                    </h5>
                    <p class="text-secondary small mb-0">
                        Student contact details, when provided.
                    </p>
                </div>

                <div class="card-body p-4">
                    <div class="mb-4">
                        <div class="text-secondary small mb-1">
                            <i class="bi bi-envelope me-2"></i>Email address
                        </div>

                        @if ($student->email)
                            <a href="mailto:{{ $student->email }}"
                               class="fw-semibold text-decoration-none text-break">
                                {{ $student->email }}
                            </a>
                        @else
                            <div class="fw-semibold">Not provided</div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <div class="text-secondary small mb-1">
                            <i class="bi bi-telephone me-2"></i>Phone number
                        </div>

                        @if ($student->phone)
                            <a href="tel:{{ $student->phone }}"
                               class="fw-semibold text-decoration-none">
                                {{ $student->phone }}
                            </a>
                        @else
                            <div class="fw-semibold">Not provided</div>
                        @endif
                    </div>

                    <div>
                        <div class="text-secondary small mb-1">
                            <i class="bi bi-geo-alt me-2"></i>Home address
                        </div>

                        <div class="fw-semibold" style="white-space: pre-line;">
                            {{ $student->address ?: 'Not provided' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Parent relationship --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-people text-primary me-2"></i>
                        Parent / Contact Information
                    </h5>
                    <p class="text-secondary small mb-0">
                        The parent account currently viewing this profile.
                    </p>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="text-secondary small mb-1">Parent name</div>
                            <div class="fw-semibold">
                                {{ $parent->full_name ?: 'Not provided' }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="text-secondary small mb-1">Relationship to student</div>
                            <div class="fw-semibold text-capitalize">
                                {{ $student->pivot->relationship ?: 'Parent' }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="text-secondary small mb-1">Parent email</div>
                            <div class="fw-semibold text-break">
                                {{ $parent->email ?: 'Not provided' }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="text-secondary small mb-1">Parent phone</div>
                            <div class="fw-semibold">
                                {{ $parent->phone ?: 'Not provided' }}
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="text-secondary small mb-1">Primary contact</div>

                            @if ($student->pivot->is_primary)
                                <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Yes, primary contact
                                </span>
                            @else
                                <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">
                                    Not the primary contact
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Read-only notice --}}
        <div class="col-12">
            <div class="alert alert-info border-0 rounded-4 mb-0">
                <i class="bi bi-info-circle me-2"></i>
                This profile is read-only. Contact the school administrator if any
                student information needs to be corrected.
            </div>
        </div>
    </div>
</div>
