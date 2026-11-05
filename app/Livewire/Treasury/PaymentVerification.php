<?php

namespace App\Livewire\Treasury;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentVerification extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $status = 'pending';
    public ?int $selectedPaymentId = null;
    public string $rejectionReason = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasRole('Treasury') && $user->school_id,
            403,
            'You are not authorized to access Treasury payments.'
        );

        return (int) $user->school_id;
    }

    private function paymentQuery()
    {
        return Payment::query()
            ->where('school_id', $this->schoolId())
            ->with([
                'student',
                'invoice.items',
                'receipt',
            ]);
    }

    public function viewPayment(int $paymentId): void
    {
        $payment = $this->paymentQuery()->findOrFail($paymentId);

        $this->selectedPaymentId = $payment->id;
        $this->rejectionReason = '';
        $this->resetValidation();
    }

    public function verifyPayment(int $paymentId): void
    {
        $schoolId = $this->schoolId();

        DB::transaction(function () use ($paymentId, $schoolId) {
            $payment = Payment::query()
                ->where('school_id', $schoolId)
                ->lockForUpdate()
                ->findOrFail($paymentId);

            if ($payment->status !== 'pending') {
                throw ValidationException::withMessages([
                    'payment' => 'Only pending payments can be verified.',
                ]);
            }

            if (! $payment->invoice_id) {
                throw ValidationException::withMessages([
                    'payment' => 'This payment is not linked to an invoice.',
                ]);
            }

            $invoice = Invoice::query()
                ->where('school_id', $schoolId)
                ->lockForUpdate()
                ->findOrFail($payment->invoice_id);

            if ((int) $payment->student_id !== (int) $invoice->student_id) {
                throw ValidationException::withMessages([
                    'payment' => 'The payment student does not match the invoice student.',
                ]);
            }

            if (strtoupper((string) $payment->currency) !== 'NGN'
                || strtoupper((string) $invoice->currency) !== 'NGN') {
                throw ValidationException::withMessages([
                    'payment' => 'Only NGN payments can be verified through this manual bank transfer workflow.',
                ]);
            }

            if ((float) $payment->amount <= 0) {
                throw ValidationException::withMessages([
                    'payment' => 'The payment amount must be greater than zero.',
                ]);
            }

            if ((float) $payment->amount > (float) $invoice->balance) {
                throw ValidationException::withMessages([
                    'payment' => 'The payment amount exceeds the current invoice balance.',
                ]);
            }

            /*
             * Treasury must confirm the money was received in the school's
             * bank account before clicking Verify. A submitted reference
             * by itself is not proof of payment.
             */

            $payment->status = 'completed';
            $payment->verified_at = now();
            $payment->paid_at = $payment->paid_at ?? now();
            $payment->save();

            $invoice->amount_paid = round(
                (float) $invoice->amount_paid + (float) $payment->amount,
                2
            );

            $invoice->balance = max(
                0,
                round((float) $invoice->amount_due - (float) $invoice->amount_paid, 2)
            );

            $invoice->status = $invoice->balance <= 0
                ? 'paid'
                : 'partially_paid';

            $invoice->save();

            // A payment must never receive duplicate receipts.
            Receipt::firstOrCreate(
                [
                    'payment_id' => $payment->id,
                ],
                [
                    'school_id' => $schoolId,
                    'invoice_id' => $invoice->id,
                    'receipt_number' => 'RCT-' . Str::upper(Str::random(12)),
                    'issued_at' => now(),
                ]
            );
        });

        $this->selectedPaymentId = null;
        $this->rejectionReason = '';
        $this->resetValidation();

        session()->flash(
            'success',
            'Payment verified. The invoice balance has been updated and a receipt has been issued.'
        );
    }

    public function rejectPayment(int $paymentId): void
    {
        $validated = $this->validate([
            'rejectionReason' => [
                'required',
                'string',
                'min:5',
                'max:500',
            ],
        ]);

        $schoolId = $this->schoolId();

        DB::transaction(function () use ($paymentId, $schoolId, $validated) {
            $payment = Payment::query()
                ->where('school_id', $schoolId)
                ->lockForUpdate()
                ->findOrFail($paymentId);

            if ($payment->status !== 'pending') {
                throw ValidationException::withMessages([
                    'payment' => 'Only pending payments can be rejected.',
                ]);
            }

            $metadata = $payment->metadata ?? [];
            $metadata['rejection_reason'] = trim($validated['rejectionReason']);
            $metadata['rejected_at'] = now()->toISOString();
            $metadata['rejected_by'] = Auth::id();

            $payment->status = 'rejected';
            $payment->metadata = $metadata;
            $payment->save();
        });

        $this->selectedPaymentId = null;
        $this->rejectionReason = '';
        $this->resetValidation();

        session()->flash(
            'success',
            'Payment rejected. The invoice balance was not changed.'
        );
    }

    public function closePaymentDetails(): void
    {
        $this->selectedPaymentId = null;
        $this->rejectionReason = '';
        $this->resetValidation();
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $payments = Payment::query()
            ->where('school_id', $schoolId)
            ->with(['student', 'invoice', 'receipt'])
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            })
            ->when(trim($this->search) !== '', function ($query) {
                $search = '%' . trim($this->search) . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'like', $search)
                        ->orWhere('provider_reference', 'like', $search)
                        ->orWhereHas('invoice', function ($invoiceQuery) use ($search) {
                            $invoiceQuery->where('invoice_number', 'like', $search);
                        })
                        ->orWhereHas('student', function ($studentQuery) use ($search) {
                            $studentQuery->whereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('name', 'like', $search);
                            });
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $counts = Payment::query()
            ->where('school_id', $schoolId)
            ->selectRaw(
                "SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count"
            )
            ->selectRaw(
                "SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count"
            )
            ->selectRaw(
                "SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count"
            )
            ->first();

        $selectedPayment = $this->selectedPaymentId
            ? $this->paymentQuery()->find($this->selectedPaymentId)
            : null;

        return view('livewire.treasury.payment-verification', [
            'payments' => $payments,
            'selectedPayment' => $selectedPayment,
            'counts' => $counts,
        ])->layout('components.layouts.treasury', [
            'title' => 'Payment Verification',
        ]);
    }
}
