<?php

namespace App\Livewire\Parent\Payments;

use App\Models\Receipt;
use App\Models\SchoolParent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class ReceiptHistory extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Locked]
    public int $parentId;

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user && $user->hasRole('Parent'), 403);

        $parent = SchoolParent::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $this->parentId = $parent->id;
    }

    private function receiptsQuery(): Builder
    {
        $user = Auth::user();

        $parent = SchoolParent::query()
            ->whereKey($this->parentId)
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $childIds = $parent->students()
            ->wherePivot('school_id', $user->school_id)
            ->select('students.id');

        return Receipt::query()
            ->whereHas('invoice', function (Builder $invoice) use ($childIds) {
                $invoice->whereIn('student_id', $childIds);
            })
            ->with([
                'invoice.student',
                'payment',
            ])
            ->latest('issued_at');
    }

    public function render()
    {
        return view('livewire.parent.payments.receipt-history', [
            'receipts' => $this->receiptsQuery()->paginate(10),
        ])->layout('components.layouts.parent');
    }
}
