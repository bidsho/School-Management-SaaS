<?php

namespace App\Livewire\School\Parents;

use App\Models\SchoolParent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Concerns\UsesPortalLayout;
class ParentIndex extends Component
{
    use WithPagination, UsesPortalLayout;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user && $user->school_id, 403);

        return (int) $user->school_id;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteParent(int $parentId): void
    {
        abort_unless(Auth::user()?->can('manage parents'), 403);

        $parent = SchoolParent::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->findOrFail($parentId);

        if ($parent->students()->exists()) {
            session()->flash(
                'error',
                'This parent is linked to students. Unlink the students before deleting the parent.'
            );

            return;
        }

        if ($parent->user_id !== null) {
            session()->flash(
                'error',
                'This parent has a login account. Disable or unlink the account through the account-management process before deleting this profile.'
            );

            return;
        }

        $parent->delete();

        session()->flash('success', 'Parent deleted successfully.');
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $parentsQuery = SchoolParent::withoutGlobalScopes()
            ->where('school_id', $schoolId);

        $parents = (clone $parentsQuery)
            ->when(trim($this->search) !== '', function (Builder $query) {
                $search = trim($this->search);

                $query->where(function (Builder $query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount('students')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10);

        return view('livewire.school.parents.parent-index', [
            'parents' => $parents,
            'totalParents' => (clone $parentsQuery)->count(),
            'linkedParents' => (clone $parentsQuery)
                ->whereHas('students')
                ->count(),
        ])->layout($this->portalLayout());
    }
}
