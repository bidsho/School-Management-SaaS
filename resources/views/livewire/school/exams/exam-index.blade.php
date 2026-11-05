
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Examinations</h3>
            <p class="text-muted mb-0">Create and manage examinations for your school.</p>
        </div>

        <button type="button" class="btn btn-primary px-4"
                wire:click="openCreateModal">
            <i class="bi bi-plus-circle me-2"></i>Create Examination
        </button>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Examinations</div>
                    <div class="fs-3 fw-bold">{{ number_format($totalExams) }}</div>
                    <i class="bi bi-journal-text text-primary"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Scheduled</div>
                    <div class="fs-3 fw-bold text-primary">
                        {{ number_format($scheduledExams) }}
                    </div>
                    <i class="bi bi-calendar-event text-primary"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Completed</div>
                    <div class="fs-3 fw-bold text-success">
                        {{ number_format($completedExams) }}
                    </div>
                    <i class="bi bi-check-circle text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label">Search</label>
                    <input type="search" class="form-control"
                           wire:model.live.debounce.350ms="search"
                           placeholder="Exam, subject or class">
                </div>

                <div class="col-sm-6 col-lg-2">
                    <label class="form-label">Exam Date</label>
                    <input type="date" class="form-control" wire:model.live="exam_date">
                </div>

                <div class="col-sm-6 col-lg-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" wire:model.live="status">
                        <option value="">All statuses</option>
                        <option value="draft">Draft</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="col-sm-6 col-lg-2">
                    <label class="form-label">Class</label>
                    <select class="form-select" wire:model.live="class_room_id">
                        <option value="">All classes</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-6 col-lg-2">
                    <label class="form-label">Subject</label>
                    <select class="form-select" wire:model.live="subject_id">
                        <option value="">All subjects</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Academic Session</label>
                    <select class="form-select" wire:model.live="academic_session_id">
                        <option value="">All sessions</option>
                        @foreach ($sessions as $session)
                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Term</label>
                    <select class="form-select" wire:model.live="term_id">
                        <option value="">All terms</option>
                        @foreach ($terms as $term)
                            <option value="{{ $term->id }}">{{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 text-end">
                    <button type="button" class="btn btn-outline-secondary"
                            wire:click="clearFilters">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-semibold mb-0">Examination Records</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Examination</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Exam Date</th>
                        <th>Total Marks</th>
                        <th>Academic Period</th>
                        <th>Status</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($exams as $exam)
                        <tr wire:key="exam-{{ $exam->id }}">
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $exam->name }}</div>
                                <div class="small text-muted">{{ ucfirst($exam->type ?? 'exam') }}</div>
                            </td>
                            <td>{{ $exam->classRoom?->name ?? '—' }}</td>
                            <td>
                                <div class="fw-semibold">{{ $exam->subject?->name ?? '—' }}</div>
                                <div class="small text-muted">{{ $exam->subject?->code ?? '' }}</div>
                            </td>
                            <td>{{ $exam->exam_date?->format('d M Y') ?? '—' }}</td>
                            <td>{{ number_format((float) $exam->total_marks, 2) }}</td>
                            <td>
                                <div>{{ $exam->academicSession?->name ?? '—' }}</div>
                                <div class="small text-muted">{{ $exam->term?->name ?? '—' }}</div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match (strtolower($exam->status ?? '')) {
                                        'scheduled' => 'bg-primary-subtle text-primary',
                                        'ongoing' => 'bg-warning-subtle text-warning-emphasis',
                                        'completed', 'approved', 'published' => 'bg-success-subtle text-success',
                                        'cancelled' => 'bg-danger-subtle text-danger',
                                        'draft' => 'bg-secondary-subtle text-secondary',
                                        default => 'bg-secondary-subtle text-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($exam->status ?? 'Unknown') }}
                                </span>
                            </td>
                            <td>{{ $exam->creator?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-journal-x fs-1 text-muted"></i>
                                <h6 class="fw-semibold mt-3">No examinations found</h6>
                                <p class="text-muted mb-3">Create your first examination or adjust the filters.</p>
                                <button type="button" class="btn btn-primary"
                                        wire:click="openCreateModal">
                                    <i class="bi bi-plus-circle me-2"></i>Create Examination
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($exams->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $exams->links() }}
            </div>
        @endif
    </div>

    {{-- Create Examination Modal --}}
    @if ($showCreateModal)
        <div class="modal d-block" tabindex="-1"
             style="background: rgba(15, 23, 42, .55);"
             role="dialog" aria-modal="true"
             aria-labelledby="createExamTitle">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow rounded-4">

                    <div class="modal-header border-0 px-4 pt-4">
                        <div>
                            <h5 class="modal-title fw-bold" id="createExamTitle">
                                <i class="bi bi-journal-plus text-primary me-2"></i>
                                Create Examination
                            </h5>
                            <p class="text-muted small mb-0">
                                Set the exam details before teachers enter scores.
                            </p>
                        </div>
                        <button type="button" class="btn-close"
                                wire:click="closeCreateModal" aria-label="Close"></button>
                    </div>

                    <form wire:submit="saveExam">
                        <div class="modal-body px-4 py-3">
                            <div class="row g-3">

                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Examination Name *</label>
                                    <input type="text" class="form-control"
                                           wire:model="name"
                                           placeholder="e.g. First Term Mathematics Examination">
                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Exam Type *</label>
                                    <select class="form-select" wire:model="new_type">
                                        <option value="exam">Examination</option>
                                        <option value="test">Test</option>
                                        <option value="quiz">Quiz</option>
                                        <option value="assessment">Assessment</option>
                                        <option value="practical">Practical</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('new_type') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Academic Session *</label>
                                    <select class="form-select" wire:model.live="new_academic_session_id">
                                        <option value="">Select session</option>
                                        @foreach ($createSessions as $session)
                                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_academic_session_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Term *</label>
                                    <select class="form-select" wire:model="new_term_id"
                                            @disabled(!$new_academic_session_id)>
                                        <option value="">Select term</option>
                                        @foreach ($createTerms as $term)
                                            <option value="{{ $term->id }}">{{ $term->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_term_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Class *</label>
                                    <select class="form-select" wire:model="new_class_room_id">
                                        <option value="">Select class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('new_class_room_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Subject *</label>
                                    <select class="form-select" wire:model="new_subject_id">
                                        <option value="">Select subject</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">
                                                {{ $subject->name }}
                                                @if ($subject->code) ({{ $subject->code }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('new_subject_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Exam Date</label>
                                    <input type="date" class="form-control" wire:model="new_exam_date">
                                    @error('new_exam_date') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Maximum Marks *</label>
                                    <input type="number" min="1" step="0.01"
                                           class="form-control" wire:model="total_marks">
                                    <small class="text-muted">Total available marks for this exam.</small>
                                    @error('total_marks') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-light"
                                    wire:click="closeCreateModal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary px-4"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveExam">
                                    <i class="bi bi-check-circle me-1"></i>Create Exam
                                </span>
                                <span wire:loading wire:target="saveExam">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Creating...
                                </span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

</div>
