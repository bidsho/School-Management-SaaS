
<div class="container-fluid py-4"
     x-data
     x-on:class-head-assignment-duplicate.window="window.alert($event.detail.message)">

    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Class Head Teachers</h3>
            <p class="text-muted mb-0">
                Assign and manage teachers responsible for each class.
            </p>
        </div>

        <button type="button"
                class="btn btn-primary"
                wire:click="openCreateModal">
            <i class="bi bi-person-plus me-1"></i>
            Assign Class Head Teacher
        </button>
    </div>

    {{-- Notifications --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Statistics --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary-subtle text-primary p-3">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Assignments</div>
                        <h4 class="fw-bold mb-0">{{ $totalAssignments }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-success-subtle text-success p-3">
                        <i class="bi bi-person-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Active Assignments</div>
                        <h4 class="fw-bold mb-0">{{ $activeAssignments }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-secondary-subtle text-secondary p-3">
                        <i class="bi bi-person-dash fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Inactive Assignments</div>
                        <h4 class="fw-bold mb-0">{{ $inactiveAssignments }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Assignment table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <h5 class="fw-bold mb-0">Assignment Records</h5>

                <div style="max-width: 350px; width: 100%;">
                    <input type="search"
                           class="form-control"
                           placeholder="Search teacher, class or session..."
                           wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Class Head Teacher</th>
                            <th>Class</th>
                            <th>Academic Session</th>
                            <th>Assignment Period</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($assignments as $assignment)
                            <tr wire:key="class-head-{{ $assignment->id }}">
                                <td class="ps-3">
                                    {{ $assignments->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $assignment->teacher?->first_name }}
                                        {{ $assignment->teacher?->last_name }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $assignment->teacher?->staff_number ?? 'No staff number' }}
                                    </small>
                                </td>

                                <td>
                                    <span class="fw-semibold">
                                        {{ $assignment->classRoom?->name ?? 'Class unavailable' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $assignment->academicSession?->name ?? 'Session unavailable' }}
                                </td>

                                <td>
                                    <small>
                                        {{ $assignment->starts_at?->format('d M Y') ?? 'Not set' }}
                                        <br>
                                        to
                                        {{ $assignment->ends_at?->format('d M Y') ?? 'Not set' }}
                                    </small>
                                </td>

                                <td>
                                    @if ($assignment->status === 'active')
                                        <span class="badge rounded-pill text-bg-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge rounded-pill text-bg-secondary">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end pe-3">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            wire:click="edit({{ $assignment->id }})">
                                        <i class="bi bi-pencil-square"></i>
                                        Edit
                                    </button>

                                    @if ($assignment->status === 'active')
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="deactivate({{ $assignment->id }})"
                                                wire:confirm="Deactivate this class head teacher assignment?">
                                            Deactivate
                                        </button>
                                    @else
                                        <button type="button"
                                                class="btn btn-sm btn-outline-success"
                                                wire:click="reactivate({{ $assignment->id }})"
                                                wire:confirm="Reactivate this assignment?">
                                            Reactivate
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-person-plus fs-1 text-muted"></i>
                                    <h6 class="fw-bold mt-3">No assignments found</h6>
                                    <p class="text-muted mb-3">
                                        Assign a teacher to a class to get started.
                                    </p>
                                    <button class="btn btn-primary"
                                            wire:click="openCreateModal">
                                        Assign Class Head Teacher
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($assignments->hasPages())
            <div class="card-footer bg-white">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit modal --}}
    @if ($showModal)
        <div class="modal fade show d-block"
             tabindex="-1"
             role="dialog"
             aria-modal="true"
             aria-labelledby="classHeadTeacherModalTitle"
             style="background: rgba(0,0,0,.5);"
             wire:keydown.escape.window="$set('showModal', false)">

            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">

                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold"
                                id="classHeadTeacherModalTitle">
                                {{ $editingId ? 'Edit Class Head Teacher' : 'Assign Class Head Teacher' }}
                            </h5>
                            <p class="text-muted small mb-0">
                                Search and select the teacher, class and academic session.
                            </p>
                        </div>

                        <button type="button"
                                class="btn-close"
                                wire:click="$set('showModal', false)"
                                aria-label="Close"></button>
                    </div>

                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="row g-3">

                                {{-- Searchable teacher selection --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Teacher <span class="text-danger">*</span>
                                    </label>

                                    @if ($selectedTeacher)
                                        <div class="border rounded-3 p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center gap-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                                         style="width: 46px; height: 46px;">
                                                        <i class="bi bi-person-fill fs-4"></i>
                                                    </div>

                                                    <div>
                                                        <div class="fw-bold">
                                                            {{ $selectedTeacher->first_name }}
                                                            {{ $selectedTeacher->last_name }}
                                                        </div>
                                                        <small class="text-muted">
                                                            Staff No:
                                                            {{ $selectedTeacher->staff_number ?? 'Not available' }}
                                                        </small>
                                                    </div>
                                                </div>

                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        wire:click="clearTeacher">
                                                    <i class="bi bi-x-circle me-1"></i>
                                                    Change
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="position-relative">
                                            <div class="input-group">
                                                <span class="input-group-text bg-white">
                                                    <i class="bi bi-search"></i>
                                                </span>

                                                <input type="search"
                                                       class="form-control"
                                                       placeholder="Search teacher by name or staff number..."
                                                       autocomplete="off"
                                                       wire:model.live.debounce.300ms="teacherSearch">
                                            </div>

                                            @if ($teacherSearch !== '')
                                                <div class="form-text">
                                                    Search results for "{{ $teacherSearch }}"
                                                </div>
                                            @endif

                                            <div class="border rounded-3 mt-2 shadow-sm"
                                                 style="max-height: 240px; overflow-y: auto;">

                                                @forelse ($teachers as $teacher)
                                                    <button type="button"
                                                            wire:key="teacher-option-{{ $teacher->id }}"
                                                            wire:click="selectTeacher({{ $teacher->id }})"
                                                            class="btn btn-light w-100 text-start rounded-0 border-bottom p-3">

                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                                                 style="width: 38px; height: 38px;">
                                                                <i class="bi bi-person-fill"></i>
                                                            </div>

                                                            <div>
                                                                <div class="fw-semibold">
                                                                    {{ $teacher->first_name }}
                                                                    {{ $teacher->last_name }}
                                                                </div>

                                                                <small class="text-muted">
                                                                    Staff No:
                                                                    {{ $teacher->staff_number ?? 'Not available' }}
                                                                </small>
                                                            </div>

                                                            <i class="bi bi-arrow-right ms-auto text-muted"></i>
                                                        </div>
                                                    </button>
                                                @empty
                                                    <div class="text-center text-muted p-4">
                                                        <i class="bi bi-person-x fs-3"></i>
                                                        <p class="mb-0 mt-2">
                                                            @if ($teacherSearch !== '')
                                                                No teacher found matching your search.
                                                            @else
                                                                No teachers available. Please create a teacher first.
                                                            @endif
                                                        </p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endif

                                    @error('teacher_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Class --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Class <span class="text-danger">*</span>
                                    </label>

                                    <select class="form-select"
                                            wire:model="class_room_id">
                                        <option value="">Select class</option>

                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('class_room_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Academic Session --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Academic Session <span class="text-danger">*</span>
                                    </label>

                                    <select class="form-select"
                                            wire:model="academic_session_id">
                                        <option value="">Select academic session</option>

                                        @foreach ($sessions as $session)
                                            <option value="{{ $session->id }}">
                                                {{ $session->name }}
                                                @if ($session->status === 'active')
                                                    (Active)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('academic_session_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Start Date --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Start Date
                                    </label>

                                    <input type="date"
                                           class="form-control"
                                           wire:model="starts_at">

                                    @error('starts_at')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- End Date --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        End Date
                                    </label>

                                    <input type="date"
                                           class="form-control"
                                           wire:model="ends_at">

                                    @error('ends_at')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            @if ($errors->has('class_room_id'))
                                <div class="alert alert-danger mt-3 mb-0">
                                    {{ $errors->first('class_room_id') }}
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-light"
                                    wire:click="$set('showModal', false)">
                                Cancel
                            </button>

                            <button type="submit"
                                    class="btn btn-primary"
                                    wire:loading.attr="disabled"
                                    wire:target="save">

                                <span wire:loading.remove wire:target="save">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ $editingId ? 'Update Assignment' : 'Save Assignment' }}
                                </span>

                                <span wire:loading wire:target="save">
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

</div>
