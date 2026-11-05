<?php

namespace App\Livewire\Parent\Announcements;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasRole('Parent'),
            403
        );

        abort_unless($user->school_id, 403);

        $announcements = Announcement::query()
            ->where('school_id', $user->school_id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function (Builder $query) {
                $query->whereNull('audience')
                    ->orWhereIn('audience', [
                        'all',
                        'parents',
                        'parent',
                    ]);
            })
            ->when(
                trim($this->search) !== '',
                function (Builder $query) {
                    $search = '%' . trim($this->search) . '%';

                    $query->where(function (Builder $query) use ($search) {
                        $query->where('title', 'like', $search)
                            ->orWhere('body', 'like', $search);
                    });
                }
            )
            ->orderByDesc('published_at')
            ->paginate(10);

        return view(
            'livewire.parent.announcements.announcement-index',
            compact('announcements')
        )->layout('components.layouts.parent');
    }
}
