
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Academic Terms</h3>
            <p class="text-secondary mb-0">
                Manage terms within each academic session.
            </p>
        </div>

        <a href="{{ route('school.terms.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create Term
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
                    <div class="text-secondary small mb-2">Total Terms</div>
                    <h3 class="fw-bold mb-0">{{ $totalTerms }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-2">Active Terms</div>
                    <h3 class="fw-bold text-success mb-0">{{ $activeTerms }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-2">Inactive Terms</div>
                    <h3 class="fw-bold text-secondary mb-0">{{ $inactiveTerms }}</h3>
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
                           placeholder="Search term or academic session...">
                </div>

                <div class="col-lg-3">
                    <select wire:model.live="sessionFilter" class="form-select">
                        <option value="">All academic sessions</option>
                        @foreach ($sessions as $session)
                            <option value="{{ $session->id }}">{{ $session->name }}</option>
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
                            <th>Term</th>
                            <th>Academic Session</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Enrollments</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($terms as $term)
                            <tr wire:key="term-{{ $term->id }}">
                                <td>
                                    <div class="fw-semibold">{{ $term->name }}</div>
                                    <small class="text-secondary">Term #{{ $term->id }}</small>
                                </td>

                                <td>{{ $term->academicSession?->name ?? 'Session unavailable' }}</td>

                                <td>{{ $term->starts_at?->format('d M Y') ?? '—' }}</td>

                                <td>{{ $term->ends_at?->format('d M Y') ?? '—' }}</td>

                                <td>{{ $term->enrollments_count }}</td>

                                <td>
                                    @if ($term->status === 'active')
                                        <span class="badge text-bg-success">Active</span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            {{ ucfirst($term->status ?: 'Inactive') }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('school.terms.edit', $term->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit term">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteTerm({{ $term->id }})"
                                                wire:confirm="Delete this term? Terms with enrollments, exams or fee structures cannot be deleted."
                                                wire:loading.attr="disabled"
                                                title="Delete term">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-calendar3 display-5 text-secondary"></i>
                                    <h6 class="fw-bold mt-3">No terms found</h6>
                                    <p class="text-secondary mb-3">
                                        Create a term for an academic session.
                                    </p>
                                    <a href="{{ route('school.terms.create') }}"
                                       class="btn btn-primary btn-sm">
                                        Create Term
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $terms->links() }}</div>
        </div>
    </div>
</div>
