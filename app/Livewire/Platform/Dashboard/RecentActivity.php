<?php

namespace App\Livewire\Platform\Dashboard;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RecentActivity extends Component
{
    public function render()
    {
        $activities = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->leftJoin('schools', 'audit_logs.school_id', '=', 'schools.id')
            ->select([
                'audit_logs.id',
                'audit_logs.action',
                'audit_logs.model_type',
                'audit_logs.model_id',
                'audit_logs.ip_address',
                'audit_logs.metadata',
                'audit_logs.created_at',
                'users.name as actor_name',
                'users.email as actor_email',
                'schools.name as school_name',
            ])
            ->orderByDesc('audit_logs.created_at')
            ->orderByDesc('audit_logs.id')
            ->limit(8)
            ->get();

        return view(
            'livewire.platform.dashboard.recent-activity',
            compact('activities')
        );
    }
}
