<?php

namespace App\Livewire\Student;

use App\Models\Student as StudentModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Profile extends Component
{
    #[Locked]
    public int $studentId;

    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $date_of_birth = '';
    public string $gender = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';

    public function mount(): void
    {
        $student = $this->getStudent();

        $this->studentId = $student->id;
        $this->first_name = $student->first_name ?? '';
        $this->middle_name = $student->middle_name ?? '';
        $this->last_name = $student->last_name ?? '';
        $this->date_of_birth = $student->date_of_birth?->format('Y-m-d') ?? '';
        $this->gender = $student->gender ?? '';
        $this->email = $student->email ?? '';
        $this->phone = $student->phone ?? '';
        $this->address = $student->address ?? '';
    }

    /**
     * Always retrieve the student using the authenticated user's school.
     */
    private function getStudent(): StudentModel
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless($user->hasRole('Student'), 403);
        abort_unless($user->school_id, 403);

        return StudentModel::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();
    }

    public function save(): void
    {
        $student = $this->getStudent();

        // Never trust a client-supplied student ID.
        abort_unless($student->id === $this->studentId, 403);

        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $student->update($validated);

        $this->dispatch('profile-saved');
        session()->flash('success', 'Your profile has been updated successfully.');
    }

    public function render()
    {
        $student = $this->getStudent();

        return view('livewire.student.profile', [
            'student' => $student,
            'enrollmentCount' => $student->enrollments()->count(),
        ])->layout('components.layouts.student', [
            'title' => 'My Profile',
        ]);
    }
}