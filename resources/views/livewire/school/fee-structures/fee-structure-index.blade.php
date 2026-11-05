
<div>
    <div class="container-fluid py-4">

        {{-- Page heading --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1">Fee Structure Management</h3>
                <p class="text-muted mb-0">
                    Create and manage fees for your school's classes and terms.
                </p>
            </div>

            <button type="button" class="btn btn-primary"
                    wire:click="create">
                <i class="bi bi-plus-circle me-1"></i>
                Create Fee Structure
            </button>
        </div>

        {{-- Flash messages --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close"
                        data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close"
                        data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Statistics --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Total Fee Structures</div>
                        <h3 class="fw-bold mb-0">{{ $totalFees }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Active</div>
                        <h3 class="fw-bold text-success mb-0">{{ $activeFees }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Inactive</div>
                        <h3 class="fw-bold text-secondary mb-0">{{ $inactiveFees }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <label for="feeSearch" class="form-label">
                            Search fee structures
                        </label>
                        <input id="feeSearch" type="search"
                               class="form-control"
                               placeholder="Search by fee name, description or currency..."
                               wire:model.live.debounce.350ms="search">
                    </div>
                </div>
            </div>
        </div>

        {{-- Fee structures table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">All Fee Structures</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Fee Name</th>
                                <th>Academic Session</th>
                                <th>Term</th>
                                <th>Class</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th class="text-end px-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($fees as $fee)
                                <tr wire:key="fee-{{ $fee->id }}">
                                    <td class="px-3">
                                        <div class="fw-semibold">
                                            {{ $fee->name }}
                                        </div>
                                        @if ($fee->description)
                                            <small class="text-muted">
                                                {{ \Illuminate\Support\Str::limit($fee->description, 55) }}
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $fee->academicSession?->name ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $fee->term?->name ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $fee->classRoom?->name ?? '—' }}
                                    </td>

                                    <td class="fw-semibold text-nowrap">
                                        {{ $fee->currency }}
                                        {{ number_format((float) $fee->amount, 2) }}
                                    </td>

                                    <td>
                                        {{ $fee->due_date?->format('d M Y') ?? '—' }}
                                    </td>

                                    <td>
                                        @if ($fee->status === 'active')
                                            <span class="badge bg-success-subtle text-success">
                                                Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-end text-nowrap px-3">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                wire:click="edit({{ $fee->id }})"
                                                title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                            Edit
                                        </button>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-warning"
                                                wire:click="toggleStatus({{ $fee->id }})"
                                                wire:confirm="Change the status of this fee structure?"
                                                title="Activate or deactivate">
                                            {{ $fee->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="delete({{ $fee->id }})"
                                                wire:confirm="Are you sure you want to delete this fee structure? This cannot be undone."
                                                @disabled($fee->invoice_items_count > 0)
                                                title="{{ $fee->invoice_items_count > 0 ? 'Already linked to invoice items' : 'Delete' }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-receipt fs-1 text-muted"></i>
                                        <h6 class="mt-3">No fee structures found</h6>
                                        <p class="text-muted mb-3">
                                            Create a fee structure to get started.
                                        </p>
                                        <button type="button"
                                                class="btn btn-primary"
                                                wire:click="create">
                                            Create Fee Structure
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($fees->hasPages())
                <div class="card-footer bg-white">
                    {{ $fees->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Create / Edit form --}}
    @if ($showForm)
        <div class="modal d-block"
             tabindex="-1"
             role="dialog"
             aria-modal="true"
             style="background: rgba(15, 23, 42, .55); overflow-y: auto;"
             wire:key="fee-structure-form">

            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">

                    <form wire:submit="save">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title fw-bold">
                                    {{ $editingId ? 'Edit Fee Structure' : 'Create Fee Structure' }}
                                </h5>
                                <p class="text-muted small mb-0">
                                    Enter the fee details below.
                                </p>
                            </div>

                            <button type="button" class="btn-close"
                                    wire:click="cancel"
                                    aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Fee Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           wire:model="name"
                                           placeholder="e.g. Tuition Fee">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Academic Session <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('academic_session_id') is-invalid @enderror"
                                            wire:model="academic_session_id">
                                        <option value="">Select academic session</option>
                                        @foreach ($academicSessions as $session)
                                            <option value="{{ $session->id }}">
                                                {{ $session->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_session_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Term <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('term_id') is-invalid @enderror"
                                            wire:model="term_id">
                                        <option value="">Select term</option>
                                        @foreach ($terms as $term)
                                            <option value="{{ $term->id }}">
                                                {{ $term->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('term_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Classroom <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('class_room_id') is-invalid @enderror"
                                            wire:model="class_room_id">
                                        <option value="">Select classroom</option>
                                        @foreach ($classRooms as $classRoom)
                                            <option value="{{ $classRoom->id }}">
                                                {{ $classRoom->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_room_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label">
                                        Amount <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           min="0.01"
                                           step="0.01"
                                           class="form-control @error('amount') is-invalid @enderror"
                                           wire:model="amount"
                                           placeholder="50000.00">
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Currency <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('currency') is-invalid @enderror"
                                            wire:model="currency">
                                        <option value="NGN">NGN — Naira</option>
                                        <option value="USD">USD — US Dollar</option>
                                        <option value="GBP">GBP — Pound</option>
                                        <option value="EUR">EUR — Euro</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Payment Due Date</label>
                                    <input type="date"
                                           class="form-control @error('due_date') is-invalid @enderror"
                                           wire:model="due_date">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            wire:model="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea rows="3"
                                              class="form-control @error('description') is-invalid @enderror"
                                              wire:model="description"
                                              placeholder="Optional details about this fee..."></textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-light border"
                                    wire:click="cancel">
                                Cancel
                            </button>

                            <button type="submit"
                                    class="btn btn-primary"
                                    wire:loading.attr="disabled"
                                    wire:target="save">
                                <span wire:loading.remove wire:target="save">
                                    {{ $editingId ? 'Update Fee Structure' : 'Save Fee Structure' }}
                                </span>
                                <span wire:loading wire:target="save">
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
