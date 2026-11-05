<?php

namespace App\Livewire\Treasury;

use App\Models\AcademicSession;
use App\Models\Invoice;
use App\Models\Term;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceManagement extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $status = '';
    public string $sessionId = '';
    public string $termId = '';

    public ?int $selectedInvoiceId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingSessionId(): void
    {
        $this->termId = '';
        $this->resetPage();
    }

    public function updatingTermId(): void
    {
        $this->resetPage();
    }

    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user, 403);
        abort_unless($user->hasRole('Treasury'), 403);
        abort_unless($user->school_id, 403);

        return (int) $user->school_id;
    }

    private function invoiceQuery(): Builder
    {
        $schoolId = $this->schoolId();

        return Invoice::query()
            ->where('school_id', $schoolId)
            ->with(['student', 'academicSession', 'term'])
            ->when($this->search !== '', function (Builder $query) {
                $search = trim($this->search);

                $query->where(function (Builder $q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('student', function (Builder $student) use ($search) {
                            $student->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('admission_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when($this->status !== '', function (Builder $query) {
                $query->where('status', $this->status);
            })
            ->when($this->sessionId !== '', function (Builder $query) use ($schoolId) {
                $query->where('academic_session_id', $this->sessionId)
                    ->whereHas('academicSession', function (Builder $session) use ($schoolId) {
                        $session->where('school_id', $schoolId);
                    });
            })
            ->when($this->termId !== '', function (Builder $query) use ($schoolId) {
                $query->where('term_id', $this->termId)
                    ->whereHas('term', function (Builder $term) use ($schoolId) {
                        $term->where('school_id', $schoolId);
                    });
            });
    }

    public function viewInvoice(int $invoiceId): void
    {
        $this->selectedInvoiceId = (int) Invoice::query()
            ->where('school_id', $this->schoolId())
            ->whereKey($invoiceId)
            ->value('id');

        abort_unless($this->selectedInvoiceId, 404);
    }

    public function closeInvoice(): void
    {
        $this->selectedInvoiceId = null;
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $invoices = $this->invoiceQuery()
            ->latest('id')
            ->paginate(10);

        $invoice = $this->selectedInvoiceId
            ? Invoice::query()
                ->where('school_id', $schoolId)
                ->with(['student', 'academicSession', 'term', 'items'])
                ->find($this->selectedInvoiceId)
            : null;

        $summary = Invoice::query()
            ->where('school_id', $schoolId)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("COALESCE(SUM(amount_due), 0) as total_due")
            ->selectRaw("COALESCE(SUM(amount_paid), 0) as total_paid")
            ->selectRaw("COALESCE(SUM(balance), 0) as total_balance")
            ->first();

        $sessions = AcademicSession::query()
            ->where('school_id', $schoolId)
            ->orderByDesc('starts_at')
            ->get();

        $terms = Term::query()
            ->where('school_id', $schoolId)
            ->when($this->sessionId !== '', function ($query) {
                $query->where('academic_session_id', $this->sessionId);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.treasury.invoice-management', [
            'invoices' => $invoices,
            'invoice' => $invoice,
            'summary' => $summary,
            'sessions' => $sessions,
            'terms' => $terms,
        ])->layout('components.layouts.treasury', [
            'title' => 'Invoice Management',
        ]);
    }
}
