<?php

namespace App\Livewire\Student;

use App\Models\Invoice;
use App\Models\Student as StudentModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Invoices extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

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

    public function render()
    {
        $student = $this->getStudent();

        $query = Invoice::query()
            ->where('school_id', $student->school_id)
            ->where('student_id', $student->id)
            ->with([
                'academicSession',
                'term',
            ])
            ->latest('issued_at')
            ->latest('id');

        $allInvoices = (clone $query)->get();

        $totalInvoiced = $allInvoices->sum(
            fn ($invoice) => (float) $invoice->amount_due
        );

        $totalPaid = $allInvoices->sum(
            fn ($invoice) => (float) $invoice->amount_paid
        );

        $totalOutstanding = $allInvoices->sum(
            fn ($invoice) => max(
                0,
                (float) $invoice->amount_due - (float) $invoice->amount_paid
            )
        );

        return view('livewire.student.invoices', [
            'student' => $student,
            'invoices' => $query->paginate(10),
            'totalInvoiced' => $totalInvoiced,
            'totalPaid' => $totalPaid,
            'totalOutstanding' => $totalOutstanding,
            'invoiceCount' => $allInvoices->count(),
        ])->layout('components.layouts.student', [
            'title' => 'My Invoices',
        ]);
    }
}
