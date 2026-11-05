

<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-clipboard2-check me-2 text-primary"></i>
                Results Approval
            </h3>

            <p class="text-muted mb-0">
                Review examination scores submitted by teachers.
            </p>
        </div>

        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-shield-check me-1"></i>
            School Administration
        </span>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button" class="btn-close"
                data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->has('review'))
        <div class="alert alert-danger shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ $errors->first('review') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                <div class="col-12 col-md-7">
                    <label for="resultsSearch" class="form-label fw-semibold">
                        Search Examinations
                    </label>

                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            id="resultsSearch"
                            type="search"
                            class="form-control"
                            placeholder="Search exam, class, subject, session or term..."
                            wire:model.live.debounce.350ms="search"
                        >
                    </div>
                </div>

                <div class="col-12 col-md-5">
                    <label for="statusFilter" class="form-label fw-semibold">
                        Results Status
                    </label>

                    <select
                        id="statusFilter"
                        class="form-select"
                        wire:model.live="statusFilter"
                    >
                        <option value="submitted">Awaiting Approval</option>
                        <option value="approved">Approved Results</option>
                        <option value="returned">Returned for Correction</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

    {{-- Examinations Table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center gap-2">
                <h5 class="fw-bold mb-0">
                    @switch($statusFilter)
                        @case('approved')
                            Approved Results
                            @break
                        @case('returned')
                            Returned Results
                            @break
                        @default
                            Examinations Awaiting Approval
                    @endswitch
                </h5>

                <span class="badge bg-primary rounded-pill">
                    {{ $exams->total() }} {{ Str::plural('examination', $exams->total()) }}
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Examination</th>
                        <th>Class / Subject</th>
                        <th>Session / Term</th>
                        <th>Students</th>
                        <th>Submitted By</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($exams as $exam)
                        <tr wire:key="approval-exam-{{ $exam->id }}">

                            {{-- Examination --}}
                            <td class="ps-3">
                                <div class="fw-semibold">
                                    {{ $exam->name }}
                                </div>

                                <div class="text-muted small">
                                    Maximum: {{ number_format((float) $exam->total_marks, 2) }}
                                    marks
                                </div>
                            </td>

                            {{-- Class and Subject --}}
                            <td>
                                <div class="fw-semibold">
                                    {{ $exam->classRoom?->name ?? '—' }}
                                </div>

                                <div class="text-muted small">
                                    {{ $exam->subject?->name ?? '—' }}
                                </div>
                            </td>

                            {{-- Session and Term --}}
                            <td>
                                <div>
                                    {{ $exam->academicSession?->name ?? '—' }}
                                </div>

                                <div class="text-muted small">
                                    {{ $exam->term?->name ?? 'No term assigned' }}
                                </div>
                            </td>

                            {{-- Score Count --}}
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-people me-1"></i>
                                    {{ $exam->scores_count }}
                                </span>
                            </td>

                            {{-- Submitted By --}}
                            <td>
                                <div>
                                    {{ $exam->resultsSubmitter?->name ?? 'Unknown user' }}
                                </div>

                                <div class="text-muted small">
                                    @if ($exam->results_submitted_at)
                                        {{ $exam->results_submitted_at->format('d M Y, h:i A') }}
                                    @else
                                        Not available
                                    @endif
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if ($exam->results_status === 'submitted')
                                    <span class="badge bg-info text-dark">
                                        Awaiting Approval
                                    </span>
                                @elseif ($exam->results_status === 'approved')
                                    <span class="badge bg-success">
                                        Approved
                                    </span>
                                @elseif ($exam->results_status === 'returned')
                                    <span class="badge bg-warning text-dark">
                                        Returned
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($exam->results_status ?? 'Unknown') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-end pe-3">
                                <div class="d-flex justify-content-end flex-wrap gap-2">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        wire:click="viewScores({{ $exam->id }})"
                                    >
                                        <i class="bi bi-eye me-1"></i>
                                        Review
                                    </button>

                                    @if ($exam->results_status === 'submitted')

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-success"
                                            wire:click="approveResults({{ $exam->id }})"
                                            wire:confirm="Approve the results for {{ addslashes($exam->name) }}? The teacher will no longer be able to edit these results."
                                            wire:loading.attr="disabled"
                                            wire:target="approveResults({{ $exam->id }})"
                                        >
                                            <i class="bi bi-check-circle me-1"></i>
                                            Approve
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-warning"
                                            wire:click="openReturnForm({{ $exam->id }})"
                                        >
                                            <i class="bi bi-arrow-return-left me-1"></i>
                                            Return
                                        </button>

                                    @endif

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-clipboard2-x text-muted"
                                    style="font-size: 2.5rem;"></i>

                                <h6 class="fw-bold mt-3 mb-1">
                                    No examinations found
                                </h6>

                                <p class="text-muted mb-0">
                                    @if ($statusFilter === 'submitted')
                                        No examinations are currently awaiting approval.
                                    @elseif ($statusFilter === 'approved')
                                        No approved examination results match your search.
                                    @else
                                        No returned results match your search.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        @if ($exams->hasPages())
            <div class="card-footer bg-white">
                {{ $exams->links() }}
            </div>
        @endif

    </div>

    {{-- Review Scores Modal --}}
    @if ($viewingExam)

        <div class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background: rgba(0, 0, 0, 0.45);">

            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">

                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold">
                                {{ $viewingExam->name }}
                            </h5>

                            <div class="text-muted small mt-1">
                                {{ $viewingExam->classRoom?->name ?? 'Class' }}
                                ·
                                {{ $viewingExam->subject?->name ?? 'Subject' }}
                                ·
                                {{ $viewingExam->academicSession?->name ?? 'Session' }}
                            </div>
                        </div>

                        <button
                            type="button"
                            class="btn-close"
                            wire:click="closeScores"
                            aria-label="Close"
                        ></button>
                    </div>

                    <div class="modal-body">

                        @if ($viewingExam->results_status === 'returned'
                            && filled($viewingExam->results_review_note))
                            <div class="alert alert-warning">
                                <div class="fw-bold mb-1">
                                    Previous correction note
                                </div>

                                {{ $viewingExam->results_review_note }}
                            </div>
                        @endif

                        <div class="d-flex flex-wrap gap-3 mb-3">
                            <span class="badge bg-light text-dark border">
                                Students: {{ $viewingExam->scores->count() }}
                            </span>

                            <span class="badge bg-light text-dark border">
                                Maximum score:
                                {{ number_format((float) $viewingExam->total_marks, 2) }}
                            </span>

                            <span class="badge
                                {{ $viewingExam->results_status === 'approved'
                                    ? 'bg-success'
                                    : ($viewingExam->results_status === 'submitted'
                                        ? 'bg-info text-dark'
                                        : 'bg-warning text-dark') }}">
                                {{ ucfirst(str_replace('_', ' ', $viewingExam->results_status)) }}
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Student</th>
                                        <th>Admission No.</th>
                                        <th class="text-end">CA</th>
                                        <th class="text-end">Exam</th>
                                        <th class="text-end">Total</th>
                                        <th>Grade</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($viewingExam->scores as $score)
                                        <tr wire:key="review-score-{{ $score->id }}">
                                            <td>{{ $loop->iteration }}</td>

                                            <td>
                                                {{ trim(
                                                    ($score->student?->first_name ?? '') . ' ' .
                                                    ($score->student?->middle_name ?? '') . ' ' .
                                                    ($score->student?->last_name ?? '')
                                                ) ?: 'Student record unavailable' }}
                                            </td>

                                            <td>
                                                {{ $score->student?->admission_number ?? '—' }}
                                            </td>

                                            <td class="text-end">
                                                {{ number_format((float) $score->ca_score, 2) }}
                                            </td>

                                            <td class="text-end">
                                                {{ number_format((float) $score->exam_score, 2) }}
                                            </td>

                                            <td class="text-end fw-bold">
                                                {{ number_format((float) $score->total_score, 2) }}
                                            </td>

                                            <td>
                                                {{ $score->grade ?? '—' }}
                                            </td>

                                            <td>
                                                {{ $score->remark ?? '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                No saved score records were found for this examination.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($viewingExam->results_status === 'submitted')
                            <div class="alert alert-info mb-0 mt-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Review the scores carefully before approving or returning these results.
                            </div>
                        @endif

                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            wire:click="closeScores"
                        >
                            Close
                        </button>

                        @if ($viewingExam->results_status === 'submitted')
                            <button
                                type="button"
                                class="btn btn-success"
                                wire:click="approveResults({{ $viewingExam->id }})"
                                wire:confirm="Approve these examination results?"
                            >
                                <i class="bi bi-check-circle me-1"></i>
                                Approve Results
                            </button>

                            <button
                                type="button"
                                class="btn btn-warning"
                                wire:click="openReturnForm({{ $viewingExam->id }})"
                            >
                                <i class="bi bi-arrow-return-left me-1"></i>
                                Return for Correction
                            </button>
                        @endif
                    </div>

                </div>
            </div>
        </div>

    @endif

    {{-- Return for Correction Modal --}}
    @if ($returningExam)

        <div class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background: rgba(0, 0, 0, 0.45);">

            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">

                    <form wire:submit="returnResults">

                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-arrow-return-left me-2 text-warning"></i>
                                Return Results for Correction
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                wire:click="closeReturnForm"
                                aria-label="Close"
                            ></button>
                        </div>

                        <div class="modal-body">

                            <p class="text-muted">
                                You are returning
                                <strong>{{ $returningExam->name }}</strong>
                                for
                                <strong>{{ $returningExam->classRoom?->name ?? 'the selected class' }}</strong>.
                            </p>

                            <label for="reviewNote" class="form-label fw-semibold">
                                Correction Note <span class="text-danger">*</span>
                            </label>

                            <textarea
                                id="reviewNote"
                                class="form-control @error('reviewNote') is-invalid @enderror"
                                rows="5"
                                wire:model="reviewNote"
                                maxlength="2000"
                                placeholder="Explain what the teacher must correct. For example: Check the missing scores for students who have not been graded."
                                required
                            ></textarea>

                            @error('reviewNote')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                The teacher will see this note and can correct and resubmit the results.
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                wire:click="closeReturnForm"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="btn btn-warning"
                                wire:loading.attr="disabled"
                                wire:target="returnResults"
                            >
                                <span wire:loading.remove wire:target="returnResults">
                                    <i class="bi bi-send me-1"></i>
                                    Return to Teacher
                                </span>

                                <span wire:loading wire:target="returnResults">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Returning...
                                </span>
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    @endif

</div>
