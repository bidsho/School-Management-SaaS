<?php

namespace App\Livewire\School\Subscription;

use App\Models\School;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Services\PaystackService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Plans extends Component
{
    #[Locked]
    public int $schoolId;

    public ?int $selectedPlanId = null;

    /*
    |--------------------------------------------------------------------------
    | Initialize Component
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user && $user->school_id, 403);

        $this->schoolId = (int) $user->school_id;

        $school = School::query()
            ->whereKey($this->schoolId)
            ->firstOrFail();

        $currentSubscription = $school->activeSubscription;

        if ($currentSubscription) {
            $this->selectedPlanId =
                (int) $currentSubscription->subscription_plan_id;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Select Subscription Plan
    |--------------------------------------------------------------------------
    */

    public function selectPlan(int $planId): void
    {
        $this->ensureSchoolAccess();

        $plan = SubscriptionPlan::query()
            ->where('status', 'active')
            ->findOrFail($planId);

        $this->selectedPlanId = (int) $plan->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Get Selected Plan
    |--------------------------------------------------------------------------
    */

    public function getSelectedPlanProperty(): ?SubscriptionPlan
    {
        if (!$this->selectedPlanId) {
            return null;
        }

        return SubscriptionPlan::query()
            ->where('status', 'active')
            ->find($this->selectedPlanId);
    }

    /*
    |--------------------------------------------------------------------------
    | Pay For Selected Plan
    |--------------------------------------------------------------------------
    */

    public function payForSelectedPlan(PaystackService $paystack)
    {
        $this->ensureSchoolAccess();

        $this->validate([
            'selectedPlanId' => [
                'required',
                'integer',
                'exists:subscription_plans,id',
            ],
        ]);

        $user = Auth::user();

        // Only allow currently active plans.
        $plan = SubscriptionPlan::query()
            ->where('status', 'active')
            ->findOrFail($this->selectedPlanId);

        $amount = (float) $plan->price;

        if ($amount <= 0) {
            $this->addError(
                'payment',
                'This plan does not have a valid payment amount.'
            );

            return;
        }

        if (!$user->email) {
            $this->addError(
                'payment',
                'Your account must have an email address before making payment.'
            );

            return;
        }

        $subscription = null;
        $payment = null;

        try {
            $reference = 'SCH-' . strtoupper(Str::random(24));

            // Paystack expects amounts in kobo.
            $amountInKobo = (int) round($amount * 100);

            /*
            |--------------------------------------------------------------------------
            | Create Pending Subscription And Payment
            |--------------------------------------------------------------------------
            */

            [$subscription, $payment] = DB::transaction(
                function () use (
                    $reference,
                    $amount,
                    $amountInKobo,
                    $plan
                ) {
                    $subscription = Subscription::create([
                        'school_id' => $this->schoolId,
                        'subscription_plan_id' => $plan->id,
                        'starts_at' => null,
                        'ends_at' => null,
                        'status' => 'pending',
                        'provider' => 'paystack',
                        'provider_reference' => $reference,
                        'metadata' => [
                            'amount' => $amount,
                            'amount_kobo' => $amountInKobo,
                            'currency' => 'NGN',
                            'plan_name' => $plan->name,
                        ],
                    ]);

                    $payment = SubscriptionPayment::create([
                        'school_id' => $this->schoolId,
                        'subscription_id' => $subscription->id,
                        'subscription_plan_id' => $plan->id,
                        'reference' => $reference,
                        'amount' => $amount,
                        'currency' => 'NGN',
                        'status' => 'pending',
                        'metadata' => [
                            'plan_name' => $plan->name,
                            'billing_interval' => $plan->interval,
                            'amount_kobo' => $amountInKobo,
                        ],
                    ]);

                    return [$subscription, $payment];
                }
            );

            /*
            |--------------------------------------------------------------------------
            | Initialize Paystack Transaction
            |--------------------------------------------------------------------------
            */

            $checkout = $paystack->initialize(
                email: $user->email,
                amountInKobo: $amountInKobo,
                reference: $reference,
                callbackUrl: route(
                    'school.subscription.paystack.callback'
                ),
                metadata: [
                    'school_id' => $this->schoolId,
                    'subscription_id' => $subscription->id,
                    'payment_id' => $payment->id,
                    'plan_id' => $plan->id,
                ],
            );

            if (empty($checkout['authorization_url'])) {
                throw new \RuntimeException(
                    'Paystack did not return a checkout URL.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Redirect To Paystack Checkout
            |--------------------------------------------------------------------------
            */

            return redirect()->away(
                $checkout['authorization_url']
            );

        } catch (\Throwable $e) {

            Log::error('Paystack subscription initialization failed.', [
                'school_id' => $this->schoolId,
                'plan_id' => $this->selectedPlanId,
                'subscription_id' => $subscription?->id,
                'payment_id' => $payment?->id,
                'error' => $e->getMessage(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Mark Failed Initialization
            |--------------------------------------------------------------------------
            */

            if ($subscription) {
                DB::transaction(function () use (
                    $subscription,
                    $payment
                ) {
                    Subscription::query()
                        ->whereKey($subscription->id)
                        ->where('status', 'pending')
                        ->update([
                            'status' => 'failed',
                        ]);

                    if ($payment) {
                        SubscriptionPayment::query()
                            ->whereKey($payment->id)
                            ->where('status', 'pending')
                            ->update([
                                'status' => 'failed',
                            ]);
                    }
                });
            }

            $this->addError(
                'payment',
                'Unable to initialize payment. Please try again.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Ensure Authenticated User Belongs To School
    |--------------------------------------------------------------------------
    */

   protected function ensureSchoolAccess(): void
{
    $user = Auth::user();

    abort_unless(
        $user &&
        $user->school_id &&
        (int) $user->school_id === $this->schoolId &&
        $user->hasRole('School Admin'),
        403,
        'Only the School Admin can manage subscriptions.'
    );
}

    /*
    |--------------------------------------------------------------------------
    | Render Component
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $this->ensureSchoolAccess();

        $school = School::query()
            ->whereKey($this->schoolId)
            ->with([
                'activeSubscription.plan',
            ])
            ->firstOrFail();

        $plans = SubscriptionPlan::query()
            ->where('status', 'active')
            ->orderBy('price')
            ->get();

        return view('livewire.school.subscription.plans', [
            'school' => $school,
            'plans' => $plans,
            'currentSubscription' => $school->activeSubscription,
        ])->layout('components.layouts.school', [
            'title' => 'Subscription Plans',
        ]);
    }
}
