<?php

namespace App\Livewire\Platform\Dashboard;

use App\Models\Subscription;
use Livewire\Component;

class SubscriptionStats extends Component
{
    public function render()
    {
        $now = now();

        $stats = [
            'total' => Subscription::count(),

            'active' => Subscription::where('status', 'active')
                ->count(),

            'expiringSoon' => Subscription::where('status', 'active')
                ->whereNotNull('ends_at')
                ->whereBetween('ends_at', [
                    $now,
                    $now->copy()->addDays(30),
                ])
                ->count(),

            'expiredByDate' => Subscription::whereNotNull('ends_at')
                ->where('ends_at', '<', $now)
                ->count(),
        ];

        // Use actual status values stored in the database.
        $statusBreakdown = Subscription::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        return view(
            'livewire.platform.dashboard.subscription-stats',
            compact('stats', 'statusBreakdown')
        )->extends('layouts.super');
    }
}
