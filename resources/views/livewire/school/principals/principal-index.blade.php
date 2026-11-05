
<div class="container-fluid py-4 principal-management">

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h3 class="fw-bold mb-1">Principal Management</h3>

            <p class="text-muted mb-0">
                Manage Principal accounts, personal information,
                employment and salary bank details.
            </p>
        </div>

        <button type="button"
                wire:click="create"
                class="btn btn-primary px-4 py-2">
            <i class="bi bi-plus-lg me-1"></i>
            Add Principal
        </button>

    </div>

    {{-- SUCCESS MESSAGE --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="bi bi-check-circle me-2"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- STATISTICS --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted mb-1">Total Principals</p>
                            <h3 class="fw-bold mb-0">
                                {{ $principals->total() }}
                            </h3>
                        </div>

                        <div class="principal-stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-people"></i>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted mb-1">Current Page</p>
                            <h3 class="fw-bold mb-0">
                                {{ $principals->count() }}
                            </h3>
                        </div>

                        <div class="principal-stat-icon bg-success-subtle text-success">
                            <i class="bi bi-person-check"></i>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted mb-1">Page Number</p>
                            <h3 class="fw-bold mb-0">
                                {{ $principals->currentPage() }}
                            </h3>
                        </div>

                        <div class="principal-stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- PRINCIPAL TABLE --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white border-0 p-4">

            <div class="row align-items-center g-3">

                <div class="col-md-6">
                    <h5 class="fw-bold mb-1">All Principals</h5>

                    <small class="text-muted">
                        View and manage your school's Principal accounts.
                    </small>
                </div>

                <div class="col-md-6">

                    <div class="input-group">

                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search"></i>
                        </span>

                        <input type="search"
                               wire:model.live.debounce.400ms="search"
                               class="form-control border-start-0"
                               placeholder="Search name, email or staff number...">

                    </div>

                </div>

            </div>

        </div>

        <div class="table-responsive">

            <table class="table align-middle table-hover mb-0">

                <thead class="table-light">

                    <tr>
                        <th class="px-4">Principal</th>
                        <th>Staff Number</th>
                        <th>Email / Login</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Hire Date</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse ($principals as $principal)

                        <tr wire:key="principal-{{ $principal->id }}">

                            <td class="px-4">

                                <div class="d-flex align-items-center gap-3">

                                    <div class="principal-avatar">
                                        {{ strtoupper(
                                            substr($principal->first_name, 0, 1) .
                                            substr($principal->last_name, 0, 1)
                                        ) }}
                                    </div>

                                    <div>
                                        <div class="fw-semibold">
                                            {{ $principal->full_name }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $principal->qualification ?: 'Principal' }}
                                        </small>
                                    </div>

                                </div>

                            </td>

                            <td>
                                {{ $principal->staff_number }}
                            </td>

                            <td>

                                <div>
                                    {{ $principal->user?->email ?? 'No account' }}
                                </div>

                                <small class="text-success">
                                    {{ $principal->user ? 'Login account linked' : 'No login account' }}
                                </small>

                            </td>

                            <td>
                                {{ $principal->phone ?: '—' }}
                            </td>

                            <td>

                                @php
                                    $statusClass = match($principal->employment_status) {
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'suspended' => 'warning',
                                        default => 'secondary',
                                    };
                                @endphp

                                <span class="badge rounded-pill text-bg-{{ $statusClass }}">
                                    {{ ucfirst($principal->employment_status) }}
                                </span>

                            </td>

                            <td>
                                {{ $principal->hired_at?->format('d M Y') ?? '—' }}
                            </td>

                            <td class="text-end px-4">

                                <div class="d-inline-flex gap-2">

                                    <button type="button"
                                            wire:click="edit({{ $principal->id }})"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit Principal">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            wire:click="delete({{ $principal->id }})"
                                            wire:confirm="Permanently delete this Principal and their login account?"
                                            title="Delete Principal">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center py-5">

                                <i class="bi bi-people fs-1 text-muted"></i>

                                <h6 class="fw-bold mt-3">
                                    No Principals Found
                                </h6>

                                <p class="text-muted mb-3">
                                    Create your first Principal account to get started.
                                </p>

                                <button type="button"
                                        wire:click="create"
                                        class="btn btn-primary btn-sm">

                                    <i class="bi bi-plus-lg me-1"></i>
                                    Add Principal

                                </button>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="card-body border-top">
            {{ $principals->links() }}
        </div>

    </div>


    {{-- CREATE / EDIT MODAL --}}
    @if ($showModal)

        <div class="principal-modal-backdrop"
             wire:key="principal-modal">

            <div class="principal-modal-dialog">

                <div class="card border-0 shadow-lg">

                    {{-- MODAL HEADER --}}
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">

                        <div>

                            <h5 class="fw-bold mb-1">

                                <i class="bi bi-person-badge text-primary me-2"></i>

                                {{ $editingPrincipalId ? 'Edit Principal' : 'Create Principal' }}

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


                    <form wire:submit="save" class="principal-form">

                        {{-- SCROLLABLE MODAL BODY --}}
                        <div class="card-body principal-modal-body">

                            {{-- LOGIN INFORMATION --}}
                            <section class="principal-form-section">

                                <h6>
                                    <i class="bi bi-shield-lock me-2"></i>
                                    Login Information
                                </h6>

                                <p class="text-muted small mb-3">
                                    These details will be used by the Principal to log in.
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
                                               placeholder="principal@example.com">

                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Password
                                            @if(!$editingPrincipalId)
                                                <span class="text-danger">*</span>
                                            @else
                                                <small class="text-muted">(Optional)</small>
                                            @endif
                                        </label>

                                        <input type="password"
                                               wire:model="password"
                                               autocomplete="new-password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               placeholder="{{ $editingPrincipalId ? 'Leave blank to keep current password' : 'Minimum 8 characters' }}">

                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Confirm Password
                                        </label>

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
                            <section class="principal-form-section">

                                <h6>
                                    <i class="bi bi-person me-2"></i>
                                    Personal Information
                                </h6>

                                <p class="text-muted small mb-3">
                                    Enter the Principal's personal and contact details.
                                </p>

                                <div class="row g-3">

                                    <div class="col-md-4">

                                        <label class="form-label">
                                            First Name <span class="text-danger">*</span>
                                        </label>

                                        <input type="text"
                                               wire:model="first_name"
                                               class="form-control @error('first_name') is-invalid @enderror"
                                               placeholder="First name">

                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>

                                    <div class="col-md-4">

                                        <label class="form-label">
                                            Middle Name
                                        </label>

                                        <input type="text"
                                               wire:model="middle_name"
                                               class="form-control"
                                               placeholder="Middle name">

                                    </div>

                                    <div class="col-md-4">

                                        <label class="form-label">
                                            Last Name <span class="text-danger">*</span>
                                        </label>

                                        <input type="text"
                                               wire:model="last_name"
                                               class="form-control @error('last_name') is-invalid @enderror"
                                               placeholder="Last name">

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
                                               placeholder="e.g. PRN-001">

                                        @error('staff_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Phone Number
                                        </label>

                                        <input type="text"
                                               wire:model="phone"
                                               class="form-control"
                                               placeholder="Phone number">

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Gender
                                        </label>

                                        <select wire:model="gender" class="form-select">

                                            <option value="">Select gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>

                                        </select>

                                        @error('gender')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Date of Birth
                                        </label>

                                        <input type="date"
                                               wire:model="date_of_birth"
                                               class="form-control">

                                        @error('date_of_birth')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror

                                    </div>

                                    <div class="col-12">

                                        <label class="form-label">
                                            Residential Address
                                        </label>

                                        <textarea wire:model="address"
                                                  rows="3"
                                                  class="form-control"
                                                  placeholder="Enter full residential address"></textarea>

                                    </div>

                                </div>

                            </section>


                            {{-- EMPLOYMENT INFORMATION --}}
                            <section class="principal-form-section">

                                <h6>
                                    <i class="bi bi-briefcase me-2"></i>
                                    Employment Information
                                </h6>

                                <p class="text-muted small mb-3">
                                    Manage the Principal's employment record.
                                </p>

                                <div class="row g-3">

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Qualification
                                        </label>

                                        <input type="text"
                                               wire:model="qualification"
                                               class="form-control"
                                               placeholder="e.g. B.Ed, M.Ed">

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Employment Status <span class="text-danger">*</span>
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

                                        <label class="form-label">
                                            Hire Date
                                        </label>

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
                            <section class="principal-form-section bank-section">

                                <h6>
                                    <i class="bi bi-bank me-2"></i>
                                    Salary Bank Details
                                </h6>

                                <p class="text-muted small mb-3">
                                    Enter the Principal's bank information for salary payments.
                                </p>

                                <div class="row g-3">

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Bank Name
                                        </label>

                                        <input type="text"
                                               wire:model="bank_name"
                                               class="form-control"
                                               placeholder="e.g. Access Bank">

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Account Name
                                        </label>

                                        <input type="text"
                                               wire:model="bank_account_name"
                                               class="form-control"
                                               placeholder="Account holder's full name">

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Account Number
                                        </label>

                                        <input type="text"
                                               wire:model="bank_account_number"
                                               autocomplete="new-password"
                                               inputmode="numeric"
                                               class="form-control @error('bank_account_number') is-invalid @enderror"
                                               placeholder="Enter bank account number">

                                        @error('bank_account_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        @if($editingPrincipalId && $hasSavedBankAccountNumber)

                                            <div class="alert alert-success py-2 mt-2 mb-0">
                                                <div class="fw-semibold">
                                                    <i class="bi bi-shield-check me-1"></i>
                                                    Bank account saved
                                                </div>

                                                <div class="small mt-1">
                                                    Account ending in ****{{ $bankAccountLastFour }}
                                                </div>

                                                <small class="text-muted">
                                                    Leave the input blank to keep the existing account number.
                                                </small>
                                            </div>

                                        @else

                                            <small class="text-muted d-block mt-2">
                                                Enter the account number without spaces.
                                            </small>

                                        @endif

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Bank Code
                                        </label>

                                        <input type="text"
                                               wire:model="bank_code"
                                               class="form-control"
                                               placeholder="Enter bank code">

                                    </div>

                                    <div class="col-12">

                                        <label class="form-label">
                                            Payment Notes
                                        </label>

                                        <textarea wire:model="payment_notes"
                                                  rows="3"
                                                  class="form-control"
                                                  placeholder="Additional salary payment information..."></textarea>

                                        @error('payment_notes')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror

                                    </div>

                                </div>

                            </section>

                        </div>


                        {{-- FIXED MODAL FOOTER --}}
                        <div class="card-footer bg-white">

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                                <small class="text-muted">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Principal information
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

                                            {{ $editingPrincipalId ? 'Update Principal' : 'Create Principal' }}

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


    {{-- PAGE AND MODAL STYLES --}}
    <style>

        .principal-management .card {
            border-radius: 16px;
        }

        .principal-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .principal-avatar {
            width: 43px;
            height: 43px;
            min-width: 43px;
            border-radius: 50%;
            background: #e7efff;
            color: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .principal-management .table th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #64748b;
            white-space: nowrap;
        }

        .principal-management .table td {
            font-size: 13px;
            white-space: nowrap;
        }

        /* Modal backdrop */
        .principal-modal-backdrop {
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

        /* Modal container */
        .principal-modal-dialog {
            width: 100%;
            max-width: 1100px;
            height: 94vh;
            max-height: 950px;
            min-height: 0;
        }

        /* Main modal card */
        .principal-modal-dialog > .card {
            height: 100%;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-radius: 18px !important;
        }

        /* Fixed header */
        .principal-modal-dialog .card-header {
            flex-shrink: 0;
            border-bottom: 1px solid #e9ecef !important;
            padding: 22px 28px !important;
        }

        /* Form layout */
        .principal-form {
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Scrollable content */
        .principal-modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 28px !important;
            overscroll-behavior: contain;
            scrollbar-width: thin;
            scrollbar-color: #adb5bd #f8f9fa;
        }

        .principal-modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .principal-modal-body::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        .principal-modal-body::-webkit-scrollbar-thumb {
            background: #adb5bd;
            border-radius: 10px;
        }

        /* Form sections */
        .principal-form-section {
            padding-bottom: 28px;
            margin-bottom: 28px;
            border-bottom: 1px solid #e9ecef;
        }

        .principal-form-section:last-child {
            border-bottom: 0;
            margin-bottom: 0;
            padding-bottom: 5px;
        }

        .principal-form-section h6 {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .2px;
            padding: 13px 16px;
            background: #f1f5ff;
            border-radius: 9px;
            border-left: 4px solid #0d6efd;
            margin-bottom: 12px;
            color: #174ea6;
        }

        .principal-form-section .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #344054;
            margin-bottom: 7px;
        }

        .principal-form-section .form-control,
        .principal-form-section .form-select {
            min-height: 44px;
            border-radius: 8px;
            font-size: 14px;
        }

        .principal-form-section textarea.form-control {
            min-height: 90px;
        }

        .principal-form-section .form-control:focus,
        .principal-form-section .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .12);
        }

        /* Bank section */
        .bank-section {
            background: #fbfdff;
            padding: 20px;
            border: 1px solid #e5eaf3;
            border-radius: 12px;
        }

        /* Fixed footer */
        .principal-modal-dialog .card-footer {
            flex-shrink: 0;
            border-top: 1px solid #e9ecef !important;
            padding: 16px 28px !important;
        }

        /* Mobile */
        @media(max-width: 767px) {

            .principal-modal-backdrop {
                padding: 8px;
                align-items: flex-start;
            }

            .principal-modal-dialog {
                height: calc(100dvh - 16px);
                max-height: none;
            }

            .principal-modal-dialog > .card {
                border-radius: 12px !important;
            }

            .principal-modal-dialog .card-header,
            .principal-modal-dialog .card-footer {
                padding: 14px !important;
            }

            .principal-modal-body {
                padding: 18px !important;
            }

            .principal-form-section {
                padding-bottom: 20px;
                margin-bottom: 20px;
            }

            .bank-section {
                padding: 14px;
            }

        }

    </style>

</div>
