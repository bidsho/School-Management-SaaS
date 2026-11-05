
<div class="container-fluid py-4">

    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Parent Profile</h3>
            <p class="text-muted mb-0">
                Manage your personal information and view your linked children.
            </p>
        </div>

        <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
            <i class="bi bi-person-badge fs-2"></i>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Profile form --}}
        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 px-4 pt-4">
                    <h5 class="fw-bold mb-1">Personal Information</h5>
                    <p class="text-muted small mb-0">
                        Keep your contact information up to date.
                    </p>
                </div>

                <div class="card-body p-4">
                    <form wire:submit="updateProfile">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label fw-semibold">
                                    First Name
                                </label>
                                <input
                                    id="first_name"
                                    type="text"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    wire:model="first_name"
                                    autocomplete="given-name"
                                >
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="last_name" class="form-label fw-semibold">
                                    Last Name
                                </label>
                                <input
                                    id="last_name"
                                    type="text"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    wire:model="last_name"
                                    autocomplete="family-name"
                                >
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label fw-semibold">
                                    Email Address
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    wire:model="email"
                                    autocomplete="email"
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    This email is also used for your login account.
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="phone" class="form-label fw-semibold">
                                    Phone Number
                                </label>
                                <input
                                    id="phone"
                                    type="text"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    wire:model="phone"
                                    autocomplete="tel"
                                    placeholder="Enter your phone number"
                                >
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="occupation" class="form-label fw-semibold">
                                    Occupation
                                </label>
                                <input
                                    id="occupation"
                                    type="text"
                                    class="form-control @error('occupation') is-invalid @enderror"
                                    wire:model="occupation"
                                    placeholder="Enter your occupation"
                                >
                                @error('occupation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">
                                    Residential Address
                                </label>
                                <textarea
                                    id="address"
                                    rows="3"
                                    class="form-control @error('address') is-invalid @enderror"
                                    wire:model="address"
                                    autocomplete="street-address"
                                    placeholder="Enter your address"
                                ></textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button
                                type="submit"
                                class="btn btn-primary px-4"
                                wire:loading.attr="disabled"
                                wire:target="updateProfile"
                            >
                                <span wire:loading.remove wire:target="updateProfile">
                                    <i class="bi bi-check2-circle me-1"></i>
                                    Save Changes
                                </span>

                                <span wire:loading wire:target="updateProfile">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Saving...
                                </span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Linked children --}}
        <div class="col-12 col-xl-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 px-4 pt-4">
                    <h5 class="fw-bold mb-1">My Children</h5>
                    <p class="text-muted small mb-0">
                        Students linked to your parent account.
                    </p>
                </div>

                <div class="card-body p-4">
                    @forelse ($children as $child)
                        <div class="d-flex align-items-start gap-3
                                    {{ !$loop->last ? 'border-bottom pb-3 mb-3' : '' }}">

                            <div class="rounded-circle bg-primary bg-opacity-10
                                        text-primary d-flex align-items-center
                                        justify-content-center flex-shrink-0"
                                 style="width: 48px; height: 48px;">
                                <i class="bi bi-mortarboard fs-5"></i>
                            </div>

                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1">
                                    {{ $child->first_name }}
                                    {{ $child->middle_name }}
                                    {{ $child->last_name }}
                                </h6>

                                <div class="small text-muted mb-1">
                                    Admission No:
                                    {{ $child->admission_number ?? 'Not assigned' }}
                                </div>

                                @if ($child->classRoom)
                                    <div class="small text-muted">
                                        Class: {{ $child->classRoom->name }}
                                    </div>
                                @endif

                                <span class="badge text-bg-light border mt-2">
                                    {{ ucfirst($child->status ?? 'active') }}
                                </span>
                            </div>

                            <a
                                href="{{ route('parent.children.show', $child->id) }}"
                                class="btn btn-sm btn-outline-primary"
                                aria-label="View child profile"
                            >
                                View
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <h6 class="fw-bold mt-3">No children linked</h6>
                            <p class="small text-muted mb-0">
                                Contact your school administrator to link your children.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Account information --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Account Information</h6>

                    <div class="d-flex justify-content-between gap-3 mb-3">
                        <span class="text-muted">Account Role</span>
                        <span class="badge text-bg-primary">Parent</span>
                    </div>

                    <div class="d-flex justify-content-between gap-3">
                        <span class="text-muted">Linked Children</span>
                        <span class="fw-semibold">{{ $children->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
