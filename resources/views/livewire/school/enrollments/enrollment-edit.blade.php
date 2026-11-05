<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Edit Student Enrollment</h4>
            <p class="text-muted mb-0">Update the student's enrollment information.</p>
        </div>

        <a href="{{ route('school.enrollments.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Enrollments
        </a>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-semibold">Enrollment Information</h6>
        </div>

        <div class="card-body p-4">
            <form wire:submit="save">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Student *</label>
                        <select class="form-select @error('student_id') is-invalid @enderror"
                                wire:model="student_id">
                            <option value="">Select student</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->first_name }}
                                    {{ $student->last_name }}
                                    @if ($student->admission_number)
                                        — {{ $student->admission_number }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Academic Session *</label>
                        <select class="form-select @error('academic_session_id') is-invalid @enderror"
                                wire:model.live="academic_session_id">
                            <option value="">Select academic session</option>
                            @foreach ($sessions as $session)
                                <option value="{{ $session->id }}">
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_session_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Term</label>
                        <select class="form-select @error('term_id') is-invalid @enderror"
                                wire:model="term_id"
                                @disabled(!$academic_session_id)>
                            <option value="">No term selected</option>
                            @foreach ($terms as $term)
                                <option value="{{ $term->id }}">
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('term_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Terms are filtered by academic session.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Class *</label>
                        <select class="form-select @error('class_room_id') is-invalid @enderror"
                                wire:model.live="class_room_id">
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
                        <label class="form-label fw-semibold">Section</label>
                        <select class="form-select @error('section_id') is-invalid @enderror"
                                wire:model="section_id"
                                @disabled(!$class_room_id)>
                            <option value="">No section selected</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">
                                    {{ $section->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('section_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Sections are filtered by class.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Enrollment Status *</label>
                        <select class="form-select @error('admission_status') is-invalid @enderror"
                                wire:model="admission_status">
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="withdrawn">Withdrawn</option>
                        </select>
                        @error('admission_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input type="date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               wire:model="start_date">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">End Date</label>
                        <input type="date"
                               class="form-control @error('end_date') is-invalid @enderror"
                               wire:model="end_date">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit"
                            class="btn btn-primary"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            <i class="bi bi-check-circle me-1"></i> Save Changes
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Saving...
                        </span>
                    </button>

                    <a href="{{ route('school.enrollments.index') }}"
                       class="btn btn-light border">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>