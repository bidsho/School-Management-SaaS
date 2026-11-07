
<div>
    
<div>
    @if ($errors->has('creation'))
    <div class="alert alert-danger rounded-3">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ $errors->first('creation') }}
    </div>
@endif
    <form wire:submit="save">
        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-header bg-white border-0 p-4">
                <h5 class="fw-bold mb-1">School Information</h5>
                <p class="text-secondary small mb-0">
                    Register the school and create its first administrator.
                </p>
            </div>

            <div class="card-body p-4">

                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('warning'))
                    <div class="alert alert-warning">
                        {{ session('warning') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- SCHOOL DETAILS --}}
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-building me-2 text-primary"></i>
                    School Details
                </h6>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            School Name <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               wire:model="name"
                               placeholder="Enter school name">

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">School Email</label>

                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               wire:model="email"
                               placeholder="info@school.com">

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone Number</label>

                        <input type="text"
                               class="form-control @error('phone') is-invalid @enderror"
                               wire:model="phone"
                               placeholder="+234...">

                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Timezone</label>

                        <select class="form-select @error('timezone') is-invalid @enderror"
                                wire:model="timezone">
                            <option value="Africa/Lagos">Africa/Lagos (Nigeria)</option>
                            <option value="Africa/Accra">Africa/Accra (Ghana)</option>
                            <option value="Africa/Nairobi">Africa/Nairobi (Kenya)</option>
                            <option value="Europe/London">Europe/London</option>
                            <option value="America/New_York">America/New_York</option>
                        </select>

                        @error('timezone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">School Address</label>

                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  wire:model="address"
                                  rows="3"
                                  placeholder="Enter the school's address"></textarea>

                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Initial Status</label>

                        <select class="form-select @error('status') is-invalid @enderror"
                                wire:model="status">
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="suspended">Suspended</option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- FIRST SCHOOL ADMIN --}}
                <h6 class="fw-bold mb-1">
                    <i class="bi bi-person-badge me-2 text-primary"></i>
                    First School Administrator
                </h6>

                <p class="text-secondary small mb-3">
                    The administrator will manage this school only.
                    A password setup link will be sent to their email.
                </p>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Administrator Name <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               class="form-control @error('adminName') is-invalid @enderror"
                               wire:model="adminName"
                               placeholder="Enter administrator's full name">

                        @error('adminName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Administrator Email <span class="text-danger">*</span>
                        </label>

                        <input type="email"
                               class="form-control @error('adminEmail') is-invalid @enderror"
                               wire:model="adminEmail"
                               placeholder="admin@school.com">

                        @error('adminEmail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            This email must not already belong to another user.
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer bg-white border-0 p-4
                        d-flex flex-wrap justify-content-end gap-2">

                <a href="{{ route('platform.schools.index') }}"
                   class="btn btn-light border rounded-3 px-4">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary rounded-3 px-4"
                        wire:loading.attr="disabled"
                        wire:target="save">

                    <span wire:loading.remove wire:target="save">
                        <i class="bi bi-check-circle me-2"></i>
                        Create School
                    </span>

                    <span wire:loading wire:target="save">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Creating...
                    </span>
                </button>
            </div>

        </div>
    </form>
</div>

</div>
