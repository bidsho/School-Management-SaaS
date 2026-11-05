<?php

namespace App\Livewire\Treasury;

use App\Models\AcademicSession;
use App\Models\Enrollment;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SchoolSetting;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class FeeAssignment extends Component
{
    public string $sessionId = '';
    public string $termId = '';
    public string $classRoomId = '';

    public function updatedSessionId(): void
    {
        $this->termId = '';
    }

    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user, 403);
        abort_unless($user->hasRole('Treasury'), 403);
        abort_unless($user->school_id, 403);

        return (int) $user->school_id;
    }

    /**
     * Get the school's configured bank details.
     */
    private function schoolBankDetails(int $schoolId): SchoolSetting
    {
        $settings = SchoolSetting::query()
            ->where('school_id', $schoolId)
            ->first();

        if (
            !$settings ||
            blank($settings->bank_name) ||
            blank($settings->bank_account_name) ||
            blank($settings->bank_account_number)
        ) {
            throw ValidationException::withMessages([
                'termId' => 'Configure the school bank name, account name and account number before generating invoices.',
            ]);
        }

        return $settings;
    }

    /**
     * Validate and retrieve the selected session, term and fees.
     */
    private function selectedFees(int $schoolId): array
    {
        if ($this->sessionId === '' || $this->termId === '') {
            throw ValidationException::withMessages([
                'sessionId' => 'Select an academic session and term.',
            ]);
        }

        $session = AcademicSession::query()
            ->where('school_id', $schoolId)
            ->findOrFail($this->sessionId);

        $term = Term::query()
            ->where('school_id', $schoolId)
            ->where('academic_session_id', $session->id)
            ->findOrFail($this->termId);

        $fees = FeeStructure::query()
            ->where('school_id', $schoolId)
            ->where('academic_session_id', $session->id)
            ->where('status', 'active')
            ->where(function ($query) use ($term) {
                $query->whereNull('term_id')
                    ->orWhere('term_id', $term->id);
            })
            ->when($this->classRoomId !== '', function ($query) use ($schoolId) {
                $classRoomId = (int) $this->classRoomId;

                // Ensure the selected class belongs to this school.
                $classExists = \App\Models\ClassRoom::query()
                    ->where('school_id', $schoolId)
                    ->whereKey($classRoomId)
                    ->exists();

                if (!$classExists) {
                    throw ValidationException::withMessages([
                        'classRoomId' => 'Select a valid class.',
                    ]);
                }

                $query->where(function ($q) use ($classRoomId) {
                    $q->whereNull('class_room_id')
                        ->orWhere('class_room_id', $classRoomId);
                });
            })
            ->orderBy('name')
            ->get();

        if ($fees->isEmpty()) {
            throw ValidationException::withMessages([
                'termId' => 'No active fee structures were found for this selection.',
            ]);
        }

        // Do not combine fees with different currencies on one invoice.
        if ($fees->pluck('currency')->unique()->count() > 1) {
            throw ValidationException::withMessages([
                'termId' => 'The selected fees use different currencies. Separate them into currency-specific invoices.',
            ]);
        }

        return [$session, $term, $fees];
    }

    /**
     * Generate invoices for active enrollments.
     */
    public function generateInvoices(): void
    {
        $schoolId = $this->schoolId();

        $this->validate([
            'sessionId' => ['required', 'integer'],
            'termId' => ['required', 'integer'],
            'classRoomId' => ['nullable', 'integer'],
        ]);

        // Stop invoice generation until the school has configured its bank.
        $schoolSettings = $this->schoolBankDetails($schoolId);

        [$session, $term, $fees] = $this->selectedFees($schoolId);

        $classRoomId = $this->classRoomId !== ''
            ? (int) $this->classRoomId
            : null;

        $subtotal = (float) $fees->sum(
            fn ($fee) => (float) $fee->amount
        );

        if ($subtotal <= 0) {
            throw ValidationException::withMessages([
                'termId' => 'The selected fees must have a total greater than zero.',
            ]);
        }

        $created = 0;
        $skipped = 0;

        DB::transaction(function () use (
            $schoolId,
            $session,
            $term,
            $fees,
            $classRoomId,
            $schoolSettings,
            &$created,
            &$skipped
        ) {
            $enrollments = Enrollment::query()
                ->where('school_id', $schoolId)
                ->where('academic_session_id', $session->id)
                ->where('admission_status', Enrollment::STATUS_ACTIVE)
                ->when($classRoomId !== null, function ($query) use ($classRoomId) {
                    $query->where('class_room_id', $classRoomId);
                })
                ->with('student')
                ->get();

            foreach ($enrollments as $enrollment) {
                if (!$enrollment->student) {
                    continue;
                }

                $studentId = $enrollment->student_id;

                // Preserve existing invoices; never overwrite financial records.
                $existingInvoice = Invoice::query()
                    ->where('school_id', $schoolId)
                    ->where('student_id', $studentId)
                    ->where('academic_session_id', $session->id)
                    ->where('term_id', $term->id)
                    ->lockForUpdate()
                    ->exists();

                if ($existingInvoice) {
                    $skipped++;
                    continue;
                }

                $invoiceSubtotal = (float) $fees->sum(
                    fn ($fee) => (float) $fee->amount
                );

                $currency = $fees->first()->currency;

                $invoice = Invoice::create([
                    'school_id' => $schoolId,
                    'student_id' => $studentId,
                    'academic_session_id' => $session->id,
                    'term_id' => $term->id,

                    'invoice_number' => $this->newInvoiceNumber($schoolId),

                    'subtotal' => $invoiceSubtotal,
                    'discount' => 0,
                    'amount_due' => $invoiceSubtotal,
                    'amount_paid' => 0,
                    'balance' => $invoiceSubtotal,
                    'currency' => $currency,
                    'status' => 'unpaid',
                    'issued_at' => now(),
                    'due_at' => $fees->whereNotNull('due_date')
                        ->min('due_date'),

                    // Snapshot of this school's bank details.
                    'bank_name' => $schoolSettings->bank_name,
                    'bank_account_name' => $schoolSettings->bank_account_name,
                    'bank_account_number' => $schoolSettings->bank_account_number,
                    'bank_payment_instructions' =>
                        $schoolSettings->bank_payment_instructions,
                ]);

                foreach ($fees as $fee) {
                    $amount = (float) $fee->amount;

                    InvoiceItem::create([
                        'school_id' => $schoolId,
                        'invoice_id' => $invoice->id,
                        'fee_structure_id' => $fee->id,
                        'description' => $fee->name,
                        'quantity' => 1,
                        'unit_amount' => $amount,
                        'total_amount' => $amount,
                    ]);
                }

                $created++;
            }
        });

        session()->flash(
            'success',
            "Invoice generation complete. Created: {$created}. Skipped because an invoice already exists: {$skipped}."
        );
    }

    /**
     * Generate a unique invoice number within the school.
     */
    private function newInvoiceNumber(int $schoolId): string
    {
        do {
            $number = 'INV-' . now()->format('Ymd') . '-'
                . Str::upper(Str::random(8));

            $exists = Invoice::query()
                ->where('school_id', $schoolId)
                ->where('invoice_number', $number)
                ->exists();
        } while ($exists);

        return $number;
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $sessions = AcademicSession::query()
            ->where('school_id', $schoolId)
            ->orderByDesc('starts_at')
            ->get();

        $terms = Term::query()
            ->where('school_id', $schoolId)
            ->when($this->sessionId !== '', function ($query) {
                $query->where(
                    'academic_session_id',
                    (int) $this->sessionId
                );
            })
            ->orderBy('name')
            ->get();

        $fees = collect();

        if ($this->sessionId !== '' && $this->termId !== '') {
            $fees = FeeStructure::query()
                ->where('school_id', $schoolId)
                ->where('academic_session_id', (int) $this->sessionId)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('term_id')
                        ->orWhere('term_id', (int) $this->termId);
                })
                ->when($this->classRoomId !== '', function ($query) {
                    $classRoomId = (int) $this->classRoomId;

                    $query->where(function ($q) use ($classRoomId) {
                        $q->whereNull('class_room_id')
                            ->orWhere('class_room_id', $classRoomId);
                    });
                })
                ->orderBy('name')
                ->get();
        }

        $classes = \App\Models\ClassRoom::query()
            ->where('school_id', $schoolId)
            ->orderBy('name')
            ->get();

        $studentCount = 0;

        if ($this->sessionId !== '') {
            $studentCount = Enrollment::query()
                ->where('school_id', $schoolId)
                ->where('academic_session_id', (int) $this->sessionId)
                ->where('admission_status', Enrollment::STATUS_ACTIVE)
                ->when($this->classRoomId !== '', function ($query) {
                    $query->where(
                        'class_room_id',
                        (int) $this->classRoomId
                    );
                })
                ->distinct('student_id')
                ->count('student_id');
        }

        return view('livewire.treasury.fee-assignment', [
            'sessions' => $sessions,
            'terms' => $terms,
            'classes' => $classes,
            'fees' => $fees,
            'studentCount' => $studentCount,
            'feeTotal' => (float) $fees->sum(
                fn ($fee) => (float) $fee->amount
            ),
        ])->layout('components.layouts.treasury', [
            'title' => 'Fee Assignment',
        ]);
    }
}
