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

    public bool $isSaving = false;

    public function save(): void
    {
        dd('error');
        Log::info('School creation form submitted.', [
            'school_name' => $this->name,
            'admin_email' => $this->adminEmail,
        ]);

        $this->resetErrorBag('creation');

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
            // Create or retrieve the School Admin role.
            $adminRole = Role::firstOrCreate([
                'name' => 'School Admin',
                'guard_name' => 'web',
            ]);

            // Create school and administrator atomically.
            $result = DB::transaction(function () use ($validated, $adminRole) {

                // Generate a unique school slug.
                $baseSlug = Str::slug($validated['name']);

                if (empty($baseSlug)) {
                    $baseSlug = 'school';
                }

                $slug = $baseSlug;
                $counter = 1;

                while (School::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
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

                // Assign School Admin permission role.
                $admin->assignRole($adminRole);

                Log::info('School and administrator created.', [
                    'school_id' => $school->id,
                    'admin_id' => $admin->id,
                ]);

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
                'trace' => $exception->getTraceAsString(),
            ]);

            $this->addError(
                'creation',
                'Unable to create the school. Please try again or contact support.'
            );

            return;
        }

        // Send invitation after the database transaction commits.
        try {
            $resetStatus = Password::sendResetLink([
                'email' => $result['admin']->email,
            ]);

            if ($resetStatus === Password::RESET_LINK_SENT) {

                session()->flash(
                    'success',
                    'School created successfully. The School Admin account has also been created, and a password setup link has been sent.'
                );

            } else {

                Log::warning('School Admin password setup email was not sent.', [
                    'email' => $result['admin']->email,
                    'status' => $resetStatus,
                ]);

                session()->flash(
                    'warning',
                    'School created successfully, but the password setup email could not be sent. You can resend the invitation later.'
                );
            }

        } catch (Throwable $exception) {

            Log::error('School Admin invitation failed.', [
                'email' => $result['admin']->email,
                'message' => $exception->getMessage(),
            ]);

            session()->flash(
                'warning',
                'School created successfully, but the invitation email failed. Please check your mail configuration.'
            );
        }

        // Return to school listing.
        $this->redirectRoute('platform.schools.index');
    }

    public function render()
    {
        return view('livewire.platform.schools.school-create')
            ->layout('layouts.super');
    }
}
