
<div class="container-fluid py-4 subject-page">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="text-muted small mb-2">
                School Management
                <i class="bi bi-chevron-right mx-1"></i>
                Subjects
            </div>
            <h3 class="fw-bold mb-1">Subjects</h3>
            <p class="text-muted mb-0">
                Manage the subjects taught in your school.
            </p>
        </div>

        @can('create subjects')
            <a href="{{ route('school.subjects.create') }}"
               class="btn btn-primary rounded-3 px-4 py-2">
                <i class="bi bi-plus-lg me-2"></i>
                Add Subject
            </a>
        @endcan
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="subject-stat-icon subject-blue">
                        <i class="bi bi-book"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Subjects</div>
                        <h3 class="fw-bold mb-0">{{ number_format($totalSubjects) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="subject-stat-icon subject-green">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Active Subjects</div>
                        <h3 class="fw-bold mb-0">{{ number_format($activeSubjects) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="subject-stat-icon subject-orange">
                        <i class="bi bi-pause-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Inactive Subjects</div>
                        <h3 class="fw-bold mb-0">{{ number_format($inactiveSubjects) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Subject Records</h5>
                    <p class="text-muted small mb-0">Search and manage your school's subjects.</p>
                </div>
                <span class="badge bg-light text-dark border px-3 py-2">
                    {{ $subjects->total() }} records
                </span>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-8">
                    <input type="search"
                           wire:model.live.debounce.350ms="search"
                           class="form-control"
                           placeholder="Search subject name, code or description...">
                </div>

                <div class="col-md-2">
                    <select wire:model.live="status" class="form-select">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-md-2">
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
            <table class="table align-middle mb-0 subject-table">
                <thead>
                    <tr>
                        <th class="ps-4">Subject</th>
                        <th>Code</th>
                        <th>Teacher Assignments</th>
                        <th>Exams</th>
                        <th>Assignments</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($subjects as $subject)
                        <tr wire:key="subject-{{ $subject->id }}">
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $subject->name }}</div>
                                <div class="text-muted small">
                                    {{ $subject->description ?: 'No description' }}
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $subject->code }}
                                </span>
                            </td>

                            <td>{{ $subject->teacher_assignments_count }}</td>
                            <td>{{ $subject->exams_count }}</td>
                            <td>{{ $subject->assignments_count }}</td>

                            <td>
                                @if ($subject->status === 'active')
                                    <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                                        Active
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">
                                    @can('edit subjects')
                                        <a href="{{ route('school.subjects.edit', ['subjectId' => $subject->id]) }}"
                                           class="btn btn-sm btn-light border subject-action"
                                           title="Edit subject">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    @endcan

                                    @can('manage subjects')
                                        <button type="button"
                                                wire:click="deleteSubject({{ $subject->id }})"
                                                wire:confirm="Delete this subject? Subjects with existing academic records cannot be deleted."
                                                wire:loading.attr="disabled"
                                                class="btn btn-sm btn-light border subject-action text-danger"
                                                title="Delete subject">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-journal-x fs-1 text-secondary"></i>
                                <h6 class="fw-bold mt-3">No subjects found</h6>
                                <p class="text-muted small mb-0">
                                    Add a subject or change your search filters.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($subjects->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $subjects->links() }}
            </div>
        @endif
    </div>

    <style>
        .subject-page {
            color: #273142;
        }

        .subject-stat-icon {
            width: 52px;
            height: 52px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            font-size: 23px;
        }

        .subject-blue {
            background: #edf4ff;
            color: #0d6efd;
        }

        .subject-green {
            background: #eaf8f0;
            color: #198754;
        }

        .subject-orange {
            background: #fff5e6;
            color: #d97706;
        }

        .subject-page .form-control,
        .subject-page .form-select {
            min-height: 43px;
            border-color: #e1e6ee;
            border-radius: 9px;
            font-size: 13px;
        }

        .subject-table thead th {
            background: #f8fafc;
            color: #748094;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            white-space: nowrap;
            padding: 15px 12px;
        }

        .subject-table tbody td {
            padding: 16px 12px;
            border-color: #f0f2f6;
            font-size: 13px;
        }

        .subject-table tbody tr:hover {
            background: #fafcff;
        }

        .subject-action {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
    </style>
</div>
