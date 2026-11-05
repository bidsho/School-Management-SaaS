
<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Platform Users</h5>
                    <p class="text-muted small mb-0">
                        Search users and manage account statuses across your platform.
                    </p>
                </div>

                <div class="rounded-3 bg-primary-subtle text-primary p-3">
                    <i class="bi bi-people fs-3"></i>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-lg-4">
                    <label class="form-label small fw-semibold">Search users</label>
                    <input type="search"
                           class="form-control"
                           placeholder="Name or email..."
                           wire:model.live.debounce.300ms="search">
                </div>

                <div class="col-lg-3">
                    <label class="form-label small fw-semibold">School</label>
                    <select class="form-select" wire:model.live="schoolFilter">
                        <option value="">All schools</option>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}">
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3">
                    <label class="form-label small fw-semibold">Role</label>
                    <select class="form-select" wire:model.live="roleFilter">
                        <option value="">All roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label small fw-semibold">Status</label>
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="fw-bold mb-1">User Accounts</h5>
            <p class="text-muted small mb-0">
                {{ $users->total() }} user account(s) found
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">User</th>
                        <th>School</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $user)
                        <tr wire:key="platform-user-{{ $user->id }}">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary-subtle text-primary
                                                d-flex align-items-center justify-content-center"
                                         style="width:42px;height:42px;flex-shrink:0">
                                        <i class="bi bi-person fs-5"></i>
                                    </div>

                                    <div>
                                        <div class="fw-semibold">
                                            {{ $user->name }}

                                            @if ($user->id === auth()->id())
                                                <span class="badge text-bg-light border ms-1">
                                                    You
                                                </span>
                                            @endif
                                        </div>
                                        <div class="small text-muted">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if ($user->school)
                                    <span class="fw-medium">{{ $user->school->name }}</span>
                                @else
                                    <span class="text-muted">Platform account</span>
                                @endif
                            </td>

                            <td>
                                @forelse ($user->roles as $role)
                                    <span class="badge text-bg-light border me-1 mb-1">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-muted small">No role assigned</span>
                                @endforelse
                            </td>

                            <td>
                                @if ($user->status === 'active')
                                    <span class="badge text-bg-success">Active</span>
                                @elseif ($user->status === 'suspended')
                                    <span class="badge text-bg-danger">Suspended</span>
                                @else
                                    <span class="badge text-bg-secondary">
                                        {{ ucfirst($user->status ?? 'Unknown') }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div>{{ $user->created_at?->format('d M Y') ?? '—' }}</div>
                                <div class="small text-muted">
                                    {{ $user->created_at?->diffForHumans() }}
                                </div>
                            </td>

                            <td class="text-end pe-4">
                                @if ($user->id !== auth()->id()
                                    && ! $user->hasRole('Super Admin')
                                    && in_array($user->status, ['active', 'suspended'], true))
                                    <button type="button"
                                            class="btn btn-sm {{ $user->status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                            wire:click="toggleStatus({{ $user->id }})"
                                            wire:confirm="Are you sure you want to {{ $user->status === 'active' ? 'suspend' : 'activate' }} this account?">
                                        <i class="bi {{ $user->status === 'active' ? 'bi-person-slash' : 'bi-person-check' }} me-1"></i>
                                        {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
                                    </button>
                                @else
                                    <span class="text-muted small">
                                        Protected
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-people fs-1 text-muted"></i>
                                <h6 class="mt-3">No users found</h6>
                                <p class="text-muted small mb-0">
                                    Try changing your search or filters.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
