
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Student Attendance</h3>
            <p class="text-muted mb-0">
                View and monitor attendance records for your school.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">All Records</div>
                    <div class="fs-3 fw-bold">{{ number_format($totalRecords) }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Present</div>
                    <div class="fs-3 fw-bold text-success">{{ number_format($presentCount) }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Absent</div>
                    <div class="fs-3 fw-bold text-danger">{{ number_format($absentCount) }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Late</div>
                    <div class="fs-3 fw-bold text-warning">{{ number_format($lateCount) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label">Search Student</label>
                    <input type="search"
                           class="form-control"
                           wire:model.live.debounce.350ms="search"
                           placeholder="Name or admission number">
                </div>

                <div class="col-sm-6 col-lg-2">
                    <label class="form-label">Attendance Date</label>
                    <input type="date"
                           class="form-control"
                           wire:model.live="date">
                </div>

                <div class="col-sm-6 col-lg-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" wire:model.live="status">
                        <option value="">All statuses</option>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="excused">Excused</option>
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
                    <label class="form-label">Section</label>
                    <select class="form-select" wire:model.live="section_id">
                        <option value="">All sections</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
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

                <div class="col-12 d-flex justify-content-end">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            wire:click="clearFilters">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">Attendance Records</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Student</th>
                        <th>Class / Section</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Academic Period</th>
                        <th>Marked By</th>
                        <th>Remarks</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($attendances as $attendance)
                        <tr wire:key="attendance-{{ $attendance->id }}">
                            <td class="ps-4">
                                <div class="fw-semibold">
                                    {{ trim(($attendance->student?->first_name ?? '') . ' ' . ($attendance->student?->last_name ?? '')) ?: 'Student unavailable' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $attendance->student?->admission_number ?? 'No admission number' }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $attendance->classRoom?->name ?? '—' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $attendance->section?->name ?? 'No section' }}
                                </div>
                            </td>

                            <td>
                                {{ $attendance->attendance_date?->format('d M Y') ?? '—' }}
                            </td>

                            <td>
                                @php
                                    $status = strtolower($attendance->status ?? '');
                                    $badgeClass = match ($status) {
                                        'present' => 'bg-success-subtle text-success',
                                        'absent' => 'bg-danger-subtle text-danger',
                                        'late' => 'bg-warning-subtle text-warning-emphasis',
                                        'excused' => 'bg-info-subtle text-info-emphasis',
                                        default => 'bg-secondary-subtle text-secondary',
                                    };
                                @endphp

                                <span class="badge {{ $badgeClass }}">
                                    {{ ucfirst($status ?: 'Unknown') }}
                                </span>
                            </td>

                            <td>
                                <div>{{ $attendance->academicSession?->name ?? '—' }}</div>
                                <div class="small text-muted">
                                    {{ $attendance->term?->name ?? '—' }}
                                </div>
                            </td>

                            <td>
                                {{ $attendance->marker?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $attendance->remarks ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-calendar2-check fs-1 text-muted"></i>
                                <h6 class="fw-semibold mt-3">No attendance records found</h6>
                                <p class="text-muted mb-0">
                                    Try changing your search or filters.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($attendances->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
