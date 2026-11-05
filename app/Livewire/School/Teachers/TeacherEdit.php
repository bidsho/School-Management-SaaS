<?php

namespace App\Livewire\School\Teachers;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class TeacherEdit extends Component
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

    /**
     * Retrieve a teacher belonging to the authenticated user's school.
     */
    private function getTeacher(): Teacher
    {
        $user = Auth::user();

        abort_unless($user, 401);

        abort_if(
            $user->isSuperAdmin(),
            403,
            'Super Admin accounts must use platform routes.'
        );

        abort_unless(
            $user->school_id,
            403,
            'Your account is not assigned to a school.'
        );

        return Teacher::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->findOrFail($this->teacherId);
    }

    /**
     * Initialize the component with the selected teacher.
     */
    public function mount(int $teacher): void
    {
        $record = $this->loadTeacherByRouteId($teacher);

        $this->teacherId = $record->id;
        $this->staff_number = $record->staff_number ?? '';
        $this->first_name = $record->first_name ?? '';
        $this->middle_name = $record->middle_name ?? '';
        $this->last_name = $record->last_name ?? '';
        $this->phone = $record->phone ?? '';
        $this->address = $record->address ?? '';
        $this->qualification = $record->qualification ?? '';
        $this->employment_status = $record->employment_status ?? 'active';

        $this->hired_at = $record->hired_at?->format('Y-m-d') ?? '';
    }

    /**
     * Load the route-selected teacher within the current school.
     */
    private function loadTeacherByRouteId(int $teacher): Teacher
    {
        $user = Auth::user();

        abort_unless($user, 401);

        abort_if($user->isSuperAdmin(), 403);

        abort_unless($user->school_id, 403);

        return Teacher::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->findOrFail($teacher);
    }

    /**
     * Validation rules.
     */
    protected function rules(): array
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_unless($user->school_id, 403);

        return [
            'staff_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('teachers', 'staff_number')
                    ->where('school_id', $user->school_id)
                    ->ignore($this->teacherId),
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'qualification' => [
                'nullable',
                'string',
                'max:255',
            ],

            'employment_status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                    'on_leave',
                    'terminated',
                ]),
            ],

            'hired_at' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
        ];
    }

    /**
     * Save teacher changes.
     */
    public function save()
    {
        // Recheck authentication, permissions and school ownership.
        $teacher = $this->getTeacher();

        $validated = $this->validate();

        $teacher->update($validated);

        session()->flash(
            'success',
            'Teacher information updated successfully.'
        );

        return redirect()->route('school.teachers.index');
    }

    /**
     * Render the edit page.
     */
    public function render()
    {
        // Recheck access on subsequent Livewire requests.
        $teacher = $this->getTeacher();

        return view('livewire.school.teachers.teacher-edit', [
            'teacher' => $teacher,
        ])->layout('components.layouts.school', [
            'title' => 'Edit Teacher',
        ]);
    }
}
