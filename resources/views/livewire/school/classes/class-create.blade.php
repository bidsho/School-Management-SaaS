
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Create Class</h3>
            <p class="text-secondary mb-0">Add a class to your school's academic structure.</p>
        </div>
        <a href="{{ route('school.classes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Classes
        </a>
    </div>

    <form wire:submit="save">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white p-4">
                <h5 class="fw-bold mb-0">Class Information</h5>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Class Name *</label>
                        <input type="text" wire:model="name"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. JSS 1">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Class Code</label>
                        <input type="text" wire:model="code"
                               class="form-control @error('code') is-invalid @enderror"
                               placeholder="e.g. JSS1">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Level</label>
                        <select wire:model="level"
                                class="form-select @error('level') is-invalid @enderror">
                            <option value="">Select level</option>
                            <option value="Nursery">Nursery</option>
                            <option value="Primary">Primary</option>
                            <option value="Junior Secondary">Junior Secondary</option>
                            <option value="Senior Secondary">Senior Secondary</option>
                        </select>
                        @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status *</label>
                        <select wire:model="status"
                                class="form-select @error('status') is-invalid @enderror">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea wire:model="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Optional class description"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white p-4 d-flex justify-content-end gap-2">
                <a href="{{ route('school.classes.index') }}" class="btn btn-light border">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary px-4"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        <i class="bi bi-check-circle me-1"></i> Save Class
                    </span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </form>
</div>
