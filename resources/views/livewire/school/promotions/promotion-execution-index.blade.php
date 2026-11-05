
<div class="container-fluid py-4">

    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-mortarboard-fill text-primary me-2"></i>
                Student Promotion Execution
            </h3>
            <p class="text-muted mb-0">
                Execute approved promotions and prepare enrollments for the next academic session.
            </p>
        </div>

        <span class="badge bg-success-subtle text-success border px-3 py-2">
            <i class="bi bi-shield-check me-1"></i>
            Authorized administration
        </span>
    </div>

    {{-- Success message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Execution error --}}
    @error('execution')
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ $message }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @enderror

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        @foreach ([
            [
                'label' => 'Awaiting Execution',
                'key' => \App\Models\StudentPromotion::STATUS_APPROVED,
                'icon' => 'bi-hourglass-split',
                'color' => 'primary',
            ],
            [
                'label' => 'Executed',
                'key' => \App\Models\StudentPromotion::STATUS_EXECUTED,
                'icon' => 'bi-check-circle',
                'color' => 'success',
            ],
            [
                'label' => 'Rejected',
                'key' => \App\Models\StudentPromotion::STATUS_REJECTED,
                'icon' => 'bi-x-circle',
                'color' => 'danger',
            ],
            [
                'label' => 'Submitted for Review',
                'key' => \App\Models\StudentPromotion::STATUS_SUBMITTED,
                'icon' => 'bi-clipboard-check',
                'color' => 'warning',
            ],
        ] as $card)
            <div class="col-12 col-sm-6 col-xl-3"
                 wire:key="execution-count-{{ $card['key'] }}">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-1">
                                {{ $card['label'] }}
                            </div>
                            <h3 class="fw-bold mb-0">
                                {{ number_format((int) ($counts[$card['key']] ?? 0)) }}
                            </h3>
                        </div>
                        <div class="rounded-3 bg-{{ $card['color'] }}-subtle
                                    text-{{ $card['color'] }} p-3">
                            <i class="bi {{ $card['icon'] }} fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Promotion records --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Promotion Records</h5>
                    <p class="text-muted small mb-0">
                        Only approved recommendations can be executed.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <select
                        wire:model.live="status"
                        class="form-select"
                        aria-label="Filter by promotion status"
                    >
                        <option value="approved">Awaiting execution</option>
                        <option value="executed">Executed history</option>
                        <option value="all">All records</option>
                    </select>

                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        class="form-control"
                        placeholder="Search student..."
                        aria-label="Search student"
                    >
                </div>
            </div>
        </div>

        {{-- Additional filters --}}
        <div class="px-4 py-3 border-top border-bottom bg-light">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold"
                           for="sourceSessionFilter">
                        Source Academic Session
                    </label>
                    <select
                        id="sourceSessionFilter"
                        wire:model.live="sourceSessionId"
                        class="form-select"
                    >
                        <option value="">All source sessions</option>
                        @foreach ($sessions as $session)
                            <option value="{{ $session->id }}">
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold"
                           for="classRoomFilter">
                        Current Class
                    </label>
                    <select
                        id="classRoomFilter"
                        wire:model.live="classRoomId"
                        class="form-select"
                    >
                        <option value="">All classes</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Student</th>
                            <th class="py-3">From</th>
                            <th class="py-3">To</th>
                            <th class="py-3">Decision</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end px-4">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($promotions as $promotion)
                            <tr wire:key="promotion-row-{{ $promotion->id }}">
                                <td class="px-4 py-3">
                                    <div class="fw-semibold">
                                        {{ trim(implode(' ', array_filter([
                                            $promotion->student?->first_name,
                                            $promotion->student?->middle_name,
                                            $promotion->student?->last_name,
                                        ]))) ?: 'Unknown student' }}
                                    </div>
                                    <div class="text-muted small">
                                        Admission No:
                                        {{ $promotion->student?->admission_number ?? 'N/A' }}
                                    </div>
                                </td>

                                <td class="py-3">
                                    <div class="fw-semibold">
                                        {{ $promotion->fromClassRoom?->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $promotion->fromAcademicSession?->name ?? 'N/A' }}
                                    </div>
                                </td>

                                <td class="py-3">
                                    <div class="fw-semibold">
                                        @if ($promotion->decision === \App\Models\StudentPromotion::DECISION_REPEAT)
                                            {{ $promotion->fromClassRoom?->name ?? 'Current class unavailable' }}
                                            <span class="badge bg-warning-subtle text-warning-emphasis">
                                                Same class
                                            </span>
                                        @else
                                            {{ $promotion->toClassRoom?->name ?? 'Not assigned' }}
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        {{ $promotion->toAcademicSession?->name ?? 'Session missing' }}
                                    </div>
                                </td>

                                <td class="py-3">
                                    @if ($promotion->decision === \App\Models\StudentPromotion::DECISION_PROMOTE)
                                        <span class="badge bg-primary-subtle text-primary">
                                            <i class="bi bi-arrow-up-circle me-1"></i>
                                            Promote
                                        </span>
                                    @elseif ($promotion->decision === \App\Models\StudentPromotion::DECISION_REPEAT)
                                        <span class="badge bg-warning-subtle text-warning-emphasis">
                                            <i class="bi bi-arrow-repeat me-1"></i>
                                            Repeat
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ ucfirst($promotion->decision ?? 'Not set') }}
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3">
                                    @if ($promotion->status === \App\Models\StudentPromotion::STATUS_APPROVED)
                                        <span class="badge bg-success-subtle text-success">
                                            Approved
                                        </span>
                                    @elseif ($promotion->status === \App\Models\StudentPromotion::STATUS_EXECUTED)
                                        <span class="badge bg-primary-subtle text-primary">
                                            Executed
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ ucfirst($promotion->status) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end px-4 py-3">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        wire:click="viewPromotion({{ $promotion->id }})"
                                    >
                                        <i class="bi bi-eye me-1"></i>
                                        Details
                                    </button>

                                    @if ($promotion->status === \App\Models\StudentPromotion::STATUS_APPROVED)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-success mt-1 mt-lg-0"
                                            wire:click="executePromotion({{ $promotion->id }})"
                                            wire:confirm="Execute this approved recommendation? The previous enrollment will be preserved and a destination enrollment will be created."
                                            wire:loading.attr="disabled"
                                            wire:target="executePromotion({{ $promotion->id }})"
                                        >
                                            <span wire:loading.remove
                                                  wire:target="executePromotion({{ $promotion->id }})">
                                                <i class="bi bi-check2-circle me-1"></i>
                                                Execute
                                            </span>
                                            <span wire:loading
                                                  wire:target="executePromotion({{ $promotion->id }})">
                                                <span class="spinner-border spinner-border-sm me-1"
                                                      aria-hidden="true"></span>
                                                Processing...
                                            </span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <h6 class="fw-bold mt-3">
                                        No promotion records found
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        Approved recommendations will appear here when they are ready for execution.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($promotions->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $promotions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Details modal --}}
    @if ($showDetailsModal && $selectedPromotion)
        <div
            class="promotion-modal-backdrop"
            wire:click.self="closeDetails"
        >
            <div class="promotion-modal-dialog" role="dialog"
                 aria-modal="true" aria-labelledby="promotionDetailsTitle">

                <div class="promotion-modal-content">
                    <div class="d-flex justify-content-between align-items-start
                                border-bottom p-4">
                        <div>
                            <h5 class="fw-bold mb-1" id="promotionDetailsTitle">
                                Promotion Details
                            </h5>
                            <p class="text-muted small mb-0">
                                Verify the student's details before execution.
                            </p>
                        </div>

                        <button type="button" class="btn-close"
                                wire:click="closeDetails"
                                aria-label="Close"></button>
                    </div>

                    <div class="p-4">
                        <div class="rounded-3 bg-light p-3 mb-4">
                            <div class="text-muted small mb-1">Student</div>
                            <h5 class="fw-bold mb-1">
                                {{ trim(implode(' ', array_filter([
                                    $selectedPromotion->student?->first_name,
                                    $selectedPromotion->student?->middle_name,
                                    $selectedPromotion->student?->last_name,
                                ]))) ?: 'Unknown student' }}
                            </h5>
                            <div class="text-muted small">
                                Admission No:
                                {{ $selectedPromotion->student?->admission_number ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="text-muted small mb-2">
                                        Previous Enrollment
                                    </div>
                                    <div class="fw-semibold">
                                        {{ $selectedPromotion->fromClassRoom?->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $selectedPromotion->fromAcademicSession?->name ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="text-muted small mb-2">
                                        Destination Enrollment
                                    </div>
                                    <div class="fw-semibold">
                                        @if ($selectedPromotion->decision === \App\Models\StudentPromotion::DECISION_REPEAT)
                                            {{ $selectedPromotion->fromClassRoom?->name ?? 'Current class unavailable' }}
                                            <span class="badge bg-warning-subtle text-warning-emphasis">
                                                Repeat
                                            </span>
                                        @else
                                            {{ $selectedPromotion->toClassRoom?->name ?? 'Not assigned' }}
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        {{ $selectedPromotion->toAcademicSession?->name ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="small text-muted mb-1">Decision</div>
                                <div class="fw-semibold">
                                    {{ ucfirst($selectedPromotion->decision ?? 'Not set') }}
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="small text-muted mb-1">Status</div>
                                <div class="fw-semibold">
                                    {{ ucfirst($selectedPromotion->status) }}
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="small text-muted mb-1">
                                    Teacher's Recommendation
                                </div>
                                <div class="border rounded-3 p-3">
                                    {{ $selectedPromotion->recommendation_reason ?: 'No recommendation reason provided.' }}
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="small text-muted mb-1">
                                    Approval Note
                                </div>
                                <div class="border rounded-3 p-3">
                                    {{ $selectedPromotion->approval_note ?: 'No approval note provided.' }}
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="small text-muted mb-1">Approved By</div>
                                <div class="fw-semibold">
                                    {{ $selectedPromotion->reviewer?->name ?? 'N/A' }}
                                </div>
                            </div>

                            @if ($selectedPromotion->executed_at)
                                <div class="col-12">
                                    <div class="small text-muted mb-1">Executed At</div>
                                    <div class="fw-semibold">
                                        {{ $selectedPromotion->executed_at->format('d M Y, h:i A') }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if ($selectedPromotion->status === \App\Models\StudentPromotion::STATUS_APPROVED)
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                @if ($selectedPromotion->decision === \App\Models\StudentPromotion::DECISION_REPEAT)
                                    Execution will complete the previous enrollment and enroll the student in the same class for the destination academic session.
                                @else
                                    Execution will complete the previous enrollment and enroll the student in the approved destination class and academic session.
                                @endif
                                The previous enrollment record will be preserved.
                            </div>
                        @elseif ($selectedPromotion->status === \App\Models\StudentPromotion::STATUS_EXECUTED)
                            <div class="alert alert-success mb-0">
                                <i class="bi bi-check-circle me-2"></i>
                                This promotion has already been executed.
                            </div>
                        @endif
                    </div>

                    <div class="d-flex flex-wrap justify-content-end gap-2
                                border-top p-3">
                        <button type="button" class="btn btn-light border"
                                wire:click="closeDetails">
                            Close
                        </button>

                        @if ($selectedPromotion->status === \App\Models\StudentPromotion::STATUS_APPROVED)
                            <button
                                type="button"
                                class="btn btn-success"
                                wire:click="executePromotion({{ $selectedPromotion->id }})"
                                wire:confirm="Are you sure you want to execute this approved recommendation?"
                                wire:loading.attr="disabled"
                                wire:target="executePromotion"
                            >
                                <span wire:loading.remove wire:target="executePromotion">
                                    <i class="bi bi-check2-circle me-1"></i>
                                    Execute Promotion
                                </span>
                                <span wire:loading wire:target="executePromotion">
                                    <span class="spinner-border spinner-border-sm me-1"
                                          aria-hidden="true"></span>
                                    Processing...
                                </span>
                            </button>
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
            background: rgba(15, 23, 42, .58);
            overflow-y: auto;
        }

        .promotion-modal-dialog {
            width: 100%;
            max-width: 760px;
            max-height: calc(100vh - 2rem);
            margin: auto;
            overflow-y: auto;
        }

        .promotion-modal-content {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .2);
            overflow: hidden;
        }

        @media (max-width: 576px) {
            .promotion-modal-backdrop {
                align-items: flex-start;
                padding: .5rem;
            }

            .promotion-modal-dialog {
                max-height: calc(100vh - 1rem);
            }
        }
    </style>

</div>
