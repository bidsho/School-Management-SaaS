
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Create Academic Session</h3>
            <p class="text-secondary mb-0">
                Set up a new academic year for your school.
            </p>
        </div>

        <a href="{{ route('school.academic-sessions.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Sessions
        </a>
    </div>

    <form wire:submit="save">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white p-4">
                <h5 class="fw-bold mb-1">Session Information</h5>
                <p class="text-secondary small mb-0">
                    Fields marked with * are required.
                </p>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Session Name <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               wire:model="name"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. 2026/2027">

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Start Date <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               wire:model="starts_at"
                               class="form-control @error('starts_at') is-invalid @enderror">

                        @error('starts_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            End Date <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               wire:model="ends_at"
                               class="form-control @error('ends_at') is-invalid @enderror">

                        @error('ends_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status *</label>

                        <select wire:model="status"
                                class="form-select @error('status') is-invalid @enderror">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="card-footer bg-white p-4 d-flex justify-content-end gap-2">
                <a href="{{ route('school.academic-sessions.index') }}"
                   class="btn btn-light border">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary px-4"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        <i class="bi bi-check-circle me-1"></i> Save Session
                    </span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </form>
</div>
