
<div class="container-fluid py-4">

    @php
        $fullName = trim(
            $first_name . ' ' . $middle_name . ' ' . $last_name
        );

        $statusClasses = [
            'active' => 'success',
            'inactive' => 'secondary',
            'on_leave' => 'warning',
            'terminated' => 'danger',
        ];

        $statusLabels = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'on_leave' => 'On Leave',
            'terminated' => 'Terminated',
        ];

        $statusClass = $statusClasses[$employment_status] ?? 'secondary';
        $statusLabel = $statusLabels[$employment_status] ?? ucfirst($employment_status);
    @endphp

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="text-muted small mb-1">
                School Management / Teachers / Details
            </div>

            <h3 class="fw-bold mb-1">Teacher Details</h3>

            <p class="text-muted mb-0">
                View the teacher's personal and employment information.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('school.teachers.index') }}"
               class="btn btn-light border">
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>

            @can('edit teachers')
                <a href="{{ route('school.teachers.edit', ['teacher' => $teacherId]) }}"
                   class="btn btn-primary">
                    <i class="bi bi-pencil-square me-1"></i>
                    Edit Teacher
                </a>
            @endcan
        </div>
    </div>

    {{-- Teacher Profile --}}
    <div class="row g-4">

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-4">

                    <div class="teacher-avatar mx-auto mb-3">
                        {{ strtoupper(substr($first_name ?: 'T', 0, 1)) }}{{ strtoupper(substr($last_name ?: '', 0, 1)) }}
                    </div>

                    <h4 class="fw-bold mb-1">
                        {{ $fullName ?: 'Unnamed Teacher' }}
                    </h4>

                    <p class="text-muted mb-3">
                        {{ $qualification ?: 'Qualification not provided' }}
                    </p>

                    <span class="badge rounded-pill bg-{{ $statusClass }}-subtle text-{{ $statusClass }} px-3 py-2">
                        {{ $statusLabel }}
                    </span>

                    <hr class="my-4">

                    <div class="text-start">

                        <div class="profile-detail mb-3">
                            <div class="detail-icon">
                                <i class="bi bi-person-badge"></i>
                            </div>

                            <div>
                                <div class="text-muted small">Staff Number</div>
                                <div class="fw-semibold">
                                    {{ $staff_number ?: 'Not assigned' }}
                                </div>
                            </div>
                        </div>

                        <div class="profile-detail mb-3">
                            <div class="detail-icon">
                                <i class="bi bi-telephone"></i>
                            </div>

                            <div>
                                <div class="text-muted small">Phone Number</div>
                                <div class="fw-semibold">
                                    {{ $phone ?: 'Not provided' }}
                                </div>
                            </div>
                        </div>

                        <div class="profile-detail">
                            <div class="detail-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>

                            <div>
                                <div class="text-muted small">Date Hired</div>
                                <div class="fw-semibold">
                                    {{ $hired_at ? \Illuminate\Support\Carbon::parse($hired_at)->format('M d, Y') : 'Not provided' }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- Personal Information --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4 mb-4">

                <div class="card-header bg-white border-0 px-4 pt-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>
                        Personal Information
                    </h5>

                    <p class="text-muted small mb-0">
                        Teacher's basic information and contact details.
                    </p>
                </div>

                <div class="card-body p-4">

                    <div class="row g-4">

                        <div class="col-md-6">
                            <div class="info-label">First Name</div>
                            <div class="info-value">
                                {{ $first_name ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-label">Middle Name</div>
                            <div class="info-value">
                                {{ $middle_name ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-label">Last Name</div>
                            <div class="info-value">
                                {{ $last_name ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value">
                                {{ $phone ?: '—' }}
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="info-label">Home Address</div>
                            <div class="info-value">
                                {{ $address ?: 'No address provided' }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            {{-- Employment Information --}}
            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-header bg-white border-0 px-4 pt-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-briefcase text-primary me-2"></i>
                        Employment Information
                    </h5>

                    <p class="text-muted small mb-0">
                        Staff identification and employment status.
                    </p>
                </div>

                <div class="card-body p-4">

                    <div class="row g-4">

                        <div class="col-md-6">
                            <div class="info-label">Staff Number</div>
                            <div class="info-value">
                                {{ $staff_number ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-label">Qualification</div>
                            <div class="info-value">
                                {{ $qualification ?: '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-label">Employment Status</div>
                            <div class="mt-2">
                                <span class="badge rounded-pill bg-{{ $statusClass }}-subtle text-{{ $statusClass }} px-3 py-2">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-label">Date Hired</div>
                            <div class="info-value">
                                {{ $hired_at ? \Illuminate\Support\Carbon::parse($hired_at)->format('F d, Y') : '—' }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>
    <style>
    .teacher-avatar {
        width: 100px;
        height: 100px;
        border-radius: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0d6efd, #5b8def);
        color: #fff;
        font-size: 32px;
        font-weight: 700;
        box-shadow: 0 8px 24px rgba(13, 110, 253, .18);
    }

    .profile-detail {
        display: flex;
        align-items: center;
        gap: 13px;
    }

    .detail-icon {
        width: 42px;
        height: 42px;
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #eef4ff;
        color: #0d6efd;
        font-size: 18px;
    }

    .info-label {
        color: #7b8494;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 7px;
    }

    .info-value {
        color: #252b36;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .card {
        transition: box-shadow .2s ease;
    }

    .card:hover {
        box-shadow: 0 8px 28px rgba(30, 45, 70, .07) !important;
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 12px;
            padding-right: 12px;
        }
    }
</style>
</div>


