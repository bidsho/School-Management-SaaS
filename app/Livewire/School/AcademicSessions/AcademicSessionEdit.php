<?php

namespace App\Livewire\School\AcademicSessions;

use App\Models\AcademicSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class AcademicSessionEdit extends Component
{
    #[Locked]
    public int $sessionId;

    public string $name = '';
    public string $starts_at = '';
    public string $ends_at = '';
    public string $status = 'active';

    public function mount(int $sessionId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $session = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->whereKey($sessionId)
            ->firstOrFail();

        $this->sessionId = $session->id;
        $this->name = $session->name;
        $this->starts_at = $session->starts_at?->format('Y-m-d') ?? '';
        $this->ends_at = $session->ends_at?->format('Y-m-d') ?? '';
        $this->status = $session->status ?? 'inactive';
    }

    public function save()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = (int) $user->school_id;

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('academic_sessions', 'name')
                    ->where('school_id', $schoolId)
                    ->ignore($this->sessionId),
            ],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $session = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereKey($this->sessionId)
            ->firstOrFail();

        $session->update([
            'name' => trim($validated['name']),
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'status' => $validated['status'],
        ]);

        session()->flash('success', 'Academic session updated successfully.');

        return $this->redirectRoute('school.academic-sessions.index');
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        return view('livewire.school.academic-sessions.academic-session-edit')
            ->layout('components.layouts.school', [
                'title' => 'Edit Academic Session',
            ]);
    }
}
