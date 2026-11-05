<?php

namespace App\Livewire\School\Students;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Livewire\Concerns\UsesPortalLayout;

class StudentShow extends Component

{
    use UsesPortalLayout;
     public function mount(int $student): void
    {
        $user = Auth::user();

       $student = Student::query()
            ->where('school_id', $user->school_id)
            ->findOrFail($student);

        $this->student = $student;
    }
    public function render()
    {
        return view('livewire.school.students.student-show', [
            'student' => $this->student,
        ])->layout($this->portalLayout(), [
            'title' => 'Student Details',
        ]);
    }
}
