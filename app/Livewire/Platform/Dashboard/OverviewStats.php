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
        $stats = [
            'totalSchools' => School::count(),

            'activeSchools' => School::where('status', 'active')->count(),

            'totalUsers' => User::count(),

            'totalStudents' => Student::count(),

            'activeSubscriptions' => Subscription::where(
                'status',
                'active'
            )->count(),
        ];

        // Last 6 months platform growth
        $chartData = [
            'labels' => [],
            'schools' => [],
            'users' => [],
            'students' => [],
            'subscriptions' => [],
        ];

        $month = now()->startOfMonth()->subMonths(5);

        for ($i = 0; $i < 6; $i++) {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $chartData['labels'][] = $month->format('M Y');

            $chartData['schools'][] = School::whereBetween(
                'created_at',
                [$start, $end]
            )->count();

            $chartData['users'][] = User::whereBetween(
                'created_at',
                [$start, $end]
            )->count();

            $chartData['students'][] = Student::whereBetween(
                'created_at',
                [$start, $end]
            )->count();

            $chartData['subscriptions'][] = Subscription::whereBetween(
                'created_at',
                [$start, $end]
            )->count();

            $month->addMonth();
        }

        return view('livewire.platform.dashboard.overview-stats', [
            'stats' => $stats,
            'chartData' => $chartData,
        ]);
    }
}
