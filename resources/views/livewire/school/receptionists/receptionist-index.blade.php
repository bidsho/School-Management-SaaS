<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Receptionist Management</h3>
            <p class="text-muted mb-0">
                Create and manage Receptionist accounts and login details.
            </p>
        </div>

        <button type="button" wire:click="create" class="btn btn-primary px-4">
            <i class="bi bi-plus-lg me-1"></i> Add Receptionist
        </button>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- RECEPTIONIST TABLE --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white border-0 p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-1">All Receptionists</h5>
                    <small class="text-muted">
                        Manage your school's Receptionist accounts.
                    </small>
                </div>

                <div class="col-md-6">
                    <input type="search"
                           wire:model.live.debounce.400ms="search"
                           class="form-control"
                           placeholder="Search name, email or staff number...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="px-4">Receptionist</th>
                        <th>Staff Number</th>
                        <th>Email / Login</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Hire Date</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($receptionists as $receptionist)
                        <tr wire:key="receptionist-{{ $receptionist->id }}">

                            <td class="px-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary-subtle text-primary
                                                d-flex align-items-center justify-content-center fw-bold"
                                         style="width:42px;height:42px;">
                                        {{ strtoupper(
                                            substr($receptionist->first_name, 0, 1) .
                                            substr($receptionist->last_name, 0, 1)
                                        ) }}
                                    </div>

                                    <div>
                                        <div class="fw-semibold">
                                            {{ $receptionist->full_name }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $receptionist->qualification ?: 'Receptionist' }}
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td>{{ $receptionist->staff_number }}</td>

                            <td>
                                <div>
                                    {{ $receptionist->user?->email ?? 'No account' }}
                                </div>

                                <small class="{{ $receptionist->user ? 'text-success' : 'text-danger' }}">
                                    {{ $receptionist->user ? 'Login account linked' : 'No login account' }}
                                </small>
                            </td>

                            <td>{{ $receptionist->phone ?: '—' }}</td>

                            <td>
                                @php
                                    $statusClass = match ($receptionist->employment_status) {
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'suspended' => 'warning',
                                        default => 'secondary',
                                    };
                                @endphp

                                <span class="badge rounded-pill text-bg-{{ $statusClass }}">
                                    {{ ucfirst($receptionist->employment_status ?? 'Unknown') }}
                                </span>
                            </td>

                            <td>
                                {{ $receptionist->hired_at?->format('d M Y') ?? '—' }}
                            </td>

                            <td class="text-end px-4">
                                <div class="d-inline-flex gap-2">

                                    <button type="button"
                                            wire:click="edit({{ $receptionist->id }})"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit Receptionist">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button"
                                            wire:click="delete({{ $receptionist->id }})"
                                            wire:confirm="Permanently delete this Receptionist and their login account?"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete Receptionist">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-person-lines-fill fs-1 text-muted"></i>

                                <h6 class="fw-bold mt-3">No Receptionists Found</h6>

                                <p class="text-muted">
                                    Create your first Receptionist account.
                                </p>

                                <button type="button"
                                        wire:click="create"
                                        class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg me-1"></i>
                                    Add Receptionist
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="card-body border-top">
            {{ $receptionists->links() }}
        </div>

    </div>

    {{-- CREATE / EDIT MODAL --}}
    @if ($showModal)
        <div class="receptionist-modal-backdrop" wire:key="receptionist-modal">

            <div class="receptionist-modal-dialog">

                <div class="card border-0 shadow-lg">

                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1">
                                <i class="bi bi-person-badge text-primary me-2"></i>
                                {{ $editingReceptionistId ? 'Edit Receptionist' : 'Create Receptionist' }}
                            </h5>

                            <small class="text-muted">
                                Manage login, personal, employment and bank information.
                            </small>
                        </div>

                        <button type="button"
                                wire:click="closeModal"
                                class="btn-close"
                                aria-label="Close"></button>
                    </div>

                    <form wire:submit="save" class="receptionist-form">

                        <div class="card-body receptionist-modal-body">

                            {{-- LOGIN INFORMATION --}}
                            <section class="receptionist-section">
                                <h6>
                                    <i class="bi bi-shield-lock me-2"></i>
                                    Login Information
                                </h6>

                                <p class="text-muted small">
                                    These details allow the Receptionist to log in.
                                </p>

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Login Name <span class="text-danger">*</span>
                                        </label>

                                        <input type="text"
                                               wire:model="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               placeholder="Enter login name">

                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Email Address <span class="text-danger">*</span>
                                        </label>

                                        <input type="email"
                                               wire:model="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               placeholder="receptionist@example.com">

                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Password
                                            @if ($editingReceptionistId)
                                                <small class="text-muted">(Optional)</small>
                                            @else
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        <input type="password"
                                               wire:model="password"
                                               autocomplete="new-password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               placeholder="{{ $editingReceptionistId ? 'Leave blank to keep current password' : 'Minimum 8 characters' }}">

                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Confirm Password</label>

                                        <input type="password"
                                               wire:model="password_confirmation"
                                               autocomplete="new-password"
                                               class="form-control"
                                               placeholder="Confirm password">

                                        @error('password_confirmation')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                            </section>

                            {{-- PERSONAL INFORMATION --}}
                            <section class="receptionist-section">
                                <h6>
                                    <i class="bi bi-person me-2"></i>
                                    Personal Information
                                </h6>

                                <div class="row g-3">

                                    <div class="col-md-4">
                                        <label class="form-label">
                                            First Name <span class="text-danger">*</span>
                                        </label>

                                        <input type="text"
                                               wire:model="first_name"
                                               class="form-control @error('first_name') is-invalid @enderror">

                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name</label>

                                        <input type="text"
                                               wire:model="middle_name"
                                               class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">
                                            Last Name <span class="text-danger">*</span>
                                        </label>

                                        <input type="text"
                                               wire:model="last_name"
                                               class="form-control @error('last_name') is-invalid @enderror">

                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Staff Number <span class="text-danger">*</span>
                                        </label>

                                        <input type="text"
                                               wire:model="staff_number"
                                               class="form-control @error('staff_number') is-invalid @enderror"
                                               placeholder="REC-001">

                                        @error('staff_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Phone Number</label>

                                        <input type="text"
                                               wire:model="phone"
                                               class="form-control"
                                               placeholder="Phone number">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Residential Address</label>

                                        <textarea wire:model="address"
                                                  rows="3"
                                                  class="form-control"
                                                  placeholder="Enter residential address"></textarea>
                                    </div>

                                </div>
                            </section>

                            {{-- EMPLOYMENT INFORMATION --}}
                            <section class="receptionist-section">
                                <h6>
                                    <i class="bi bi-briefcase me-2"></i>
                                    Employment Information
                                </h6>

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label">Qualification</label>

                                        <input type="text"
                                               wire:model="qualification"
                                               class="form-control"
                                               placeholder="e.g. OND, HND, B.Sc">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Employment Status
                                        </label>

                                        <select wire:model="employment_status"
                                                class="form-select">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="suspended">Suspended</option>
                                        </select>

                                        @error('employment_status')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Hire Date</label>

                                        <input type="date"
                                               wire:model="hired_at"
                                               class="form-control">

                                        @error('hired_at')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                            </section>

                            {{-- BANK INFORMATION --}}
                            <section class="receptionist-section bank-section">
                                <h6>
                                    <i class="bi bi-bank me-2"></i>
                                    Salary Bank Details
                                </h6>

                                <p class="text-muted small">
                                    Bank account details are encrypted in the database.
                                </p>

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label">Bank Name</label>

                                        <input type="text"
                                               wire:model="bank_name"
                                               class="form-control"
                                               placeholder="e.g. Access Bank">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Account Name</label>

                                        <input type="text"
                                               wire:model="bank_account_name"
                                               class="form-control"
                                               placeholder="Account holder's name">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Account Number</label>

                                        <input type="text"
                                               wire:model="bank_account_number"
                                               autocomplete="new-password"
                                               inputmode="numeric"
                                               class="form-control @error('bank_account_number') is-invalid @enderror"
                                               placeholder="Enter account number">

                                        @error('bank_account_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        @if ($editingReceptionistId && $hasSavedBankAccountNumber)
                                            <div class="alert alert-success py-2 mt-2 mb-0">
                                                <strong>Bank account saved</strong>
                                                <div class="small">
                                                    Account ending in ****{{ $bankAccountLastFour }}
                                                </div>
                                                <small class="text-muted">
                                                    Leave blank to keep the existing account number.
                                                </small>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Bank Code</label>

                                        <input type="text"
                                               wire:model="bank_code"
                                               class="form-control"
                                               placeholder="Enter bank code">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Payment Notes</label>

                                        <textarea wire:model="payment_notes"
                                                  rows="3"
                                                  class="form-control"
                                                  placeholder="Additional payment information"></textarea>

                                        @error('payment_notes')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                            </section>

                        </div>

                        {{-- MODAL FOOTER --}}
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                                <small class="text-muted">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Receptionist account management
                                </small>

                                <div class="d-flex gap-2">

                                    <button type="button"
                                            wire:click="closeModal"
                                            class="btn btn-light border px-4">
                                        Cancel
                                    </button>

                                    <button type="submit"
                                            class="btn btn-primary px-4"
                                            wire:loading.attr="disabled"
                                            wire:target="save">

                                        <span wire:loading.remove wire:target="save">
                                            <i class="bi bi-save me-1"></i>
                                            {{ $editingReceptionistId ? 'Update Receptionist' : 'Create Receptionist' }}
                                        </span>

                                        <span wire:loading wire:target="save">
                                            <span class="spinner-border spinner-border-sm me-1"></span>
                                            Processing...
                                        </span>

                                    </button>

                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .receptionist-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1055;
            background: rgba(15, 23, 42, .65);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .receptionist-modal-dialog {
            width: 100%;
            max-width: 1050px;
            height: 94vh;
            max-height: 950px;
            min-height: 0;
        }

        .receptionist-modal-dialog > .card {
            height: 100%;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-radius: 18px;
        }

        .receptionist-modal-dialog .card-header,
        .receptionist-modal-dialog .card-footer {
            flex-shrink: 0;
            padding: 18px 25px;
        }

        .receptionist-form {
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .receptionist-modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            padding: 25px;
        }

        .receptionist-section {
            padding-bottom: 25px;
            margin-bottom: 25px;
            border-bottom: 1px solid #e9ecef;
        }

        .receptionist-section:last-child {
            border-bottom: 0;
            margin-bottom: 0;
        }

        .receptionist-section h6 {
            font-size: 15px;
            font-weight: 700;
            padding: 13px 16px;
            background: #f1f5ff;
            border-left: 4px solid #0d6efd;
            border-radius: 8px;
            color: #174ea6;
            margin-bottom: 15px;
        }

        .receptionist-section .form-label {
            font-size: 13px;
            font-weight: 600;
        }

        .receptionist-section .form-control,
        .receptionist-section .form-select {
            min-height: 44px;
            border-radius: 8px;
            font-size: 14px;
        }

        .receptionist-section textarea.form-control {
            min-height: 90px;
        }

        .bank-section {
            background: #fbfdff;
            border: 1px solid #e5eaf3;
            border-radius: 12px;
            padding: 20px;
        }

        @media (max-width: 767px) {
            .receptionist-modal-backdrop {
                padding: 8px;
                align-items: flex-start;
            }

            .receptionist-modal-dialog {
                height: calc(100dvh - 16px);
                max-height: none;
            }

            .receptionist-modal-body {
                padding: 16px;
            }

            .receptionist-modal-dialog .card-header,
            .receptionist-modal-dialog .card-footer {
                padding: 14px;
            }

            .bank-section {
                padding: 14px;
            }
        }
    </style>

</div>