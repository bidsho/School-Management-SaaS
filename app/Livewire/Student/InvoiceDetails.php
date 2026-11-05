<?php

namespace App\Livewire\Student;

use App\Models\Invoice;
use App\Models\Student as StudentModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class InvoiceDetails extends Component
{
    #[Locked]
    public int $invoiceId;

    public function mount(int $invoice): void
    {
        $this->invoiceId = $invoice;

        // Verify ownership before displaying this invoice.
        $this->getInvoice();
    }

    private function getStudent(): StudentModel
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless($user->hasRole('Student'), 403);
        abort_unless($user->school_id, 403);

        return StudentModel::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();
    }

    private function getInvoice(): Invoice
    {
        $student = $this->getStudent();

        return Invoice::query()
            ->where('school_id', $student->school_id)
            ->where('student_id', $student->id)
            ->with([
                'student',
                'academicSession',
                'term',
                'items.feeStructure',
                'payments',
            ])
            ->findOrFail($this->invoiceId);
    }

    public function render()
    {
        $invoice = $this->getInvoice();

        $amountDue = (float) $invoice->amount_due;
        $amountPaid = (float) $invoice->amount_paid;

        // Calculate from the invoice totals to avoid negative balances.
        $balance = max(0, $amountDue - $amountPaid);

        $isCancelled = in_array(
            strtolower($invoice->status ?? ''),
            ['cancelled', 'canceled', 'void'],
            true
        );

        if ($isCancelled) {
            $status = 'Cancelled';
            $statusClass = 'text-bg-secondary';
        } elseif ($balance <= 0) {
            $status = 'Paid';
            $statusClass = 'text-bg-success';
        } elseif ($amountPaid > 0) {
            $status = 'Partially Paid';
            $statusClass = 'text-bg-warning';
        } elseif ($invoice->due_at && $invoice->due_at->isPast()) {
            $status = 'Overdue';
            $statusClass = 'text-bg-danger';
        } else {
            $status = 'Unpaid';
            $statusClass = 'text-bg-primary';
        }

        return view('livewire.student.invoice-details', [
            'invoice' => $invoice,
            'balance' => $balance,
            'status' => $status,
            'statusClass' => $statusClass,
            'isCancelled' => $isCancelled,
        ])->layout('components.layouts.student', [
            'title' => 'Invoice Details',
        ]);
    }
}
