<?php

namespace App\Livewire\School\Teachers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class TeacherCreate extends Component
{
    public string $staff_number = '';
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';

    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public string $phone = '';
    public string $address = '';
    public string $qualification = '';
    public string $employment_status = 'active';
    public string $hired_at = '';

    protected function rules(): array
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);

        return [
            'staff_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('teachers', 'staff_number')
                    ->where('school_id', $user->school_id),
            ],

            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'qualification' => ['nullable', 'string', 'max:255'],

            'employment_status' => [
                'required',
                Rule::in(['active', 'inactive', 'on_leave', 'terminated']),
            ],

            'hired_at' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    public function save()
    {
        $schoolAdmin = Auth::user();

        abort_unless($schoolAdmin, 401);

        abort_if(
            $schoolAdmin->isSuperAdmin(),
            403,
            'Super Admin accounts must use platform routes.'
        );

        abort_unless(
            $schoolAdmin->school_id,
            403,
            'Your account is not assigned to a school.'
        );

        $validated = $this->validate();

        // Check that the role exists before creating either record.
        abort_unless(
            \Spatie\Permission\Models\Role::where('name', 'Teacher')
                ->where('guard_name', 'web')
                ->exists(),
            500,
            'The Teacher role has not been seeded.'
        );

        DB::transaction(function () use ($validated, $schoolAdmin) {
            $fullName = trim(implode(' ', array_filter([
                $validated['first_name'],
                $validated['middle_name'] ?? null,
                $validated['last_name'],
            ])));

            $teacherUser = User::create([
                'name' => $fullName,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'school_id' => $schoolAdmin->school_id,
            ]);

            $teacherUser->assignRole('Teacher');

            Teacher::create([
                'school_id' => $schoolAdmin->school_id,
                'user_id' => $teacherUser->id,
                'staff_number' => $validated['staff_number'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'qualification' => $validated['qualification'] ?? null,
                'employment_status' => $validated['employment_status'],
                'hired_at' => $validated['hired_at'] ?: null,
            ]);
        });

        $this->reset([
            'staff_number',
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'password',
            'password_confirmation',
            'phone',
            'address',
            'qualification',
            'hired_at',
        ]);

        session()->flash(
            'success',
            'Teacher and login account created successfully.'
        );

        return redirect()->route('school.teachers.index');
    }

    public function render()
    {
        return view('livewire.school.teachers.teacher-create')
            ->layout('components.layouts.school', [
                'title' => 'Register Teacher',
            ]);
    }
}