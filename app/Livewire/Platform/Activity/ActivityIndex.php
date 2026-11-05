<?php

namespace App\Livewire\Platform\Activity;

use App\Models\AuditLog;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $actionFilter = '';
    public string $schoolFilter = '';
    public string $dateFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedActionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSchoolFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'actionFilter',
            'schoolFilter',
            'dateFilter',
        ]);

        $this->resetPage();
    }

    public function render()
    {
        $query = AuditLog::query()
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->leftJoin('schools', 'audit_logs.school_id', '=', 'schools.id')
            ->select([
                'audit_logs.*',
                'users.name as actor_name',
                'users.email as actor_email',
                'schools.name as school_name',
            ]);

        $query
            ->when($this->search !== '', function (Builder $query) {
                $search = '%' . trim($this->search) . '%';

                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery
                        ->where('audit_logs.action', 'like', $search)
                        ->orWhere('audit_logs.model_type', 'like', $search)
                        ->orWhere('audit_logs.ip_address', 'like', $search)
                        ->orWhere('users.name', 'like', $search)
                        ->orWhere('users.email', 'like', $search)
                        ->orWhere('schools.name', 'like', $search);
                });
            })
            ->when($this->actionFilter !== '', function (Builder $query) {
                $query->where('audit_logs.action', $this->actionFilter);
            })
            ->when($this->schoolFilter !== '', function (Builder $query) {
                $query->where('audit_logs.school_id', $this->schoolFilter);
            })
            ->when($this->dateFilter !== '', function (Builder $query) {
                match ($this->dateFilter) {
                    'today' => $query->whereDate(
                        'audit_logs.created_at',
                        today()
                    ),
                    '7days' => $query->where(
                        'audit_logs.created_at',
                        '>=',
                        now()->subDays(7)->startOfDay()
                    ),
                    '30days' => $query->where(
                        'audit_logs.created_at',
                        '>=',
                        now()->subDays(30)->startOfDay()
                    ),
                    default => null,
                };
            });

        $activities = $query
            ->orderByDesc('audit_logs.created_at')
            ->orderByDesc('audit_logs.id')
            ->paginate(15);

        $schools = School::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $actions = AuditLog::query()
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view(
            'livewire.platform.activity.activity-index',
            compact('activities', 'schools', 'actions')
        )->extends('layouts.super');
    }
}
