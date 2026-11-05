
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Create Subject</h3>
            <p class="text-muted mb-0">Add a subject to your school's curriculum.</p>
        </div>

        <a href="{{ route('school.subjects.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Subjects
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form wire:submit="save">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Subject Name *</label>
                        <input type="text"
                               wire:model="name"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Mathematics">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Subject Code *</label>
                        <input type="text"
                               wire:model="code"
                               class="form-control @error('code') is-invalid @enderror"
                               placeholder="e.g. MTH101">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea wire:model="description"
                                  rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Describe this subject (optional)"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                </div>

                <hr class="my-4">

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        <i class="bi bi-check-circle me-1"></i> Save Subject
                    </span>
                    <span wire:loading wire:target="save">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Saving...
                    </span>
                </button>

                <a href="{{ route('school.subjects.index') }}"
                   class="btn btn-light border ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
