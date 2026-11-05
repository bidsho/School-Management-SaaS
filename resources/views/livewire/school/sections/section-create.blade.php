
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Create Section</h3>
            <p class="text-secondary mb-0">
                Add a section to one of your school's classes.
            </p>
        </div>

        <a href="{{ route('school.sections.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Sections
        </a>
    </div>

    @if ($classes->isEmpty())
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            There are no active classes available. Create an active class first.
            <a href="{{ route('school.classes.create') }}" class="alert-link">
                Create Class
            </a>
        </div>
    @endif

    <form wire:submit="save">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white p-4">
                <h5 class="fw-bold mb-1">Section Information</h5>
                <p class="text-secondary small mb-0">
                    Fields marked with * are required.
                </p>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Class <span class="text-danger">*</span>
                        </label>

                        <select wire:model="class_room_id"
                                class="form-select @error('class_room_id') is-invalid @enderror">
                            <option value="">Select class</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('class_room_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Section Name <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               wire:model="name"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. A, B, Gold or Silver">

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Capacity</label>

                        <input type="number"
                               wire:model="capacity"
                               min="1"
                               max="10000"
                               class="form-control @error('capacity') is-invalid @enderror"
                               placeholder="e.g. 40">

                        <div class="form-text">
                            Optional maximum number of students.
                        </div>

                        @error('capacity')
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
                <a href="{{ route('school.sections.index') }}"
                   class="btn btn-light border">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary px-4"
                        wire:loading.attr="disabled"
                        @disabled($classes->isEmpty())>
                    <span wire:loading.remove wire:target="save">
                        <i class="bi bi-check-circle me-1"></i> Save Section
                    </span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </form>
</div>
