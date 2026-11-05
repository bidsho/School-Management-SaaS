<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaystackSubscriptionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Paystack Callback
    |--------------------------------------------------------------------------
    */

    public function callback(Request $request, PaystackService $paystack)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()
                ->route('school.subscription.plans')
                ->with('error', 'Payment reference was not provided.');
        }

        try {
            $transaction = $paystack->verify($reference);

            $this->activateSubscription($reference, $transaction);

            return redirect()
                ->route('school.subscription.plans')
                ->with(
                    'success',
                    'Payment successful. Your subscription is now active.'
                );

        } catch (\Throwable $e) {

            Log::error('Paystack callback failed.', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('school.subscription.plans')
                ->with(
                    'error',
                    'Payment could not be confirmed. Please contact support if you were debited.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Paystack Webhook
    |--------------------------------------------------------------------------
    */

    public function webhook(Request $request, PaystackService $paystack)
    {
        $secret = config('services.paystack.secret_key');
        $signature = $request->header('x-paystack-signature');

        if (!$secret || !$signature) {
            return response()->json([
                'message' => 'Invalid signature.',
            ], 401);
        }

        $expectedSignature = hash_hmac(
            'sha512',
            $request->getContent(),
            $secret
        );

        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json([
                'message' => 'Invalid signature.',
            ], 401);
        }

        $event = $request->json()->all();

        if (($event['event'] ?? null) !== 'charge.success') {
            return response()->json([
                'message' => 'Event received.',
            ]);
        }

        $reference = $event['data']['reference'] ?? null;

        if (!$reference) {
            return response()->json([
                'message' => 'Payment reference missing.',
            ], 400);
        }

        try {
            $transaction = $paystack->verify($reference);

            $this->activateSubscription($reference, $transaction);

            return response()->json([
                'message' => 'Webhook processed.',
            ]);

        } catch (\Throwable $e) {

            Log::error('Paystack webhook failed.', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to process payment.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Activate Verified Subscription And Record Payment
    |--------------------------------------------------------------------------
    */

    private function activateSubscription(
        string $reference,
        array $transaction
    ): void {

        DB::transaction(function () use ($reference, $transaction) {

            $subscription = Subscription::query()
                ->where('provider', 'paystack')
                ->where('provider_reference', $reference)
                ->lockForUpdate()
                ->firstOrFail();

            $expectedAmount = (int) data_get(
                $subscription->metadata,
                'amount_kobo',
                0
            );

            $receivedAmount = (int) ($transaction['amount'] ?? 0);

            /*
            |--------------------------------------------------------------------------
            | Verify Paystack Transaction
            |--------------------------------------------------------------------------
            */

            if (
                ($transaction['status'] ?? null) !== 'success' ||
                ($transaction['reference'] ?? null) !== $reference ||
                strtoupper($transaction['currency'] ?? '') !== 'NGN' ||
                $expectedAmount <= 0 ||
                $receivedAmount !== $expectedAmount
            ) {
                throw new \RuntimeException(
                    'Paystack transaction verification failed.'
                );
            }

            $plan = $subscription->plan()->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | Mark Payment Successful
            |--------------------------------------------------------------------------
            */

            $payment = SubscriptionPayment::query()
                ->where('reference', $reference)
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                throw new \RuntimeException(
                    'Subscription payment record not found.'
                );
            }

            $payment->update([
                'school_id' => $subscription->school_id,
                'subscription_id' => $subscription->id,
                'subscription_plan_id' => $plan->id,
                'transaction_id' => isset($transaction['id'])
                    ? (string) $transaction['id']
                    : null,
                'amount' => $receivedAmount / 100,
                'currency' => 'NGN',
                'status' => 'successful',
                'channel' => $transaction['channel'] ?? null,
                'paid_at' => !empty($transaction['paid_at'])
                    ? Carbon::parse($transaction['paid_at'])
                    : now(),
                'metadata' => array_merge(
                    $payment->metadata ?? [],
                    [
                        'plan_name' => $plan->name,
                        'gateway_response' => $transaction['gateway_response'] ?? null,
                        'fees' => $transaction['fees'] ?? null,
                    ]
                ),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Subscription Activation
            |--------------------------------------------------------------------------
            */

            if ($subscription->status === 'active') {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate Subscription Expiry
            |--------------------------------------------------------------------------
            */

            $now = now();

            $endDate = match (strtolower($plan->interval)) {
                'day', 'daily' => $now->copy()->addDay(),
                'week', 'weekly' => $now->copy()->addWeek(),
                'month', 'monthly' => $now->copy()->addMonth(),
                'quarter', 'quarterly' => $now->copy()->addQuarter(),
                'year', 'yearly', 'annually' => $now->copy()->addYear(),

                default => throw new \RuntimeException(
                    'Unsupported subscription billing interval.'
                ),
            };

            /*
            |--------------------------------------------------------------------------
            | Expire Previous Active Subscription
            |--------------------------------------------------------------------------
            */

            Subscription::query()
                ->where('school_id', $subscription->school_id)
                ->where('status', 'active')
                ->where('id', '!=', $subscription->id)
                ->update([
                    'status' => 'expired',
                ]);

            /*
            |--------------------------------------------------------------------------
            | Activate New Subscription
            |--------------------------------------------------------------------------
            */

            $subscription->update([
                'status' => 'active',
                'starts_at' => $now,
                'ends_at' => $endDate,
                'metadata' => array_merge(
                    $subscription->metadata ?? [],
                    [
                        'currency' => 'NGN',
                        'paid_amount_kobo' => $receivedAmount,
                        'paystack_transaction_id' => $transaction['id'] ?? null,
                        'paid_at' => $transaction['paid_at'] ?? null,
                    ]
                ),
            ]);
        });
    }
}
