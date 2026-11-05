
<div class="student-page">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="student-heading-icon">
                    <i class="bi bi-people-fill"></i>
                </span>
                <span class="text-uppercase small fw-semibold text-primary">
                    School Management
                </span>
            </div>

            <h2 class="fw-bold mb-1">Students</h2>

            <p class="text-secondary mb-0">
                Manage student records, contact information and enrollment details.
            </p>
        </div>

        
            <a href="{{ route('school.students.create') }}"
               class="btn btn-primary rounded-3 px-4 py-2 shadow-sm">
                <i class="bi bi-person-plus-fill me-2"></i>
                Register Student
            </a>
       
    </div>

    {{-- FLASH MESSAGES --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- SUMMARY CARDS --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="student-stat-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary small fw-medium mb-2">
                            Matching Students
                        </p>
                        <h3 class="fw-bold mb-0">
                            {{ number_format($students->total()) }}
                        </h3>
                        <span class="small text-secondary">
                            Based on current filters
                        </span>
                    </div>

                    <div class="student-stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="student-stat-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary small fw-medium mb-2">
                            Active Filters
                        </p>
                        <h3 class="fw-bold mb-0">
                            {{ (trim($search) !== '' ? 1 : 0) + ($status !== '' ? 1 : 0) }}
                        </h3>
                        <span class="small text-secondary">
                            Search and status
                        </span>
                    </div>

                    <div class="student-stat-icon bg-success-subtle text-success">
                        <i class="bi bi-funnel-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="student-stat-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary small fw-medium mb-2">
                            Current Page
                        </p>
                        <h3 class="fw-bold mb-0">
                            {{ $students->currentPage() }}
                        </h3>
                        <span class="small text-secondary">
                            Of {{ max(1, $students->lastPage()) }} pages
                        </span>
                    </div>

                    <div class="student-stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-file-earmark-person-fill"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- STUDENT DIRECTORY --}}
    <div class="card student-main-card border-0 shadow-sm">

        <div class="card-header bg-white border-0 px-3 px-md-4 pt-4 pb-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Student Directory</h5>
                    <p class="text-secondary small mb-0">
                        View and manage registered students.
                    </p>
                </div>

                <div class="student-results-count">
                    <i class="bi bi-people me-1"></i>
                    {{ $students->firstItem() ?? 0 }}–{{ $students->lastItem() ?? 0 }}
                    of {{ $students->total() }} students
                </div>
            </div>
        </div>

        {{-- SEARCH AND FILTERS --}}
        <div class="px-3 px-md-4 pb-4">
            <div class="student-filter-panel">
                <div class="row g-3 align-items-end">

                    <div class="col-12 col-lg-6">
                        <label for="studentSearch" class="form-label small fw-semibold">
                            Search Students
                        </label>

                        <div class="input-group student-search-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>

                            <input type="search"
                                   id="studentSearch"
                                   class="form-control"
                                   placeholder="Name, admission number, email or phone..."
                                   wire:model.live.debounce.300ms="search"
                                   autocomplete="off">
                        </div>
                    </div>

                    <div class="col-12 col-sm-7 col-lg-3">
                        <label for="studentStatus" class="form-label small fw-semibold">
                            Student Status
                        </label>

                        <select id="studentStatus"
                                class="form-select"
                                wire:model.live="status">
                            <option value="">All statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="graduated">Graduated</option>
                            <option value="transferred">Transferred</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-5 col-lg-3">
                        <button type="button"
                                wire:click="clearFilters"
                                class="btn btn-outline-secondary w-100 rounded-3">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>
                            Clear Filters
                        </button>
                    </div>

                </div>

                <div class="d-flex align-items-center gap-2 text-secondary small mt-3"
                     wire:loading.delay
                     wire:target="search,status,clearFilters">
                    <span class="spinner-border spinner-border-sm text-primary"
                          aria-hidden="true"></span>
                    Updating student list...
                </div>
            </div>
        </div>

        {{-- STUDENT TABLE --}}
        <div class="table-responsive student-table-wrapper">
            <table class="table align-middle mb-0 student-table">

                <thead>
                    <tr>
                        <th class="ps-3 ps-md-4">STUDENT</th>
                        <th>ADMISSION NO.</th>
                        <th>CONTACT INFORMATION</th>
                        <th>STATUS</th>
                        <th class="text-end pe-3 pe-md-4">ACTIONS</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($students as $student)
                        <tr wire:key="student-row-{{ $student->id }}">

                            {{-- STUDENT NAME --}}
                            <td class="ps-3 ps-md-4">
                                <div class="d-flex align-items-center gap-3">

                                    @if ($student->photo)
                                        <img
                                            src="{{ \Illuminate\Support\Facades\Storage::url($student->photo) }}"
                                            alt="{{ $student->first_name }}"
                                            class="student-avatar">
                                    @else
                                        <div class="student-avatar student-avatar-placeholder">
                                            {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($student->last_name ?? '', 0, 1)) }}
                                        </div>
                                    @endif

                                    <div class="student-name-cell">
                                        <div class="fw-semibold text-dark">
                                            {{ trim($student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name) }}
                                        </div>

                                        <div class="text-secondary small">
                                            {{ $student->gender ? ucfirst($student->gender) : 'Gender not specified' }}
                                        </div>
                                    </div>

                                </div>
                            </td>

                            {{-- ADMISSION NUMBER --}}
                            <td>
                                <span class="admission-number">
                                    {{ $student->admission_number ?: 'Not assigned' }}
                                </span>
                            </td>

                            {{-- CONTACT DETAILS --}}
                            <td>
                                <div class="student-contact">

                                    @if ($student->email)
                                        <div class="mb-1">
                                            <i class="bi bi-envelope me-2 text-secondary"></i>
                                            {{ $student->email }}
                                        </div>
                                    @endif

                                    @if ($student->phone)
                                        <div>
                                            <i class="bi bi-telephone me-2 text-secondary"></i>
                                            {{ $student->phone }}
                                        </div>
                                    @endif

                                    @if (!$student->email && !$student->phone)
                                        <span class="text-secondary small">
                                            No contact details
                                        </span>
                                    @endif

                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td>
                                @php
                                    $statusClass = match (strtolower($student->status ?? '')) {
                                        'active' => 'status-active',
                                        'inactive' => 'status-inactive',
                                        'graduated' => 'status-graduated',
                                        'transferred' => 'status-transferred',
                                        default => 'status-default',
                                    };
                                @endphp

                                <span class="student-status {{ $statusClass }}">
                                    <span class="status-dot"></span>
                                    {{ ucfirst($student->status ?? 'Unknown') }}
                                </span>
                            </td>

                            {{-- VIEW, EDIT AND DELETE --}}
                            <td class="text-end pe-3 pe-md-4">
                                <div class="d-inline-flex align-items-center gap-2">

                                    @can('view students')
                                        <a href="{{ route('school.students.show', $student->id) }}"
                                           class="btn btn-sm student-action-btn action-view"
                                           title="View Student"
                                           aria-label="View student">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endcan

                                    @can('edit students')
                                        <a href="{{ route('school.students.edit', $student->id) }}"
                                           class="btn btn-sm student-action-btn action-edit"
                                           title="Edit Student"
                                           aria-label="Edit student">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    @endcan

                                    @can('delete students')
                                        <button type="button"
                                                wire:click="confirmDelete({{ $student->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="confirmDelete"
                                                class="btn btn-sm student-action-btn action-delete"
                                                title="Delete Student"
                                                aria-label="Delete student">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    @endcan

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5">
                                <div class="student-empty-state text-center">
                                    <div class="student-empty-icon mx-auto mb-3">
                                        <i class="bi bi-people"></i>
                                    </div>

                                    <h6 class="fw-bold mb-2">
                                        No students found
                                    </h6>

                                    <p class="text-secondary small mb-3">
                                        @if (trim($search) !== '' || $status !== '')
                                            Try changing your search or clearing the filters.
                                        @else
                                            No student records have been registered yet.
                                        @endif
                                    </p>

                                    @if (trim($search) !== '' || $status !== '')
                                        <button type="button"
                                                wire:click="clearFilters"
                                                class="btn btn-outline-primary btn-sm rounded-3 px-3">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                                            Clear Filters
                                        </button>
                                    @else
                                        @can('create students')
                                            <a href="{{ route('school.students.create') }}"
                                               class="btn btn-primary btn-sm rounded-3 px-3">
                                                <i class="bi bi-person-plus me-1"></i>
                                                Register First Student
                                            </a>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($students->hasPages())
            <div class="card-footer bg-white border-top px-3 px-md-4 py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-secondary small">
                        Showing {{ $students->firstItem() }}
                        to {{ $students->lastItem() }}
                        of {{ $students->total() }} results
                    </div>

                    <div class="student-pagination">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- DELETE CONFIRMATION MODAL --}}
    @if ($deleteStudentId !== null)
        <div class="student-delete-overlay"
             wire:key="delete-student-modal-{{ $deleteStudentId }}">

            <div class="student-delete-modal"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="deleteStudentTitle">

                <div class="text-center mb-3">
                    <div class="student-delete-icon mx-auto mb-3">
                        <i class="bi bi-trash3"></i>
                    </div>

                    <h5 id="deleteStudentTitle" class="fw-bold mb-2">
                        Delete Student?
                    </h5>

                    <p class="text-secondary mb-0">
                        Are you sure you want to delete
                        <strong class="text-dark">{{ $deleteStudentName }}</strong>?
                    </p>
                </div>

                <div class="alert alert-warning small rounded-3 mt-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    This action cannot be undone. Students with existing academic
                    or financial records cannot be deleted.
                </div>

                @error('deleteStudent')
                    <div class="alert alert-danger small rounded-3">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        {{ $message }}
                    </div>
                @enderror

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button"
                            wire:click="cancelDelete"
                            wire:loading.attr="disabled"
                            class="btn btn-light border rounded-3 px-3">
                        Cancel
                    </button>

                    <button type="button"
                            wire:click="deleteStudent"
                            wire:loading.attr="disabled"
                            wire:target="deleteStudent"
                            class="btn btn-danger rounded-3 px-3">

                        <span wire:loading.remove wire:target="deleteStudent">
                            <i class="bi bi-trash3 me-1"></i>
                            Delete Student
                        </span>

                        <span wire:loading wire:target="deleteStudent">
                            <span class="spinner-border spinner-border-sm me-1"
                                  aria-hidden="true"></span>
                            Deleting...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    @endif
<style>
    .student-page {
        color: #1e293b;
    }

    .student-heading-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        color: #0d6efd;
        background: #eaf2ff;
        font-size: 18px;
    }

    .student-stat-card {
        padding: 22px;
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 17px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, .035);
    }

    .student-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        border-radius: 15px;
        font-size: 22px;
        flex-shrink: 0;
    }

    .student-main-card {
        overflow: hidden;
        border-radius: 18px;
        background: #fff;
    }

    .student-results-count {
        padding: 8px 12px;
        border-radius: 10px;
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .student-filter-panel {
        padding: 18px;
        border: 1px solid #edf0f5;
        border-radius: 14px;
        background: #f8fafc;
    }

    .student-filter-panel .form-label {
        color: #475569;
        margin-bottom: 8px;
    }

    .student-filter-panel .form-control,
    .student-filter-panel .form-select,
    .student-search-group .input-group-text {
        min-height: 44px;
        border-color: #e2e8f0;
        border-radius: 10px;
    }

    .student-search-group .input-group-text {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        background: #fff;
        color: #64748b;
    }

    .student-search-group .form-control {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .student-filter-panel .form-control:focus,
    .student-filter-panel .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .10);
    }

    .student-table thead th {
        padding-top: 15px;
        padding-bottom: 15px;
        background: #f8fafc;
        border-top: 1px solid #edf0f5;
        border-bottom: 1px solid #edf0f5;
        color: #64748b;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .065em;
        white-space: nowrap;
    }

    .student-table tbody td {
        padding-top: 17px;
        padding-bottom: 17px;
        border-bottom-color: #f1f5f9;
        vertical-align: middle;
    }

    .student-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .student-table tbody tr {
        transition: background .15s ease;
    }

    .student-table tbody tr:hover {
        background: #fbfdff;
    }

    .student-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 43px;
        height: 43px;
        min-width: 43px;
        border-radius: 13px;
        object-fit: cover;
    }

    .student-avatar-placeholder {
        background: #eaf2ff;
        color: #0d6efd;
        font-size: 13px;
        font-weight: 700;
    }

    .student-name-cell {
        min-width: 145px;
        line-height: 1.5;
    }

    .admission-number {
        display: inline-block;
        padding: 6px 9px;
        border: 1px solid #e9edf4;
        border-radius: 8px;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .student-contact {
        min-width: 150px;
        color: #475569;
        font-size: 12px;
        overflow-wrap: anywhere;
    }

    .student-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-active {
        color: #15803d;
        background: #dcfce7;
    }

    .status-inactive {
        color: #b91c1c;
        background: #fee2e2;
    }

    .status-graduated {
        color: #6d28d9;
        background: #ede9fe;
    }

    .status-transferred {
        color: #b45309;
        background: #fef3c7;
    }

    .status-default {
        color: #475569;
        background: #e2e8f0;
    }

    .student-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border: 1px solid transparent;
        border-radius: 9px;
        transition: all .15s ease;
    }

    .action-view {
        color: #0d6efd;
        background: #eff6ff;
    }

    .action-view:hover {
        color: #fff;
        background: #0d6efd;
    }

    .action-edit {
        color: #64748b;
        background: #f1f5f9;
    }

    .action-edit:hover {
        color: #fff;
        background: #64748b;
    }

    .action-delete {
        color: #dc3545;
        background: #fff1f2;
    }

    .action-delete:hover {
        color: #fff;
        background: #dc3545;
    }

    .student-empty-state {
        max-width: 380px;
        margin: 0 auto;
        padding: 15px;
    }

    .student-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 65px;
        height: 65px;
        border-radius: 20px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 28px;
    }

    .student-delete-overlay {
        position: fixed;
        inset: 0;
        z-index: 1090;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, .58);
        backdrop-filter: blur(3px);
    }

    .student-delete-modal {
        width: 100%;
        max-width: 460px;
        padding: 28px;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 20px 60px rgba(15, 23, 42, .20);
        animation: studentModalIn .18s ease-out;
    }

    .student-delete-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 66px;
        height: 66px;
        border-radius: 20px;
        background: #fff0f0;
        color: #dc3545;
        font-size: 27px;
    }

    @keyframes studentModalIn {
        from {
            opacity: 0;
            transform: translateY(8px) scale(.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @media (max-width: 767.98px) {
        .student-stat-card {
            padding: 17px;
        }

        .student-filter-panel {
            padding: 14px;
        }

        .student-delete-modal {
            padding: 22px;
        }
    }
</style>
</div>


