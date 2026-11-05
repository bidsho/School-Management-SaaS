<?php

namespace App\Livewire\Platform\Users;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $schoolFilter = '';

    public string $roleFilter = '';

    public string $statusFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSchoolFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function toggleStatus(int $userId): void
    {
        $user = User::with('roles')->findOrFail($userId);

        // Never allow this action to suspend or deactivate its own account.
        if ($user->id === auth()->id()) {
            session()->flash(
                'error',
                'You cannot change your own account status here.'
            );

            return;
        }

        // Protect Super Admin accounts from this user-management action.
        if ($user->hasRole('Super Admin')) {
            session()->flash(
                'error',
                'Super Admin accounts cannot be suspended from this screen.'
            );

            return;
        }

        if (! in_array($user->status, ['active', 'suspended'], true)) {
            session()->flash(
                'error',
                'This account has an unsupported status and was not changed.'
            );

            return;
        }

        $user->update([
            'status' => $user->status === 'active'
                ? 'suspended'
                : 'active',
        ]);

        session()->flash(
            'success',
            'User account status updated successfully.'
        );
    }

    public function render()
    {
        $users = User::query()
            ->with(['school', 'roles'])
            ->when($this->search !== '', function (Builder $query) {
                $search = '%' . trim($this->search) . '%';

                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search);
                });
            })
            ->when($this->schoolFilter !== '', function (Builder $query) {
                $query->where('school_id', $this->schoolFilter);
            })
            ->when($this->roleFilter !== '', function (Builder $query) {
                $query->whereHas('roles', function (Builder $roleQuery) {
                    $roleQuery->where('roles.name', $this->roleFilter);
                });
            })
            ->when($this->statusFilter !== '', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);

        $schools = School::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['name']);

        return view(
            'livewire.platform.users.user-index',
            compact('users', 'schools', 'roles')
        )->extends('layouts.super');
    }
}
