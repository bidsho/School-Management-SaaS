<?php

namespace App\Livewire\Platform\Schools;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Throwable;

class SchoolCreate extends Component
{
    // School details
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $status = 'active';
    public string $timezone = 'Africa/Lagos';

    // First school administrator
    public string $adminName = '';
    public string $adminEmail = '';

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('schools', 'email'),
            ],

            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],

            'status' => [
                'required',
                Rule::in(['active', 'pending', 'suspended']),
            ],

            'timezone' => ['required', 'timezone'],

            'adminName' => ['required', 'string', 'max:255'],

            'adminEmail' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
        ]);

        try {
            // Ensure the School Admin role exists.
            $adminRole = Role::firstOrCreate([
                'name' => 'School Admin',
                'guard_name' => 'web',
            ]);

            // Create the school and administrator in one transaction.
            $result = DB::transaction(function () use ($validated, $adminRole) {

                $baseSlug = Str::slug($validated['name']);

                if ($baseSlug === '') {
                    $baseSlug = 'school';
                }

                $slug = $baseSlug;
                $counter = 1;

                while (School::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }

                // Create school.
                $school = new School();

                $school->forceFill([
                    'name' => $validated['name'],
                    'slug' => $slug,
                    'email' => $validated['email'] ?: null,
                    'phone' => $validated['phone'] ?: null,
                    'address' => $validated['address'] ?: null,
                    'status' => $validated['status'],
                    'timezone' => $validated['timezone'],
                ]);

                $school->save();

                // Create first school administrator.
                $admin = new User();

                $admin->forceFill([
                    'name' => $validated['adminName'],
                    'email' => $validated['adminEmail'],
                    'password' => Hash::make(Str::random(64)),
                    'school_id' => $school->id,
                    'status' => 'active',
                ]);

                $admin->save();

                // Assign School Admin role.
                $admin->assignRole($adminRole);

                return [
                    'school' => $school,
                    'admin' => $admin,
                ];
            });

        } catch (Throwable $exception) {

            Log::error('School creation failed.', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            $this->addError(
                'creation',
                'The school could not be created. Please check the application logs or contact support.'
            );

            return;
        }

        /*
         * Send password setup email AFTER the transaction commits.
         * Mail failure must not undo successful school creation.
         */
        try {
            $resetStatus = Password::sendResetLink([
                'email' => $result['admin']->email,
            ]);

            if ($resetStatus === Password::RESET_LINK_SENT) {

                session()->flash(
                    'success',
                    'School and School Admin created successfully. A password setup link has been sent to the administrator.'
                );

            } else {

                Log::warning('School administrator password email was not sent.', [
                    'email' => $result['admin']->email,
                    'status' => $resetStatus,
                ]);

                session()->flash(
                    'warning',
                    'School and administrator created successfully, but the password setup email could not be sent. You can resend the password setup link later.'
                );
            }

        } catch (Throwable $exception) {

            Log::error('School administrator invitation email failed.', [
                'email' => $result['admin']->email,
                'message' => $exception->getMessage(),
            ]);

            session()->flash(
                'warning',
                'School and administrator created successfully, but the invitation email failed. Check your mail configuration and resend the password setup link.'
            );
        }

        $this->redirectRoute('platform.schools.index');
    }

    public function render()
    {
        return view('livewire.platform.schools.school-create')
            ->extends('layouts.super');
    }
}
