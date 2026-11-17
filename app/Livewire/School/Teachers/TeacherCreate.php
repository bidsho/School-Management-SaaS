<?php

namespace App\Livewire\School\Teachers;

use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

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

    /**
     * Ensure that only School Admins and Principals
     * belonging to an active school can create teachers.
     */
    private function authorizedSchoolUser(): User
    {
        $user = Auth::user();

        abort_unless($user, 401);

        abort_if(
            $user->isSuperAdmin(),
            403,
            'Super Admin accounts must use platform routes.'
        );

        abort_unless(
            $user->hasAnyRole(['School Admin', 'Principal']),
            403,
            'You are not authorized to register teachers.'
        );

        abort_unless(
            $user->school_id,
            403,
            'Your account is not assigned to a school.'
        );

        $school = School::query()
            ->whereKey($user->school_id)
            ->where('status', 'active')
            ->first();

        abort_unless(
            $school,
            403,
            'Your school is unavailable or inactive.'
        );

        return $user;
    }

    /**
     * Validation rules.
     */
    protected function rules(): array
    {
        $user = $this->authorizedSchoolUser();

        return [
            'staff_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('teachers', 'staff_number')
                    ->where(
                        fn ($query) => $query->where(
                            'school_id',
                            $user->school_id
                        )
                    ),
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
     * Register a teacher and create the login account.
     */
    public function save()
    {
        $schoolUser = $this->authorizedSchoolUser();

        // Normalize values before validation.
        $this->email = Str::lower(trim($this->email));
        $this->staff_number = strtoupper(trim($this->staff_number));

        $this->first_name = trim($this->first_name);
        $this->middle_name = trim($this->middle_name);
        $this->last_name = trim($this->last_name);

        $validated = $this->validate();

        // Confirm that the Spatie Teacher role exists.
        $teacherRoleExists = Role::query()
            ->where('name', 'Teacher')
            ->where('guard_name', 'web')
            ->exists();

        abort_unless(
            $teacherRoleExists,
            500,
            'The Teacher role has not been seeded. Please contact the system administrator.'
        );

        $schoolId = $schoolUser->school_id;

        DB::transaction(function () use ($validated, $schoolId) {

            // Create the teacher's full name.
            $fullName = trim(implode(' ', array_filter([
                $validated['first_name'],
                $validated['middle_name'] ?? null,
                $validated['last_name'],
            ])));

            /*
             * Create the teacher login account.
             *
             * IMPORTANT:
             * The database role column is explicitly set to teacher.
             * This prevents the database default school_admin role
             * from being assigned to a newly created teacher.
             */
            $teacherUser = new User();

            $teacherUser->name = $fullName;
            $teacherUser->email = Str::lower($validated['email']);
            $teacherUser->password = Hash::make($validated['password']);
            $teacherUser->school_id = $schoolId;
            $teacherUser->role = 'teacher';
            $teacherUser->status = 'active';

            $teacherUser->save();

            // Assign the Spatie role.
            $teacherUser->assignRole('Teacher');

            /*
             * Create the teacher profile.
             *
             * The school ID comes from the authenticated
             * School Admin or Principal, never from the form.
             */
            Teacher::create([
                'school_id' => $schoolId,

                'user_id' => $teacherUser->id,

                'staff_number' => $validated['staff_number'],

                'first_name' => $validated['first_name'],

                'middle_name' => $validated['middle_name'] ?: null,

                'last_name' => $validated['last_name'],

                'phone' => $validated['phone'] ?: null,

                'address' => $validated['address'] ?: null,

                'qualification' => $validated['qualification'] ?: null,

                'employment_status' => $validated['employment_status'],

                'hired_at' => $validated['hired_at'] ?: null,
            ]);
        });

        // Clear the form after successful registration.
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

        $this->employment_status = 'active';

        $this->resetValidation();

        session()->flash(
            'success',
            'Teacher and login account created successfully.'
        );

        return redirect()->route('school.teachers.index');
    }

    /**
     * Display the registration page.
     */
    public function render()
    {
        $this->authorizedSchoolUser();

        return view('livewire.school.teachers.teacher-create')
            ->layout('components.layouts.school', [
                'title' => 'Register Teacher',
            ]);
    }
}
