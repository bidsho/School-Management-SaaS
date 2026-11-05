<?php

namespace App\Livewire\School\Students;

use App\Models\SchoolParent;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use App\Livewire\Concerns\UsesPortalLayout;

class StudentCreate extends Component
{
    use UsesPortalLayout;
    public string $admission_number = '';
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $date_of_birth = '';
    public string $gender = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';

    // Parent / guardian selection.
    public string $parentId = '';
    public string $parentSearch = '';
    public bool $showParentResults = false;
    public string $relationship = 'parent';

    /**
     * Clear the selected parent when the search changes.
     */
    public function updatedParentSearch(): void
    {
        $this->parentId = '';
        $this->showParentResults = true;
        $this->resetValidation('parentId');
    }

    /**
     * Open parent search results.
     */
    public function openParentResults(): void
    {
        $this->showParentResults = true;
    }

    /**
     * Select a parent belonging to the current school.
     */
    public function selectParent(int $parentId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $parent = SchoolParent::query()
            ->where('school_id', $user->school_id)
            ->findOrFail($parentId);

        $this->parentId = (string) $parent->id;

        $this->parentSearch = trim(
            $parent->first_name . ' ' . $parent->last_name
        );

        $this->showParentResults = false;
        $this->resetValidation('parentId');
    }

    /**
     * Clear the selected parent.
     */
    public function clearParent(): void
    {
        $this->parentId = '';
        $this->parentSearch = '';
        $this->showParentResults = false;
        $this->resetValidation('parentId');
    }

    /**
     * Create a student and their login account.
     */
    public function save()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = (int) $user->school_id;

        $validated = $this->validate([
            'admission_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'admission_number')
                    ->where('school_id', $schoolId),
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

            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
            ],

            'gender' => [
                'nullable',
                Rule::in(['male', 'female', 'other']),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')
                    ->where('school_id', $schoolId),
                Rule::unique('users', 'email'),
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

            'parentId' => [
                'nullable',
                'integer',
                Rule::exists('parents', 'id')
                    ->where('school_id', $schoolId),
            ],

            'relationship' => [
                'required',
                Rule::in([
                    'mother',
                    'father',
                    'guardian',
                    'parent',
                    'other',
                ]),
            ],
        ]);

        $student = DB::transaction(function () use (
            $validated,
            $schoolId
        ) {
            // Create a login account with a random, unknown password.
            $studentUser = User::create([
                'name' => trim(
                    $validated['first_name'] . ' ' .
                    $validated['last_name']
                ),
                'email' => $validated['email'],
                'password' => Hash::make(Str::random(64)),
                'school_id' => $schoolId,
            ]);

            // Give the student the Student role.
            $studentUser->assignRole('Student');

            // Create the student record.
            $student = new Student();

            $student->forceFill([
                'school_id' => $schoolId,
                'user_id' => $studentUser->id,
                'admission_number' => $validated['admission_number'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?: null,
                'last_name' => $validated['last_name'],
                'date_of_birth' => $validated['date_of_birth'] ?: null,
                'gender' => $validated['gender'] ?: null,
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?: null,
                'address' => $validated['address'] ?: null,
                'status' => 'active',
            ])->save();

            // Link an existing parent when one was selected.
            if (!empty($validated['parentId'])) {
                $parent = SchoolParent::query()
                    ->where('school_id', $schoolId)
                    ->findOrFail((int) $validated['parentId']);

                $student->parents()->attach($parent->id, [
                    'school_id' => $schoolId,
                    'relationship' => $validated['relationship'],
                    'is_primary' => true,
                ]);
            }

            return $student;
        });

        // Send the student a password setup link.
        try {
            $status = Password::sendResetLink([
                'email' => $student->email,
            ]);

            if ($status === Password::RESET_LINK_SENT) {
                session()->flash(
                    'success',
                    'Student registered successfully. A password setup link has been sent to the student email.'
                );
            } else {
                report(new \RuntimeException(
                    'Student created, but the password setup link could not be sent: ' . $status
                ));

                session()->flash(
                    'warning',
                    'Student registered successfully, but the password setup link could not be sent. Check your mail and password-reset configuration.'
                );
            }
        } catch (\Throwable $exception) {
            report($exception);

            session()->flash(
                'warning',
                'Student registered successfully, but the password setup email failed. Check your mail configuration and try sending the setup link again.'
            );
        }

        return $this->redirectRoute('school.students.index');
    }

    /**
     * Display parents available for selection.
     */
    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $parents = collect();
        $search = trim($this->parentSearch);

        if ($this->showParentResults && $search !== '') {
            $parents = SchoolParent::query()
                ->where('school_id', $user->school_id)
                ->where(function ($query) use ($search) {
                    $query->where(
                        'first_name',
                        'like',
                        "%{$search}%"
                    )->orWhere(
                        'last_name',
                        'like',
                        "%{$search}%"
                    )->orWhere(
                        'email',
                        'like',
                        "%{$search}%"
                    )->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    );
                })
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->limit(10)
                ->get([
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                ]);
        }

        $selectedParent = null;

        if ($this->parentId !== '') {
            $selectedParent = SchoolParent::query()
                ->where('school_id', $user->school_id)
                ->find($this->parentId);
        }

        return view(
            'livewire.school.students.student-create',
            compact('parents', 'selectedParent')
        )->layout($this->portalLayout(), [
            'title' => 'Register Student',
        ]);
    }
}
