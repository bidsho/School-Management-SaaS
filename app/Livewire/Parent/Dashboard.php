<?php

namespace App\Livewire\Parent;

use App\Models\Invoice;
use App\Models\SchoolParent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 403);

        abort_unless(
            $user->hasRole('Parent') && $user->school_id,
            403,
            'You are not authorized to access the Parent Dashboard.'
        );

        // Only use the authenticated parent's own profile.
        $parent = SchoolParent::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->first();

        $children = collect();
        $childIds = [];

        if ($parent) {
            // The parent_student pivot table links parents to students.
            $children = $parent->students()
                ->wherePivot('school_id', $user->school_id)
                ->withCount(['invoices', 'attendanceRecords', 'examScores'])
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();

            $childIds = $children->pluck('id')->all();
        }

        // Restrict all invoice summaries to the parent's linked children
        // and the authenticated user's school.
        $invoices = Invoice::query()
            ->where('school_id', $user->school_id)
            ->whereIn('student_id', $childIds);

        $totalInvoices = (clone $invoices)->count();

        $outstandingInvoices = (clone $invoices)
            ->where('balance', '>', 0);

        $outstandingCount = (clone $outstandingInvoices)->count();

        // Keep financial totals separated by currency.
        $outstandingByCurrency = (clone $outstandingInvoices)
            ->selectRaw('currency, SUM(balance) as total')
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        $recentInvoices = (clone $invoices)
            ->with(['student'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('livewire.parent.dashboard', [
            'parent' => $parent,
            'children' => $children,
            'totalChildren' => $children->count(),
            'totalInvoices' => $totalInvoices,
            'outstandingCount' => $outstandingCount,
            'outstandingByCurrency' => $outstandingByCurrency,
            'recentInvoices' => $recentInvoices,
        ])->layout('components.layouts.parent', [
            'title' => 'Parent Dashboard',
        ]);
    }
}
