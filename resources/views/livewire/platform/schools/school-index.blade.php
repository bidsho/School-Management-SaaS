
<div>
    {{-- Feedback --}}
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

    <div class="card border-0 shadow-sm rounded-4">
        {{-- Search and filter --}}
        <div class="card-body p-4 border-bottom">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="search"
                            class="form-control bg-light border-start-0"
                            placeholder="Search school name, email or slug..."
                            wire:model.live.debounce.300ms="search"
                        >
                    </div>
                </div>

                <div class="col-md-4">
                    <select class="form-select bg-light" wire:model.live="status">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">School</th>
                        <th class="py-3">Contact</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Registered</th>
                        <th class="text-end pe-4 py-3">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($schools as $school)
                        @php
                            $statusClass = match ($school->status) {
                                'active' => 'success',
                                'pending' => 'warning',
                                'suspended' => 'danger',
                                default => 'secondary',
                            };
                        @endphp

                        <tr wire:key="school-{{ $school->id }}">
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="rounded-3 bg-primary bg-opacity-10 text-primary
                                               d-flex align-items-center justify-content-center"
                                        style="width:44px;height:44px;flex-shrink:0;"
                                    >
                                        <i class="bi bi-building fs-5"></i>
                                    </div>

                                    <div>
                                        <div class="fw-semibold">{{ $school->name }}</div>
                                        <small class="text-secondary">{{ $school->slug }}</small>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <div>{{ $school->email ?: '—' }}</div>
                                <small class="text-secondary">
                                    {{ $school->phone ?: 'No phone added' }}
                                </small>
                            </td>

                            <td class="py-3">
                                <span class="badge rounded-pill bg-{{ $statusClass }}-subtle
                                             text-{{ $statusClass }} px-3 py-2">
                                    {{ ucfirst($school->status ?? 'unknown') }}
                                </span>
                            </td>

                            <td class="py-3 text-secondary">
                                {{ $school->created_at?->format('M d, Y') ?? '—' }}
                            </td>

                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    <a
                                        href="{{ route('platform.schools.edit', $school) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit school"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                        Edit
                                    </a>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="deleteSchool({{ $school->id }})"
                                        wire:confirm="Are you sure you want to delete this school? This cannot be undone."
                                    >
                                        <i class="bi bi-trash"></i>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-buildings display-5 text-secondary"></i>
                                <h6 class="fw-bold mt-3">No schools found</h6>
                                <p class="text-secondary small mb-0">
                                    Try another search or create a school.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($schools->hasPages())
            <div class="card-body border-top">
                {{ $schools->links() }}
            </div>
        @endif
    </div>
</div>
