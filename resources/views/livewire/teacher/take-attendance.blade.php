<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Student Attendance</h3>
            <p class="text-muted mb-0">
                Mark daily attendance and submit your end-of-term report.
            </p>
        </div>

        <span class="badge bg-primary-subtle text-primary px-3 py-2">
            <i class="bi bi-calendar-check me-1"></i> Teacher Portal
        </span>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please check the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Class and date selection --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-funnel text-primary me-2"></i>
                Attendance Details
            </h5>

            <div class="row g-3">
                <div class="col-lg-7">
                    <label class="form-label fw-semibold">Assigned Class</label>

                    <select class="form-select"
                            wire:model.live="selectedAssignmentId">
                        <option value="">Choose class and subject</option>

                        @foreach ($assignments as $assignment)
                            <option value="{{ $assignment->id }}">
                                {{ $assignment->classRoom?->name
                                    ?? $assignment->classRoom?->class_name
                                    ?? 'Class' }}
                                @if ($assignment->section)
                                    — {{ $assignment->section->name
                                        ?? $assignment->section->section_name
                                        ?? 'Section' }}
                                @endif
                                | {{ $assignment->subject?->name
                                    ?? $assignment->subject?->subject_name
                                    ?? 'Subject' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-5">
                    <label class="form-label fw-semibold">Attendance Date</label>

                    <input type="date"
                           class="form-control"
                           wire:model.live="attendanceDate"
                           max="{{ now()->toDateString() }}">
                </div>
            </div>

            @if ($selectedAssignment)
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light">
                            <small class="text-muted d-block">Academic Session</small>
                            <strong>
                                {{ $selectedAssignment->academicSession?->name
                                    ?? $selectedAssignment->academicSession?->session_name
                                    ?? 'Not specified' }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light">
                            <small class="text-muted d-block">Term</small>
                            <strong>
                                {{ $selectedAssignment->term?->name
                                    ?? $selectedAssignment->term?->term_name
                                    ?? 'Not specified' }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light">
                            <small class="text-muted d-block">Students</small>
                            <strong>{{ $students->count() }} enrolled</strong>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Daily attendance --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="fw-bold mb-1">Daily Register</h5>
            <p class="text-muted small mb-0">
                Mark every student before saving.
            </p>
        </div>

        @if (!$selectedAssignment)
            <div class="text-center py-5">
                <i class="bi bi-mortarboard text-primary" style="font-size: 3rem"></i>
                <h5 class="fw-bold mt-3">Select a Class</h5>
                <p class="text-muted mb-0">Choose one of your assigned classes above.</p>
            </div>
        @elseif ($students->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 3rem"></i>
                <h5 class="fw-bold mt-3">No Active Students</h5>
                <p class="text-muted mb-0">
                    No active enrolments were found for this class and term.
                </p>
            </div>
        @else
            <form wire:submit="saveAttendance">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Student</th>
                                <th style="min-width: 190px">Status</th>
                                <th class="pe-4" style="min-width: 220px">Remarks</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($students as $index => $enrollment)
                                @php($student = $enrollment->student)

                                @if ($student)
                                    <tr wire:key="student-{{ $student->id }}">
                                        <td class="ps-4 text-muted">{{ $index + 1 }}</td>

                                        <td>
                                            <div class="fw-semibold">
                                                {{ $student->user?->name
                                                    ?? $student->name
                                                    ?? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))
                                                    ?: 'Student' }}
                                            </div>

                                            @if ($student->admission_number ?? false)
                                                <small class="text-muted">
                                                    {{ $student->admission_number }}
                                                </small>
                                            @endif
                                        </td>

                                        <td>
                                            <select class="form-select form-select-sm"
                                                    wire:model="attendance.{{ $student->id }}">
                                                <option value="">Choose status</option>
                                                <option value="present">Present</option>
                                                <option value="absent">Absent</option>
                                                <option value="late">Late</option>
                                                <option value="excused">Excused</option>
                                            </select>
                                        </td>

                                        <td class="pe-4">
                                            <input type="text"
                                                   class="form-control form-control-sm"
                                                   wire:model="remarks.{{ $student->id }}"
                                                   maxlength="1000"
                                                   placeholder="Optional remarks">
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-4 border-top">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Save daily attendance before submitting the term report.
                    </small>

                    <button type="submit"
                            class="btn btn-primary px-4"
                            wire:loading.attr="disabled"
                            wire:target="saveAttendance">
                        <span wire:loading.remove wire:target="saveAttendance">
                            <i class="bi bi-save me-2"></i>Save Attendance
                        </span>
                        <span wire:loading wire:target="saveAttendance">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        @endif
    </div>

    {{-- End-of-term report --}}
    @if ($selectedAssignment)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="fw-bold mb-1">
                    <i class="bi bi-file-earmark-check text-primary me-2"></i>
                    End-of-Term Attendance Report
                </h5>
                <p class="text-muted small mb-0">
                    Review the saved records for this class, then submit the report to your administrator.
                </p>
            </div>

            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-6 col-xl">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">Records</small>
                            <h3 class="fw-bold mb-0">{{ $termAttendance->count() }}</h3>
                        </div>
                    </div>

                    <div class="col-6 col-xl">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">Present</small>
                            <h3 class="fw-bold text-success mb-0">
                                {{ $termAttendance->where('status', 'present')->count() }}
                            </h3>
                        </div>
                    </div>

                    <div class="col-6 col-xl">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">Absent</small>
                            <h3 class="fw-bold text-danger mb-0">
                                {{ $termAttendance->where('status', 'absent')->count() }}
                            </h3>
                        </div>
                    </div>

                    <div class="col-6 col-xl">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">Late</small>
                            <h3 class="fw-bold text-warning mb-0">
                                {{ $termAttendance->where('status', 'late')->count() }}
                            </h3>
                        </div>
                    </div>

                    <div class="col-6 col-xl">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block">Excused</small>
                            <h3 class="fw-bold text-info mb-0">
                                {{ $termAttendance->where('status', 'excused')->count() }}
                            </h3>
                        </div>
                    </div>
                </div>

                @if ($submission)
                    <div class="alert {{ $submission->status === 'approved' ? 'alert-success' : ($submission->status === 'returned' ? 'alert-warning' : 'alert-info') }}">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <strong>Submission status:</strong>
                                {{ ucfirst($submission->status) }}
                                @if ($submission->submitted_at)
                                    <div class="small mt-1">
                                        Submitted {{ $submission->submitted_at->format('d M Y, g:i A') }}
                                    </div>
                                @endif
                            </div>

                            @if ($submission->admin_remarks)
                                <div class="small">
                                    <strong>Administrator feedback:</strong>
                                    {{ $submission->admin_remarks }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if (!$submission || $submission->status === 'returned')
                    <form wire:submit="submitTermReport">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Note to Administrator (optional)
                            </label>
                            <textarea class="form-control"
                                      rows="3"
                                      maxlength="2000"
                                      wire:model="teacherRemarks"
                                      placeholder="Add a note about this term's attendance..."></textarea>
                        </div>

                        <button type="submit"
                                class="btn btn-success px-4"
                                wire:loading.attr="disabled"
                                wire:target="submitTermReport"
                                @disabled($termAttendance->isEmpty())>
                            <span wire:loading.remove wire:target="submitTermReport">
                                <i class="bi bi-send-check me-2"></i>
                                Submit Term Report to Admin
                            </span>
                            <span wire:loading wire:target="submitTermReport">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Submitting...
                            </span>
                        </button>

                        @if ($termAttendance->isEmpty())
                            <p class="text-danger small mt-2 mb-0">
                                You must save attendance records before submitting.
                            </p>
                        @endif
                    </form>
                @elseif ($submission->status === 'pending')
                    <p class="text-muted mb-0">
                        <i class="bi bi-clock-history me-1"></i>
                        Your report is awaiting administrator review.
                    </p>
                @else
                    <p class="text-success mb-0">
                        <i class="bi bi-check-circle me-1"></i>
                        Your report has been approved.
                    </p>
                @endif
            </div>
        </div>
    @endif

</div>