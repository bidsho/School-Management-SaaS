
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Parent Management</h3>
            <p class="text-muted mb-0">Manage parent profiles and their linked students.</p>
        </div>

        @can('create parents')
            <a href="{{ route('school.parents.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> Add Parent
            </a>
        @endcan
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total Parents</div>
                    <div class="fs-3 fw-bold">{{ number_format($totalParents) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Parents Linked to Students</div>
                    <div class="fs-3 fw-bold text-success">
                        {{ number_format($linkedParents) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body border-bottom">
            <label class="form-label fw-semibold">Search parents</label>
            <input type="search"
                   class="form-control"
                   placeholder="Search by name, email or phone..."
                   wire:model.live.debounce.350ms="search">
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Parent</th>
                        <th>Contact</th>
                        <th>Occupation</th>
                        <th>Linked Students</th>
                        <th>Login Account</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($parents as $parent)
                        <tr wire:key="parent-{{ $parent->id }}">
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $parent->full_name }}</div>
                                <div class="text-muted small">Parent ID: {{ $parent->id }}</div>
                            </td>

                            <td>
                                <div>{{ $parent->email ?: 'No email' }}</div>
                                <div class="text-muted small">{{ $parent->phone ?: 'No phone' }}</div>
                            </td>

                            <td>{{ $parent->occupation ?: '—' }}</td>

                            <td>
                                <span class="badge bg-primary-subtle text-primary">
                                    {{ $parent->students_count }}
                                </span>
                            </td>

                            <td>
                                @if ($parent->user_id)
                                    <span class="badge bg-success-subtle text-success">Linked</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Not linked</span>
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                @can('edit parents')
                                    <a href="{{ route('school.parents.edit', ['parentId' => $parent->id]) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Edit parent">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endcan

                                @can('manage parents')
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            wire:click="deleteParent({{ $parent->id }})"
                                            wire:confirm="Delete this parent profile? This action cannot be undone."
                                            wire:loading.attr="disabled"
                                            title="Delete parent">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-people fs-1 text-muted"></i>
                                <h6 class="fw-semibold mt-3">No parents found</h6>
                                <p class="text-muted mb-0">Add a parent or change your search.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($parents->hasPages())
            <div class="card-footer bg-white">
                {{ $parents->links() }}
            </div>
        @endif
    </div>
</div>
