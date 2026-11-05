<?php

namespace App\Livewire\Parent\Payments;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SchoolParent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class InvoiceDetails extends Component
{
    #[Locked]
    public int $invoiceId;

    public string $amount = '';

    public string $providerReference = '';

    public string $senderName = '';

    public string $transferDate = '';

    public string $notes = '';

    public bool $showPaymentForm = false;

    public function mount(int $invoice): void
    {
        $this->invoiceId = $invoice;

        $record = $this->authorizedInvoice();

        $this->amount = number_format(
            max(0, (float) $record->balance),
            2,
            '.',
            ''
        );

        $this->transferDate = now()->toDateString();
    }

    /**
     * Only allow a parent to access an invoice for their linked child
     * within the authenticated user's current school.
     */
    private function authorizedInvoice(): Invoice
    {
        $user = Auth::user();

        abort_unless($user && $user->school_id, 403);

        $parent = SchoolParent::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $childIds = $parent->students()
            ->where('students.school_id', $user->school_id)
            ->wherePivot('school_id', $user->school_id)
            ->pluck('students.id');

        $invoice = Invoice::query()
            ->where('school_id', $user->school_id)
            ->whereIn('student_id', $childIds)
            ->with([
                'student',
                'items',
                'academicSession',
                'term',
                'payments' => fn ($query) => $query->latest(),
            ])
            ->findOrFail($this->invoiceId);

        // This workflow accepts NGN invoices only.
        abort_unless(
            strtoupper((string) $invoice->currency) === 'NGN',
            422,
            'This invoice is not denominated in NGN.'
        );

        return $invoice;
    }

    public function openPaymentForm(): void
    {
        $invoice = $this->authorizedInvoice();

        if ((float) $invoice->balance <= 0) {
            $this->addError('amount', 'This invoice has no outstanding balance.');

            return;
        }

        if (! $this->hasBankDetails($invoice)) {
            $this->addError(
                'bankDetails',
                'The school has not configured complete bank transfer details for this invoice. Please contact the school.'
            );

            return;
        }

        $this->amount = number_format(
            (float) $invoice->balance,
            2,
            '.',
            ''
        );

        $this->showPaymentForm = true;
    }

    private function hasBankDetails(Invoice $invoice): bool
    {
        return filled($invoice->bank_name)
            && filled($invoice->bank_account_name)
            && filled($invoice->bank_account_number);
    }

    public function submitPayment(): void
    {
        $invoice = $this->authorizedInvoice();

        if (! $this->hasBankDetails($invoice)) {
            throw ValidationException::withMessages([
                'bankDetails' => 'Bank transfer details are not configured for this invoice. Please contact the school.',
            ]);
        }

        $balance = max(0, (float) $invoice->balance);

        $validated = $this->validate([
            'amount' => [
                'required',
                'numeric',
                'gt:0',
                'lte:' . number_format($balance, 2, '.', ''),
            ],
            'providerReference' => [
                'required',
                'string',
                'max:150',
            ],
            'senderName' => [
                'required',
                'string',
                'max:150',
            ],
            'transferDate' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'amount.lte' => 'The transfer amount cannot exceed the outstanding balance.',
            'providerReference.required' => 'Enter the bank transfer reference.',
            'senderName.required' => 'Enter the name on the sending bank account.',
            'transferDate.before_or_equal' => 'The transfer date cannot be in the future.',
        ]);

        $user = Auth::user();

        $payment = DB::transaction(function () use (
            $user,
            $validated
        ) {
            // Re-check authorization and balance inside the transaction.
            $invoice = $this->authorizedInvoice();

            $lockedInvoice = Invoice::query()
                ->where('school_id', $user->school_id)
                ->whereKey($invoice->id)
                ->lockForUpdate()
                ->firstOrFail();

            $currentBalance = max(
                0,
                (float) $lockedInvoice->balance
            );

            $amount = round((float) $validated['amount'], 2);

            if ($currentBalance <= 0 || $amount > $currentBalance) {
                throw ValidationException::withMessages([
                    'amount' => 'The invoice balance has changed. Refresh the page and try again.',
                ]);
            }

            // Prevent reusing a bank transfer reference in this school.
            $duplicateReference = Payment::query()
                ->where('school_id', $user->school_id)
                ->where('provider', 'manual_bank_transfer')
                ->where('provider_reference', trim($validated['providerReference']))
                ->exists();

            if ($duplicateReference) {
                throw ValidationException::withMessages([
                    'providerReference' => 'This bank transfer reference has already been submitted.',
                ]);
            }

            do {
                $reference = 'PMT-' . Str::upper(Str::random(12));
            } while (
                Payment::query()
                    ->where('reference', $reference)
                    ->exists()
            );

            return Payment::create([
                'school_id' => $user->school_id,
                'invoice_id' => $lockedInvoice->id,
                'student_id' => $lockedInvoice->student_id,
                'reference' => $reference,
                'provider' => 'manual_bank_transfer',
                'provider_reference' => trim($validated['providerReference']),
                'amount' => $amount,
                'currency' => 'NGN',
                'status' => 'pending',
                'paid_at' => null,
                'verified_at' => null,
                'metadata' => [
                    'submitted_by_user_id' => $user->id,
                    'sender_name' => trim($validated['senderName']),
                    'transfer_date' => $validated['transferDate'],
                    'parent_notes' => $validated['notes'] ?? null,
                    'submitted_at' => now()->toISOString(),
                ],
            ]);
        });

        $this->reset([
            'providerReference',
            'senderName',
            'notes',
        ]);

        $this->showPaymentForm = false;

        session()->flash(
            'success',
            'Your bank transfer has been submitted for verification. Reference: ' . $payment->reference
        );

        $this->dispatch('payment-submitted');
    }

    public function render()
    {
        return view('livewire.parent.payments.invoice-details', [
            'invoice' => $this->authorizedInvoice(),
        ])->layout('components.layouts.parent');
    }
}
