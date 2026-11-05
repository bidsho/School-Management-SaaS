
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Sections</h3>
            <p class="text-secondary mb-0">
                Organize classes into sections such as JSS 1A and JSS 1B.
            </p>
        </div>

        <a href="{{ route('school.sections.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create Section
        </a>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-2">Total Sections</div>
                    <h3 class="fw-bold mb-0">{{ $totalSections }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-2">Active Sections</div>
                    <h3 class="fw-bold text-success mb-0">{{ $activeSections }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-2">Inactive Sections</div>
                    <h3 class="fw-bold text-secondary mb-0">{{ $inactiveSections }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-3 p-md-4">

            <div class="row g-3 mb-4">
                <div class="col-lg-5">
                    <input type="search"
                           wire:model.live.debounce.300ms="search"
                           class="form-control"
                           placeholder="Search section or class...">
                </div>

                <div class="col-lg-3">
                    <select wire:model.live="classFilter" class="form-select">
                        <option value="">All classes</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <select wire:model.live="status" class="form-select">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <button type="button"
                            wire:click="clearFilters"
                            class="btn btn-outline-secondary w-100">
                        Reset
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Section</th>
                            <th>Class</th>
                            <th>Capacity</th>
                            <th>Students</th>
                            <th>Teachers</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($sections as $section)
                            <tr wire:key="section-{{ $section->id }}">
                                <td>
                                    <div class="fw-semibold">{{ $section->name }}</div>
                                    <small class="text-secondary">
                                        Section #{{ $section->id }}
                                    </small>
                                </td>

                                <td>{{ $section->classRoom?->name ?? 'Class unavailable' }}</td>

                                <td>
                                    {{ $section->capacity !== null ? number_format($section->capacity) : 'Not set' }}
                                </td>

                                <td>{{ $section->enrollments_count }}</td>

                                <td>{{ $section->teacher_assignments_count }}</td>

                                <td>
                                    @if ($section->status === 'active')
                                        <span class="badge text-bg-success">Active</span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            {{ ucfirst($section->status ?: 'Unknown') }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('school.sections.edit', $section->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit section">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteSection({{ $section->id }})"
                                                wire:confirm="Delete this section? Sections with enrollments or teacher assignments cannot be deleted."
                                                wire:loading.attr="disabled"
                                                title="Delete section">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-diagram-3 display-5 text-secondary"></i>
                                    <h6 class="fw-bold mt-3">No sections found</h6>
                                    <p class="text-secondary mb-3">
                                        Create a section for one of your classes.
                                    </p>
                                    <a href="{{ route('school.sections.create') }}"
                                       class="btn btn-primary btn-sm">
                                        Create Section
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $sections->links() }}
            </div>

        </div>
    </div>
</div>
