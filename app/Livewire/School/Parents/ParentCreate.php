<?php

namespace App\Livewire\School\Parents;

use App\Models\SchoolParent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use App\Livewire\Concerns\UsesPortalLayout;

class ParentCreate extends Component
{
     use  UsesPortalLayout;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $occupation = '';

    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->school_id,
            403,
            'You are not authorized to manage parents.'
        );

        return (int) $user->school_id;
    }

    protected function rules(): array
    {
        $schoolId = $this->schoolId();

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],

            // Login emails must be unique across all user accounts.
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('parents', 'email')
                    ->where('school_id', $schoolId),
            ],

            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
            'occupation' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function save(): void
    {
        $admin = Auth::user();

        abort_unless(
            $admin && $admin->can('create parents'),
            403,
            'You do not have permission to create parents.'
        );

        $schoolId = $this->schoolId();

        $validated = $this->validate();

        $email = Str::lower(trim($validated['email']));

        // Ensure the submitted email is still available.
        if (User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This email already has a login account.',
            ]);
        }

        // Find the existing Parent role. Do not create a role
        // automatically if the application's roles are misconfigured.
        $parentRoleExists = Role::query()
            ->where('name', 'Parent')
            ->where('guard_name', 'web')
            ->exists();

        if (! $parentRoleExists) {
            throw ValidationException::withMessages([
                'email' => 'The Parent role has not been configured. Please contact the system administrator.',
            ]);
        }

        $fullName = trim(
            $validated['first_name'].' '.$validated['last_name']
        );

        $parent = DB::transaction(function () use (
            $validated,
            $schoolId,
            $email,
            $fullName
        ) {
            // Create a user account with an undisclosed random password.
            // The parent will set their own password from the reset email.
            $account = new User();

            $account->name = $fullName;
            $account->email = $email;
            $account->school_id = $schoolId;
            $account->password = Hash::make(Str::random(64));
            $account->save();

            $account->assignRole('Parent');

            // Link the profile to the newly created login account.
            return SchoolParent::create([
                'school_id' => $schoolId,
                'user_id' => $account->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $email,
                'phone' => $validated['phone'] ?: null,
                'address' => $validated['address'] ?: null,
                'occupation' => $validated['occupation'] ?: null,
            ]);
        });

        // The account and profile now exist. Ask Fortify/Laravel's
        // configured password broker to email a password setup link.
        $resetStatus = Password::sendResetLink([
            'email' => $parent->email,
        ]);

        if ($resetStatus === Password::RESET_LINK_SENT) {
            session()->flash(
                'success',
                'Parent profile and login account created successfully. A password setup link has been requested for '.$parent->email.'.'
            );
        } else {
            // The account still exists; an administrator can resend
            // the reset link after checking mail configuration.
            session()->flash(
                'warning',
                'The parent account was created, but the password setup email could not be sent. Please check your mail configuration and resend the link.'
            );
        }

        $this->redirectRoute('school.parents.index');
    }

    public function render()
    {
        return view('livewire.school.parents.parent-create')
            ->layout($this->portalLayout());
    }
}
