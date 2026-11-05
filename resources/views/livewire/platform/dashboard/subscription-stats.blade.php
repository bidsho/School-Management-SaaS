
<div class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h5 class="fw-bold mb-1">Subscription Overview</h5>
            <p class="text-secondary small mb-0">
                Monitor subscription activity across all schools.
            </p>
        </div>

        <a href="{{ route('platform.subscriptions.index') }}"
           class="btn btn-outline-primary btn-sm">
            Manage Subscriptions
            <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row g-3">
        {{-- Total subscriptions --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary small fw-semibold">
                            Total Subscriptions
                        </span>
                        <span class="rounded-3 bg-primary bg-opacity-10 text-primary p-2">
                            <i class="bi bi-collection fs-5"></i>
                        </span>
                    </div>

                    <h3 class="fw-bold mb-1">
                        {{ number_format($stats['total']) }}
                    </h3>
                    <p class="text-secondary small mb-0">
                        All subscription records
                    </p>
                </div>
            </div>
        </div>

        {{-- Active subscriptions --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary small fw-semibold">
                            Active Subscriptions
                        </span>
                        <span class="rounded-3 bg-success bg-opacity-10 text-success p-2">
                            <i class="bi bi-check-circle fs-5"></i>
                        </span>
                    </div>

                    <h3 class="fw-bold mb-1">
                        {{ number_format($stats['active']) }}
                    </h3>
                    <p class="text-secondary small mb-0">
                        Records marked active
                    </p>
                </div>
            </div>
        </div>

        {{-- Expiring soon --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary small fw-semibold">
                            Expiring Soon
                        </span>
                        <span class="rounded-3 bg-warning bg-opacity-10 text-warning p-2">
                            <i class="bi bi-clock-history fs-5"></i>
                        </span>
                    </div>

                    <h3 class="fw-bold mb-1">
                        {{ number_format($stats['expiringSoon']) }}
                    </h3>
                    <p class="text-secondary small mb-0">
                        Active records ending within 30 days
                    </p>
                </div>
            </div>
        </div>

        {{-- Expired by date --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary small fw-semibold">
                            Past End Date
                        </span>
                        <span class="rounded-3 bg-danger bg-opacity-10 text-danger p-2">
                            <i class="bi bi-calendar-x fs-5"></i>
                        </span>
                    </div>

                    <h3 class="fw-bold mb-1">
                        {{ number_format($stats['expiredByDate']) }}
                    </h3>
                    <p class="text-secondary small mb-0">
                        Records with an elapsed end date
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Status breakdown --}}
    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-header bg-white border-0 px-4 pt-4">
            <h6 class="fw-bold mb-1">Subscription Status Breakdown</h6>
            <p class="text-secondary small mb-0">
                Counts based on the statuses stored in your database.
            </p>
        </div>

        <div class="card-body p-4">
            @if ($statusBreakdown->isNotEmpty())
                <div class="row g-3">
                    @foreach ($statusBreakdown as $item)
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <span class="text-secondary small">
                                        {{ $item->status ?: 'Unspecified' }}
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        {{ number_format($item->total) }}
                                    </span>
                                </div>

                                <div class="progress mt-3" style="height: 6px;">
                                    <div
                                        class="progress-bar"
                                        role="progressbar"
                                        style="width: {{ $stats['total'] > 0 ? ($item->total / $stats['total']) * 100 : 0 }}%;"
                                        aria-valuenow="{{ $item->total }}"
                                        aria-valuemin="0"
                                        aria-valuemax="{{ $stats['total'] }}"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-1 text-secondary"></i>
                    <p class="text-secondary mb-0 mt-2">
                        No subscription records have been created yet.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
