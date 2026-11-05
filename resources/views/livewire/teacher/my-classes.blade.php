
<div class="container-fluid py-4">

    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">My Classes & Students</h3>
            <p class="text-muted mb-0">
                View your assigned classes, subjects, sections and students.
            </p>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
                        <i class="bi bi-easel2 fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Assigned Classes</div>
                        <h3 class="fw-bold mb-0">{{ $totalClasses }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Unique Students</div>
                        <h3 class="fw-bold mb-0">{{ $totalStudents }}</h3>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Class assignments --}}
    @forelse ($classAssignments as $assignment)

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1">
                            {{ $assignment->classRoom?->name
                                ?? $assignment->classRoom?->class_name
                                ?? 'Class not found' }}
                        </h5>

                        <div class="text-muted small">
                            Subject:
                            <span class="fw-semibold text-dark">
                                {{ $assignment->subject?->name
                                    ?? $assignment->subject?->subject_name
                                    ?? 'Not specified' }}
                            </span>
                        </div>
                    </div>

                    <span class="badge text-bg-primary">
                        {{ $assignment->enrollments->count() }} students
                    </span>
                </div>
            </div>

            <div class="card-body">

                {{-- Class details --}}
                <div class="row g-3 mb-4">

                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted small mb-1">
                                Section
                            </div>

                            <div class="fw-semibold">
                                {{ $assignment->section?->name
                                    ?? $assignment->section?->section_name
                                    ?? 'No section assigned' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted small mb-1">
                                Academic Session
                            </div>

                            <div class="fw-semibold">
                                {{ $assignment->academicSession?->name
                                    ?? $assignment->academicSession?->session_name
                                    ?? 'Not specified' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted small mb-1">
                                Term
                            </div>

                            <div class="fw-semibold">
                                {{ $assignment->term?->name
                                    ?? $assignment->term?->term_name
                                    ?? 'Not specified' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted small mb-1">
                                Students in this assignment
                            </div>

                            <div class="fw-semibold">
                                {{ $assignment->enrollments->count() }}
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Students table --}}
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-people me-2"></i>
                    Students taking this subject
                </h6>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Admission Number</th>
                                <th>Section</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($assignment->enrollments as $enrollment)

                                @php
                                    $student = $enrollment->student;
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ trim(
                                                ($student->first_name ?? '') . ' ' .
                                                ($student->last_name ?? '')
                                            ) ?: 'Name unavailable' }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $student->admission_number
                                            ?? $student->admission_no
                                            ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $assignment->section?->name
                                            ?? $assignment->section?->section_name
                                            ?? 'No section assigned' }}
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-people fs-3 d-block mb-2"></i>
                                        No students found for this class and section.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    @empty

        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-easel fs-1 text-muted"></i>
                <h5 class="fw-bold mt-3">No classes assigned yet</h5>
                <p class="text-muted mb-0">
                    Contact your school administrator to assign your classes and subjects.
                </p>
            </div>
        </div>

    @endforelse

</div>
