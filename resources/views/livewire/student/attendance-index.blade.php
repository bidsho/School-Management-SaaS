
<div class="container-fluid py-4">

    <div class="mb-4">
        <h3 class="fw-bold mb-1">My Attendance</h3>
        <p class="text-secondary mb-0">
            Review your attendance records and daily attendance status.
        </p>
    </div>

    <div class="row g-3 mb-4">
        @foreach ([
            ['Total Records', $totalRecords, 'bi-calendar-check', 'primary'],
            ['Present', $presentCount, 'bi-check-circle', 'success'],
            ['Absent', $absentCount, 'bi-x-circle', 'danger'],
            ['Late', $lateCount, 'bi-clock-history', 'warning'],
        ] as [$label, $count, $icon, $color])
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary">{{ $label }}</span>
                            <span class="rounded-3 bg-{{ $color }}-subtle text-{{ $color }} p-2">
                                <i class="bi {{ $icon }} fs-5"></i>
                            </span>
                        </div>
                        <h2 class="fw-bold mt-3 mb-0">
                            {{ number_format($count) }}
                        </h2>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Attendance History</h5>
                    <p class="text-secondary small mb-0">
                        Your recorded attendance entries.
                    </p>
                </div>

                <div style="min-width: 180px">
                    <select wire:model.live="filterStatus"
                            class="form-select rounded-3">
                        <option value="">All statuses</option>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Class</th>
                            <th>Session</th>
                            <th>Term</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($attendanceRecords as $record)
                            <tr wire:key="attendance-{{ $record->id }}">
                                <td class="text-nowrap">
                                    {{ $record->attendance_date?->format('d M Y') ?? '—' }}
                                </td>

                                <td>
                                    {{ $record->classRoom?->name ?? '—' }}
                                </td>

                                <td>
                                    {{ $record->academicSession?->name ?? '—' }}
                                </td>

                                <td>
                                    {{ $record->term?->name ?? '—' }}
                                </td>

                                <td>
                                    @php
                                        $statusClass = match (strtolower($record->status)) {
                                            'present' => 'success',
                                            'absent' => 'danger',
                                            'late' => 'warning',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} px-3 py-2">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $record->remarks ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-calendar2-x display-5 text-secondary"></i>
                                    <h6 class="fw-bold mt-3">No attendance records yet</h6>
                                    <p class="text-secondary small mb-0">
                                        Your school attendance records will appear here when available.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $attendanceRecords->links() }}
            </div>
        </div>
    </div>
</div>
