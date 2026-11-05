<?php

namespace App\Livewire\Treasury;

use App\Models\AcademicSession;
use App\Models\Invoice;
use App\Models\Term;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OutstandingBalances extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    /**
     * Available filters:
     * all       = all invoices
     * outstanding = unpaid and partially paid invoices
     * unpaid    = invoices with no payment
     * partial   = invoices with some payment remaining
     * overdue   = outstanding invoices past their due date
     * paid      = fully paid invoices
     */
    public string $statusFilter = 'outstanding';

    public string $sessionFilter = '';
    public string $termFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSessionFilter(): void
    {
        $this->resetPage();

        // A term must belong to the selected academic session.
        $this->termFilter = '';
    }

    public function updatedTermFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Restrict all financial information to the authenticated
     * Treasury user's school.
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
     * Base invoice query, scoped to this school.
     *
     * Students with zero payments are included whenever their
     * invoice balance is greater than zero.
     */
    private function invoicesQuery(): Builder
    {
        $query = Invoice::query()
            ->where('school_id', $this->schoolId())
            ->with([
                'student',
                'academicSession',
                'term',
            ]);

        $search = trim($this->search);

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function (Builder $student) use ($search) {
                        $student->where(function (Builder $nameQuery) use ($search) {
                            $nameQuery
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('admission_number', 'like', "%{$search}%")
                                ->orWhereRaw(
                                    "CONCAT(first_name, ' ', last_name) LIKE ?",
                                    ["%{$search}%"]
                                );
                        });
                    });
            });
        }

        if ($this->sessionFilter !== '') {
            $query->where(
                'academic_session_id',
                (int) $this->sessionFilter
            );
        }

        if ($this->termFilter !== '') {
            $query->where('term_id', (int) $this->termFilter);
        }

        switch ($this->statusFilter) {
            case 'all':
                // Show every invoice.
                break;

            case 'unpaid':
                // The entire invoice balance remains unpaid.
                $query->where('balance', '>', 0)
                    ->where(function (Builder $q) {
                        $q->whereNull('amount_paid')
                            ->orWhere('amount_paid', '<=', 0);
                    });
                break;

            case 'partial':
                // Some money has been paid, but a balance remains.
                $query->where('balance', '>', 0)
                    ->where('amount_paid', '>', 0);
                break;

            case 'overdue':
                $query->where('balance', '>', 0)
                    ->whereNotNull('due_at')
                    ->whereDate('due_at', '<', now()->toDateString());
                break;

            case 'paid':
                $query->where('balance', '<=', 0);
                break;

            case 'outstanding':
            default:
                // Includes both completely unpaid and partially paid.
                $query->where('balance', '>', 0);
                break;
        }

        return $query;
    }

    /**
     * Summary figures use the same school and academic filters.
     */
    private function summaryQuery(int $schoolId): Builder
    {
        $query = Invoice::query()
            ->where('school_id', $schoolId);

        if ($this->sessionFilter !== '') {
            $query->where(
                'academic_session_id',
                (int) $this->sessionFilter
            );
        }

        if ($this->termFilter !== '') {
            $query->where('term_id', (int) $this->termFilter);
        }

        return $query;
    }

    /**
     * Reset filters.
     */
    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'sessionFilter',
            'termFilter',
        ]);

        $this->statusFilter = 'outstanding';

        $this->resetPage();
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $sessions = AcademicSession::query()
            ->where('school_id', $schoolId)
            ->orderByDesc('starts_at')
            ->get();

        $termsQuery = Term::query()
            ->where('school_id', $schoolId);

        if ($this->sessionFilter !== '') {
            $termsQuery->where(
                'academic_session_id',
                (int) $this->sessionFilter
            );
        }

        $terms = $termsQuery
            ->orderBy('name')
            ->get();

        $summaryQuery = $this->summaryQuery($schoolId);

        $summary = [
            'invoices' => (clone $summaryQuery)->count(),

            'outstanding_count' => (clone $summaryQuery)
                ->where('balance', '>', 0)
                ->count(),

            'unpaid_count' => (clone $summaryQuery)
                ->where('balance', '>', 0)
                ->where(function (Builder $q) {
                    $q->whereNull('amount_paid')
                        ->orWhere('amount_paid', '<=', 0);
                })
                ->count(),

            'partial_count' => (clone $summaryQuery)
                ->where('balance', '>', 0)
                ->where('amount_paid', '>', 0)
                ->count(),

            'outstanding_total' => (clone $summaryQuery)
                ->where('balance', '>', 0)
                ->sum('balance'),

            'overdue_count' => (clone $summaryQuery)
                ->where('balance', '>', 0)
                ->whereNotNull('due_at')
                ->whereDate('due_at', '<', now()->toDateString())
                ->count(),

            'overdue_total' => (clone $summaryQuery)
                ->where('balance', '>', 0)
                ->whereNotNull('due_at')
                ->whereDate('due_at', '<', now()->toDateString())
                ->sum('balance'),

            'paid_count' => (clone $summaryQuery)
                ->where('balance', '<=', 0)
                ->count(),

            'total_expected' => (clone $summaryQuery)
                ->sum('amount_due'),

            'total_collected' => (clone $summaryQuery)
                ->sum('amount_paid'),
        ];

        $invoices = $this->invoicesQuery()
            ->orderByRaw('CASE WHEN balance > 0 THEN 0 ELSE 1 END')
            ->orderBy('due_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.treasury.outstanding-balances', [
            'invoices' => $invoices,
            'sessions' => $sessions,
            'terms' => $terms,
            'summary' => $summary,
        ])->layout('components.layouts.treasury', [
            'title' => 'Outstanding Balances',
        ]);
    }
}
