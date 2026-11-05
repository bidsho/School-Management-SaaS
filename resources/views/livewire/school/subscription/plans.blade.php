
<div class="container-fluid py-4">

    {{-- =====================================================
        SUCCESS AND ERROR MESSAGES
    ====================================================== --}}

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"
             role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"
             role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

        <div>
            <h3 class="fw-bold mb-1">
                Subscription Plans
            </h3>

            <p class="text-muted mb-0">
                Manage your school's subscription and choose the right plan.
            </p>
        </div>

        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
            <i class="bi bi-shield-check me-1"></i>
            School Subscription
        </span>

    </div>


    {{-- =====================================================
        CURRENT SUBSCRIPTION
    ====================================================== --}}

    @if($currentSubscription && $currentSubscription->plan)

        <div class="card border-0 shadow-sm rounded-4 mb-4">

            <div class="card-body p-4">

                <div class="row align-items-center g-3">

                    <div class="col-md-8">

                        <div class="d-flex align-items-center gap-3">

                            <div class="rounded-3 bg-success-subtle text-success
                                        d-flex align-items-center justify-content-center"
                                 style="width:55px;height:55px;">

                                <i class="bi bi-check-circle-fill fs-3"></i>

                            </div>

                            <div>

                                <span class="text-muted small fw-semibold">
                                    CURRENT SUBSCRIPTION
                                </span>

                                <h5 class="fw-bold mb-1 mt-1">
                                    {{ $currentSubscription->plan->name }}
                                </h5>

                                <p class="text-muted mb-0 small">
                                    @if($currentSubscription->ends_at)
                                        Expires:
                                        {{ $currentSubscription->ends_at->format('d M Y') }}
                                    @else
                                        No expiry date specified
                                    @endif
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4 text-md-end">

                        <span class="badge bg-success px-3 py-2 rounded-pill">
                            {{ ucfirst($currentSubscription->status) }}
                        </span>

                    </div>

                </div>

            </div>

        </div>

    @endif


    {{-- =====================================================
        SUBSCRIPTION PLANS
    ====================================================== --}}

    <div class="row g-4">

        @forelse($plans as $plan)

            @php

                $isSelected = (int) $selectedPlanId === (int) $plan->id;

                $isCurrent = $currentSubscription
                    && (int) $currentSubscription->subscription_plan_id
                        === (int) $plan->id;

                $features = is_array($plan->features)
                    ? $plan->features
                    : [];

            @endphp

            <div class="col-xl-4 col-lg-6 col-md-6">

                <div class="card h-100 border-0 shadow-sm rounded-4
                    {{ $isSelected ? 'subscription-selected' : '' }}">

                    <div class="card-body p-4 d-flex flex-column">

                        {{-- Plan Name --}}

                        <div class="d-flex justify-content-between
                                    align-items-center mb-3">

                            <h5 class="fw-bold mb-0">
                                {{ $plan->name }}
                            </h5>

                            @if($isCurrent)

                                <span class="badge bg-success rounded-pill">
                                    Current Plan
                                </span>

                            @elseif($isSelected)

                                <span class="badge bg-primary rounded-pill">
                                    Selected
                                </span>

                            @endif

                        </div>


                        {{-- Description --}}

                        <p class="text-muted mb-4">
                            {{ $plan->description ?: 'A subscription plan designed for your school.' }}
                        </p>


                        {{-- Price --}}

                        <div class="mb-4">

                            <div class="d-flex align-items-baseline gap-1">

                                <span class="text-muted fw-semibold">
                                    ₦
                                </span>

                                <h2 class="fw-bold mb-0">
                                    {{ number_format((float) $plan->price, 2) }}
                                </h2>

                            </div>

                            <span class="text-muted small">
                                per {{ ucfirst($plan->interval) }}
                            </span>

                        </div>

                        <hr>


                        {{-- Plan Limits --}}

                        <h6 class="fw-bold mb-3">
                            Plan Limits
                        </h6>

                        <div class="d-flex justify-content-between mb-3">

                            <span class="text-muted">
                                <i class="bi bi-people me-2"></i>
                                Students
                            </span>

                            <strong>
                                {{ $plan->student_limit === null
                                    ? 'Unlimited'
                                    : number_format($plan->student_limit) }}
                            </strong>

                        </div>

                        <div class="d-flex justify-content-between mb-3">

                            <span class="text-muted">
                                <i class="bi bi-person-badge me-2"></i>
                                Staff
                            </span>

                            <strong>
                                {{ $plan->staff_limit === null
                                    ? 'Unlimited'
                                    : number_format($plan->staff_limit) }}
                            </strong>

                        </div>

                        <hr>


                        {{-- Features --}}

                        <h6 class="fw-bold mb-3">
                            Plan Features
                        </h6>

                        <ul class="list-unstyled flex-grow-1 mb-4">

                            @forelse($features as $feature)

                                @php
                                    $featureName = is_string($feature)
                                        ? $feature
                                        : ($feature['name'] ?? '');
                                @endphp

                                @if(filled($featureName))

                                    <li class="d-flex align-items-start mb-3">

                                        <i class="bi bi-check-circle-fill
                                                  text-success me-2 mt-1"></i>

                                        <span>
                                            {{ $featureName }}
                                        </span>

                                    </li>

                                @endif

                            @empty

                                <li class="text-muted small">
                                    Contact us for plan features.
                                </li>

                            @endforelse

                        </ul>


                        {{-- Choose Plan Button --}}

                        <button
                            type="button"
                            wire:click="selectPlan({{ $plan->id }})"
                            wire:loading.attr="disabled"
                            wire:target="selectPlan"
                            class="btn w-100 py-2 fw-semibold
                                {{ $isSelected
                                    ? 'btn-primary'
                                    : 'btn-outline-primary' }}"
                        >

                            @if($isSelected)

                                <i class="bi bi-check-circle me-1"></i>
                                Selected Plan

                            @else

                                <i class="bi bi-arrow-right-circle me-1"></i>
                                Choose This Plan

                            @endif

                        </button>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="card border-0 shadow-sm rounded-4">

                    <div class="card-body text-center py-5">

                        <i class="bi bi-box-seam display-3 text-muted"></i>

                        <h5 class="fw-bold mt-3">
                            No Subscription Plans Available
                        </h5>

                        <p class="text-muted mb-0">
                            Please contact the platform administrator.
                        </p>

                    </div>

                </div>

            </div>

        @endforelse

    </div>


    {{-- =====================================================
        SELECTED PLAN AND PAYSTACK CHECKOUT
    ====================================================== --}}

    @if($this->selectedPlan)

        <div class="card border-0 shadow-sm rounded-4 mt-4">

            <div class="card-body p-4">

                <div class="row align-items-center g-4">

                    {{-- Selected Plan Details --}}

                    <div class="col-lg-7">

                        <span class="text-muted small fw-semibold">
                            YOUR SELECTED PLAN
                        </span>

                        <h4 class="fw-bold mt-2 mb-1">
                            {{ $this->selectedPlan->name }}
                        </h4>

                        <h3 class="text-primary fw-bold mb-2">
                            ₦{{ number_format((float) $this->selectedPlan->price, 2) }}

                            <small class="text-muted fs-6 fw-normal">
                                / {{ ucfirst($this->selectedPlan->interval) }}
                            </small>
                        </h3>

                        <p class="text-muted mb-0">
                            Complete your subscription payment securely with Paystack.
                        </p>

                    </div>


                    {{-- Payment Button --}}

                    <div class="col-lg-5">

                        <button
                            type="button"
                            wire:click="payForSelectedPlan"
                            wire:loading.attr="disabled"
                            wire:target="payForSelectedPlan"
                            class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-3"
                        >

                            <span wire:loading.remove
                                  wire:target="payForSelectedPlan">

                                <i class="bi bi-lock-fill me-2"></i>
                                Pay with Paystack

                            </span>

                            <span wire:loading
                                  wire:target="payForSelectedPlan">

                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Initializing Payment...

                            </span>

                        </button>

                        <div class="text-center mt-3">

                            <small class="text-muted">
                                <i class="bi bi-shield-lock-fill text-success me-1"></i>
                                Secure payment powered by Paystack
                            </small>

                        </div>

                    </div>

                </div>


                {{-- Payment Error --}}

                @error('payment')

                    <div class="alert alert-danger mt-4 mb-0">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        {{ $message }}
                    </div>

                @enderror

            </div>

        </div>

    @endif

</div>


{{-- =====================================================
    CUSTOM STYLES
====================================================== --}}

@push('styles')

<style>

    .subscription-selected {
        border: 2px solid #0d6efd !important;
        box-shadow: 0 8px 25px rgba(13, 110, 253, 0.10) !important;
        transform: translateY(-3px);
        transition: all .25s ease;
    }

    .card {
        transition: all .25s ease;
    }

    .card:hover {
        transform: translateY(-3px);
    }

    .subscription-selected:hover {
        transform: translateY(-3px);
    }

</style>

@endpush
