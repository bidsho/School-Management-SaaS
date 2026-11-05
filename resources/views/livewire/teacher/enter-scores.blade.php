

<div class="container-fluid py-4">

    @php
        $resultsStatus = $selectedExam?->results_status ?? 'not_submitted';

        $resultsLocked = in_array(
            $resultsStatus,
            ['submitted', 'approved'],
            true
        );

        $resultsBadge = match ($resultsStatus) {
            'submitted' => 'bg-info text-dark',
            'approved' => 'bg-success',
            'returned' => 'bg-warning text-dark',
            default => 'bg-secondary',
        };

        $resultsLabel = match ($resultsStatus) {
            'submitted' => 'Awaiting Approval',
            'approved' => 'Approved',
            'returned' => 'Returned for Correction',
            default => 'Not Submitted',
        };
    @endphp

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-journal-check text-primary me-2"></i>
                Enter Student Results
            </h3>

            <p class="text-muted mb-0">
                Enter scores, grades and remarks for students in your assigned classes.
            </p>
        </div>

        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-person-workspace me-1"></i>
            Teacher Portal
        </span>
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button" class="btn-close"
                data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- General Errors --}}
    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <div class="fw-bold mb-2">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Please correct the following:
            </div>

            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Examination Selection --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-funnel text-primary me-2"></i>
                Select Examination
            </h5>
        </div>

        <div class="card-body">
            <div class="row g-3">

                {{-- Academic Session --}}
                <div class="col-12 col-md-6 col-xl-3">
                    <label for="academicSessionId" class="form-label fw-semibold">
                        Academic Session
                    </label>

                    <select
                        id="academicSessionId"
                        class="form-select"
                        wire:model.live="academicSessionId"
                    >
                        <option value="">Select session</option>

                        @foreach ($sessions as $session)
                            <option value="{{ $session->id }}">
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Term --}}
                <div class="col-12 col-md-6 col-xl-3">
                    <label for="termId" class="form-label fw-semibold">
                        Term
                    </label>

                    <select
                        id="termId"
                        class="form-select"
                        wire:model.live="termId"
                        @disabled(!$academicSessionId)
                    >
                        <option value="">Select term</option>

                        @foreach ($terms as $term)
                            <option value="{{ $term->id }}">
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Class and Subject --}}
                <div class="col-12 col-md-6 col-xl-3">
                    <label for="assignmentId" class="form-label fw-semibold">
                        Class / Subject
                    </label>

                    <select
                        id="assignmentId"
                        class="form-select"
                        wire:model.live="assignmentId"
                        @disabled(!$academicSessionId || !$termId)
                    >
                        <option value="">Select assignment</option>

                        @foreach ($assignments as $assignment)
                            <option value="{{ $assignment->id }}">
                                {{ $assignment->classRoom?->name ?? 'Class' }}
                                —
                                {{ $assignment->subject?->name ?? 'Subject' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Examination --}}
                <div class="col-12 col-md-6 col-xl-3">
                    <label for="examId" class="form-label fw-semibold">
                        Examination
                    </label>

                    <select
                        id="examId"
                        class="form-select"
                        wire:model.live="examId"
                        @disabled(!$assignmentId)
                    >
                        <option value="">Select examination</option>

                        @foreach ($exams as $exam)
                            <option value="{{ $exam->id }}">
                                {{ $exam->name }}
                                ({{ $exam->total_marks }} marks)
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>

    {{-- Selected Examination Details --}}
    @if ($examId && $selectedExam)

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center g-3">

                    <div class="col">
                        <h5 class="fw-bold mb-2">
                            {{ $selectedExam->name }}
                        </h5>

                        <div class="d-flex flex-wrap gap-3 text-muted small">
                            <span>
                                <i class="bi bi-building me-1"></i>
                                {{ $selectedExam->classRoom?->name ?? 'Class' }}
                            </span>

                            <span>
                                <i class="bi bi-book me-1"></i>
                                {{ $selectedExam->subject?->name ?? 'Subject' }}
                            </span>

                            <span>
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $selectedExam->academicSession?->name ?? 'Session' }}
                            </span>

                            <span>
                                <i class="bi bi-award me-1"></i>
                                Maximum: {{ $selectedExam->total_marks }} marks
                            </span>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="text-muted small mb-1">Results Status</div>

                        <span class="badge {{ $resultsBadge }} px-3 py-2">
                            {{ $resultsLabel }}
                        </span>
                    </div>

                </div>
            </div>
        </div>

        {{-- Results Status Information --}}
        @if ($resultsStatus === 'submitted')
            <div class="alert alert-info shadow-sm">
                <i class="bi bi-hourglass-split me-2"></i>
                <strong>Results submitted.</strong>
                The results are awaiting School Admin or Principal approval.
                Editing is disabled until the results are returned.
            </div>

        @elseif ($resultsStatus === 'approved')
            <div class="alert alert-success shadow-sm">
                <i class="bi bi-patch-check-fill me-2"></i>
                <strong>Results approved.</strong>
                Scores, grades and remarks are locked.
            </div>

        @elseif ($resultsStatus === 'returned')
            <div class="alert alert-warning shadow-sm">
                <i class="bi bi-arrow-counterclockwise me-2"></i>
                <strong>Results returned for correction.</strong>
                Correct the required fields, save your changes, and submit again.

                @if (filled($selectedExam->results_review_note))
                    <hr>

                    <div class="fw-bold mb-1">Administrator's Correction Note</div>
                    <div>{{ $selectedExam->results_review_note }}</div>
                @endif
            </div>

        @else
            <div class="alert alert-light border shadow-sm">
                <i class="bi bi-info-circle text-primary me-2"></i>
                Enter a score, grade and remark for every student. Save your work
                before submitting the results for approval.
            </div>
        @endif

    @endif

    {{-- Student Score Sheet --}}
    @if ($examId && count($scores))

        <form wire:submit="saveScores">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h5 class="fw-bold mb-1">
                                <i class="bi bi-people text-primary me-2"></i>
                                Student Results Sheet
                            </h5>

                            <p class="text-muted small mb-0">
                                Enter scores, then assign a grade and remark to each student.
                            </p>
                        </div>

                        <span class="badge bg-primary rounded-pill px-3 py-2">
                            {{ count($scores) }} Students
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th style="min-width: 200px;">Student</th>
                                <th style="min-width: 145px;">Admission No.</th>
                                <th style="min-width: 120px;">CA Score</th>
                                <th style="min-width: 135px;">Exam Score</th>
                                <th style="min-width: 125px;">Total</th>
                                <th style="min-width: 115px;">Grade</th>
                                <th style="min-width: 220px;">Remark</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($scores as $studentId => $score)
                                <tr wire:key="student-result-{{ $studentId }}">

                                    <td class="ps-3 text-muted">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- Student Name --}}
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $score['name'] ?? 'Student' }}
                                        </div>
                                    </td>

                                    {{-- Admission Number --}}
                                    <td>
                                        {{ $score['admission_number'] ?? '—' }}
                                    </td>

                                    {{-- CA Score --}}
                                    <td>
                                        <input
                                            type="number"
                                            class="form-control @error("scores.$studentId.ca_score") is-invalid @enderror"
                                            wire:model="scores.{{ $studentId }}.ca_score"
                                            min="0"
                                            max="{{ $selectedExam->total_marks }}"
                                            step="0.01"
                                            placeholder="0.00"
                                            @disabled($resultsLocked)
                                        >

                                        @error("scores.$studentId.ca_score")
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </td>

                                    {{-- Examination Score --}}
                                    <td>
                                        <input
                                            type="number"
                                            class="form-control @error("scores.$studentId.exam_score") is-invalid @enderror"
                                            wire:model="scores.{{ $studentId }}.exam_score"
                                            min="0"
                                            max="{{ $selectedExam->total_marks }}"
                                            step="0.01"
                                            placeholder="0.00"
                                            @disabled($resultsLocked)
                                        >

                                        @error("scores.$studentId.exam_score")
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </td>

                                    {{-- Total --}}
                                    <td>
                                        <span class="fw-bold">
                                            {{
                                                number_format(
                                                    (float) ($score['ca_score'] ?? 0)
                                                    + (float) ($score['exam_score'] ?? 0),
                                                    2
                                                )
                                            }}
                                        </span>

                                        <span class="text-muted small">
                                            / {{ $selectedExam->total_marks }}
                                        </span>
                                    </td>

                                    {{-- Grade --}}
                                    <td>
                                        <input
                                            type="text"
                                            class="form-control @error("scores.$studentId.grade") is-invalid @enderror"
                                            wire:model="scores.{{ $studentId }}.grade"
                                            maxlength="10"
                                            placeholder="e.g. A"
                                            @disabled($resultsLocked)
                                        >

                                        @error("scores.$studentId.grade")
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </td>

                                    {{-- Remark --}}
                                    <td>
                                        <input
                                            type="text"
                                            class="form-control @error("scores.$studentId.remark") is-invalid @enderror"
                                            wire:model="scores.{{ $studentId }}.remark"
                                            maxlength="255"
                                            placeholder="e.g. Excellent"
                                            @disabled($resultsLocked)
                                        >

                                        @error("scores.$studentId.remark")
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                {{-- Actions --}}
                <div class="card-footer bg-white p-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                        <div class="text-muted small">
                            @if ($resultsStatus === 'approved')
                                <i class="bi bi-lock-fill me-1"></i>
                                Approved results cannot be edited.
                            @elseif ($resultsStatus === 'submitted')
                                <i class="bi bi-lock-fill me-1"></i>
                                Submitted results are locked.
                            @else
                                <i class="bi bi-info-circle me-1"></i>
                                All four fields must be completed for every student.
                            @endif
                        </div>

                        <div class="d-flex flex-wrap gap-2">

                            @if (!$resultsLocked)

                                {{-- Save Scores, Grades and Remarks --}}
                                <button
                                    type="submit"
                                    class="btn btn-outline-primary px-4"
                                    wire:loading.attr="disabled"
                                    wire:target="saveScores"
                                >
                                    <span wire:loading.remove wire:target="saveScores">
                                        <i class="bi bi-save me-1"></i>
                                        Save Results
                                    </span>

                                    <span wire:loading wire:target="saveScores">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        Saving...
                                    </span>
                                </button>

                                {{-- Submit for Approval --}}
                                <button
                                    type="button"
                                    class="btn btn-primary px-4"
                                    wire:click="submitResults"
                                    wire:confirm="Have you saved and checked every student's scores, grade and remark? Submit these results for approval?"
                                    wire:loading.attr="disabled"
                                    wire:target="submitResults"
                                >
                                    <span wire:loading.remove wire:target="submitResults">
                                        <i class="bi bi-send-check me-1"></i>
                                        Submit for Approval
                                    </span>

                                    <span wire:loading wire:target="submitResults">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        Submitting...
                                    </span>
                                </button>

                            @else

                                <button
                                    type="button"
                                    class="btn btn-secondary px-4"
                                    disabled
                                >
                                    <i class="bi bi-lock-fill me-1"></i>
                                    Results Locked
                                </button>

                            @endif

                        </div>
                    </div>
                </div>

            </div>

        </form>

    @elseif ($examId)

        {{-- No Active Students --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>

                <h5 class="fw-bold mt-3">No Active Students Found</h5>

                <p class="text-muted mb-0">
                    No active student enrollments were found for this class,
                    session, term and assignment. Check the enrollment records.
                </p>
            </div>
        </div>

    @else

        {{-- Empty State --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-clipboard2-data text-primary"
                    style="font-size: 3.5rem;"></i>

                <h5 class="fw-bold mt-3">Ready to Enter Results?</h5>

                <p class="text-muted mb-0">
                    Select an academic session, term, class/subject and examination
                    to load your students.
                </p>
            </div>
        </div>

    @endif

</div>
