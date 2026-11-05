
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Academic Sessions</h3>
            <p class="text-secondary mb-0">
                Manage academic years and their start and end dates.
            </p>
        </div>

        <a href="{{ route('school.academic-sessions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create Session
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
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-2">Total Sessions</div>
                    <h3 class="fw-bold mb-0">{{ $totalSessions }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-2">Active Sessions</div>
                    <h3 class="fw-bold text-success mb-0">{{ $activeSessions }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-2">Inactive Sessions</div>
                    <h3 class="fw-bold text-secondary mb-0">{{ $inactiveSessions }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-3 p-md-4">

            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <input type="search"
                           wire:model.live.debounce.300ms="search"
                           class="form-control"
                           placeholder="Search academic session...">
                </div>

                <div class="col-md-2">
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
                            <th>Academic Session</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Terms</th>
                            <th>Enrollments</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($sessions as $session)
                            <tr wire:key="academic-session-{{ $session->id }}">
                                <td>
                                    <div class="fw-semibold">{{ $session->name }}</div>
                                    <small class="text-secondary">
                                        Session #{{ $session->id }}
                                    </small>
                                </td>

                                <td>
                                    {{ $session->starts_at?->format('d M Y') ?? '—' }}
                                </td>

                                <td>
                                    {{ $session->ends_at?->format('d M Y') ?? '—' }}
                                </td>

                                <td>{{ $session->terms_count }}</td>
                                <td>{{ $session->enrollments_count }}</td>

                                <td>
                                    @if ($session->status === 'active')
                                        <span class="badge text-bg-success">Active</span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            {{ ucfirst($session->status ?: 'Inactive') }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('school.academic-sessions.edit', $session->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit session">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteSession({{ $session->id }})"
                                                wire:confirm="Delete this academic session? Sessions with related academic records cannot be deleted."
                                                wire:loading.attr="disabled"
                                                title="Delete session">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-calendar3 display-5 text-secondary"></i>
                                    <h6 class="fw-bold mt-3">No academic sessions found</h6>
                                    <p class="text-secondary mb-3">
                                        Create your school's first academic session.
                                    </p>
                                    <a href="{{ route('school.academic-sessions.create') }}"
                                       class="btn btn-primary btn-sm">
                                        Create Session
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
</div>
