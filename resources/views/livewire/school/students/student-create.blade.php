
<div>
    {{-- Page Header --}}
    <div class="mb-4">
        <a href="{{ route('school.students.index') }}"
           class="text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Students
        </a>

        <h2 class="h4 fw-bold mt-3 mb-1">Register Student</h2>

        <p class="text-secondary mb-0">
            Enter the student's information below. A student login account
            will be created automatically.
        </p>
    </div>

    {{-- Registration Form --}}
    <form wire:submit="save">
        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">

                {{-- Personal Information --}}
                <h3 class="h6 fw-bold mb-3">
                    <i class="bi bi-person me-2 text-primary"></i>
                    Personal Information
                </h3>

                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="admission_number" class="form-label">
                            Admission Number *
                        </label>

                        <input
                            type="text"
                            id="admission_number"
                            class="form-control @error('admission_number') is-invalid @enderror"
                            wire:model="admission_number"
                            placeholder="e.g. STU-2026-001"
                            required
                        >

                        @error('admission_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="first_name" class="form-label">
                            First Name *
                        </label>

                        <input
                            type="text"
                            id="first_name"
                            class="form-control @error('first_name') is-invalid @enderror"
                            wire:model="first_name"
                            required
                        >

                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="last_name" class="form-label">
                            Last Name *
                        </label>

                        <input
                            type="text"
                            id="last_name"
                            class="form-control @error('last_name') is-invalid @enderror"
                            wire:model="last_name"
                            required
                        >

                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="middle_name" class="form-label">
                            Middle Name
                        </label>

                        <input
                            type="text"
                            id="middle_name"
                            class="form-control @error('middle_name') is-invalid @enderror"
                            wire:model="middle_name"
                        >

                        @error('middle_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="date_of_birth" class="form-label">
                            Date of Birth
                        </label>

                        <input
                            type="date"
                            id="date_of_birth"
                            class="form-control @error('date_of_birth') is-invalid @enderror"
                            wire:model="date_of_birth"
                        >

                        @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="gender" class="form-label">Gender</label>

                        <select
                            id="gender"
                            class="form-select @error('gender') is-invalid @enderror"
                            wire:model="gender"
                        >
                            <option value="">Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>

                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <hr class="my-4">

                {{-- Contact Information --}}
                <h3 class="h6 fw-bold mb-3">
                    <i class="bi bi-envelope me-2 text-primary"></i>
                    Contact Information
                </h3>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="email" class="form-label">
                            Student Email Address *
                        </label>

                        <input
                            type="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            wire:model="email"
                            placeholder="student@example.com"
                            autocomplete="email"
                            required
                        >

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            This email will be used to sign in and receive
                            the password setup link.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label">
                            Phone Number
                        </label>

                        <input
                            type="text"
                            id="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            wire:model="phone"
                            placeholder="Student phone number"
                        >

                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label">
                            Home Address
                        </label>

                        <textarea
                            id="address"
                            class="form-control @error('address') is-invalid @enderror"
                            wire:model="address"
                            rows="3"
                            placeholder="Enter the student's home address"
                        ></textarea>

                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <hr class="my-4">

                {{-- Parent / Guardian --}}
                <h3 class="h6 fw-bold mb-3">
                    <i class="bi bi-people me-2 text-primary"></i>
                    Parent / Guardian
                </h3>

                <div class="card border shadow-none">
                    <div class="card-header bg-white py-3">
                        <div class="fw-semibold">Link an Existing Parent</div>

                        <small class="text-muted">
                            Search for a parent or guardian registered
                            in your school.
                        </small>
                    </div>

                    <div class="card-body">

                        <label for="parentSearch" class="form-label">
                            Search Parent
                        </label>

                        <div class="position-relative">

                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search"></i>
                                </span>

                                <input
                                    type="text"
                                    id="parentSearch"
                                    class="form-control @error('parentId') is-invalid @enderror"
                                    wire:model.live.debounce.300ms="parentSearch"
                                    wire:focus="openParentResults"
                                    placeholder="Search by name, email or phone..."
                                    autocomplete="off"
                                >

                                @if ($parentId !== '' && $parentId !== null)
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        wire:click="clearParent"
                                    >
                                        <i class="bi bi-x-lg me-1"></i>
                                        Change
                                    </button>
                                @endif
                            </div>

                            {{-- Search Results --}}
                            @if (
                                $showParentResults &&
                                trim($parentSearch) !== '' &&
                                ($parentId === '' || $parentId === null)
                            )
                                <div
                                    class="list-group position-absolute w-100 shadow mt-1"
                                    style="z-index: 1050; max-height: 280px; overflow-y: auto;"
                                >
                                    @forelse ($parents as $parent)
                                        <button
                                            type="button"
                                            wire:key="parent-result-{{ $parent->id }}"
                                            wire:click="selectParent({{ $parent->id }})"
                                            class="list-group-item list-group-item-action text-start py-3"
                                        >
                                            <div class="d-flex align-items-center gap-3">

                                                <div
                                                    class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                                    style="width: 42px; height: 42px;"
                                                >
                                                    {{ strtoupper(
                                                        substr($parent->first_name ?? '', 0, 1) .
                                                        substr($parent->last_name ?? '', 0, 1)
                                                    ) }}
                                                </div>

                                                <div class="flex-grow-1 min-w-0">

                                                    <div class="fw-semibold">
                                                        {{ $parent->first_name }}
                                                        {{ $parent->last_name }}
                                                    </div>

                                                    @if ($parent->email)
                                                        <small class="text-muted d-block">
                                                            <i class="bi bi-envelope me-1"></i>
                                                            {{ $parent->email }}
                                                        </small>
                                                    @endif

                                                    @if ($parent->phone)
                                                        <small class="text-muted d-block">
                                                            <i class="bi bi-telephone me-1"></i>
                                                            {{ $parent->phone }}
                                                        </small>
                                                    @endif

                                                </div>

                                                <i class="bi bi-arrow-up-left text-primary"></i>

                                            </div>
                                        </button>
                                    @empty
                                        <div class="list-group-item py-4 text-center">
                                            <i class="bi bi-person-x fs-3 text-muted"></i>

                                            <p class="mb-1 mt-2 fw-semibold">
                                                No parent found
                                            </p>

                                            <small class="text-muted">
                                                Try another name, email address
                                                or phone number.
                                            </small>
                                        </div>
                                    @endforelse
                                </div>
                            @endif

                        </div>

                        @error('parentId')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- Selected Parent --}}
                        @if ($selectedParent)
                            <div class="alert alert-success mt-3 mb-0">
                                <div class="d-flex align-items-center gap-3">

                                    <div
                                        class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                        style="width: 46px; height: 46px;"
                                    >
                                        {{ strtoupper(
                                            substr($selectedParent->first_name ?? '', 0, 1) .
                                            substr($selectedParent->last_name ?? '', 0, 1)
                                        ) }}
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="fw-bold">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            {{ $selectedParent->first_name }}
                                            {{ $selectedParent->last_name }}
                                        </div>

                                        @if ($selectedParent->email)
                                            <small class="d-block">
                                                {{ $selectedParent->email }}
                                            </small>
                                        @endif

                                        @if ($selectedParent->phone)
                                            <small class="d-block">
                                                {{ $selectedParent->phone }}
                                            </small>
                                        @endif

                                        <small class="text-success">
                                            Existing parent selected
                                        </small>
                                    </div>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-success"
                                        wire:click="clearParent"
                                    >
                                        Remove
                                    </button>

                                </div>
                            </div>
                        @else
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Parent selection is optional. You can link
                                a parent later.
                            </div>
                        @endif

                        {{-- Relationship --}}
                        <div class="mt-4">
                            <label for="relationship" class="form-label">
                                Relationship to Student
                            </label>

                            <select
                                id="relationship"
                                class="form-select @error('relationship') is-invalid @enderror"
                                wire:model="relationship"
                                @disabled($parentId === '' || $parentId === null)
                            >
                                <option value="parent">Parent</option>
                                <option value="mother">Mother</option>
                                <option value="father">Father</option>
                                <option value="guardian">Guardian</option>
                                <option value="other">Other</option>
                            </select>

                            @error('relationship')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>
                </div>

                <hr class="my-4">

                {{-- Student Portal Access --}}
                <h3 class="h6 fw-bold mb-3">
                    <i class="bi bi-shield-lock me-2 text-primary"></i>
                    Student Portal Access
                </h3>

                <div class="alert alert-info mb-0">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-person-check fs-4"></i>

                        <div>
                            <div class="fw-semibold">
                                Student login will be created automatically
                            </div>

                            <p class="small mb-0 mt-1">
                                After registration, the system will create
                                a student user account and assign the Student
                                role. A password setup link will be sent to
                                the student's email address.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="card-footer bg-white p-3 d-flex justify-content-end gap-2">

                <a
                    href="{{ route('school.students.index') }}"
                    class="btn btn-light border"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    <span wire:loading.remove wire:target="save">
                        <i class="bi bi-check-circle me-1"></i>
                        Register Student
                    </span>

                    <span wire:loading wire:target="save">
                        <span class="spinner-border spinner-border-sm me-1"
                              role="status" aria-hidden="true"></span>
                        Creating Student Account...
                    </span>
                </button>

            </div>

        </div>
    </form>
</div>
