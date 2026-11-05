
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Create Term</h3>
            <p class="text-secondary mb-0">
                Add an academic term to an existing school session.
            </p>
        </div>

        <a href="{{ route('school.terms.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Terms
        </a>
    </div>

    @if ($sessions->isEmpty())
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Create an academic session before adding a term.
            <a href="{{ route('school.academic-sessions.create') }}" class="alert-link">
                Create Academic Session
            </a>
        </div>
    @endif

    <form wire:submit="save">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white p-4">
                <h5 class="fw-bold mb-1">Term Information</h5>
                <p class="text-secondary small mb-0">
                    Complete the required fields below.
                </p>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Academic Session <span class="text-danger">*</span>
                        </label>

                        <select wire:model="academic_session_id"
                                class="form-select @error('academic_session_id') is-invalid @enderror">
                            <option value="">Select academic session</option>
                            @foreach ($sessions as $session)
                                <option value="{{ $session->id }}">
                                    {{ $session->name }}
                                    @if ($session->starts_at && $session->ends_at)
                                        ({{ $session->starts_at->format('Y') }}–{{ $session->ends_at->format('Y') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        @error('academic_session_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Term Name <span class="text-danger">*</span>
                        </label>

                        <select wire:model="name"
                                class="form-select @error('name') is-invalid @enderror">
                            <option value="">Select term</option>
                            <option value="First Term">First Term</option>
                            <option value="Second Term">Second Term</option>
                            <option value="Third Term">Third Term</option>
                        </select>

                        @error('name')
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

                </div>
            </div>

            <div class="card-footer bg-white p-4 d-flex justify-content-end gap-2">
                <a href="{{ route('school.terms.index') }}"
                   class="btn btn-light border">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary px-4"
                        wire:loading.attr="disabled"
                        @disabled($sessions->isEmpty())>
                    <span wire:loading.remove wire:target="save">
                        <i class="bi bi-check-circle me-1"></i> Save Term
                    </span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </form>
</div>
