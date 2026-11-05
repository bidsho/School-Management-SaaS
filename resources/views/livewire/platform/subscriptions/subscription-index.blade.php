
<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">All Subscriptions</h5>
                    <p class="text-muted small mb-0">
                        Monitor school plans, billing periods, and subscription status.
                    </p>
                </div>
                <div class="rounded-3 bg-primary-subtle text-primary p-3">
                    <i class="bi bi-credit-card-2-front fs-3"></i>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="search"
                           class="form-control"
                           placeholder="School, plan, email, or reference..."
                           wire:model.live.debounce.300ms="search">
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Status</label>
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="trial">Trial</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="expired">Expired</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Subscription Plan</label>
                    <select class="form-select" wire:model.live="planFilter">
                        <option value="">All plans</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}">
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="fw-bold mb-0">Subscription Records</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">School</th>
                        <th>Plan</th>
                        <th>Price</th>
                        <th>Period</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($subscriptions as $subscription)
                        <tr wire:key="subscription-{{ $subscription->id }}">
                            <td class="ps-4 py-3">
                                <div class="fw-semibold">
                                    {{ $subscription->school?->name ?? 'School unavailable' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $subscription->school?->email ?? 'No email' }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $subscription->plan?->name ?? 'Plan unavailable' }}
                                </div>
                                <div class="small text-muted">
                                    {{ ucfirst($subscription->plan?->interval ?? 'unknown') }}
                                </div>
                            </td>

                            <td>
                                @if ($subscription->plan)
                                    ₦{{ number_format((float) $subscription->plan->price, 2) }}
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                <div class="small">
                                    <span class="text-muted">Starts:</span>
                                    {{ $subscription->starts_at?->format('d M Y') ?? '—' }}
                                </div>
                                <div class="small">
                                    <span class="text-muted">Ends:</span>
                                    {{ $subscription->ends_at?->format('d M Y') ?? 'No end date' }}
                                </div>
                                @if ($subscription->trial_ends_at)
                                    <div class="small text-info">
                                        Trial ends:
                                        {{ $subscription->trial_ends_at->format('d M Y') }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                @php
                                    $statusClass = match (strtolower($subscription->status ?? '')) {
                                        'active' => 'success',
                                        'trial' => 'info',
                                        'pending' => 'warning',
                                        'cancelled', 'expired', 'suspended' => 'secondary',
                                        default => 'light',
                                    };
                                @endphp

                                <span class="badge text-bg-{{ $statusClass }}">
                                    {{ ucfirst($subscription->status ?? 'unknown') }}
                                </span>
                            </td>

                            <td class="text-end pe-4">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        wire:click="openStatusModal({{ $subscription->id }})">
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Manage
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-credit-card fs-1 text-muted"></i>
                                <h6 class="mt-3">No subscriptions found</h6>
                                <p class="text-muted small mb-0">
                                    Subscription records will appear here when created.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($subscriptions->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>

    @if ($showStatusModal)
        <div class="modal d-block"
             tabindex="-1"
             role="dialog"
             aria-modal="true"
             style="background: rgba(15, 23, 42, .5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Manage Subscription</h5>
                        <button type="button"
                                class="btn-close"
                                wire:click="closeStatusModal"></button>
                    </div>

                    <form wire:submit="updateStatus">
                        <div class="modal-body">
                            <p class="text-muted small">
                                Change the subscription status. This does not process a payment
                                or renew the subscription dates.
                            </p>

                            <label class="form-label">New Status</label>
                            <select class="form-select @error('newStatus') is-invalid @enderror"
                                    wire:model="newStatus">
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="trial">Trial</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="expired">Expired</option>
                                <option value="suspended">Suspended</option>
                            </select>

                            @error('newStatus')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-light border"
                                    wire:click="closeStatusModal">
                                Cancel
                            </button>

                            <button type="submit"
                                    class="btn btn-primary"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="updateStatus">
                                    Save Status
                                </span>
                                <span wire:loading wire:target="updateStatus">
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
