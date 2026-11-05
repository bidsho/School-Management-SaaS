<?php

namespace App\Livewire\School\Terms;

use App\Models\AcademicSession;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TermIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $sessionFilter = '';

    protected string $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingSessionFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status', 'sessionFilter']);
        $this->resetPage();
    }

    public function deleteTerm(int $termId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $term = Term::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->whereKey($termId)
            ->firstOrFail();

        if (
            $term->enrollments()->exists()
            || $term->exams()->exists()
            || $term->feeStructures()->exists()
        ) {
            session()->flash(
                'error',
                'This term cannot be deleted because it has associated enrollments, exams or fee structures.'
            );

            return;
        }

        $term->delete();

        session()->flash('success', 'Term deleted successfully.');

        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = (int) $user->school_id;

        $query = Term::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with('academicSession')
            ->withCount(['enrollments', 'exams']);

        if (trim($this->search) !== '') {
            $search = trim($this->search);

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhereHas('academicSession', function ($sessionQuery) use ($search, $schoolId) {
                        $sessionQuery->where('school_id', $schoolId)
                            ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->sessionFilter !== '') {
            $query->where('academic_session_id', $this->sessionFilter);
        }

        $terms = $query
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->paginate(10);

        $statsQuery = Term::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        $sessions = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->orderByDesc('starts_at')
            ->get(['id', 'name']);

        return view('livewire.school.terms.term-index', [
            'terms' => $terms,
            'sessions' => $sessions,
            'totalTerms' => (clone $statsQuery)->count(),
            'activeTerms' => (clone $statsQuery)->where('status', 'active')->count(),
            'inactiveTerms' => (clone $statsQuery)->where('status', 'inactive')->count(),
        ])->layout('components.layouts.school', [
            'title' => 'Academic Terms',
        ]);
    }
}
