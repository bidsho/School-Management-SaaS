<?php

namespace App\Livewire\Platform\Subscriptions;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class SubscriptionIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $planFilter = '';

    public ?int $selectedSubscriptionId = null;

    public string $newStatus = '';

    public bool $showStatusModal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPlanFilter(): void
    {
        $this->resetPage();
    }

    public function openStatusModal(int $id): void
    {
        $subscription = Subscription::findOrFail($id);

        $this->selectedSubscriptionId = $subscription->id;
        $this->newStatus = $subscription->status;
        $this->showStatusModal = true;
    }

    public function updateStatus(): void
    {
        $validated = $this->validate([
            'selectedSubscriptionId' => [
                'required',
                'integer',
                'exists:subscriptions,id',
            ],
            'newStatus' => [
                'required',
                'string',
                Rule::in([
                    'active',
                    'pending',
                    'trial',
                    'cancelled',
                    'expired',
                    'suspended',
                ]),
            ],
        ]);

        $subscription = Subscription::findOrFail(
            $validated['selectedSubscriptionId']
        );

        // Update status only. Do not claim payment or renewal.
        $subscription->update([
            'status' => $validated['newStatus'],
        ]);

        $this->closeStatusModal();

        session()->flash(
            'success',
            'Subscription status updated successfully.'
        );
    }

    public function closeStatusModal(): void
    {
        $this->showStatusModal = false;
        $this->selectedSubscriptionId = null;
        $this->newStatus = '';

        $this->resetValidation();
    }

    public function render()
    {
        $subscriptions = Subscription::query()
            ->with(['school', 'plan'])
            ->when($this->search !== '', function (Builder $query) {
                $search = '%' . $this->search . '%';

                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery
                        ->whereHas('school', function (Builder $schoolQuery) use ($search) {
                            $schoolQuery
                                ->where('name', 'like', $search)
                                ->orWhere('email', 'like', $search);
                        })
                        ->orWhereHas('plan', function (Builder $planQuery) use ($search) {
                            $planQuery->where('name', 'like', $search);
                        })
                        ->orWhere('provider_reference', 'like', $search);
                });
            })
            ->when($this->statusFilter !== '', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->planFilter !== '', function (Builder $query) {
                $query->where(
                    'subscription_plan_id',
                    $this->planFilter
                );
            })
            ->latest()
            ->paginate(10);

        $plans = \App\Models\SubscriptionPlan::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view(
            'livewire.platform.subscriptions.subscription-index',
            compact('subscriptions', 'plans')
        )->extends('layouts.super');
    }
}
