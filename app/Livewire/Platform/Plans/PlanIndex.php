<?php

namespace App\Livewire\Platform\Plans;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class PlanIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingPlanId = null;

    public string $name = '';
    public string $description = '';
    public string $price = '0.00';
    public string $interval = 'monthly';
    public string $student_limit = '';
    public string $staff_limit = '';
    public string $featuresText = '';
    public string $status = 'active';

    public bool $showForm = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function createPlan(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editPlan(int $id): void
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $this->editingPlanId = $plan->id;
        $this->name = $plan->name;
        $this->description = $plan->description ?? '';
        $this->price = (string) $plan->price;
        $this->interval = $plan->interval;
        $this->student_limit = $plan->student_limit === null
            ? ''
            : (string) $plan->student_limit;
        $this->staff_limit = $plan->staff_limit === null
            ? ''
            : (string) $plan->staff_limit;

        $this->featuresText = collect($plan->features ?? [])
            ->map(fn ($feature) => is_string($feature)
                ? $feature
                : json_encode($feature))
            ->implode("\n");

        $this->status = $plan->status;
        $this->showForm = true;
    }

    public function savePlan(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'interval' => [
                'required',
                Rule::in(['monthly', 'quarterly', 'yearly']),
            ],
            'student_limit' => ['nullable', 'integer', 'min:1'],
            'staff_limit' => ['nullable', 'integer', 'min:1'],
            'featuresText' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $features = collect(preg_split(
            '/\r\n|\r|\n/',
            $validated['featuresText'] ?? ''
        ))
            ->map(fn ($feature) => trim($feature))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?: null,
            'price' => $validated['price'],
            'interval' => $validated['interval'],
            'student_limit' => $validated['student_limit'] ?: null,
            'staff_limit' => $validated['staff_limit'] ?: null,
            'features' => $features,
            'status' => $validated['status'],
        ];

        if ($this->editingPlanId) {
            $plan = SubscriptionPlan::findOrFail($this->editingPlanId);
            $plan->update($data);

            session()->flash('success', 'Subscription plan updated.');
        } else {
            SubscriptionPlan::create($data);

            session()->flash('success', 'Subscription plan created.');
        }

        $this->resetForm();
        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $plan->update([
            'status' => $plan->status === 'active'
                ? 'inactive'
                : 'active',
        ]);

        session()->flash('success', 'Subscription plan status updated.');
    }

    public function deletePlan(int $id): void
    {
        $plan = SubscriptionPlan::findOrFail($id);

        if ($plan->subscriptions()->exists()) {
            session()->flash(
                'error',
                'This plan has subscription records and cannot be deleted. Deactivate it instead.'
            );

            return;
        }

        $plan->delete();

        session()->flash('success', 'Subscription plan deleted.');
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingPlanId',
            'name',
            'description',
            'price',
            'interval',
            'student_limit',
            'staff_limit',
            'featuresText',
            'status',
        ]);

        $this->price = '0.00';
        $this->interval = 'monthly';
        $this->status = 'active';
        $this->showForm = false;
        $this->resetValidation();
    }

    public function render()
    {
        $plans = SubscriptionPlan::query()
            ->withCount('subscriptions')
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    $subQuery
                        ->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view(
            'livewire.platform.plans.plan-index',
            compact('plans')
        )->extends('layouts.super');
    }
}
