<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
            ],

            'password' => [
                'required',
                'string',
                'confirmed',
                Password::defaults(),
            ],
        ])->validate();

        return DB::transaction(function () use ($input): User {
            /*
             * Check whether this is the first account.
             * This check alone does not prevent simultaneous registrations
             * from both being considered the first user.
             */
            $isFirstUser = ! User::query()->exists();

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            /*
             * Only the initial account receives Super Admin.
             * Subsequent public registrations receive no privileged role.
             */
            if ($isFirstUser) {
                $role = Role::firstOrCreate([
                    'name' => 'Super Admin',
                    'guard_name' => 'web',
                ]);

                $user->assignRole($role);
            }

            return $user;
        });
    }
}
