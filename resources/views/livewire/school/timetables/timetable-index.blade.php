
<div class="container-fluid py-4">

    {{-- Page heading --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Timetable Management</h3>
            <p class="text-muted mb-0">
                Create and manage class schedules, subjects, and teachers.
            </p>
        </div>

        <button type="button" class="btn btn-primary"
                wire:click="create">
            <i class="bi bi-plus-lg me-1"></i>
            Add Timetable Entry
        </button>
    </div>

    {{-- Success message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form --}}
    @if ($showForm)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        {{ $editingId ? 'Edit Timetable Entry' : 'Create Timetable Entry' }}
                    </h5>

                    <button type="button" class="btn-close"
                            wire:click="cancel" aria-label="Close"></button>
                </div>
            </div>

            <div class="card-body p-4">
                <form wire:submit="save">
                    <div class="row g-3">

                        {{-- Academic session --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Academic Session <span class="text-danger">*</span>
                            </label>

                            <select wire:model="academic_session_id"
                                    class="form-select @error('academic_session_id') is-invalid @enderror">
                                <option value="">Select academic session</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}">
                                        {{ $session->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('academic_session_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Term --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Term</label>

                            <select wire:model="term_id"
                                    class="form-select @error('term_id') is-invalid @enderror">
                                <option value="">Select term (optional)</option>
                                @foreach ($terms as $term)
                                    <option value="{{ $term->id }}">
                                        {{ $term->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('term_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Class --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Class <span class="text-danger">*</span>
                            </label>

                            <select wire:model="class_room_id"
                                    class="form-select @error('class_room_id') is-invalid @enderror">
                                <option value="">Select class</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('class_room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Section --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Section</label>

                            <select wire:model="section_id"
                                    class="form-select @error('section_id') is-invalid @enderror">
                                <option value="">Select section (optional)</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}">
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('section_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Subject --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Subject <span class="text-danger">*</span>
                            </label>

                            <select wire:model="subject_id"
                                    class="form-select @error('subject_id') is-invalid @enderror">
                                <option value="">Select subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Teacher --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teacher</label>

                            <select wire:model="teacher_id"
                                    class="form-select @error('teacher_id') is-invalid @enderror">
                                <option value="">Select teacher (optional)</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">
                                        {{ trim($teacher->first_name.' '.($teacher->middle_name ?? '').' '.$teacher->last_name) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Day --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Day of Week <span class="text-danger">*</span>
                            </label>

                            <select wire:model="day_of_week"
                                    class="form-select @error('day_of_week') is-invalid @enderror">
                                @foreach ($days as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                @endforeach
                            </select>

                            @error('day_of_week')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Room --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Room / Venue</label>

                            <input type="text" wire:model="room"
                                   class="form-control @error('room') is-invalid @enderror"
                                   placeholder="e.g. Room 12 or Science Lab">

                            @error('room')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Start time --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Start Time <span class="text-danger">*</span>
                            </label>

                            <input type="time" wire:model="starts_at"
                                   class="form-control @error('starts_at') is-invalid @enderror">

                            @error('starts_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- End time --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                End Time <span class="text-danger">*</span>
                            </label>

                            <input type="time" wire:model="ends_at"
                                   class="form-control @error('ends_at') is-invalid @enderror">

                            @error('ends_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <hr class="my-4">

                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <button type="button" class="btn btn-light border"
                                wire:click="cancel">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-primary"
                                wire:loading.attr="disabled"
                                wire:target="save">
                            <span wire:loading.remove wire:target="save">
                                <i class="bi bi-check-lg me-1"></i>
                                {{ $editingId ? 'Update Timetable' : 'Save Timetable' }}
                            </span>

                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Search and filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">Search timetable</label>

                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>

                        <input type="search" wire:model.live.debounce.300ms="search"
                               class="form-control"
                               placeholder="Search subject, teacher, class, day or room...">
                    </div>
                </div>

                <div class="col-lg-3">
                    <label class="form-label fw-semibold">Filter by day</label>

                    <select wire:model.live="dayFilter" class="form-select">
                        <option value="">All days</option>
                        @foreach ($days as $day)
                            <option value="{{ $day }}">{{ $day }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3">
                    <label class="form-label fw-semibold">Filter by class</label>

                    <select wire:model.live="classFilter" class="form-select">
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
    </div>

    {{-- Timetable entries --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-calendar-week me-2 text-primary"></i>
                Timetable Entries
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Day & Time</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Room</th>
                        <th>Session / Term</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($entries as $entry)
                        <tr wire:key="timetable-{{ $entry->id }}">
                            <td class="ps-3">
                                <div class="fw-semibold">
                                    {{ $entry->day_of_week }}
                                </div>

                                <small class="text-muted">
                                    {{ \Illuminate\Support\Carbon::parse($entry->starts_at)->format('g:i A') }}
                                    –
                                    {{ \Illuminate\Support\Carbon::parse($entry->ends_at)->format('g:i A') }}
                                </small>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $entry->classRoom?->name ?? '—' }}
                                </div>

                                @if ($entry->section)
                                    <small class="text-muted">
                                        {{ $entry->section->name }}
                                    </small>
                                @endif
                            </td>

                            <td>{{ $entry->subject?->name ?? '—' }}</td>

                            <td>
                                @if ($entry->teacher)
                                    {{ trim($entry->teacher->first_name.' '.($entry->teacher->middle_name ?? '').' '.$entry->teacher->last_name) }}
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>

                            <td>{{ $entry->room ?: '—' }}</td>

                            <td>
                                <div>{{ $entry->academicSession?->name ?? '—' }}</div>
                                <small class="text-muted">
                                    {{ $entry->term?->name ?? 'No term' }}
                                </small>
                            </td>

                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-1">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            wire:click="edit({{ $entry->id }})"
                                            title="Edit timetable entry">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            wire:click="delete({{ $entry->id }})"
                                            wire:confirm="Are you sure you want to delete this timetable entry?"
                                            title="Delete timetable entry">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-calendar-x text-muted"
                                       style="font-size: 42px;"></i>
                                </div>

                                <h6 class="fw-bold">No timetable entries found</h6>

                                <p class="text-muted small mb-3">
                                    Create a timetable entry or change your search filters.
                                </p>

                                <button type="button" class="btn btn-primary btn-sm"
                                        wire:click="create">
                                    <i class="bi bi-plus-lg me-1"></i>
                                    Add First Entry
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($entries->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $entries->links() }}
            </div>
        @endif
    </div>

</div>
