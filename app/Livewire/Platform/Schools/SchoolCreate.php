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
    public string $name = '';
    public ?string $email = null;
    public ?string $phone = null;
    public string $timezone = 'Africa/Lagos';
    public ?string $address = null;
    public string $status = 'active';

    public string $adminName = '';
    public string $adminEmail = '';

    public bool $isSaving = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'timezone' => ['required', 'timezone'],
            'address' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in([
                'active',
                'pending',
                'suspended',
            ])],

            'adminName' => ['required', 'string', 'max:255'],
            'adminEmail' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Please enter the school name.',
            'timezone.timezone' => 'Please select a valid timezone.',
            'status.in' => 'Please select a valid school status.',
            'adminName.required' => 'Please enter the administrator name.',
            'adminEmail.required' => 'Please enter the administrator email.',
            'adminEmail.unique' => 'This email already exists on the platform.',
        ];
    }

    public function save()
    {
        if ($this->isSaving) {
            return;
        }

        $this->resetErrorBag('creation');

        $validated = $this->validate();

        $this->isSaving = true;

        try {
            [$school, $admin] = DB::transaction(function () use ($validated) {

                // Get or create the School Admin role.
                $role = Role::firstOrCreate([
                    'name' => 'School Admin',
                    'guard_name' => 'web',
                ]);

                // Generate a unique slug.
                $baseSlug = Str::slug($validated['name']) ?: 'school';

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
                    'password' => Hash::make(Str::random(48)),
                    'school_id' => $school->id,
                    'status' => 'active',
                ]);

                $admin->save();

                // Assign School Admin role.
                $admin->assignRole($role);

                return [$school, $admin];

            }, 3);

            Log::info('School and administrator created.', [
                'school_id' => $school->id,
                'admin_id' => $admin->id,
            ]);

        } catch (Throwable $exception) {

            Log::error('School registration failed.', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            $this->isSaving = false;

            $this->addError(
                'creation',
                'School creation failed. Please check the application logs or contact support.'
            );

            return;
        }

        // Send password setup email after successful database transaction.
        try {
            $passwordStatus = Password::sendResetLink([
                'email' => $admin->email,
            ]);

            if ($passwordStatus === Password::RESET_LINK_SENT) {
                session()->flash(
                    'success',
                    'School and administrator created successfully. A password setup link has been sent.'
                );
            } else {
                Log::warning('Password setup link was not sent.', [
                    'school_id' => $school->id,
                    'admin_id' => $admin->id,
                    'status' => $passwordStatus,
                ]);

                session()->flash(
                    'warning',
                    'School and administrator were created, but the password setup email could not be sent.'
                );
            }

        } catch (Throwable $exception) {

            Log::error('Password setup email failed.', [
                'school_id' => $school->id,
                'admin_id' => $admin->id,
                'message' => $exception->getMessage(),
            ]);

            session()->flash(
                'warning',
                'School and administrator were created, but the password setup email failed. Check your mail configuration.'
            );
        }

        $this->isSaving = false;

        return $this->redirectRoute('platform.schools.index');
    }

    public function render()
    {
        return view('livewire.platform.schools.school-create')
            ->layout('layouts.super');
    }
}