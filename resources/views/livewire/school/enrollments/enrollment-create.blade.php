
<div class="container-fluid py-4">

    {{-- Page header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Create Enrollment</h3>
            <p class="text-secondary mb-0">
                Enroll a student into a class for an academic session.
            </p>
        </div>

        <a href="{{ route('school.enrollments.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Enrollments
        </a>
    </div>

    {{-- Success message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    <form wire:submit="save">
        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-header bg-white border-bottom py-3 px-4">
                <h5 class="mb-1 fw-bold">Enrollment Information</h5>
                <small class="text-secondary">
                    Complete the required fields below.
                </small>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">

                    {{-- Student details --}}
                    <div class="col-12">
                        <h6 class="fw-bold text-primary">
                            <i class="bi bi-person-fill me-2"></i>
                            Student Details
                        </h6>
                        <hr>
                    </div>

                    {{-- Searchable student --}}
                    <div class="col-md-6">
                        <label for="studentSearch" class="form-label fw-semibold">
                            Student <span class="text-danger">*</span>
                        </label>

                        <div class="position-relative">
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search"></i>
                                </span>

                                <input
                                    type="text"
                                    id="studentSearch"
                                    class="form-control @error('student_id') is-invalid @enderror"
                                    placeholder="Search name or admission number..."
                                    wire:model.live.debounce.300ms="studentSearch"
                                    wire:focus="openStudentResults"
                                    autocomplete="off"
                                    aria-label="Search students"
                                >

                                @if ($student_id !== '')
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        wire:click="clearStudent"
                                    >
                                        <i class="bi bi-arrow-repeat me-1"></i>
                                        Change
                                    </button>
                                @endif
                            </div>

                            {{-- Dropdown results --}}
                            @if ($showStudentResults && trim($studentSearch) !== '' && $student_id === '')
                                <div
                                    class="list-group position-absolute w-100 shadow mt-1"
                                    style="z-index: 1050; max-height: 280px; overflow-y: auto;"
                                >
                                    @forelse ($students as $student)
                                        <button
                                            type="button"
                                            wire:key="student-result-{{ $student->id }}"
                                            wire:click="selectStudent({{ $student->id }})"
                                            class="list-group-item list-group-item-action text-start py-3"
                                        >
                                            <div class="d-flex align-items-center gap-3">
                                                <div
                                                    class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                                    style="width: 42px; height: 42px;"
                                                >
                                                    <i class="bi bi-person-fill fs-5"></i>
                                                </div>

                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold">
                                                        {{ $student->first_name }}
                                                        {{ $student->last_name }}
                                                    </div>

                                                    <small class="text-muted">
                                                        Admission No:
                                                        {{ $student->admission_number }}
                                                    </small>
                                                </div>

                                                <i class="bi bi-arrow-up-left text-primary"></i>
                                            </div>
                                        </button>
                                    @empty
                                        <div class="list-group-item text-center py-4">
                                            <i class="bi bi-person-x fs-3 text-muted"></i>

                                            <div class="fw-semibold mt-2">
                                                No student found
                                            </div>

                                            <small class="text-muted">
                                                Try a different name or admission number.
                                            </small>
                                        </div>
                                    @endforelse
                                </div>
                            @endif
                        </div>

                        @error('student_id')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- Selected student --}}
                        @if ($selectedStudent)
                            <div class="alert alert-success mt-3 mb-0">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 44px; height: 44px;"
                                    >
                                        <i class="bi bi-check-lg fs-5"></i>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="fw-bold">
                                            {{ $selectedStudent->first_name }}
                                            {{ $selectedStudent->last_name }}
                                        </div>

                                        <small class="d-block">
                                            Admission No:
                                            {{ $selectedStudent->admission_number }}
                                        </small>

                                        <small class="text-success">
                                            Student selected successfully
                                        </small>
                                    </div>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-success"
                                        wire:click="clearStudent"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Search and select the student to enroll.
                            </div>
                        @endif
                    </div>

                    {{-- Academic placement --}}
                    <div class="col-12 mt-4">
                        <h6 class="fw-bold text-primary">
                            <i class="bi bi-mortarboard-fill me-2"></i>
                            Academic Placement
                        </h6>
                        <hr>
                    </div>

                    {{-- Academic session --}}
                    <div class="col-md-6">
                        <label for="academic_session_id" class="form-label fw-semibold">
                            Academic Session <span class="text-danger">*</span>
                        </label>

                        <select
                            id="academic_session_id"
                            wire:model.live="academic_session_id"
                            class="form-select @error('academic_session_id') is-invalid @enderror"
                        >
                            <option value="">Select academic session</option>

                            @foreach ($academicSessions as $session)
                                <option value="{{ $session->id }}">
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('academic_session_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Term --}}
                    <div class="col-md-6">
                        <label for="term_id" class="form-label fw-semibold">
                            Term
                        </label>

                        <select
                            id="term_id"
                            wire:model="term_id"
                            class="form-select @error('term_id') is-invalid @enderror"
                            @disabled(!$academic_session_id)
                        >
                            <option value="">Select term (optional)</option>

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

                    {{-- Class --}}
                    <div class="col-md-6">
                        <label for="class_room_id" class="form-label fw-semibold">
                            Class <span class="text-danger">*</span>
                        </label>

                        <select
                            id="class_room_id"
                            wire:model.live="class_room_id"
                            class="form-select @error('class_room_id') is-invalid @enderror"
                        >
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

                    {{-- Section --}}
                    <div class="col-md-6">
                        <label for="section_id" class="form-label fw-semibold">
                            Section
                        </label>

                        <select
                            id="section_id"
                            wire:model="section_id"
                            class="form-select @error('section_id') is-invalid @enderror"
                            @disabled(!$class_room_id)
                        >
                            <option value="">Select section (optional)</option>

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

                    {{-- Enrollment status --}}
                    <div class="col-12 mt-4">
                        <h6 class="fw-bold text-primary">
                            <i class="bi bi-calendar-check me-2"></i>
                            Enrollment Status
                        </h6>
                        <hr>
                    </div>

                    <div class="col-md-4">
                        <label for="admission_status" class="form-label fw-semibold">
                            Status <span class="text-danger">*</span>
                        </label>

                        <select
                            id="admission_status"
                            wire:model="admission_status"
                            class="form-select @error('admission_status') is-invalid @enderror"
                        >
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="withdrawn">Withdrawn</option>
                            <option value="transferred">Transferred</option>
                        </select>

                        @error('admission_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Start date --}}
                    <div class="col-md-4">
                        <label for="start_date" class="form-label fw-semibold">
                            Start Date <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            id="start_date"
                            wire:model="start_date"
                            class="form-control @error('start_date') is-invalid @enderror"
                        >

                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- End date --}}
                    <div class="col-md-4">
                        <label for="end_date" class="form-label fw-semibold">
                            End Date
                        </label>

                        <input
                            type="date"
                            id="end_date"
                            wire:model="end_date"
                            class="form-control @error('end_date') is-invalid @enderror"
                        >

                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Form actions --}}
            <div class="card-footer bg-white border-top p-4 d-flex flex-wrap justify-content-end gap-2">
                <a
                    href="{{ route('school.enrollments.index') }}"
                    class="btn btn-light border px-4"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary px-4"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    <span wire:loading.remove wire:target="save">
                        <i class="bi bi-check-circle me-1"></i>
                        Save Enrollment
                    </span>

                    <span wire:loading wire:target="save">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Saving...
                    </span>
                </button>
            </div>

        </div>
    </form>
</div>
