<div class="container-fluid py-4">

    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Treasury Staff</h3>
            <p class="text-muted mb-0">
                Manage Treasury staff, employee details and login accounts.
            </p>
        </div>

        <button type="button" wire:click="create" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i>
            Add Treasury Staff
        </button>
    </div>

    {{-- Success message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Warning message --}}
    @if (session()->has('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('warning') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Staff table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-1">All Treasury Staff</h5>
                    <p class="text-muted small mb-0">
                        View and manage registered Treasury employees.
                    </p>
                </div>

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            class="form-control"
                            placeholder="Search name, email or employee number..."
                            aria-label="Search Treasury staff"
                        >

                        @if ($search !== '')
                            <button type="button"
                                    wire:click="$set('search', '')"
                                    class="btn btn-outline-secondary">
                                Clear
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Staff Member</th>
                            <th class="py-3">Employee No.</th>
                            <th class="py-3">Phone</th>
                            <th class="py-3">Job Title</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Date Joined</th>
                            <th class="text-end px-4 py-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($treasuries as $treasury)
                            <tr wire:key="treasury-{{ $treasury->id }}">
                                <td class="px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div
                                            class="rounded-circle bg-primary-subtle text-primary
                                                   d-flex align-items-center justify-content-center
                                                   fw-bold flex-shrink-0"
                                            style="width: 42px; height: 42px;"
                                        >
                                            {{ strtoupper(substr($treasury->user->name, 0, 1)) }}
                                        </div>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $treasury->user->name }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $treasury->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="fw-semibold">
                                        {{ $treasury->employee_number }}
                                    </span>
                                </td>

                                <td>{{ $treasury->phone ?: '—' }}</td>

                                <td>{{ $treasury->job_title }}</td>

                                <td>
                                    @if ($treasury->status === 'active')
                                        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">
                                            <i class="bi bi-pause-circle me-1"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $treasury->date_joined?->format('d M Y') ?? '—' }}
                                </td>

                                <td class="text-end px-4">
                                    <div class="d-inline-flex gap-1">
                                        <button
                                            type="button"
                                            wire:click="edit({{ $treasury->id }})"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit staff"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="resendSetupLink({{ $treasury->id }})"
                                            wire:confirm="Send a password setup link to {{ $treasury->user->email }}?"
                                            wire:loading.attr="disabled"
                                            class="btn btn-sm btn-outline-info"
                                            title="Send password setup link"
                                        >
                                            <i class="bi bi-envelope-arrow-up"></i>
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="delete({{ $treasury->id }})"
                                            wire:confirm="Permanently delete this Treasury profile and its login account?"
                                            wire:loading.attr="disabled"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete staff"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-people text-muted"
                                           style="font-size: 3rem;"></i>
                                    </div>

                                    <h6 class="fw-bold">No Treasury staff found</h6>

                                    <p class="text-muted mb-3">
                                        Add a staff member or change your search.
                                    </p>

                                    @if ($search === '')
                                        <button type="button"
                                                wire:click="create"
                                                class="btn btn-primary btn-sm">
                                            <i class="bi bi-person-plus me-1"></i>
                                            Add Treasury Staff
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($treasuries->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $treasuries->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit modal --}}
    @if ($showForm)
        <div
            class="modal d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background: rgba(15, 23, 42, .55);"
            wire:key="treasury-form-modal"
        >
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-3">

                    <form wire:submit="save">
                        <div class="modal-header px-4 py-3">
                            <div>
                                <h5 class="modal-title fw-bold">
                                    {{ $editingId ? 'Edit Treasury Staff' : 'Register Treasury Staff' }}
                                </h5>

                                <p class="text-muted small mb-0 mt-1">
                                    Enter the employee's information below.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="btn-close"
                                wire:click="resetForm"
                                aria-label="Close"
                            ></button>
                        </div>

                        <div class="modal-body p-4">
                            <div class="row g-3">

                                {{-- Full name --}}
                                <div class="col-md-6">
                                    <label for="treasury-name" class="form-label fw-semibold">
                                        Full Name <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        id="treasury-name"
                                        type="text"
                                        wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Enter full name"
                                        autocomplete="name"
                                    >

                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <label for="treasury-email" class="form-label fw-semibold">
                                        Email Address <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        id="treasury-email"
                                        type="email"
                                        wire:model="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="employee@example.com"
                                        autocomplete="email"
                                    >

                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Employee number --}}
                                <div class="col-md-6">
                                    <label for="treasury-employee-number" class="form-label fw-semibold">
                                        Employee Number <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        id="treasury-employee-number"
                                        type="text"
                                        wire:model="employee_number"
                                        class="form-control @error('employee_number') is-invalid @enderror"
                                        placeholder="e.g. TR-001"
                                    >

                                    @error('employee_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Phone --}}
                                <div class="col-md-6">
                                    <label for="treasury-phone" class="form-label fw-semibold">
                                        Phone Number
                                    </label>

                                    <input
                                        id="treasury-phone"
                                        type="tel"
                                        wire:model="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        placeholder="Enter phone number"
                                        autocomplete="tel"
                                    >

                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Job title --}}
                                <div class="col-md-6">
                                    <label for="treasury-job-title" class="form-label fw-semibold">
                                        Job Title <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        id="treasury-job-title"
                                        type="text"
                                        wire:model="job_title"
                                        class="form-control @error('job_title') is-invalid @enderror"
                                        placeholder="Treasury Officer"
                                    >

                                    @error('job_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <label for="treasury-status" class="form-label fw-semibold">
                                        Employment Status <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        id="treasury-status"
                                        wire:model="status"
                                        class="form-select @error('status') is-invalid @enderror"
                                    >
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>

                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Date joined --}}
                                <div class="col-md-6">
                                    <label for="treasury-date-joined" class="form-label fw-semibold">
                                        Date Joined
                                    </label>

                                    <input
                                        id="treasury-date-joined"
                                        type="date"
                                        wire:model="date_joined"
                                        class="form-control @error('date_joined') is-invalid @enderror"
                                    >

                                    @error('date_joined')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Notes --}}
                                <div class="col-12">
                                    <label for="treasury-notes" class="form-label fw-semibold">
                                        Notes
                                    </label>

                                    <textarea
                                        id="treasury-notes"
                                        wire:model="notes"
                                        rows="3"
                                        class="form-control @error('notes') is-invalid @enderror"
                                        placeholder="Optional notes about this employee"
                                    ></textarea>

                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @unless ($editingId)
                                    <div class="col-12">
                                        <div class="alert alert-info small mb-0">
                                            <i class="bi bi-envelope me-1"></i>
                                            After registration, the system will attempt to email
                                            the employee a password setup link.
                                        </div>
                                    </div>
                                @endunless

                            </div>
                        </div>

                        <div class="modal-footer px-4 py-3">
                            <button
                                type="button"
                                wire:click="resetForm"
                                class="btn btn-light border"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                wire:loading.attr="disabled"
                                wire:target="save"
                            >
                                <span wire:loading.remove wire:target="save">
                                    <i class="bi bi-check2-circle me-1"></i>
                                    {{ $editingId ? 'Update Staff' : 'Register Staff' }}
                                </span>

                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-1"
                                          aria-hidden="true"></span>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

</div>