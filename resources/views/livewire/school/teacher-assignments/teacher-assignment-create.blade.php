
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Create Teacher Assignment</h3>
            <p class="text-muted mb-0">Assign a teacher to a subject, class and academic period.</p>
        </div>

        <a href="{{ route('school.teacher-assignments.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form wire:submit="save">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Teacher *</label>
                        <select wire:model="teacher_id"
                                class="form-select @error('teacher_id') is-invalid @enderror">
                            <option value="">Select teacher</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ trim(($teacher->first_name ?? '') . ' ' . ($teacher->middle_name ?? '') . ' ' . ($teacher->last_name ?? '')) ?: 'Name unavailable' }}
                                    @if ($teacher->staff_number)
                                        — {{ $teacher->staff_number }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Subject *</label>
                        <select wire:model="subject_id"
                                class="form-select @error('subject_id') is-invalid @enderror">
                            <option value="">Select subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Class *</label>
                        <select wire:model.live="class_room_id"
                                class="form-select @error('class_room_id') is-invalid @enderror">
                            <option value="">Select class</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_room_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Section</label>
                        <select wire:model="section_id"
                                class="form-select @error('section_id') is-invalid @enderror">
                            <option value="">No section</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">
                                    {{ $section->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('section_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Academic Session *</label>
                        <select wire:model.live="academic_session_id"
                                class="form-select @error('academic_session_id') is-invalid @enderror">
                            <option value="">Select session</option>
                            @foreach ($sessions as $session)
                                <option value="{{ $session->id }}">
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_session_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Term</label>
                        <select wire:model="term_id"
                                class="form-select @error('term_id') is-invalid @enderror">
                            <option value="">No specific term</option>
                            @foreach ($terms as $term)
                                <option value="{{ $term->id }}">
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('term_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('school.teacher-assignments.index') }}"
                       class="btn btn-light border">Cancel</a>

                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            <i class="bi bi-check-circle me-1"></i> Save Assignment
                        </span>
                        <span wire:loading wire:target="save">
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
