<?php

namespace App\Livewire\Platform\Dashboard;

use App\Models\School;
use Livewire\Component;

class RecentSchools extends Component
{
    public function render()
    {
        $schools = School::query()
            ->with(['subscription.plan'])
            ->latest()
            ->take(5)
            ->get();

        return view(
            'livewire.platform.dashboard.recent-schools',
            compact('schools')
        );
    }
}
