<div class="container-fluid py-4">

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Attendance Submissions</h3>
        <p class="text-muted mb-0">
            Review end-of-term attendance reports submitted by teachers.
        </p>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Term Reports</h5>
                    <p class="text-muted small mb-0">
                        Approve completed reports or return them for correction.
                    </p>
                </div>

                <select class="form-select w-auto"
                        wire:model.live="statusFilter">
                    <option value="pending">Pending review</option>
                    <option value="approved">Approved</option>
                    <option value="returned">Returned</option>
                    <option value="all">All submissions</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Teacher</th>
                        <th>Class / Section</th>
                        <th>Session / Term</th>
                        <th>Records</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th class="pe-4">Review</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($submissions as $submission)
                        <tr wire:key="submission-{{ $submission->id }}">
                            <td class="ps-4">
                                <div class="fw-semibold">
                                    {{ $submission->teacher?->user?->name
                                        ?? $submission->submitter?->name
                                        ?? 'Teacher' }}
                                </div>
                                <small class="text-muted">
                                    {{ $submission->submitter?->email }}
                                </small>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $submission->classRoom?->name
                                        ?? $submission->classRoom?->class_name
                                        ?? 'Class' }}
                                </div>
                                <small class="text-muted">
                                    {{ $submission->section?->name
                                        ?? $submission->section?->section_name
                                        ?? 'No section' }}
                                </small>
                            </td>

                            <td>
                                <div>
                                    {{ $submission->academicSession?->name
                                        ?? $submission->academicSession?->session_name
                                        ?? 'Session' }}
                                </div>
                                <small class="text-muted">
                                    {{ $submission->term?->name
                                        ?? $submission->term?->term_name
                                        ?? 'Term' }}
                                </small>
                            </td>

                            <td>
                                <div class="small">
                                    Total: <strong>{{ $submission->total_records }}</strong>
                                </div>
                                <div class="small text-success">
                                    Present: {{ $submission->present_count }}
                                </div>
                                <div class="small text-danger">
                                    Absent: {{ $submission->absent_count }}
                                </div>
                                <div class="small text-warning">
                                    Late: {{ $submission->late_count }}
                                </div>
                                <div class="small text-info">
                                    Excused: {{ $submission->excused_count }}
                                </div>
                            </td>

                            <td>
                                {{ $submission->submitted_at?->format('d M Y') ?? '—' }}
                                <div class="text-muted small">
                                    {{ $submission->submitted_at?->format('g:i A') }}
                                </div>
                            </td>

                            <td>
                                @if ($submission->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif ($submission->status === 'returned')
                                    <span class="badge bg-warning text-dark">Returned</span>
                                @else
                                    <span class="badge bg-primary">Pending</span>
                                @endif
                            </td>

                            <td class="pe-4">
                                @if ($submission->status === 'pending')
                                    <button class="btn btn-sm btn-outline-primary"
                                            wire:click="$set('reviewingSubmissionId', {{ $submission->id }})">
                                        Review
                                    </button>
                                @else
                                    <span class="text-muted small">
                                        {{ $submission->reviewer?->name ?? 'Reviewed' }}
                                    </span>
                                    @if ($submission->admin_remarks)
                                        <div class="small text-muted mt-1">
                                            {{ $submission->admin_remarks }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>

                        @if ((int) $reviewingSubmissionId === $submission->id
                            && $submission->status === 'pending')
                            <tr wire:key="review-{{ $submission->id }}">
                                <td colspan="7" class="p-4 bg-light">
                                    <h6 class="fw-bold mb-2">
                                        Review report for
                                        {{ $submission->classRoom?->name
                                            ?? $submission->classRoom?->class_name
                                            ?? 'Class' }}
                                    </h6>

                                    @if ($submission->teacher_remarks)
                                        <p class="small mb-3">
                                            <strong>Teacher's note:</strong>
                                            {{ $submission->teacher_remarks }}
                                        </p>
                                    @endif

                                    <label class="form-label fw-semibold">
                                        Feedback / Remarks
                                    </label>

                                    <textarea class="form-control mb-3"
                                              rows="3"
                                              wire:model="adminRemarks"
                                              maxlength="2000"
                                              placeholder="Add feedback for the teacher..."></textarea>

                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-success"
                                                wire:click="approve({{ $submission->id }})"
                                                wire:loading.attr="disabled">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Approve Report
                                        </button>

                                        <button class="btn btn-warning"
                                                wire:click="returnForCorrection({{ $submission->id }})"
                                                wire:loading.attr="disabled">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                                            Return for Correction
                                        </button>

                                        <button class="btn btn-outline-secondary"
                                                wire:click="$set('reviewingSubmissionId', null)">
                                            Cancel
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <h6 class="fw-bold mt-3">No submissions found</h6>
                                <p class="text-muted mb-0">
                                    Reports matching this status will appear here.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $submissions->links() }}
        </div>
    </div>
</div>