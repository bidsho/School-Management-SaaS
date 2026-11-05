<?php

namespace App\Livewire\Parent\Children;

use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.parent')]
#[Title('My Children')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        // Ensure the user is authenticated and has the Parent role.
        abort_unless($user && $user->hasRole('Parent'), 403);

        // Ensure the account belongs to a school.
        abort_unless(
            $user->school_id,
            403,
            'Your account is not linked to a school.'
        );

        // Retrieve the existing parent profile for this user and school.
        $parent = $user->parentProfile()
            ->where('school_id', $user->school_id)
            ->first();

        // Temporary diagnostics: remove after resolving the issue.
        Log::info('Parent Children Debug', [
            'user_id' => $user->id,
            'user_school_id' => $user->school_id,
            'has_parent_role' => $user->hasRole('Parent'),
            'parent_profile_id' => $parent?->id,
            'parent_user_id' => $parent?->user_id,
            'parent_school_id' => $parent?->school_id,
            'component' => static::class,
        ]);

        // Start with an empty, school-scoped result.
        $students = Student::query()
            ->where('school_id', $user->school_id)
            ->whereRaw('1 = 0')
            ->paginate(10);

        if ($parent !== null) {
            $search = trim($this->search);

            $students = $parent->students()
                ->wherePivot('school_id', $user->school_id)
                ->where('students.school_id', $user->school_id)
                ->when(
                    $search !== '',
                    function (Builder $query) use ($search) {
                        $query->where(function (Builder $query) use ($search) {
                            $query->where(
                                'students.first_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'students.middle_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'students.last_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'students.admission_number',
                                'like',
                                "%{$search}%"
                            );
                        });
                    }
                )
                ->orderBy('students.first_name')
                ->orderBy('students.last_name')
                ->paginate(10);
        }

        return view('livewire.parent.children.index', [
            'students' => $students,
            'parentProfileExists' => $parent !== null,
        ]);
    }
}
