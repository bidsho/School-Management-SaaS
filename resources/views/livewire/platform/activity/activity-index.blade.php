
<div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Platform Activity Logs</h5>
                    <p class="text-muted small mb-0">
                        Review recorded actions across your school management platform.
                    </p>
                </div>

                <div class="rounded-3 bg-primary-subtle text-primary p-3">
                    <i class="bi bi-clock-history fs-3"></i>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-lg-4">
                    <label class="form-label small fw-semibold">Search activity</label>
                    <input type="search"
                           class="form-control"
                           placeholder="Action, user, school, IP..."
                           wire:model.live.debounce.300ms="search">
                </div>

                <div class="col-lg-3">
                    <label class="form-label small fw-semibold">Action</label>
                    <select class="form-select" wire:model.live="actionFilter">
                        <option value="">All actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}">{{ $action }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3">
                    <label class="form-label small fw-semibold">School</label>
                    <select class="form-select" wire:model.live="schoolFilter">
                        <option value="">All schools</option>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}">
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label small fw-semibold">Date range</label>
                    <select class="form-select" wire:model.live="dateFilter">
                        <option value="">All time</option>
                        <option value="today">Today</option>
                        <option value="7days">Last 7 days</option>
                        <option value="30days">Last 30 days</option>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="button"
                        class="btn btn-sm btn-outline-secondary"
                        wire:click="clearFilters">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="fw-bold mb-1">Activity History</h5>
            <p class="text-muted small mb-0">
                {{ number_format($activities->total()) }} recorded event(s)
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Action</th>
                        <th>User</th>
                        <th>School</th>
                        <th>Record</th>
                        <th>IP Address</th>
                        <th>Date</th>
                        <th class="pe-4">Details</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($activities as $activity)
                        <tr wire:key="activity-{{ $activity->id }}">
                            <td class="ps-4">
                                <span class="badge text-bg-light border">
                                    {{ $activity->action ?: 'Unknown action' }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $activity->actor_name ?? 'Deleted or unknown user' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $activity->actor_email ?? '' }}
                                </div>
                            </td>

                            <td>
                                {{ $activity->school_name ?? 'Platform-level / unassigned' }}
                            </td>

                            <td>
                                <div class="small">
                                    {{ $activity->model_type
                                        ? class_basename($activity->model_type)
                                        : '—' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $activity->model_id
                                        ? '#' . $activity->model_id
                                        : '' }}
                                </div>
                            </td>

                            <td>
                                <span class="small">
                                    {{ $activity->ip_address ?: '—' }}
                                </span>
                            </td>

                            <td>
                                <div class="small">
                                    {{ $activity->created_at?->format('d M Y') ?? '—' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $activity->created_at?->format('h:i A') ?? '' }}
                                </div>
                            </td>

                            <td class="pe-4">
                                @php
                                    $metadata = $activity->metadata;

                                    if (is_string($metadata)) {
                                        $metadata = json_decode($metadata, true);
                                    }
                                @endphp

                                @if (is_array($metadata) && count($metadata))
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#activity-details-{{ $activity->id }}"
                                            aria-expanded="false">
                                        View
                                    </button>
                                @else
                                    <span class="text-muted small">No details</span>
                                @endif
                            </td>
                        </tr>

                        @if (is_array($metadata ?? null) && count($metadata))
                            <tr class="bg-light">
                                <td colspan="7" class="px-4 py-0 border-0">
                                    <div class="collapse py-3"
                                         id="activity-details-{{ $activity->id }}">
                                        <div class="small fw-semibold mb-2">
                                            Activity metadata
                                        </div>

                                        <pre class="bg-white border rounded p-3 mb-0 small text-wrap">{{ json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

                                        @if ($activity->user_agent)
                                            <div class="small text-muted mt-2">
                                                User agent: {{ $activity->user_agent }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-journal-text fs-1 text-muted"></i>
                                <h6 class="mt-3">No activity found</h6>
                                <p class="text-muted small mb-0">
                                    Try changing your filters or search terms.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($activities->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
