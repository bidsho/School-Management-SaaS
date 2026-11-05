<?php

namespace App\Livewire\Treasury;

use App\Models\BankTransaction;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentReconciliation extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $statusFilter = 'all';
    public string $dateFrom = '';
    public string $dateTo = '';

    public string $transactionDate = '';
    public string $description = '';
    public string $reference = '';
    public string $amount = '';
    public string $currency = 'NGN';
    public string $notes = '';

    public ?int $selectedTransactionId = null;
    public string $paymentSearch = '';
    public ?int $selectedPaymentId = null;

    public function mount(): void
    {
        $this->schoolId();
        $this->transactionDate = now()->toDateString();
    }

    /**
     * Ensure the authenticated user is a Treasury user
     * assigned to a school.
     */
    private function schoolId(): int
    {
        $user = auth()->user();

        abort_unless($user, 403);
        abort_unless($user->hasRole('Treasury'), 403);

        abort_unless(
            $user->school_id,
            403,
            'Your account is not assigned to a school.'
        );

        return (int) $user->school_id;
    }

    /**
     * All bank transactions must belong to the current school.
     */
    private function transactionsQuery(): Builder
    {
        return BankTransaction::query()
            ->where('school_id', $this->schoolId())
            ->with([
                'payment.student',
                'matchedBy',
                'createdBy',
            ]);
    }

    /**
     * All payment candidates must belong to the current school.
     */
    private function paymentsQuery(): Builder
    {
        return Payment::query()
            ->where('school_id', $this->schoolId());
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingPaymentSearch(): void
    {
        $this->selectedPaymentId = null;
    }

    /**
     * Clear transaction filters.
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->dateFrom = '';
        $this->dateTo = '';

        $this->resetPage();
    }

    /**
     * Record a bank transaction manually.
     *
     * This records the transaction for reconciliation.
     * It does not independently verify that money reached
     * the school's bank account.
     */
    public function saveTransaction(): void
    {
        $schoolId = $this->schoolId();

        $validated = $this->validate([
            'transactionDate' => [
                'required',
                'date',
            ],
            'description' => [
                'required',
                'string',
                'max:255',
            ],
            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],
            'amount' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
            ],
            'currency' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Za-z]{3}$/',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        BankTransaction::create([
            'school_id' => $schoolId,
            'transaction_date' => $validated['transactionDate'],
            'description' => trim($validated['description']),
            'reference' => filled($validated['reference'] ?? null)
                ? trim($validated['reference'])
                : null,
            'amount' => $validated['amount'],
            'currency' => strtoupper($validated['currency']),
            'status' => 'unmatched',
            'notes' => filled($validated['notes'] ?? null)
                ? trim($validated['notes'])
                : null,
            'created_by' => auth()->id(),
        ]);

        $this->reset([
            'description',
            'reference',
            'amount',
            'notes',
        ]);

        $this->currency = 'NGN';
        $this->transactionDate = now()->toDateString();

        session()->flash(
            'success',
            'Bank transaction recorded successfully.'
        );

        $this->resetPage();
    }

    /**
     * Select an unmatched bank transaction.
     */
    public function selectTransaction(int $transactionId): void
    {
        $transaction = $this->transactionsQuery()
            ->whereKey($transactionId)
            ->firstOrFail();

        abort_if(
            $transaction->payment_id !== null
                || $transaction->status !== 'unmatched',
            422,
            'Only unmatched bank transactions can be selected.'
        );

        $this->selectedTransactionId = $transaction->id;

        $this->paymentSearch = $transaction->reference ?? '';

        $this->selectedPaymentId = null;
    }

    /**
     * Match a bank transaction to a completed payment.
     */
    public function matchTransaction(): void
    {
        $schoolId = $this->schoolId();

        $this->validate([
            'selectedTransactionId' => [
                'required',
                'integer',
            ],
            'selectedPaymentId' => [
                'required',
                'integer',
            ],
        ]);

        DB::transaction(function () use ($schoolId) {
            $bankTransaction = BankTransaction::query()
                ->where('school_id', $schoolId)
                ->whereKey($this->selectedTransactionId)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(
                $bankTransaction->payment_id !== null
                    || $bankTransaction->status !== 'unmatched',
                422,
                'This bank transaction has already been matched.'
            );

            $payment = Payment::query()
                ->where('school_id', $schoolId)
                ->whereKey($this->selectedPaymentId)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $payment->status === 'completed',
                422,
                'Only verified, completed payments can be reconciled.'
            );

            $alreadyMatched = BankTransaction::query()
                ->where('school_id', $schoolId)
                ->where('payment_id', $payment->id)
                ->exists();

            abort_if(
                $alreadyMatched,
                422,
                'This payment has already been reconciled.'
            );

            $paymentAmount = number_format(
                (float) $payment->amount,
                2,
                '.',
                ''
            );

            $bankAmount = number_format(
                (float) $bankTransaction->amount,
                2,
                '.',
                ''
            );

            abort_if(
                $paymentAmount !== $bankAmount,
                422,
                'The payment amount does not match the bank transaction.'
            );

            abort_if(
                strtoupper((string) $payment->currency)
                    !== strtoupper((string) $bankTransaction->currency),
                422,
                'The payment currency does not match the bank transaction.'
            );

            $bankTransaction->update([
                'payment_id' => $payment->id,
                'status' => 'matched',
                'matched_by' => auth()->id(),
                'matched_at' => now(),
            ]);
        });

        $this->selectedTransactionId = null;
        $this->selectedPaymentId = null;
        $this->paymentSearch = '';

        session()->flash(
            'success',
            'Payment and bank transaction reconciled successfully.'
        );

        $this->resetPage();
    }

    /**
     * Cancel the matching process.
     */
    public function cancelMatching(): void
    {
        $this->selectedTransactionId = null;
        $this->selectedPaymentId = null;
        $this->paymentSearch = '';
    }

    /**
     * Reverse a reconciliation match.
     *
     * This only reverses the reconciliation link.
     * It does not refund or reverse the actual payment.
     */
    public function undoMatch(int $transactionId): void
    {
        $schoolId = $this->schoolId();

        DB::transaction(function () use ($schoolId, $transactionId) {
            $transaction = BankTransaction::query()
                ->where('school_id', $schoolId)
                ->whereKey($transactionId)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $transaction->status === 'matched'
                    && $transaction->payment_id !== null,
                422,
                'This transaction is not currently matched.'
            );

            $transaction->update([
                'payment_id' => null,
                'status' => 'unmatched',
                'matched_by' => null,
                'matched_at' => null,
            ]);
        });

        session()->flash(
            'success',
            'Reconciliation match has been reversed successfully.'
        );

        $this->resetPage();
    }

    /**
     * Display reconciliation records, statistics,
     * and eligible payments for matching.
     */
    public function render()
    {
        $schoolId = $this->schoolId();

        $allowedStatuses = [
            'all',
            'matched',
            'unmatched',
        ];

        $statusFilter = in_array(
            $this->statusFilter,
            $allowedStatuses,
            true
        )
            ? $this->statusFilter
            : 'all';

        $transactions = $this->transactionsQuery()
            ->when(trim($this->search) !== '', function (Builder $query) {
                $term = '%' . trim($this->search) . '%';

                $query->where(function (Builder $q) use ($term) {
                    $q->where('description', 'like', $term)
                        ->orWhere('reference', 'like', $term);
                });
            })
            ->when(
                $statusFilter !== 'all',
                function (Builder $query) use ($statusFilter) {
                    $query->where('status', $statusFilter);
                }
            )
            ->when(
                $this->dateFrom !== '',
                function (Builder $query) {
                    $query->whereDate(
                        'transaction_date',
                        '>=',
                        $this->dateFrom
                    );
                }
            )
            ->when(
                $this->dateTo !== '',
                function (Builder $query) {
                    $query->whereDate(
                        'transaction_date',
                        '<=',
                        $this->dateTo
                    );
                }
            )
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(10);

        $stats = BankTransaction::query()
            ->where('school_id', $schoolId)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw(
                "SUM(CASE WHEN status = 'matched' THEN 1 ELSE 0 END) as matched"
            )
            ->selectRaw(
                "SUM(CASE WHEN status = 'unmatched' THEN 1 ELSE 0 END) as unmatched"
            )
            ->selectRaw(
                "COALESCE(SUM(CASE WHEN status = 'unmatched' THEN amount ELSE 0 END), 0) as unmatched_amount"
            )
            ->first();

        $selectedTransaction = $this->selectedTransactionId
            ? $this->transactionsQuery()
                ->whereKey($this->selectedTransactionId)
                ->first()
            : null;

        $candidatePayments = collect();

        if (
            $selectedTransaction
            && $selectedTransaction->payment_id === null
            && $selectedTransaction->status === 'unmatched'
        ) {
            $candidatePayments = $this->paymentsQuery()
                ->where('status', 'completed')
                ->where('amount', $selectedTransaction->amount)
                ->whereRaw(
                    'UPPER(currency) = ?',
                    [strtoupper((string) $selectedTransaction->currency)]
                )
                ->whereDoesntHave('bankTransaction')
                ->when(
                    trim($this->paymentSearch) !== '',
                    function (Builder $query) {
                        $term = '%' . trim($this->paymentSearch) . '%';

                        $query->where(function (Builder $q) use ($term) {
                            $q->where('reference', 'like', $term)
                                ->orWhere(
                                    'provider_reference',
                                    'like',
                                    $term
                                );
                        });
                    }
                )
                ->with([
                    'student',
                    'invoice',
                ])
                ->latest('id')
                ->limit(50)
                ->get();
        }

        return view('livewire.treasury.payment-reconciliation', [
            'transactions' => $transactions,
            'stats' => $stats,
            'selectedTransaction' => $selectedTransaction,
            'candidatePayments' => $candidatePayments,
        ])->layout('components.layouts.treasury', [
            'title' => 'Payment Reconciliation',
        ]);
    }
}
