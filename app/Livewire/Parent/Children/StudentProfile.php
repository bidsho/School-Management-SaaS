<?php

namespace App\Livewire\Parent\Children;

use App\Models\SchoolParent;
use App\Models\Student;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.parent')]
#[Title('Student Profile')]
class StudentProfile extends Component
{
    public Student $student;

    public SchoolParent $parent;

    public function mount(int $studentId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless($user->hasRole('Parent'), 403);
        abort_unless($user->school_id, 403, 'Your account is not linked to a school.');

        // Resolve the authenticated parent's profile inside their school.
        $this->parent = $user->parentProfile()
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        // Only allow access to students explicitly linked to this parent.
        // The school filter also verifies the pivot record belongs to this tenant.
        $this->student = $this->parent->students()
            ->wherePivot('school_id', $user->school_id)
            ->where('students.id', $studentId)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.parent.children.student-profile');
    }
}
