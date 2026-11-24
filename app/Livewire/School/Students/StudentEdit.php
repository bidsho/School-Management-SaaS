<?php

namespace App\Livewire\School\Students;

use App\Models\SchoolParent;
use App\Models\Student;
use App\Models\User;
use App\Livewire\Concerns\UsesPortalLayout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

class StudentEdit extends Component
{
    use UsesPortalLayout;

    #[Locked]
    public int $studentId;

    public string $first_name = '';
    public string $last_name = '';
    public string $middle_name = '';
    public string $admission_number = '';
    public string $email = '';
    public string $phone = '';
    public string $date_of_birth = '';
    public string $gender = '';
    public string $address = '';
    public string $status = 'active';

    public string $parentId = '';
    public string $relationship = 'parent';

    public bool $createLoginAccount = false;

    /*
    |--------------------------------------------------------------------------
    | School Authorization
    |--------------------------------------------------------------------------
    */

    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        return (int) $user->school_id;
    }

    /*
    |--------------------------------------------------------------------------
    | Retrieve Student Safely
    |--------------------------------------------------------------------------
    */

    private function studentRecord(): Student
    {
        return Student::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->findOrFail($this->studentId);
    }

    /*
    |--------------------------------------------------------------------------
    | Initialize Component
    |--------------------------------------------------------------------------
    */

    public function mount(int $student): void
    {
        $schoolId = $this->schoolId();

        $record = Student::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with(['parents', 'user'])
            ->findOrFail($student);

        $this->studentId = (int) $record->id;

        $this->first_name = $record->first_name ?? '';
        $this->middle_name = $record->middle_name ?? '';
        $this->last_name = $record->last_name ?? '';
        $this->admission_number = $record->admission_number ?? '';
        $this->email = $record->email ?? '';
        $this->phone = $record->phone ?? '';

        $this->date_of_birth = $record->date_of_birth
            ? $record->date_of_birth->format('Y-m-d')
            : '';

        $this->gender = $record->gender ?? '';
        $this->address = $record->address ?? '';
        $this->status = $record->status ?? 'active';

        /*
         * Existing student accounts must not be recreated.
         */
        $this->createLoginAccount = false;

        /*
         * Load the current primary parent.
         */
        $primaryParent = $record->parents->first(
            fn ($parent) => (bool) $parent->pivot->is_primary
        );

        $this->parentId = $primaryParent
            ? (string) $primaryParent->id
            : '';

        $this->relationship = $primaryParent?->pivot->relationship
            ?: 'parent';
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected function rules(): array
    {
        $schoolId = $this->schoolId();
        $student = $this->studentRecord();

        $emailRules = [
            'nullable',
            'email',
            'max:255',
            Rule::unique('students', 'email')
                ->where('school_id', $schoolId)
                ->ignore($student->id),
        ];

        /*
         * Existing login accounts must have a valid email.
         */
        if ($student->user_id || $this->createLoginAccount) {
            $emailRules = [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')
                    ->where('school_id', $schoolId)
                    ->ignore($student->id),
            ];
        }

        return [
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

            'admission_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'admission_number')
                    ->where('school_id', $schoolId)
                    ->ignore($student->id),
            ],

            'email' => $emailRules,

            'phone' => [
                'nullable',
                'string',
                'max:30',
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

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
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
                    'grandparent',
                    'other',
                ]),
            ],

            'createLoginAccount' => [
                'boolean',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Save Student
    |--------------------------------------------------------------------------
    */

    public function save()
    {
        $schoolId = $this->schoolId();

        /*
         * Always retrieve the student from the current school.
         */
        $student = Student::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with('user')
            ->findOrFail($this->studentId);

        $validated = $this->validate();

        $loginUser = $student->user;

        /*
         * Validate the login email separately from the student email.
         */
        if ($loginUser || $this->createLoginAccount) {
            $this->validate([
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')
                        ->ignore($loginUser?->id),
                ],
            ]);
        }

        /*
         * Prevent inconsistent records where user_id exists
         * but the associated User record cannot be loaded.
         */
        if ($student->user_id && ! $student->user) {
            abort(409, 'The student login account is inconsistent. Please contact the system administrator.');
        }

        $studentUser = null;
        $createdLoginAccount = false;

        DB::transaction(function () use (
            $student,
            $validated,
            $schoolId,
            &$studentUser,
            &$createdLoginAccount
        ) {
            /*
             * Create a login account only when explicitly requested.
             */
            if (! $student->user_id && $this->createLoginAccount) {
                $studentUser = new User();

                $studentUser->forceFill([
                    'name' => trim(
                        $validated['first_name'] . ' ' .
                        $validated['last_name']
                    ),
                    'email' => $validated['email'],
                    'password' => Hash::make(Str::random(64)),
                    'school_id' => $schoolId,
                ]);

                $studentUser->save();

                /*
                 * Assign the Student role.
                 */
                $studentUser->assignRole('Student');

                /*
                 * Link the new account to the student.
                 */
                $student->user_id = $studentUser->id;

                $createdLoginAccount = true;
            }

            /*
             * Synchronize an existing login account.
             *
             * This also corrects an existing user's school_id
             * if it was previously missing or incorrect.
             */
            elseif ($student->user) {
                $studentUser = $student->user;

                $studentUser->name = trim(
                    $validated['first_name'] . ' ' .
                    $validated['last_name']
                );

                $studentUser->email = $validated['email'];

                /*
                 * IMPORTANT:
                 * Ensure the linked student user belongs
                 * to the correct school.
                 */
                $studentUser->school_id = $schoolId;

                $studentUser->save();
            }

            /*
             * Update the student record.
             */
            $student->fill([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?: null,
                'last_name' => $validated['last_name'],
                'admission_number' => $validated['admission_number'],
                'email' => $validated['email'] ?: null,
                'phone' => $validated['phone'] ?: null,
                'date_of_birth' => $validated['date_of_birth'] ?: null,
                'gender' => $validated['gender'] ?: null,
                'address' => $validated['address'] ?: null,
                'status' => $validated['status'],
            ]);

            /*
             * Ensure the student record also retains
             * the correct school_id.
             */
            $student->school_id = $schoolId;

            $student->save();

            /*
             * Remove the existing primary parent relationship only.
             * Other parent/guardian relationships are preserved.
             */
            $student->parents()
                ->newPivotStatement()
                ->where('student_id', $student->id)
                ->where('school_id', $schoolId)
                ->where('is_primary', true)
                ->delete();

            /*
             * Attach the selected parent as the primary parent.
             */
            if (! empty($validated['parentId'])) {
                $parent = SchoolParent::query()
                    ->where('school_id', $schoolId)
                    ->findOrFail((int) $validated['parentId']);

                $alreadyLinked = $student->parents()
                    ->where('parents.id', $parent->id)
                    ->exists();

                $pivotData = [
                    'school_id' => $schoolId,
                    'relationship' => $validated['relationship'],
                    'is_primary' => true,
                ];

                if ($alreadyLinked) {
                    $student->parents()->updateExistingPivot(
                        $parent->id,
                        $pivotData
                    );
                } else {
                    $student->parents()->attach(
                        $parent->id,
                        $pivotData
                    );
                }
            }
        }, 3);

        /*
        |--------------------------------------------------------------------------
        | Send Password Setup Link
        |--------------------------------------------------------------------------
        |
        | Email is sent after the transaction so a mail failure
        | does not roll back the student update.
        */

        $passwordLinkSent = false;

        if ($createdLoginAccount && $studentUser) {
            try {
                $status = Password::sendResetLink([
                    'email' => $studentUser->email,
                ]);

                $passwordLinkSent =
                    $status === Password::RESET_LINK_SENT;

            } catch (Throwable $exception) {
                report($exception);
            }
        }

        /*
         * User feedback.
         */
        if ($createdLoginAccount && $passwordLinkSent) {
            session()->flash(
                'success',
                'Student updated and login account created. A password setup link has been sent to the student email.'
            );
        } elseif ($createdLoginAccount) {
            session()->flash(
                'warning',
                'Student updated and login account created, but the password setup email could not be confirmed. Check your mail configuration and resend the password setup link.'
            );
        } else {
            session()->flash(
                'success',
                'Student information updated successfully.'
            );
        }

        return redirect()->route('school.students.index');
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $schoolId = $this->schoolId();

        $student = Student::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with('user')
            ->findOrFail($this->studentId);

        /*
         * Parents are restricted to the current school.
         */
        $parents = SchoolParent::query()
            ->where('school_id', $schoolId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get([
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
            ]);

        return view(
            'livewire.school.students.student-edit',
            compact('student', 'parents')
        )->layout($this->portalLayout(), [
            'title' => 'Edit Student',
        ]);
    }
}
