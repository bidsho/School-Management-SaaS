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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Throwable;

class FeeAssignment extends Component
{
    public string $sessionId = '';
    public string $termId = '';
    public string $classRoomId = '';

    /**
     * Reset term when academic session changes.
     */
    public function updatedSessionId(): void
    {
        $this->termId = '';
    }

    /**
     * Get and validate the current school.
     */
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
     *
     * Existing invoices are never overwritten.
     * Emails are sent only after the database transaction succeeds.
     */
    public function generateInvoices(): void
    {
        $schoolId = $this->schoolId();

        $this->validate([
            'sessionId' => ['required', 'integer'],
            'termId' => ['required', 'integer'],
            'classRoomId' => ['nullable', 'integer'],
        ]);

        // Stop invoice generation until bank details are configured.
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

        // Collect newly-created invoices for notification after commit.
        $createdInvoices = [];

        DB::transaction(function () use (
            $schoolId,
            $session,
            $term,
            $fees,
            $classRoomId,
            $schoolSettings,
            &$created,
            &$skipped,
            &$createdInvoices
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
                $student = $enrollment->student;

                if (!$student) {
                    continue;
                }

                $studentId = $enrollment->student_id;

                // Preserve existing invoices.
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

                    // Snapshot of the school's bank details.
                    'bank_name' => $schoolSettings->bank_name,
                    'bank_account_name' => $schoolSettings->bank_account_name,
                    'bank_account_number' => $schoolSettings->bank_account_number,
                    'bank_payment_instructions' =>
                        $schoolSettings->bank_payment_instructions,
                ]);

                // Create invoice items.
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

                // Keep the created records for email notification.
                $createdInvoices[] = [
                    'invoice' => $invoice,
                    'student' => $student,
                ];

                $created++;
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Email parents after transaction has completed successfully.
        |--------------------------------------------------------------------------
        */

        $emailsSent = 0;
        $emailsFailed = 0;

        foreach ($createdInvoices as $record) {
            $result = $this->sendInvoiceEmail(
                $record['invoice'],
                $record['student'],
                $session,
                $term
            );

            $emailsSent += $result['sent'];
            $emailsFailed += $result['failed'];
        }

        session()->flash(
            'success',
            "Invoice generation complete. "
            . "Created: {$created}. "
            . "Skipped because an invoice already exists: {$skipped}. "
            . "Parent emails sent: {$emailsSent}. "
            . "Email failures: {$emailsFailed}."
        );
    }

    /**
     * Send invoice notification email to linked parents/guardians.
     *
     * Assumes Student has parents() relationship and SchoolParent
     * has a user() relationship.
     */
    private function sendInvoiceEmail(
        Invoice $invoice,
        $student,
        $session,
        $term
    ): array {
        $sent = 0;
        $failed = 0;

        try {
            $parents = $student->parents()
                ->with('user')
                ->get();

            $recipients = $parents
                ->map(fn ($parent) => $parent->user?->email)
                ->filter(fn ($email) => filled($email))
                ->unique()
                ->values();

        } catch (Throwable $e) {
            Log::error('Unable to retrieve invoice parent recipients.', [
                'invoice_id' => $invoice->id,
                'student_id' => $student->id,
                'school_id' => $invoice->school_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'sent' => 0,
                'failed' => 1,
            ];
        }

        if ($recipients->isEmpty()) {
            Log::warning('Invoice created but no parent email was found.', [
                'invoice_id' => $invoice->id,
                'student_id' => $student->id,
                'school_id' => $invoice->school_id,
            ]);

            return [
                'sent' => 0,
                'failed' => 1,
            ];
        }

        $items = InvoiceItem::query()
            ->where('invoice_id', $invoice->id)
            ->get();

        $studentName = trim(
            ($student->first_name ?? '') . ' '
            . ($student->middle_name ?? '') . ' '
            . ($student->last_name ?? '')
        );

        $feeRows = '';

        foreach ($items as $item) {
            $feeRows .= '
                <tr>
                    <td style="padding:10px;border-bottom:1px solid #eee;">
                        ' . e($item->description) . '
                    </td>
                    <td style="padding:10px;border-bottom:1px solid #eee;text-align:right;">
                        ' . e($invoice->currency) . ' '
                        . number_format((float) $item->total_amount, 2) . '
                    </td>
                </tr>';
        }

        $dueDate = $invoice->due_at
            ? \Illuminate\Support\Carbon::parse($invoice->due_at)
                ->format('d M Y')
            : 'Not specified';

        $paymentInstructions = filled($invoice->bank_payment_instructions)
            ? nl2br(e($invoice->bank_payment_instructions))
            : 'Please contact the school Treasury office for payment instructions.';

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>School Fee Invoice</title>
        </head>

        <body style="margin:0;background:#f4f7fb;font-family:Arial,sans-serif;color:#243247;">

            <div style="max-width:650px;margin:30px auto;background:#ffffff;border-radius:12px;overflow:hidden;">

                <div style="background:#163b65;padding:25px;color:white;">
                    <h2 style="margin:0;">School Fee Invoice</h2>
                    <p style="margin:8px 0 0;">
                        ' . e($invoice->invoice_number) . '
                    </p>
                </div>

                <div style="padding:25px;">

                    <p>Dear Parent/Guardian,</p>

                    <p>
                        A new school fee invoice has been generated for
                        <strong>' . e($studentName) . '</strong>.
                    </p>

                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="border-collapse:collapse;margin:20px 0;">

                        <tr>
                            <td style="padding:10px;background:#f8fafc;">
                                Academic Session
                            </td>
                            <td style="padding:10px;background:#f8fafc;text-align:right;">
                                ' . e($session->name) . '
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:10px;">
                                Term
                            </td>
                            <td style="padding:10px;text-align:right;">
                                ' . e($term->name) . '
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:10px;background:#f8fafc;">
                                Invoice Number
                            </td>
                            <td style="padding:10px;background:#f8fafc;text-align:right;">
                                ' . e($invoice->invoice_number) . '
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:10px;">
                                Payment Due Date
                            </td>
                            <td style="padding:10px;text-align:right;">
                                ' . e($dueDate) . '
                            </td>
                        </tr>
                    </table>

                    <h3>Fee Breakdown</h3>

                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="border-collapse:collapse;">

                        <thead>
                            <tr style="background:#f1f5f9;">
                                <th style="padding:10px;text-align:left;">
                                    Description
                                </th>
                                <th style="padding:10px;text-align:right;">
                                    Amount
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            ' . $feeRows . '
                        </tbody>
                    </table>

                    <div style="background:#eff6ff;padding:18px;margin-top:20px;border-radius:8px;">
                        <h2 style="margin:0;text-align:right;color:#163b65;">
                            Total Due:
                            ' . e($invoice->currency) . ' '
                            . number_format((float) $invoice->amount_due, 2) . '
                        </h2>
                    </div>

                    <h3 style="margin-top:25px;">Payment Details</h3>

                    <p>
                        <strong>Bank:</strong>
                        ' . e($invoice->bank_name ?? 'Not configured') . '
                    </p>

                    <p>
                        <strong>Account Name:</strong>
                        ' . e($invoice->bank_account_name ?? 'Not configured') . '
                    </p>

                    <p>
                        <strong>Account Number:</strong>
                        ' . e($invoice->bank_account_number ?? 'Not configured') . '
                    </p>

                    <p>' . $paymentInstructions . '</p>

                    <p>
                        Kindly ensure payment is made before the due date.
                        Use the invoice number as your payment reference
                        where applicable.
                    </p>

                    <p>
                        If you have already made this payment, please disregard
                        this notification or contact your school Treasury office.
                    </p>

                    <p>Thank you.</p>

                </div>

                <div style="background:#f8fafc;padding:18px;text-align:center;color:#64748b;font-size:12px;">
                    This is an automated school fee notification.
                    Please contact your school for assistance.
                </div>

            </div>

        </body>
        </html>';

        // Send to each unique linked parent email.
        foreach ($recipients as $email) {
            try {
                Mail::html($html, function ($message) use ($email, $invoice) {
                    $message
                        ->to($email)
                        ->subject(
                            'School Fee Invoice - '
                            . $invoice->invoice_number
                        );
                });

                $sent++;

            } catch (Throwable $e) {
                $failed++;

                Log::error('Invoice email sending failed.', [
                    'invoice_id' => $invoice->id,
                    'student_id' => $student->id,
                    'school_id' => $invoice->school_id,
                    'recipient' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
        ];
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

    /**
     * Render Treasury Fee Assignment page.
     */
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
