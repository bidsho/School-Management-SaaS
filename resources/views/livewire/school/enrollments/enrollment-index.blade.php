
<div class="container-fluid py-4 enrollment-page">

    @php
        $statusStyles = [
            'active' => ['success', 'Active'],
            'pending' => ['warning', 'Pending'],
            'completed' => ['primary', 'Completed'],
            'withdrawn' => ['danger', 'Withdrawn'],
            'transferred' => ['info', 'Transferred'],
        ];
    @endphp

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="text-muted small mb-2">
                School Management
                <i class="bi bi-chevron-right mx-1"></i>
                Enrollments
            </div>

            <h3 class="fw-bold mb-1">Student Enrollments</h3>

            <p class="text-muted mb-0">
                Manage student class placements and academic enrollment records.
            </p>
        </div>

       
                <a href="{{ route('school.enrollments.create') }}"
                   class="btn btn-primary rounded-3 px-4 py-2">
                    <i class="bi bi-plus-lg me-2"></i>
                    New Enrollment
                </a>
            
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Statistics --}}
    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="enrollment-stat-icon stat-blue">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Enrollments</div>
                        <h3 class="fw-bold mb-0">
                            {{ number_format($totalEnrollments) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="enrollment-stat-icon stat-green">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Active Enrollments</div>
                        <h3 class="fw-bold mb-0">
                            {{ number_format($activeEnrollments) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="enrollment-stat-icon stat-orange">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pending Enrollments</div>
                        <h3 class="fw-bold mb-0">
                            {{ number_format($pendingEnrollments) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Enrollment Table --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Enrollment Records</h5>
                    <p class="text-muted small mb-0">
                        Search and filter enrollment records for your school.
                    </p>
                </div>

                <span class="badge bg-light text-dark border px-3 py-2">
                    {{ $enrollments->total() }} records found
                </span>
            </div>

            {{-- Search and Filters --}}
            <div class="row g-3 mt-2">

                <div class="col-lg-7">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>

                        <input
                            type="search"
                            wire:model.live.debounce.350ms="search"
                            class="form-control border-start-0 ps-0"
                            placeholder="Search student, admission number, class or section..."
                            aria-label="Search enrollments"
                        >
                    </div>
                </div>

                <div class="col-sm-7 col-lg-3">
                    <select wire:model.live="status"
                            class="form-select"
                            aria-label="Filter by enrollment status">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="withdrawn">Withdrawn</option>
                        <option value="transferred">Transferred</option>
                    </select>
                </div>

                <div class="col-sm-5 col-lg-2">
                    <button type="button"
                            wire:click="clearFilters"
                            class="btn btn-light border w-100">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </button>
                </div>

            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 enrollment-table">

                <thead>
                    <tr>
                        <th class="ps-4">Student</th>
                        <th>Admission No.</th>
                        <th>Class &amp; Section</th>
                        <th>Academic Session</th>
                        <th>Term</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($enrollments as $enrollment)
                        @php
                            $student = $enrollment->student;
                            $classRoom = $enrollment->classRoom;
                            $section = $enrollment->section;
                            $session = $enrollment->academicSession;
                            $term = $enrollment->term;

                            $studentName = $student
                                ? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))
                                : 'Student unavailable';

                            $badge = $statusStyles[$enrollment->admission_status]
                                ?? [
                                    'secondary',
                                    ucfirst(str_replace(
                                        '_',
                                        ' ',
                                        $enrollment->admission_status ?? 'unknown'
                                    )),
                                ];
                        @endphp

                        <tr wire:key="enrollment-{{ $enrollment->id }}">

                            {{-- Student --}}
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="student-mini-avatar">
                                        {{ $student
                                            ? strtoupper(
                                                substr($student->first_name ?: 'S', 0, 1) .
                                                substr($student->last_name ?: '', 0, 1)
                                            )
                                            : '?'
                                        }}
                                    </div>

                                    <div>
                                        <div class="fw-semibold text-dark">
                                            {{ $studentName }}
                                        </div>

                                        <div class="text-muted small">
                                            {{ $student->email ?? 'No email provided' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Admission Number --}}
                            <td>
                                <span class="fw-semibold">
                                    {{ $student->admission_number ?? '—' }}
                                </span>
                            </td>

                            {{-- Class and Section --}}
                            <td>
                                <div class="fw-semibold">
                                    {{ $classRoom->name ?? '—' }}
                                </div>

                                <div class="text-muted small">
                                    Section: {{ $section->name ?? 'Not assigned' }}
                                </div>
                            </td>

                            {{-- Academic Session --}}
                            <td>
                                {{ $session->name ?? '—' }}
                            </td>

                            {{-- Term --}}
                            <td>
                                {{ $term->name ?? '—' }}
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="badge rounded-pill bg-{{ $badge[0] }}-subtle text-{{ $badge[0] }} px-3 py-2">
                                    {{ $badge[1] }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">

                                    {{-- View --}}
                                    @if (\Illuminate\Support\Facades\Route::has('school.enrollments.show'))
                                        @can('view enrollments')
                                            <a href="{{ route('school.enrollments.show', ['enrollment' => $enrollment->id]) }}"
                                               class="btn btn-sm btn-light border action-btn"
                                               title="View enrollment"
                                               aria-label="View enrollment">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endcan
                                    @endif

                                    {{-- Edit --}}
                                    
                                       <a href="{{ route('school.enrollments.edit', ['enrollmentId' => $enrollment->id]) }}"
                                               class="btn btn-sm btn-light border action-btn"
                                               title="Edit enrollment"
                                               aria-label="Edit enrollment">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                    

                                    {{-- Delete --}}
                                    @can('manage students')
                                        <button type="button"
                                                class="btn btn-sm btn-light border action-btn text-danger"
                                                title="Delete enrollment"
                                                aria-label="Delete enrollment"
                                                wire:click="deleteEnrollment({{ $enrollment->id }})"
                                                wire:confirm="Are you sure you want to permanently delete this enrollment? The student record itself will not be deleted."
                                                wire:loading.attr="disabled"
                                                wire:target="deleteEnrollment({{ $enrollment->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endcan

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-enrollment-icon mx-auto mb-3">
                                    <i class="bi bi-journal-x"></i>
                                </div>

                                <h6 class="fw-bold mb-1">No enrollments found</h6>

                                <p class="text-muted small mb-3">
                                    @if ($search !== '' || $status !== '')
                                        Try changing your search or filter.
                                    @else
                                        Enrollment records will appear here when students are enrolled.
                                    @endif
                                </p>

                                @if ($search !== '' || $status !== '')
                                    <button type="button"
                                            wire:click="clearFilters"
                                            class="btn btn-outline-primary btn-sm">
                                        Clear filters
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- Pagination --}}
        @if ($enrollments->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $enrollments->links() }}
            </div>
        @endif

    </div>

    {{-- Page Styles --}}
    <style>
        .enrollment-page {
            color: #273142;
        }

        .enrollment-page .card {
            transition: box-shadow .2s ease;
        }

        .enrollment-page .card:hover {
            box-shadow: 0 8px 26px rgba(30, 45, 70, .06) !important;
        }

        .enrollment-page .form-control,
        .enrollment-page .form-select,
        .enrollment-page .input-group-text {
            min-height: 43px;
            border-color: #e1e6ee;
            border-radius: 9px;
            font-size: 13px;
        }

        .enrollment-page .input-group .input-group-text {
            border-radius: 9px 0 0 9px;
        }

        .enrollment-page .input-group .form-control {
            border-radius: 0 9px 9px 0;
        }

        .enrollment-page .form-control:focus,
        .enrollment-page .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .09);
        }

        .enrollment-stat-icon {
            width: 52px;
            height: 52px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            font-size: 23px;
        }

        .stat-blue {
            color: #0d6efd;
            background: #edf4ff;
        }

        .stat-green {
            color: #198754;
            background: #eaf8f0;
        }

        .stat-orange {
            color: #d97706;
            background: #fff5e6;
        }

        .enrollment-table thead th {
            background: #f8fafc;
            color: #748094;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .45px;
            white-space: nowrap;
            padding: 15px 12px;
            border-bottom: 1px solid #edf0f5;
        }

        .enrollment-table tbody td {
            padding: 16px 12px;
            border-color: #f0f2f6;
            font-size: 13px;
        }

        .enrollment-table tbody tr:hover {
            background: #fafcff;
        }

        .student-mini-avatar {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: #edf4ff;
            color: #0d6efd;
            font-size: 13px;
            font-weight: 700;
        }

        .action-btn {
            width: 33px;
            height: 33px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #5b6472;
        }

        .action-btn:hover {
            color: #0d6efd;
            background: #edf4ff;
        }

        .action-btn.text-danger:hover {
            color: #dc3545 !important;
            background: #fff0f1;
        }

        .empty-enrollment-icon {
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            background: #f1f5f9;
            color: #94a3b8;
            font-size: 28px;
        }

        .enrollment-page .badge {
            font-weight: 600;
            font-size: 11px;
        }

        @media (max-width: 768px) {
            .enrollment-page {
                padding-left: 10px;
                padding-right: 10px;
            }
        }
    </style>

</div>
