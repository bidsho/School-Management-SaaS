<?php

namespace App\Livewire\Parent\Payments;

use App\Models\Receipt;
use App\Models\SchoolParent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ReceiptDetails extends Component
{
    #[Locked]
    public int $receiptId;

    /**
     * Load the receipt using the numeric route parameter.
     */
    public function mount(int $receipt): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasRole('Parent'),
            403
        );

        abort_unless($user->school_id, 403);

        $parent = SchoolParent::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        // Get only students linked to this parent in this school.
        $childIds = $parent->students()
            ->wherePivot('school_id', $user->school_id)
            ->pluck('students.id');

        abort_if($childIds->isEmpty(), 404);

        // Find a receipt belonging to an authorized, verified payment.
        $authorizedReceipt = Receipt::query()
            ->whereKey($receipt)
            ->where('receipts.school_id', $user->school_id)
            ->whereHas('payment', function (Builder $query) use (
                $user,
                $childIds
            ) {
                $query
                    ->where('school_id', $user->school_id)
                    ->whereIn('student_id', $childIds)
                    ->whereNotNull('verified_at')
                    ->whereIn('status', [
                        'successful',
                        'success',
                        'paid',
                        'completed',
                    ]);
            })
            ->firstOrFail();

        $this->receiptId = (int) $authorizedReceipt->id;
    }

    /**
     * Recheck authorization whenever the component renders.
     */
    public function render()
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasRole('Parent'),
            403
        );

        abort_unless($user->school_id, 403);

        $parent = SchoolParent::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $childIds = $parent->students()
            ->wherePivot('school_id', $user->school_id)
            ->pluck('students.id');

        $receipt = Receipt::query()
            ->whereKey($this->receiptId)
            ->where('receipts.school_id', $user->school_id)
            ->whereHas('payment', function (Builder $query) use (
                $user,
                $childIds
            ) {
                $query
                    ->where('school_id', $user->school_id)
                    ->whereIn('student_id', $childIds)
                    ->whereNotNull('verified_at')
                    ->whereIn('status', [
                        'successful',
                        'success',
                        'paid',
                        'completed',
                    ]);
            })
            ->with([
                'school',
                'invoice',
                'payment.student',
            ])
            ->firstOrFail();

        return view(
            'livewire.parent.payments.receipt-details',
            compact('receipt')
        )->layout('components.layouts.parent');
    }
}
