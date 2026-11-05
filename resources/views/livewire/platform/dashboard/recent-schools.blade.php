
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 px-4 pt-4 pb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div
                    class="rounded-3 bg-primary bg-opacity-10 text-primary
                           d-flex align-items-center justify-content-center"
                    style="width: 48px; height: 48px;"
                >
                    <i class="bi bi-buildings fs-4"></i>
                </div>

                <div>
                    <h5 class="fw-bold mb-1">Recent Schools</h5>
                    <p class="text-secondary small mb-0">
                        The latest schools registered on your platform.
                    </p>
                </div>
            </div>

            <a href="{{ route('platform.schools.index') }}"
               class="btn btn-outline-primary btn-sm">
                View All Schools
                <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        @if ($schools->isNotEmpty())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary small">School</th>
                            <th class="py-3 text-secondary small">Subscription Plan</th>
                            <th class="py-3 text-secondary small">Status</th>
                            <th class="py-3 text-secondary small">Date Registered</th>
                            <th class="pe-4 py-3 text-end text-secondary small">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($schools as $school)
                            @php
                                $status = strtolower($school->status ?? 'unknown');

                                $statusClass = match ($status) {
                                    'active' => 'success',
                                    'pending' => 'warning',
                                    'suspended', 'inactive' => 'danger',
                                    default => 'secondary',
                                };

                                $plan = $school->subscription?->plan;
                            @endphp

                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        @if ($school->logo)
                                            <img
                                                src="{{ asset('storage/' . $school->logo) }}"
                                                alt="{{ $school->name }} logo"
                                                class="rounded-3 border"
                                                style="width: 44px; height: 44px; object-fit: cover;"
                                            >
                                        @else
                                            <div
                                                class="rounded-3 bg-primary bg-opacity-10
                                                       text-primary fw-bold
                                                       d-flex align-items-center justify-content-center"
                                                style="width: 44px; height: 44px; flex-shrink: 0;"
                                            >
                                                {{ strtoupper(substr($school->name, 0, 1)) }}
                                            </div>
                                        @endif

                                        <div>
                                            <div class="fw-semibold text-dark">
                                                {{ $school->name }}
                                            </div>
                                            <div class="text-secondary small">
                                                {{ $school->email ?: 'No email provided' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3">
                                    @if ($plan)
                                        <span class="fw-semibold">
                                            {{ $plan->name }}
                                        </span>
                                    @else
                                        <span class="text-secondary small">
                                            No plan assigned
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3">
                                    <span class="badge rounded-pill bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} px-3 py-2">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>

                                <td class="py-3">
                                    <div class="small fw-medium">
                                        {{ $school->created_at?->format('M d, Y') ?? '—' }}
                                    </div>
                                    <div class="text-secondary small">
                                        {{ $school->created_at?->format('h:i A') ?? '' }}
                                    </div>
                                </td>

                                <td class="pe-4 py-3 text-end">
                                    <a
                                        href="{{ route('platform.schools.edit', $school) }}"
                                        class="btn btn-sm btn-light border"
                                        title="Manage {{ $school->name }}"
                                    >
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5 px-3">
                <div
                    class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3"
                    style="width: 72px; height: 72px;"
                >
                    <i class="bi bi-buildings text-secondary fs-2"></i>
                </div>

                <h6 class="fw-bold">No schools registered yet</h6>
                <p class="text-secondary small mb-3">
                    Newly registered schools will appear here.
                </p>

                <a href="{{ route('platform.schools.create') }}"
                   class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>
                    Add First School
                </a>
            </div>
        @endif
    </div>

    @if ($schools->isNotEmpty())
        <div class="card-footer bg-white border-0 px-4 py-3">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-secondary small">
                    Showing {{ $schools->count() }} most recently registered schools
                </span>

                <a href="{{ route('platform.schools.index') }}"
                   class="small text-decoration-none fw-semibold">
                    Manage schools
                    <i class="bi bi-arrow-up-right ms-1"></i>
                </a>
            </div>
        </div>
    @endif
</div>
