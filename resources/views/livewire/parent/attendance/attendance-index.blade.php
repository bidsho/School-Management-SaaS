
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Student Attendance</h2>
            <p class="text-muted mb-0">
                View your child's attendance history and daily status.
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
            @php
                $cards = [
                    ['label' => 'Total Records', 'key' => 'total', 'icon' => 'calendar3', 'color' => 'primary'],
                    ['label' => 'Present', 'key' => 'present', 'icon' => 'check-circle', 'color' => 'success'],
                    ['label' => 'Absent', 'key' => 'absent', 'icon' => 'x-circle', 'color' => 'danger'],
                    ['label' => 'Late', 'key' => 'late', 'icon' => 'clock-history', 'color' => 'warning'],
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="text-muted small">
                                    {{ $card['label'] }}
                                </span>
                                <i class="bi bi-{{ $card['icon'] }} text-{{ $card['color'] }} fs-5"></i>
                            </div>

                            <h3 class="fw-bold mb-0">
                                {{ $summary[$card['key']] }}
                            </h3>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Attendance Rate</span>
                            <span class="fw-bold text-primary">
                                {{ number_format($summary['percentage'], 1) }}%
                            </span>
                        </div>

                        <div class="progress rounded-pill" style="height: 10px;">
                            <div class="progress-bar"
                                 role="progressbar"
                                 style="width: {{ min(100, $summary['percentage']) }}%"
                                 aria-valuenow="{{ $summary['percentage'] }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>

                        <small class="text-muted d-block mt-2">
                            Calculated from recorded attendance in the selected date range.
                            Present and late records count as attended.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Filter Attendance</h5>

                <form wire:submit="applyFilters">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">From Date</label>
                            <input type="date"
                                   class="form-control rounded-3"
                                   wire:model="fromDate">
                            @error('fromDate')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">To Date</label>
                            <input type="date"
                                   class="form-control rounded-3"
                                   wire:model="toDate">
                            @error('toDate')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Attendance Status</label>
                            <select class="form-select rounded-3" wire:model="status">
                                <option value="all">All statuses</option>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                                <option value="excused">Excused</option>
                            </select>
                            @error('status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary rounded-3">
                                <i class="bi bi-funnel me-1"></i>
                                Apply Filters
                            </button>

                            <button type="button"
                                    wire:click="resetFilters"
                                    class="btn btn-light border rounded-3">
                                Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-1">Attendance History</h5>
                    <p class="text-muted small mb-0">
                        Records are displayed from newest to oldest.
                    </p>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Class</th>
                                <th class="py-3">Section</th>
                                <th class="py-3">Term</th>
                                <th class="py-3">Remarks</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($attendance as $record)
                                @php
                                    $statusClasses = [
                                        'present' => 'success',
                                        'absent' => 'danger',
                                        'late' => 'warning',
                                        'excused' => 'info',
                                    ];

                                    $statusClass = $statusClasses[strtolower($record->status)] ?? 'secondary';
                                @endphp

                                <tr wire:key="attendance-{{ $record->id }}">
                                    <td class="px-4 py-3 fw-semibold text-nowrap">
                                        {{ $record->attendance_date?->format('d M Y') ?? '—' }}
                                    </td>

                                    <td>
                                        <span class="badge rounded-pill text-bg-{{ $statusClass }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $record->classRoom?->name ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $record->section?->name ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $record->term?->name ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $record->remarks ?: '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-calendar-x fs-2 text-muted"></i>
                                        <h6 class="mt-3">No attendance records found</h6>
                                        <p class="text-muted small mb-0">
                                            Attendance may not have been marked for this child
                                            or there are no records matching your filters.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($attendance instanceof \Illuminate\Pagination\AbstractPaginator)
                    <div class="p-3">
                        {{ $attendance->links() }}
                    </div>
                @endif
            </div>
        </div>

    @endif
</div>
