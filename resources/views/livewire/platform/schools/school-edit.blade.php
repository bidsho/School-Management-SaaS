
<div class="container-fluid py-3">

    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="update">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-1">School Information</h5>
                <p class="text-muted small mb-0">
                    Update the school's details and operational status.
                </p>
            </div>

            <div class="card-body p-4">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">School Name *</label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               wire:model="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">School Email</label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               wire:model="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text"
                               class="form-control @error('phone') is-invalid @enderror"
                               wire:model="phone">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Timezone *</label>
                        <select class="form-select @error('timezone') is-invalid @enderror"
                                wire:model="timezone">
                            <option value="Africa/Lagos">Africa/Lagos</option>
                            <option value="UTC">UTC</option>
                            <option value="Africa/Accra">Africa/Accra</option>
                            <option value="Europe/London">Europe/London</option>
                            <option value="America/New_York">America/New_York</option>
                        </select>
                        @error('timezone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">School Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  wire:model="address"
                                  rows="3"></textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">School Status *</label>
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
            </div>

            <div class="card-footer bg-white d-flex justify-content-between py-3">
                <a href="{{ route('platform.schools.index') }}"
                   class="btn btn-light border">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>Save Changes</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </div>
    </form>
</div>
