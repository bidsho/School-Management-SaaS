
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Child's Timetable</h3>
            <p class="text-muted mb-0">
                View your child's weekly lessons, teachers, and classrooms.
            </p>
        </div>

        <span class="badge text-bg-primary px-3 py-2">
            <i class="bi bi-calendar-week me-1"></i>
            Weekly Schedule
        </span>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <label for="studentSelect" class="form-label fw-semibold">
                Select Child
            </label>

            <select
                id="studentSelect"
                class="form-select"
                wire:model.live="studentId"
            >
                @foreach ($children as $child)
                    <option value="{{ $child->id }}">
                        {{ $child->first_name }}
                        {{ $child->middle_name }}
                        {{ $child->last_name }}
                        ({{ $child->admission_number }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($children->isEmpty())
        <div class="alert alert-info">
            No children are currently linked to your parent account.
        </div>
    @elseif ($timetable->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                <h5 class="fw-bold mt-3">No timetable available</h5>
                <p class="text-muted mb-0">
                    Your child's timetable has not been published or assigned yet.
                    Please contact the school.
                </p>
            </div>
        </div>
    @else
        @php
            $days = [
                'Monday', 'Tuesday', 'Wednesday', 'Thursday',
                'Friday', 'Saturday', 'Sunday',
            ];
        @endphp

        @foreach ($days as $day)
            @if (isset($timetable[$day]) && $timetable[$day]->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-calendar-day text-primary me-2"></i>
                            {{ $day }}
                        </h5>
                    </div>

                    <div class="card-body px-4">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>Subject</th>
                                        <th>Teacher</th>
                                        <th>Classroom</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($timetable[$day] as $entry)
                                        <tr>
                                            <td class="text-nowrap">
                                                {{ \Illuminate\Support\Carbon::parse($entry->starts_at)->format('h:i A') }}
                                                –
                                                {{ \Illuminate\Support\Carbon::parse($entry->ends_at)->format('h:i A') }}
                                            </td>

                                            <td class="fw-semibold">
                                                {{ $entry->subject?->name ?? 'Subject not assigned' }}
                                            </td>

                                            <td>
                                                {{ $entry->teacher?->name
                                                    ?? $entry->teacher?->user?->name
                                                    ?? 'Not assigned' }}
                                            </td>

                                            <td>
                                                {{ $entry->room
                                                    ?? $entry->classRoom?->name
                                                    ?? 'Not specified' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

</div>
