
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Exam Results</h2>
            <p class="text-muted mb-0">
                Review your child's published examination results and academic progress.
            </p>
        </div>

        <a href="{{ route('parent.children.index') }}"
           class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i>
            My Children
        </a>
    </div>

    @if ($children->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-people fs-1 text-muted"></i>
                <h5 class="mt-3">No linked students</h5>
                <p class="text-muted mb-0">
                    Contact your school administrator to link your child to your account.
                </p>
            </div>
        </div>
    @else

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <label for="studentId" class="form-label fw-semibold">
                    Select Child
                </label>

                <select id="studentId"
                        class="form-select rounded-3"
                        wire:model.live="studentId">
                    @foreach ($children as $child)
                        <option value="{{ $child->id }}">
                            {{ $child->first_name }}
                            {{ $child->middle_name }}
                            {{ $child->last_name }}
                            — {{ $child->admission_number }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 mb-4">

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Published Exams</span>
                            <i class="bi bi-journal-check fs-4 text-primary"></i>
                        </div>
                        <h3 class="fw-bold mb-0">{{ $summary['exams'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Subject Results</span>
                            <i class="bi bi-book fs-4 text-success"></i>
                        </div>
                        <h3 class="fw-bold mb-0">{{ $summary['subjects'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Average Percentage</span>
                            <i class="bi bi-graph-up-arrow fs-4 text-info"></i>
                        </div>
                        <h3 class="fw-bold mb-0">
                            {{ $summary['average'] !== null
                                ? number_format($summary['average'], 1) . '%'
                                : '—' }}
                        </h3>
                        <small class="text-muted">
                            Across published subject results with a valid maximum score
                        </small>
                    </div>
                </div>
            </div>

        </div>

        @forelse ($resultsByExam as $examId => $examScores)
            @php
                $firstScore = $examScores->first();
                $exam = $firstScore->exam;
                $totalObtained = $examScores->sum(
                    fn ($score) => (float) $score->total_score
                );
                $totalPossible = $examScores->sum(
                    fn ($score) => (float) ($score->exam?->total_marks ?? 0)
                );
            @endphp

            <div class="card border-0 shadow-sm rounded-4 mb-4"
                 wire:key="exam-results-{{ $examId }}">

                <div class="card-body p-4 border-bottom">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                        <div>
                            <h5 class="fw-bold mb-1">
                                {{ $exam->name ?? 'Examination' }}
                            </h5>

                            <div class="text-muted small">
                                {{ $exam->type ? ucfirst($exam->type) : 'Exam' }}

                                @if ($exam->term?->name)
                                    <span class="mx-1">•</span>
                                    {{ $exam->term->name }}
                                @endif

                                @if ($exam->academicSession?->name)
                                    <span class="mx-1">•</span>
                                    {{ $exam->academicSession->name }}
                                @endif
                            </div>

                            @if ($exam->exam_date)
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $exam->exam_date->format('d M Y') }}
                                </div>
                            @endif
                        </div>

                        <span class="badge text-bg-success rounded-pill px-3 py-2">
                            Published
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Subject</th>
                                <th class="py-3">CA Score</th>
                                <th class="py-3">Exam Score</th>
                                <th class="py-3">Total</th>
                                <th class="py-3">Grade</th>
                                <th class="py-3">Remark</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($examScores as $score)
                                <tr wire:key="exam-score-{{ $score->id }}">
                                    <td class="px-4 py-3 fw-semibold">
                                        {{ $score->subject?->name
                                            ?? $exam->subject?->name
                                            ?? 'Subject' }}
                                    </td>

                                    <td>{{ number_format((float) $score->ca_score, 2) }}</td>

                                    <td>{{ number_format((float) $score->exam_score, 2) }}</td>

                                    <td class="fw-bold">
                                        {{ number_format((float) $score->total_score, 2) }}
                                        <span class="text-muted fw-normal">
                                            / {{ number_format((float) ($exam->total_marks ?? 0), 2) }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($score->grade)
                                            <span class="badge text-bg-primary rounded-pill">
                                                {{ $score->grade }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $score->remark ?: '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot class="table-light">
                            <tr>
                                <th class="px-4 py-3">Total</th>
                                <th>—</th>
                                <th>—</th>
                                <th class="py-3">
                                    {{ number_format($totalObtained, 2) }}
                                    / {{ number_format($totalPossible, 2) }}
                                </th>
                                <th colspan="2">
                                    @if ($totalPossible > 0)
                                        {{ number_format(($totalObtained / $totalPossible) * 100, 1) }}%
                                    @else
                                        —
                                    @endif
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

        @empty
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                    <h5 class="mt-3">No published results yet</h5>
                    <p class="text-muted mb-0">
                        Results will appear here when the school publishes your child's examination results.
                    </p>
                </div>
            </div>
        @endforelse

        <div class="alert alert-light border rounded-3 small mt-3">
            <i class="bi bi-shield-check me-1"></i>
            Only published examination results for your linked child are displayed.
        </div>

    @endif
</div>
