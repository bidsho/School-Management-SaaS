<?php

namespace App\Livewire\Receptionist;

use App\Models\Receptionist;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Dashboard extends Component
{
    #[Locked]
    public int $receptionistId;

    /**
     * Load the authenticated Receptionist.
     */
    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user, 403, 'Please log in first.');

        abort_unless(
            $user->hasRole('Receptionist'),
            403,
            'You are not authorized to access this dashboard.'
        );

        abort_unless(
            $user->school_id,
            403,
            'Your account is not assigned to a school.'
        );

        $receptionist = Receptionist::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->first();

        abort_unless(
            $receptionist,
            403,
            'Your Receptionist profile is not linked to your login account. Contact your School Administrator.'
        );

        $this->receptionistId = $receptionist->id;
    }

    /**
     * Get the authenticated Receptionist profile.
     */
    protected function getReceptionist(): Receptionist
    {
        $user = Auth::user();

        abort_unless($user, 403);

        return Receptionist::query()
            ->where('id', $this->receptionistId)
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->with(['school', 'user'])
            ->firstOrFail();
    }

    /**
     * Render dashboard.
     */
    public function render(): View
    {
        $receptionist = $this->getReceptionist();

        return view('livewire.receptionist.dashboard', [
            'receptionist' => $receptionist,
            'school' => $receptionist->school,
            'today' => now()->format('l, d F Y'),
        ])->layout('components.layouts.receptionist');
    }
}
