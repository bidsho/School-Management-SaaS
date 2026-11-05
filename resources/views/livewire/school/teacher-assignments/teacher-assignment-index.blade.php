
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Teacher Assignments</h3>
            <p class="text-muted mb-0">Manage which teachers teach subjects in each class.</p>
        </div>

        @can('create teacher assignments')
            <a href="{{ route('school.teacher-assignments.create') }}"
               class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> New Assignment
            </a>
        @endcan
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Assignments</div>
                    <div class="fs-3 fw-bold">{{ number_format($totalAssignments) }}</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Teachers Assigned</div>
                    <div class="fs-3 fw-bold">{{ number_format($totalTeachers) }}</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Subjects Assigned</div>
                    <div class="fs-3 fw-bold">{{ number_format($totalSubjects) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body border-bottom">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="search"
                               class="form-control"
                               placeholder="Search teacher, staff number, subject, class or session..."
                               wire:model.live.debounce.350ms="search">
                    </div>
                </div>

                <div class="col-md-4 text-md-end">
                    <span class="text-muted small">
                        Showing {{ $assignments->firstItem() ?? 0 }}–{{ $assignments->lastItem() ?? 0 }}
                        of {{ $assignments->total() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Teacher</th>
                        <th>Subject</th>
                        <th>Class / Section</th>
                        <th>Academic Period</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                        <tr wire:key="teacher-assignment-{{ $assignment->id }}">
                            <td class="ps-4">
                                <div class="fw-semibold text-dark">
                                    {{ trim(
                                        ($assignment->teacher?->first_name ?? '') . ' ' .
                                        ($assignment->teacher?->middle_name ?? '') . ' ' .
                                        ($assignment->teacher?->last_name ?? '')
                                    ) ?: 'Name unavailable' }}
                                </div>
                                <div class="text-muted small">
                                    {{ $assignment->teacher?->staff_number
                                        ? 'Staff No: ' . $assignment->teacher->staff_number
                                        : 'No staff number' }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $assignment->subject?->name ?? 'Subject unavailable' }}
                                </div>
                                <div class="text-muted small">
                                    {{ $assignment->subject?->code ?? '' }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $assignment->classRoom?->name ?? 'Class unavailable' }}
                                </div>
                                <div class="text-muted small">
                                    {{ $assignment->section?->name ?? 'No section' }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $assignment->academicSession?->name ?? 'Session unavailable' }}
                                </div>
                                <div class="text-muted small">
                                    {{ $assignment->term?->name ?? 'All terms / no specific term' }}
                                </div>
                            </td>

                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    @can('edit teacher assignments')
                                        <a href="{{ route('school.teacher-assignments.edit', ['assignmentId' => $assignment->id]) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit assignment">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    @endcan

                                    @can('manage teacher assignments')
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteAssignment({{ $assignment->id }})"
                                                wire:confirm="Are you sure you want to delete this teacher assignment?"
                                                wire:loading.attr="disabled"
                                                title="Delete assignment">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-person-workspace fs-1 text-muted"></i>
                                <h6 class="fw-semibold mt-3">No teacher assignments found</h6>
                                <p class="text-muted mb-0">
                                    Add an assignment or try another search.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($assignments->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>
