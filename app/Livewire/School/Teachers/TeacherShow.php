<?php

namespace App\Livewire\School\Teachers;

use App\Models\Teacher;
use Livewire\Attributes\Locked;
use Livewire\Component;

class TeacherShow extends Component
{
    #[Locked]
    public int $teacherId;

    public string $staff_number = '';
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $phone = '';
    public string $address = '';
    public string $qualification = '';
    public string $employment_status = 'active';
    public string $hired_at = '';

    public function mount(int $teacher): void
    {
        $user = auth()->user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        // Restrict the record to the authenticated user's school.
        $record = Teacher::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->findOrFail($teacher);

        $this->teacherId = $record->id;
        $this->staff_number = $record->staff_number ?? '';
        $this->first_name = $record->first_name ?? '';
        $this->middle_name = $record->middle_name ?? '';
        $this->last_name = $record->last_name ?? '';
        $this->phone = $record->phone ?? '';
        $this->address = $record->address ?? '';
        $this->qualification = $record->qualification ?? '';
        $this->employment_status = $record->employment_status ?? 'active';
        $this->hired_at = $record->hired_at
            ? $record->hired_at->format('Y-m-d')
            : '';
    }

    public function render()
    {
        return view('livewire.school.teachers.teacher-show')
            ->layout('components.layouts.school', [
                'title' => 'Teacher Details',
            ]);
    }
}
