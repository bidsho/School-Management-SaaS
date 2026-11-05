
<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Subscription Plans</h5>
                    <p class="text-muted mb-0 small">
                        Manage the pricing and limits offered to schools.
                    </p>
                </div>

                <button type="button"
                        class="btn btn-primary"
                        wire:click="createPlan">
                    <i class="bi bi-plus-lg me-2"></i>
                    Create Plan
                </button>
            </div>

            <div class="mt-4">
                <input type="search"
                       class="form-control"
                       style="max-width: 380px"
                       placeholder="Search plans..."
                       wire:model.live.debounce.300ms="search">
            </div>
        </div>
    </div>

    @if ($showForm)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white p-4">
                <h5 class="fw-bold mb-1">
                    {{ $editingPlanId ? 'Edit Subscription Plan' : 'Create Subscription Plan' }}
                </h5>
                <p class="text-muted small mb-0">
                    Configure the price, billing interval, limits and included features.
                </p>
            </div>

            <form wire:submit="savePlan">
                <div class="card-body p-4">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Plan Name *</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   wire:model="name"
                                   placeholder="e.g. Professional">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Price (₦) *</label>
                            <input type="number"
                                   min="0"
                                   step="0.01"
                                   class="form-control @error('price') is-invalid @enderror"
                                   wire:model="price">
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Billing Interval *</label>
                            <select class="form-select @error('interval') is-invalid @enderror"
                                    wire:model="interval">
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                            @error('interval')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Plan Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    wire:model="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Student Limit</label>
                            <input type="number"
                                   min="1"
                                   class="form-control @error('student_limit') is-invalid @enderror"
                                   wire:model="student_limit"
                                   placeholder="Leave blank for unlimited">
                            @error('student_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Staff Limit</label>
                            <input type="number"
                                   min="1"
                                   class="form-control @error('staff_limit') is-invalid @enderror"
                                   wire:model="staff_limit"
                                   placeholder="Leave blank for unlimited">
                            @error('staff_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      wire:model="description"
                                      rows="2"
                                      placeholder="Describe this plan"></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Plan Features</label>
                            <textarea class="form-control @error('featuresText') is-invalid @enderror"
                                      wire:model="featuresText"
                                      rows="5"
                                      placeholder="Student management&#10;Attendance tracking&#10;Fee management"></textarea>
                            <div class="form-text">
                                Enter one feature per line.
                            </div>
                            @error('featuresText')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="card-footer bg-white p-3 d-flex justify-content-end gap-2">
                    <button type="button"
                            class="btn btn-light border"
                            wire:click="cancelForm">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="savePlan">
                            {{ $editingPlanId ? 'Save Changes' : 'Create Plan' }}
                        </span>
                        <span wire:loading wire:target="savePlan">
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Plan</th>
                        <th>Price</th>
                        <th>Limits</th>
                        <th>Subscribers</th>
                        <th>Status</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($plans as $plan)
                        <tr wire:key="subscription-plan-{{ $plan->id }}">
                            <td class="px-4 py-3">
                                <div class="fw-semibold">{{ $plan->name }}</div>
                                <div class="text-muted small">
                                    {{ $plan->description ?: 'No description' }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    ₦{{ number_format((float) $plan->price, 2) }}
                                </div>
                                <div class="text-muted small">
                                    {{ ucfirst($plan->interval) }}
                                </div>
                            </td>

                            <td>
                                <div class="small">
                                    Students:
                                    {{ $plan->student_limit ?? 'Unlimited' }}
                                </div>
                                <div class="small">
                                    Staff:
                                    {{ $plan->staff_limit ?? 'Unlimited' }}
                                </div>
                            </td>

                            <td>
                                <span class="badge text-bg-light border">
                                    {{ $plan->subscriptions_count }}
                                </span>
                            </td>

                            <td>
                                @if ($plan->status === 'active')
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">
                                        {{ ucfirst($plan->status) }}
                                    </span>
                                @endif
                            </td>

                            <td class="text-end px-4">
                                <div class="d-inline-flex gap-2">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            wire:click="editPlan({{ $plan->id }})">
                                        <i class="bi bi-pencil"></i>
                                        Edit
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            wire:click="toggleStatus({{ $plan->id }})"
                                            wire:confirm="Change the status of this plan?">
                                        {{ $plan->status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>

                                    @if ($plan->subscriptions_count === 0)
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="deletePlan({{ $plan->id }})"
                                                wire:confirm="Permanently delete this plan?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-tags fs-1 text-muted"></i>
                                <h6 class="mt-3">No subscription plans found</h6>
                                <p class="text-muted small mb-3">
                                    Create your first plan to get started.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($plans->hasPages())
            <div class="card-footer bg-white">
                {{ $plans->links() }}
            </div>
        @endif
    </div>
</div>
