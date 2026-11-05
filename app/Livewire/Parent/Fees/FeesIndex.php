<?php

namespace App\Livewire\Parent\Fees;

use App\Models\Invoice;
use App\Models\SchoolParent;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.parent')]
#[Title('School Fees')]
class FeesIndex extends Component
{
    use WithPagination;

    #[Locked]
    public int $parentId;

    public string $studentId = 'all';

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless($user->hasRole('Parent'), 403);
        abort_unless($user->school_id, 403);

        $parent = $user->parentProfile()
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $this->parentId = (int) $parent->id;
    }

    private function parentProfile(): SchoolParent
    {
        $user = Auth::user();

        abort_unless($user && $user->hasRole('Parent'), 403);
        abort_unless($user->school_id, 403);

        return $user->parentProfile()
            ->whereKey($this->parentId)
            ->where('school_id', $user->school_id)
            ->firstOrFail();
    }

    private function childrenQuery(): Builder
    {
        $parent = $this->parentProfile();

        return Student::query()
            ->whereIn(
                'students.id',
                $parent->students()
                    ->wherePivot('school_id', Auth::user()->school_id)
                    ->select('students.id')
            );
    }

    private function validateSelectedChild(): void
    {
        if ($this->studentId === 'all') {
            return;
        }

        abort_unless(
            ctype_digit($this->studentId)
                && $this->childrenQuery()
                    ->whereKey((int) $this->studentId)
                    ->exists(),
            404
        );
    }

    public function updatedStudentId(): void
    {
        $this->validateSelectedChild();
        $this->resetPage();
    }

    private function invoicesQuery(): Builder
    {
        $this->validateSelectedChild();

        $parent = $this->parentProfile();

        $childIds = $parent->students()
            ->wherePivot('school_id', Auth::user()->school_id)
            ->select('students.id');

        $query = Invoice::query()
            ->whereIn('student_id', $childIds)
            ->with([
                'student',
                'academicSession',
                'term',
                'items',
            ]);

        if ($this->studentId !== 'all') {
            $query->where('student_id', (int) $this->studentId);
        }

        return $query;
    }

    public function render()
    {
        $children = $this->childrenQuery()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $query = $this->invoicesQuery();

        $summary = [
            'count' => (clone $query)->count(),
            'amount_due' => (clone $query)->sum('amount_due'),
            'amount_paid' => (clone $query)->sum('amount_paid'),
            'balance' => (clone $query)->sum('balance'),
        ];

        $invoices = $this->invoicesQuery()
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view(
            'livewire.parent.fees.fees-index',
            compact('children', 'invoices', 'summary')
        );
    }
}
