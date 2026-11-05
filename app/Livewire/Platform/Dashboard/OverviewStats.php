<?php

namespace App\Livewire\Platform\Dashboard;

use App\Models\School;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\User;
use Livewire\Component;

class OverviewStats extends Component
{
    public function render()
    {
        return view('livewire.platform.dashboard.overview-stats', [
            'stats' => [
                'totalSchools' => School::count(),

                'activeSchools' => School::where(
                    'status',
                    'active'
                )->count(),

                'totalUsers' => User::count(),

                'totalStudents' => Student::count(),

                'activeSubscriptions' => Subscription::where(
                    'status',
                    'active'
                )->count(),
            ],
        ]);
    }
}
