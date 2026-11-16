
<div class="container-fluid py-4"
     x-data
     x-on:assignment-duplicate.window="window.alert($event.detail.message)">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Create Teacher Assignment</h3>
            <p class="text-muted mb-0">
                Assign a teacher to a subject, class and academic period.
            </p>
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

                    {{-- SEARCHABLE TEACHER --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Select Teacher <span class="text-danger">*</span>
                        </label>

                        @if($selectedTeacher)

                            <div class="border rounded-3 p-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center">

                                    <div>
                                        <div class="fw-bold text-dark">
                                            {{ $selectedTeacher->first_name }}
                                            {{ $selectedTeacher->middle_name }}
                                            {{ $selectedTeacher->last_name }}
                                        </div>

                                        <small class="text-muted">
                                            Staff No:
                                            {{ $selectedTeacher->staff_number ?: 'N/A' }}
                                        </small>
                                    </div>

                                    <button type="button"
                                            wire:click="clearTeacher"
                                            class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-circle"></i> Change
                                    </button>

                                </div>
                            </div>

                        @else

                            <div class="position-relative">

                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-search"></i>
                                    </span>

                                    <input type="text"
                                           wire:model.live.debounce.300ms="teacherSearch"
                                           class="form-control @error('teacher_id') is-invalid @enderror"
                                           placeholder="Search teacher name or staff number..."
                                           autocomplete="off">
                                </div>

                                @if(strlen(trim($teacherSearch)) > 0)

                                    <div class="border rounded-3 shadow-sm mt-2 bg-white"
                                         style="max-height: 260px; overflow-y: auto;">

                                        @forelse($teachers as $teacher)

                                            <button type="button"
                                                    wire:key="teacher-option-{{ $teacher->id }}"
                                                    wire:click="selectTeacher({{ $teacher->id }})"
                                                    class="w-100 text-start border-0 border-bottom bg-white p-3">

                                                <div class="d-flex align-items-center gap-2">

                                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                                                         style="width: 40px; height: 40px; flex-shrink: 0;">
                                                        <i class="bi bi-person-fill"></i>
                                                    </div>

                                                    <div>
                                                        <div class="fw-semibold text-dark">
                                                            {{ $teacher->first_name }}
                                                            {{ $teacher->middle_name }}
                                                            {{ $teacher->last_name }}
                                                        </div>

                                                        <small class="text-muted">
                                                            Staff No:
                                                            {{ $teacher->staff_number ?: 'N/A' }}
                                                        </small>
                                                    </div>

                                                </div>

                                            </button>

                                        @empty

                                            <div class="p-3 text-center text-muted">
                                                <i class="bi bi-person-x fs-4 d-block mb-2"></i>
                                                No teacher found.
                                            </div>

                                        @endforelse

                                    </div>

                                @else

                                    <small class="text-muted mt-2 d-block">
                                        Type a teacher's name or staff number to search.
                                    </small>

                                @endif

                            </div>

                        @endif

                        @error('teacher_id')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- SUBJECT --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Subject <span class="text-danger">*</span>
                        </label>

                        <select wire:model="subject_id"
                                class="form-select @error('subject_id') is-invalid @enderror">

                            <option value="">Select subject</option>

                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                            @endforeach

                        </select>

                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- CLASS --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Class <span class="text-danger">*</span>
                        </label>

                        <select wire:model.live="class_room_id"
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

                    {{-- SECTION --}}
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

                        @error('section_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ACADEMIC SESSION --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Academic Session <span class="text-danger">*</span>
                        </label>

                        <select wire:model.live="academic_session_id"
                                class="form-select @error('academic_session_id') is-invalid @enderror">

                            <option value="">Select session</option>

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

                    {{-- TERM --}}
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

                        @error('term_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- ACTION BUTTONS --}}
                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a href="{{ route('school.teacher-assignments.index') }}"
                       class="btn btn-light border">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-primary"
                            wire:loading.attr="disabled">

                        <span wire:loading.remove wire:target="save">
                            <i class="bi bi-check-circle me-1"></i>
                            Save Assignment
                        </span>

                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Saving...
                        </span>

                    </button>

                </div>

            </form>
        </div>
    </div>
</div>
