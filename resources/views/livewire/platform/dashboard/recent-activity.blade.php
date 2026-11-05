
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 px-4 pt-4 pb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div
                    class="rounded-3 bg-info bg-opacity-10 text-info
                           d-flex align-items-center justify-content-center"
                    style="width: 48px; height: 48px;"
                >
                    <i class="bi bi-activity fs-4"></i>
                </div>

                <div>
                    <h5 class="fw-bold mb-1">Recent Activity</h5>
                    <p class="text-secondary small mb-0">
                        Latest recorded actions across your platform.
                    </p>
                </div>
            </div>

            <a href="{{ route('platform.activity.index') }}"
               class="btn btn-outline-primary btn-sm">
                View All Activity
                <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        @if ($activities->isNotEmpty())
            <div class="list-group list-group-flush">
                @foreach ($activities as $activity)
                    @php
                        $action = strtolower((string) $activity->action);

                        $icon = match (true) {
                            str_contains($action, 'delete') => 'bi-trash',
                            str_contains($action, 'create'),
                            str_contains($action, 'store') => 'bi-plus-circle',
                            str_contains($action, 'update'),
                            str_contains($action, 'edit') => 'bi-pencil-square',
                            str_contains($action, 'login') => 'bi-box-arrow-in-right',
                            str_contains($action, 'payment') => 'bi-credit-card',
                            default => 'bi-record-circle',
                        };

                        $color = match (true) {
                            str_contains($action, 'delete') => 'danger',
                            str_contains($action, 'create'),
                            str_contains($action, 'store') => 'success',
                            str_contains($action, 'update'),
                            str_contains($action, 'edit') => 'primary',
                            str_contains($action, 'login') => 'info',
                            default => 'secondary',
                        };

                        $modelName = $activity->model_type
                            ? class_basename($activity->model_type)
                            : null;
                    @endphp

                    <div class="list-group-item border-0 border-bottom px-4 py-3">
                        <div class="d-flex align-items-start gap-3">
                            <div
                                class="rounded-circle bg-{{ $color }} bg-opacity-10
                                       text-{{ $color }}
                                       d-flex align-items-center justify-content-center"
                                style="width: 42px; height: 42px; flex-shrink: 0;"
                            >
                                <i class="bi {{ $icon }} fs-5"></i>
                            </div>

                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-semibold text-dark">
                                            {{ ucwords(str_replace(['_', '-'], ' ', $activity->action ?: 'Activity')) }}
                                        </div>

                                        <div class="text-secondary small mt-1">
                                            <i class="bi bi-person me-1"></i>
                                            {{ $activity->actor_name ?: 'Unknown user' }}

                                            @if ($activity->actor_email)
                                                <span class="mx-1">·</span>
                                                {{ $activity->actor_email }}
                                            @endif
                                        </div>
                                    </div>

                                    <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} rounded-pill">
                                        {{ ucfirst($color === 'secondary' ? 'Activity' : $color) }}
                                    </span>
                                </div>

                                <div class="d-flex flex-wrap gap-3 mt-2 text-secondary small">
                                    @if ($modelName)
                                        <span>
                                            <i class="bi bi-database me-1"></i>
                                            {{ $modelName }}
                                            @if ($activity->model_id)
                                                #{{ $activity->model_id }}
                                            @endif
                                        </span>
                                    @endif

                                    @if ($activity->school_name)
                                        <span>
                                            <i class="bi bi-building me-1"></i>
                                            {{ $activity->school_name }}
                                        </span>
                                    @endif

                                    <span>
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Illuminate\Support\Carbon::parse($activity->created_at)->format('M d, Y h:i A') }}
                                    </span>
                                </div>

                                @if ($activity->metadata)
                                    <details class="mt-2">
                                        <summary class="small text-primary" style="cursor: pointer;">
                                            View activity details
                                        </summary>

                                        <pre class="small bg-light rounded-3 p-3 mt-2 mb-0 text-wrap">{{ is_array($activity->metadata) ? json_encode($activity->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : (json_encode(json_decode($activity->metadata, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: $activity->metadata) }}</pre>
                                    </details>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 px-3">
                <div
                    class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3"
                    style="width: 72px; height: 72px;"
                >
                    <i class="bi bi-clock-history text-secondary fs-2"></i>
                </div>

                <h6 class="fw-bold">No activity recorded yet</h6>
                <p class="text-secondary small mb-0">
                    Recorded platform actions will appear here.
                </p>
            </div>
        @endif
    </div>

    @if ($activities->isNotEmpty())
        <div class="card-footer bg-white border-0 px-4 py-3">
            <div class="d-flex justify-content-between align-items-center gap-2">
                <span class="text-secondary small">
                    Showing {{ $activities->count() }} recent records
                </span>

                <a href="{{ route('platform.activity.index') }}"
                   class="text-decoration-none small fw-semibold">
                    Open activity logs
                    <i class="bi bi-arrow-up-right ms-1"></i>
                </a>
            </div>
        </div>
    @endif
</div>
