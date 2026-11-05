<?php

namespace App\Livewire\Platform\Schools;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Spatie\Permission\Models\Role;

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

        // Ensure the expected role exists before creating either record.
        $adminRole = Role::firstOrCreate([
            'name' => 'School Admin',
            'guard_name' => 'web',
        ]);

        // Create the school and its first administrator atomically.
        $school = DB::transaction(function () use ($validated, $adminRole) {
            $baseSlug = Str::slug($validated['name']);

            if ($baseSlug === '') {
                $baseSlug = 'school';
            }

            $slug = $baseSlug;
            $counter = 1;

            while (School::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            $school = School::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'email' => $validated['email'] ?: null,
                'phone' => $validated['phone'] ?: null,
                'address' => $validated['address'] ?: null,
                'status' => $validated['status'],
                'timezone' => $validated['timezone'],
            ]);

            $admin = new User();

            $admin->forceFill([
                'name' => $validated['adminName'],
                'email' => $validated['adminEmail'],
                'password' => Hash::make(Str::random(64)),
                'school_id' => $school->id,
                'status' => 'active',
            ]);

            $admin->save();

            $admin->assignRole($adminRole);

            return $school;
        });

        // Send the invitation only after the database transaction commits.
        $resetStatus = Password::sendResetLink([
            'email' => $validated['adminEmail'],
        ]);

        if ($resetStatus !== Password::RESET_LINK_SENT) {
            session()->flash(
                'warning',
                'The school and administrator were created, but the password setup email could not be sent. Check your mail configuration and resend the password reset link.'
            );
        } else {
            session()->flash(
                'success',
                'School and School Admin created successfully. A password setup link has been sent to the administrator.'
            );
        }

        $this->redirectRoute('platform.schools.index');
    }

    public function render()
    {
        return view('livewire.platform.schools.school-create')->extends('layouts.super');
    }
}
