
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Classes</h3>
            <p class="text-secondary mb-0">Manage classes in your school.</p>
        </div>

        <a href="{{ route('school.classes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create Class
        </a>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
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
                    <div class="text-secondary small">Total Classes</div>
                    <h3 class="fw-bold mb-0">{{ $totalClasses }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="text-secondary small">Active Classes</div>
                    <h3 class="fw-bold text-success mb-0">{{ $activeClasses }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="text-secondary small">Inactive Classes</div>
                    <h3 class="fw-bold text-secondary mb-0">{{ $inactiveClasses }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-3 p-md-4">

            <div class="row g-3 mb-4">
                <div class="col-md-7">
                    <input type="search"
                           wire:model.live.debounce.300ms="search"
                           class="form-control"
                           placeholder="Search class name, code or level...">
                </div>

                <div class="col-md-3">
                    <select wire:model.live="status" class="form-select">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="button" wire:click="clearFilters"
                            class="btn btn-outline-secondary w-100">
                        Reset
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Class</th>
                            <th>Code</th>
                            <th>Level</th>
                            <th>Sections</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($classes as $class)
                            <tr wire:key="class-{{ $class->id }}">
                                <td>
                                    <div class="fw-semibold">{{ $class->name }}</div>
                                    <small class="text-secondary">
                                        {{ $class->description ?: 'No description' }}
                                    </small>
                                </td>
                                <td>{{ $class->code ?: '—' }}</td>
                                <td>{{ $class->level ?: '—' }}</td>
                                <td>
                                    <span class="badge text-bg-light border">
                                        {{ $class->sections_count }}
                                    </span>
                                </td>
                                <td>
                                    @if ($class->status === 'active')
                                        <span class="badge text-bg-success">Active</span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            {{ ucfirst($class->status ?: 'Unknown') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('school.classes.edit', $class->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit class">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteClass({{ $class->id }})"
                                                wire:confirm="Delete this class? Classes with related academic records cannot be deleted."
                                                wire:loading.attr="disabled"
                                                title="Delete class">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-mortarboard display-5 text-secondary"></i>
                                    <h6 class="fw-bold mt-3">No classes found</h6>
                                    <p class="text-secondary mb-0">
                                        Create a class or adjust your search.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $classes->links() }}
            </div>
        </div>
    </div>
</div>
