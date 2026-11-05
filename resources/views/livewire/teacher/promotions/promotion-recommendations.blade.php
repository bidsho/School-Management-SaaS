
<div class="container-fluid py-4">

    {{-- Handle missing teacher assignment or source session --}}
    @if (! $assignment || ! $sourceSession)

        <div class="row justify-content-center">
            <div class="col-12 col-xl-9">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5 text-center">
                        <i class="bi bi-exclamation-circle text-warning"
                           style="font-size: 3rem;"></i>

                        <h4 class="fw-bold mt-3 mb-2">
                            Promotion Recommendations Unavailable
                        </h4>

                        <p class="text-muted mb-3">
                            {{ $pageError ?? 'Your account does not currently have a valid active class-head-teacher assignment and academic session.' }}
                        </p>

                        <p class="small text-muted mb-0">
                            Please ask your School Admin to confirm your teacher
                            record, class-head-teacher assignment, and active academic session.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    @else

        {{-- Page header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-arrow-up-square text-primary me-2"></i>
                    Student Promotion Recommendations
                </h3>

                <p class="text-muted mb-0">
                    Recommend whether students should be promoted, repeat
                    their class, or receive further academic review.
                </p>
            </div>

            <span class="badge bg-light text-dark border px-3 py-2">
                <i class="bi bi-person-badge me-1"></i>
                Class Head Teacher
            </span>
        </div>

        {{-- Success message --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm"
                 role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <div class="fw-bold mb-2">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Please review the following:
                </div>

                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Current class and source session --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-center">

                    <div class="col-12 col-md-6">
                        <div class="text-muted small mb-1">Current Class</div>

                        <h5 class="fw-bold mb-0">
                            {{ $assignment->classRoom?->name ?? 'Assigned Class' }}
                        </h5>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="text-muted small mb-1">Current Academic Session</div>

                        <h6 class="fw-semibold mb-0">
                            {{ $sourceSession->name ?? 'Session unavailable' }}
                        </h6>
                    </div>

                </div>
            </div>
        </div>

        {{-- Instructions --}}
        <div class="alert alert-info border-0 shadow-sm">
            <i class="bi bi-info-circle-fill me-2"></i>

            <strong>Before recommending:</strong>
            review each student's approved examination results. The School Admin
            or Principal will assign the destination academic session during
            review. A recommendation is not a promotion until it is approved
            and executed.
        </div>

        {{-- Recommendations --}}
        <form wire:submit="submitRecommendations">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">

                        <div>
                            <h5 class="fw-bold mb-1">
                                Students in
                                {{ $assignment->classRoom?->name ?? 'Assigned Class' }}
                            </h5>

                            <p class="text-muted small mb-0">
                                Complete a recommendation for each student.
                            </p>
                        </div>

                        <span class="badge bg-primary rounded-pill px-3 py-2">
                            {{ count($recommendations) }} Students
                        </span>

                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th style="min-width: 200px;">Student</th>
                                <th style="min-width: 140px;">Admission Number</th>
                                <th style="min-width: 120px;">Approved Exams</th>
                                <th style="min-width: 120px;">Average Score</th>
                                <th style="min-width: 180px;">Recommendation</th>
                                <th style="min-width: 190px;">Destination Class</th>
                                <th style="min-width: 240px;">Reason / Note</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($recommendations as $studentId => $student)

                                <tr wire:key="promotion-student-{{ $studentId }}">

                                    <td class="ps-3 text-muted">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $student['name'] }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $student['admission_number'] ?: '—' }}
                                    </td>

                                    <td>
                                        @if ($student['approved_exam_count'] > 0)
                                            <span class="badge bg-success-subtle text-success border">
                                                {{ $student['approved_exam_count'] }} results
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border">
                                                No approved results
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($student['average_score'] !== null)
                                            <span class="fw-bold">
                                                {{ number_format($student['average_score'], 2) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>

                                    {{-- Recommendation decision --}}
                                    <td>
                                        <select
                                            class="form-select @error("recommendations.$studentId.decision") is-invalid @enderror"
                                            wire:model="recommendations.{{ $studentId }}.decision"
                                            @disabled(in_array(
                                                $student['existing_status'] ?? '',
                                                ['submitted', 'approved', 'executed'],
                                                true
                                            ))
                                        >
                                            <option value="promote">Promote</option>
                                            <option value="repeat">Repeat Class</option>
                                            <option value="review">Needs Review</option>
                                        </select>

                                        @error("recommendations.$studentId.decision")
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        @if (($student['existing_status'] ?? null) === 'submitted')
                                            <div class="small text-info mt-1">
                                                Already submitted
                                            </div>
                                        @elseif (($student['existing_status'] ?? null) === 'approved')
                                            <div class="small text-success mt-1">
                                                Already approved
                                            </div>
                                        @elseif (($student['existing_status'] ?? null) === 'executed')
                                            <div class="small text-success mt-1">
                                                Promotion executed
                                            </div>
                                        @elseif (($student['existing_status'] ?? null) === 'rejected')
                                            <div class="small text-danger mt-1">
                                                Previously rejected — you may revise and resubmit
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Destination class --}}
                                    <td>
                                        @if ($student['decision'] === 'promote')

                                            <select
                                                class="form-select @error("recommendations.$studentId.to_class_room_id") is-invalid @enderror"
                                                wire:model="recommendations.{{ $studentId }}.to_class_room_id"
                                                @disabled(in_array(
                                                    $student['existing_status'] ?? '',
                                                    ['submitted', 'approved', 'executed'],
                                                    true
                                                ))
                                            >
                                                <option value="">Select class</option>

                                                @foreach ($classes as $class)
                                                    <option value="{{ $class->id }}">
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error("recommendations.$studentId.to_class_room_id")
                                                <div class="text-danger small mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        @elseif ($student['decision'] === 'repeat')

                                            <span class="badge bg-warning-subtle text-warning-emphasis border">
                                                Remain in current class
                                            </span>

                                        @else

                                            <span class="text-muted small">
                                                To be determined after review
                                            </span>

                                        @endif
                                    </td>

                                    {{-- Recommendation reason --}}
                                    <td>
                                        <textarea
                                            class="form-control @error("recommendations.$studentId.recommendation_reason") is-invalid @enderror"
                                            rows="2"
                                            maxlength="2000"
                                            placeholder="{{ $student['decision'] === 'review'
                                                ? 'Explain why further review is required...'
                                                : 'Optional recommendation note...' }}"
                                            wire:model="recommendations.{{ $studentId }}.recommendation_reason"
                                            @disabled(in_array(
                                                $student['existing_status'] ?? '',
                                                ['submitted', 'approved', 'executed'],
                                                true
                                            ))
                                        ></textarea>

                                        @error("recommendations.$studentId.recommendation_reason")
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </td>

                                </tr>

                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-people text-muted"
                                           style="font-size: 3rem;"></i>

                                        <h6 class="fw-bold mt-3">
                                            No Active Students Found
                                        </h6>

                                        <p class="text-muted mb-0">
                                            There are no active enrollments in
                                            your assigned class for the current
                                            academic session.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                {{-- Footer --}}
                @if (count($recommendations))
                    <div class="card-footer bg-white p-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                            <div class="text-muted small">
                                <i class="bi bi-shield-check me-1"></i>
                                The School Admin or Principal will review
                                recommendations and assign the next session.
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary px-4"
                                wire:loading.attr="disabled"
                                wire:target="submitRecommendations"
                            >
                                <span wire:loading.remove wire:target="submitRecommendations">
                                    <i class="bi bi-send-check me-1"></i>
                                    Submit Recommendations
                                </span>

                                <span wire:loading wire:target="submitRecommendations">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Submitting...
                                </span>
                            </button>

                        </div>
                    </div>
                @endif

            </div>

        </form>

    @endif

</div>
