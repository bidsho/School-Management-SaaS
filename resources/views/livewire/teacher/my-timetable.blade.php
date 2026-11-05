
<div class="container-fluid py-4">

    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Timetable</h3>
            <p class="text-muted mb-0">
                View your teaching schedule, classes and lesson times.
            </p>
        </div>

        <button
            type="button"
            class="btn btn-outline-primary"
            onclick="window.print()"
        >
            <i class="bi bi-printer me-2"></i>Print Timetable
        </button>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
                        <i class="bi bi-calendar-week fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Scheduled Lessons</div>
                        <h3 class="fw-bold mb-0">
                            {{ $timetable->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3">
                        <i class="bi bi-book fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Subjects</div>
                        <h3 class="fw-bold mb-0">
                            {{ $timetable->pluck('subject_id')->filter()->unique()->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-info bg-opacity-10 text-info p-3">
                        <i class="bi bi-easel fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Classes</div>
                        <h3 class="fw-bold mb-0">
                            {{ $timetable->pluck('class_room_id')->filter()->unique()->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Session and term information --}}
    @if ($timetable->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Academic Session</div>
                        <div class="fw-semibold">
                            {{ $timetable->first()->academicSession?->name
                                ?? $timetable->first()->academicSession?->session_name
                                ?? 'Not specified' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">Term</div>
                        <div class="fw-semibold">
                            {{ $timetable->first()->term?->name
                                ?? $timetable->first()->term?->term_name
                                ?? 'Not specified' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Timetable --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-calendar3 me-2 text-primary"></i>
                Weekly Teaching Schedule
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Day</th>
                            <th>Time</th>
                            <th>Subject</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Room</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($timetable as $entry)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge text-bg-primary">
                                        {{ ucfirst((string) $entry->day_of_week) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $entry->starts_at
                                            ? \Illuminate\Support\Carbon::parse($entry->starts_at)->format('g:i A')
                                            : '—' }}
                                    </div>
                                    <div class="text-muted small">
                                        to
                                        {{ $entry->ends_at
                                            ? \Illuminate\Support\Carbon::parse($entry->ends_at)->format('g:i A')
                                            : '—' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $entry->subject?->name
                                            ?? $entry->subject?->subject_name
                                            ?? 'Subject not found' }}
                                    </div>
                                </td>

                                <td>
                                    {{ $entry->classRoom?->name
                                        ?? $entry->classRoom?->class_name
                                        ?? '—' }}
                                </td>

                                <td>
                                    {{ $entry->section?->name
                                        ?? $entry->section?->section_name
                                        ?? 'Not assigned' }}
                                </td>

                                <td>
                                    {{ $entry->room ?: 'Not specified' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-calendar-x fs-1 text-muted"></i>

                                    <h5 class="fw-bold mt-3">
                                        No timetable available
                                    </h5>

                                    <p class="text-muted mb-0">
                                        Your school administrator needs to add
                                        timetable entries and assign them to you.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .container-fluid,
        .container-fluid * {
            visibility: visible;
        }

        .container-fluid {
            position: absolute;
            inset: 0;
            width: 100%;
        }

        button,
        .btn,
        nav,
        aside {
            display: none !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>

</div>

