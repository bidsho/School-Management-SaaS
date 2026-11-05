<?php

namespace App\Livewire\School\AcademicSessions;

use App\Models\AcademicSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AcademicSessionIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    protected string $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status']);
        $this->resetPage();
    }

    public function deleteSession(int $sessionId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $session = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->whereKey($sessionId)
            ->firstOrFail();

        if (
            $session->terms()->exists()
            || $session->enrollments()->exists()
            || $session->exams()->exists()
            || $session->feeStructures()->exists()
        ) {
            session()->flash(
                'error',
                'This session cannot be deleted because it has associated terms, enrollments, exams or fee structures.'
            );

            return;
        }

        $session->delete();

        session()->flash('success', 'Academic session deleted successfully.');

        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = (int) $user->school_id;

        $query = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        if (trim($this->search) !== '') {
            $search = trim($this->search);

            $query->where('name', 'like', "%{$search}%");
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        $sessions = $query
            ->withCount(['terms', 'enrollments'])
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->paginate(10);

        $statsQuery = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        return view('livewire.school.academic-sessions.academic-session-index', [
            'sessions' => $sessions,
            'totalSessions' => (clone $statsQuery)->count(),
            'activeSessions' => (clone $statsQuery)
                ->where('status', 'active')->count(),
            'inactiveSessions' => (clone $statsQuery)
                ->where('status', 'inactive')->count(),
        ])->layout('components.layouts.school', [
            'title' => 'Academic Sessions',
        ]);
    }
}
