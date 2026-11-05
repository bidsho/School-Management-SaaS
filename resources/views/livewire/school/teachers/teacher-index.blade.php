
<div class="container-fluid py-4">

    {{-- Page Heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Teachers Management</h3>
            <p class="text-muted mb-0">
                Manage your school's teaching staff, qualifications, and employment records.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('school.dashboard') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Dashboard
            </a>

            @can('create teachers')
                <a href="{{ route('school.teachers.create') }}"
                   class="btn btn-primary px-4">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    Add New Teacher
                </a>
            @endcan
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm"
             role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm"
             role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <strong>Please check the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        {{-- Total Teachers --}}
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-people-fill text-primary fs-3"></i>
                    </div>

                    <div>
                        <p class="text-muted small mb-1">Total Teachers</p>
                        <h3 class="fw-bold mb-0">
                            {{ number_format($totalTeachers) }}
                        </h3>
                        <small class="text-muted">Registered at your school</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Teachers --}}
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-3 bg-success bg-opacity-10 p-3">
                        <i class="bi bi-person-check-fill text-success fs-3"></i>
                    </div>

                    <div>
                        <p class="text-muted small mb-1">Active Teachers</p>
                        <h3 class="fw-bold mb-0">
                            {{ number_format(
                                $teachers->getCollection()->where('employment_status', 'active')->count()
                            ) }}
                        </h3>
                        <small class="text-muted">On current page</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Qualifications --}}
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-3 bg-info bg-opacity-10 p-3">
                        <i class="bi bi-mortarboard-fill text-info fs-3"></i>
                    </div>

                    <div>
                        <p class="text-muted small mb-1">Qualifications</p>
                        <h3 class="fw-bold mb-0">
                            {{ number_format(
                                $teachers->getCollection()
                                    ->filter(fn ($teacher) => filled($teacher->qualification))
                                    ->count()
                            ) }}
                        </h3>
                        <small class="text-muted">On current page</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Search and Filters --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-funnel me-2 text-primary"></i>
                    Search and Filter
                </h5>

                @if ($search !== '' || $employmentStatus !== '')
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            wire:click="clearFilters">
                        <i class="bi bi-x-circle me-1"></i>
                        Clear Filters
                    </button>
                @endif
            </div>

            <div class="row g-3 align-items-end">

                {{-- Search --}}
                <div class="col-lg-8">
                    <label for="teacherSearch" class="form-label fw-semibold">
                        Search Teachers
                    </label>

                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            id="teacherSearch"
                            type="search"
                            class="form-control"
                            placeholder="Search by name, staff number, or phone..."
                            wire:model.live.debounce.300ms="search"
                            autocomplete="off"
                        >
                    </div>
                </div>

                {{-- Employment Status --}}
                <div class="col-lg-4">
                    <label for="employmentStatus" class="form-label fw-semibold">
                        Employment Status
                    </label>

                    <select
                        id="employmentStatus"
                        class="form-select"
                        wire:model.live="employmentStatus"
                    >
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="on_leave">On Leave</option>
                        <option value="terminated">Terminated</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

    {{-- Teachers Table --}}
    <div class="card border-0 shadow-sm rounded-3">

        <div class="card-header bg-white border-0 px-4 py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-bold mb-1">Teaching Staff</h5>
                    <p class="text-muted small mb-0">
                        Teacher records belonging to your school.
                    </p>
                </div>

                @can('create teachers')
                    <a href="{{ route('school.teachers.create') }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-lg me-1"></i>
                        Register Teacher
                    </a>
                @endcan
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Teacher</th>
                        <th>Staff Number</th>
                        <th>Phone Number</th>
                        <th>Qualification</th>
                        <th>Employment Status</th>
                        <th>Date Hired</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr wire:key="teacher-{{ $teacher->id }}">

                            {{-- Teacher Details --}}
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">

                                    <div
                                        class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                                        style="width: 44px; height: 44px; min-width: 44px;"
                                    >
                                        {{ strtoupper(
                                            substr($teacher->first_name ?? '', 0, 1) .
                                            substr($teacher->last_name ?? '', 0, 1)
                                        ) }}
                                    </div>

                                    <div>
                                        <div class="fw-semibold text-dark">
                                            {{ trim(
                                                ($teacher->first_name ?? '') . ' ' .
                                                ($teacher->middle_name ?? '') . ' ' .
                                                ($teacher->last_name ?? '')
                                            ) }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $teacher->user?->email ?? 'No linked account' }}
                                        </small>
                                    </div>

                                </div>
                            </td>

                            {{-- Staff Number --}}
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $teacher->staff_number ?: 'Not assigned' }}
                                </span>
                            </td>

                            {{-- Phone --}}
                            <td>
                                {{ $teacher->phone ?: '—' }}
                            </td>

                            {{-- Qualification --}}
                            <td>
                                {{ $teacher->qualification ?: 'Not provided' }}
                            </td>

                            {{-- Employment Status --}}
                            <td>
                                @php
                                    $status = strtolower($teacher->employment_status ?? '');

                                    $statusClass = match ($status) {
                                        'active' => 'text-bg-success',
                                        'inactive' => 'text-bg-secondary',
                                        'on_leave' => 'text-bg-warning',
                                        'terminated' => 'text-bg-danger',
                                        default => 'bg-light text-dark border',
                                    };

                                    $statusLabel = match ($status) {
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'on_leave' => 'On Leave',
                                        'terminated' => 'Terminated',
                                        default => 'Not Set',
                                    };
                                @endphp

                                <span class="badge rounded-pill {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            {{-- Date Hired --}}
                            <td>
                                {{ $teacher->hired_at?->format('d M, Y') ?? '—' }}
                            </td>

                            {{-- Actions --}}
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button
                                        type="button"
                                        class="btn btn-light border btn-sm"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        aria-label="Teacher actions"
                                    >
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">

                                        @can('view teachers')
                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route('school.teachers.show', $teacher->id) }}"
                                                >
                                                    <i class="bi bi-eye me-2 text-primary"></i>
                                                    View Profile
                                                </a>
                                            </li>
                                        @endcan

                                        @can('edit teachers')
                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route('school.teachers.edit', ['teacher' => $teacher->id]) }}"
                                                >
                                                    <i class="bi bi-pencil-square me-2 text-success"></i>
                                                    Edit Teacher
                                                </a>
                                            </li>
                                        @endcan

                                    </ul>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">

                                <div class="mb-3">
                                    <i class="bi bi-people display-4 text-muted"></i>
                                </div>

                                <h5 class="fw-semibold">No Teachers Found</h5>

                                <p class="text-muted mb-3">
                                    @if ($search !== '' || $employmentStatus !== '')
                                        No teachers match your current search or filter.
                                    @else
                                        You have not registered any teachers yet.
                                    @endif
                                </p>

                                @can('create teachers')
                                    <a href="{{ route('school.teachers.create') }}"
                                       class="btn btn-primary">
                                        <i class="bi bi-person-plus-fill me-2"></i>
                                        Add Your First Teacher
                                    </a>
                                @endcan

                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- Pagination --}}
        @if ($teachers->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                    <small class="text-muted">
                        Showing {{ $teachers->firstItem() ?? 0 }}
                        to {{ $teachers->lastItem() ?? 0 }}
                        of {{ $teachers->total() }} teachers
                    </small>

                    <div>
                        {{ $teachers->links() }}
                    </div>

                </div>
            </div>
        @endif

    </div>

</div>
