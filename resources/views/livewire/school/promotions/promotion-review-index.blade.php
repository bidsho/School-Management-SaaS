
<div class="container-fluid py-4">

    {{-- Page header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-mortarboard-fill text-primary me-2"></i>
                Student Promotion Review
            </h3>
            <p class="text-muted mb-0">
                Review teacher recommendations and select the destination academic session.
            </p>
        </div>

        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-shield-check me-1"></i>
            Administration
        </span>
    </div>

    {{-- Success message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm" role="alert">
            <strong>Please review the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        @foreach ([
            ['label' => 'Awaiting Review', 'key' => 'submitted', 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
            ['label' => 'Approved', 'key' => 'approved', 'icon' => 'bi-check-circle', 'color' => 'success'],
            ['label' => 'Rejected', 'key' => 'rejected', 'icon' => 'bi-x-circle', 'color' => 'danger'],
            ['label' => 'Executed', 'key' => 'executed', 'icon' => 'bi-mortarboard', 'color' => 'primary'],
        ] as $card)
            <div class="col-12 col-sm-6 col-xl-3" wire:key="promotion-count-{{ $card['key'] }}">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-{{ $card['color'] }}-subtle text-{{ $card['color'] }} p-3">
                            <i class="bi {{ $card['icon'] }} fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">{{ $card['label'] }}</div>
                            <h4 class="fw-bold mb-0">
                                {{ $counts[$card['key']] ?? 0 }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Search and status filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-7">
                    <label for="promotionSearch" class="form-label fw-semibold">
                        Search recommendations
                    </label>
                    <input
                        id="promotionSearch"
                        type="search"
                        class="form-control"
                        placeholder="Student name, admission number or class..."
                        wire:model.live.debounce.300ms="search"
                    >
                </div>

                <div class="col-12 col-md-5">
                    <label for="promotionStatus" class="form-label fw-semibold">
                        Filter by status
                    </label>
                    <select
                        id="promotionStatus"
                        class="form-select"
                        wire:model.live="status"
                    >
                        <option value="submitted">Awaiting Review</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="executed">Executed</option>
                        <option value="draft">Draft</option>
                        <option value="all">All Statuses</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Recommendations table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Promotion Recommendations</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Student</th>
                        <th>Current Class</th>
                        <th>Next Class</th>
                        <th>Decision</th>
                        <th>Recommended By</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($promotions as $promotion)
                        <tr wire:key="promotion-review-{{ $promotion->id }}">
                            <td class="ps-3">
                                <div class="fw-semibold">
                                    {{ trim(($promotion->student?->first_name ?? '') . ' ' . ($promotion->student?->middle_name ? $promotion->student->middle_name . ' ' : '') . ($promotion->student?->last_name ?? '')) ?: 'Unknown Student' }}
                                </div>
                                <small class="text-muted">
                                    {{ $promotion->student?->admission_number ?? 'No admission number' }}
                                </small>
                            </td>

                            <td>
                                {{ $promotion->fromClassRoom?->name ?? '—' }}
                                <div class="small text-muted">
                                    {{ $promotion->fromAcademicSession?->name ?? '—' }}
                                </div>
                            </td>

                            <td>
                                {{ $promotion->toClassRoom?->name ?? '—' }}
                                <div class="small text-muted">
                                    {{ $promotion->toAcademicSession?->name ?? 'Session pending review' }}
                                </div>
                            </td>

                            <td>
                                @if ($promotion->decision === 'promote')
                                    <span class="badge bg-success-subtle text-success border">
                                        Promote
                                    </span>
                                @elseif ($promotion->decision === 'repeat')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border">
                                        Repeat Class
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info-emphasis border">
                                        Needs Review
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $promotion->recommender?->name ?? '—' }}
                                <div class="small text-muted">
                                    {{ $promotion->recommended_at?->format('d M Y') ?? '—' }}
                                </div>
                            </td>

                            <td>
                                @php
                                    $statusClasses = [
                                        'draft' => 'secondary',
                                        'submitted' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'executed' => 'primary',
                                    ];

                                    $statusClass = $statusClasses[$promotion->status] ?? 'secondary';
                                @endphp

                                <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} border">
                                    {{ ucfirst($promotion->status) }}
                                </span>
                            </td>

                            <td class="text-end pe-3">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    wire:click="viewPromotion({{ $promotion->id }})"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    Review
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                <h6 class="fw-bold mt-3">No Recommendations Found</h6>
                                <p class="text-muted mb-0">
                                    No recommendations match the selected filters.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($promotions->hasPages())
            <div class="card-footer bg-white">
                {{ $promotions->links() }}
            </div>
        @endif
    </div>

    {{-- Review modal --}}
    @if ($showReviewModal && $selectedPromotion)
        <div class="promotion-modal-backdrop" wire:click.self="closeReview">
            <div class="promotion-modal-panel">
                <div class="card border-0 shadow-lg">

                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="fw-bold mb-1">Review Promotion</h5>
                            <small class="text-muted">
                                Recommendation #{{ $selectedPromotion->id }}
                            </small>
                        </div>

                        <button
                            type="button"
                            class="btn-close"
                            aria-label="Close"
                            wire:click="closeReview"
                        ></button>
                    </div>

                    <div class="card-body">
                        <div class="row g-3 mb-4">

                            <div class="col-12 col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <small class="text-muted d-block mb-1">Student</small>
                                    <strong>
                                        {{ trim(($selectedPromotion->student?->first_name ?? '') . ' ' . ($selectedPromotion->student?->middle_name ? $selectedPromotion->student->middle_name . ' ' : '') . ($selectedPromotion->student?->last_name ?? '')) ?: 'Unknown Student' }}
                                    </strong>
                                    <div class="small text-muted mt-1">
                                        {{ $selectedPromotion->student?->admission_number ?? 'No admission number' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <small class="text-muted d-block mb-1">Recommendation</small>
                                    <strong>{{ ucfirst($selectedPromotion->decision ?? 'Not specified') }}</strong>
                                    <div class="small text-muted mt-1">
                                        Status: {{ ucfirst($selectedPromotion->status) }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <small class="text-muted d-block mb-1">From</small>
                                    <strong>{{ $selectedPromotion->fromClassRoom?->name ?? '—' }}</strong>
                                    <div class="small text-muted">
                                        {{ $selectedPromotion->fromAcademicSession?->name ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <small class="text-muted d-block mb-1">Current destination</small>
                                    <strong>{{ $selectedPromotion->toClassRoom?->name ?? 'Not assigned' }}</strong>
                                    <div class="small text-muted">
                                        {{ $selectedPromotion->toAcademicSession?->name ?? 'Session not selected' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="border rounded-3 p-3">
                                    <small class="text-muted d-block mb-1">
                                        Teacher's Recommendation Note
                                    </small>
                                    <p class="mb-0">
                                        {{ $selectedPromotion->recommendation_reason ?: 'No note supplied.' }}
                                    </p>
                                </div>
                            </div>

                            @if ($selectedPromotion->approval_note)
                                <div class="col-12">
                                    <div class="alert alert-secondary mb-0">
                                        <strong>Previous review note:</strong>
                                        <p class="mb-0 mt-1">
                                            {{ $selectedPromotion->approval_note }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if ($selectedPromotion->status === 'submitted')
                            {{-- Destination session is selected by the reviewer --}}
                            <div class="mb-3">
                                <label for="toAcademicSessionId" class="form-label fw-semibold">
                                    Destination Academic Session
                                    <span class="text-danger">*</span>
                                </label>

                                <select
                                    id="toAcademicSessionId"
                                    class="form-select @error('toAcademicSessionId') is-invalid @enderror"
                                    wire:model="toAcademicSessionId"
                                >
                                    <option value="">Select destination session</option>

                                    @foreach ($academicSessions as $academicSession)
                                        <option
                                            value="{{ $academicSession->id }}"
                                            @disabled($academicSession->id === $selectedPromotion->from_academic_session_id)
                                        >
                                            {{ $academicSession->name }}
                                            @if ($academicSession->id === $selectedPromotion->from_academic_session_id)
                                                (Current session)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                @error('toAcademicSessionId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="form-text">
                                    Choose the academic session in which this recommendation should take effect.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="reviewNote" class="form-label fw-semibold">
                                    Review note
                                    <span class="text-muted fw-normal">
                                        (optional for approval, required for rejection)
                                    </span>
                                </label>

                                <textarea
                                    id="reviewNote"
                                    rows="3"
                                    maxlength="2000"
                                    class="form-control @error('reviewNote') is-invalid @enderror"
                                    wire:model="reviewNote"
                                    placeholder="Add an approval note or explain why the recommendation should be rejected..."
                                ></textarea>

                                @error('reviewNote')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div class="card-footer bg-white d-flex flex-wrap justify-content-end gap-2">
                        <button
                            type="button"
                            class="btn btn-light border"
                            wire:click="closeReview"
                        >
                            Close
                        </button>

                        @if ($selectedPromotion->status === 'submitted')
                            <button
                                type="button"
                                class="btn btn-danger"
                                wire:click="rejectPromotion({{ $selectedPromotion->id }})"
                                wire:loading.attr="disabled"
                                wire:target="rejectPromotion"
                                wire:confirm="Reject this promotion recommendation? The reason you entered will be recorded."
                            >
                                <i class="bi bi-x-circle me-1"></i>
                                Reject
                            </button>

                            <button
                                type="button"
                                class="btn btn-success"
                                wire:click="approvePromotion({{ $selectedPromotion->id }})"
                                wire:loading.attr="disabled"
                                wire:target="approvePromotion"
                                wire:confirm="Approve this recommendation for the selected academic session? The student's enrolment will not change until promotion execution."
                            >
                                <span wire:loading.remove wire:target="approvePromotion">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Approve
                                </span>
                                <span wire:loading wire:target="approvePromotion">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Approving...
                                </span>
                            </button>

                        @elseif ($selectedPromotion->status === 'approved')
                            <span class="badge bg-success-subtle text-success border align-self-center px-3 py-2">
                                Approved — awaiting execution
                            </span>

                        @elseif ($selectedPromotion->status === 'rejected')
                            <span class="badge bg-danger-subtle text-danger border align-self-center px-3 py-2">
                                Rejected
                            </span>

                        @elseif ($selectedPromotion->status === 'executed')
                            <span class="badge bg-primary-subtle text-primary border align-self-center px-3 py-2">
                                Executed
                            </span>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @endif

    <style>
        .promotion-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1055;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(15, 23, 42, .55);
            overflow-y: auto;
        }

        .promotion-modal-panel {
            width: 100%;
            max-width: 850px;
            max-height: 92vh;
            overflow-y: auto;
            border-radius: .75rem;
        }
    </style>

</div>
