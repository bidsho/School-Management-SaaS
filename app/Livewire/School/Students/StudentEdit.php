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
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;
use App\Livewire\Concerns\UsesPortalLayout;

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

    /**
     * Resolve the current school and authorize access.
     */
    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        return (int) $user->school_id;
    }

    /**
     * Always retrieve the student within the authenticated user's school.
     */
    private function studentRecord(): Student
    {
        return Student::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->findOrFail($this->studentId);
    }

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

        // Never enable account creation automatically for an existing account.
        $this->createLoginAccount = false;

        $primaryParent = $record->parents->first(
            fn ($parent) => (bool) $parent->pivot->is_primary
        );

        $this->parentId = $primaryParent
            ? (string) $primaryParent->id
            : '';

        $this->relationship = $primaryParent?->pivot->relationship
            ?: 'parent';
    }

    /**
     * Validation rules for student details and parent selection.
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

        // A student with a login account must retain a valid login email.
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

    public function save()
    {
        $schoolId = $this->schoolId();

        $student = Student::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with('user')
            ->findOrFail($this->studentId);

        $validated = $this->validate();

        /*
         * Validate the login email against the users table.
         * This is separate from the students table's email constraint.
         */
        $loginUser = $student->user;

        if ($loginUser || $this->createLoginAccount) {
            $userEmailRules = [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($loginUser?->id),
            ];

            $this->validate([
                'email' => $userEmailRules,
            ]);
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
             * Create a login account only when explicitly requested
             * and when this student does not already have one.
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

                $studentUser->assignRole('Student');

                $student->user_id = $studentUser->id;

                $createdLoginAccount = true;
            } elseif ($student->user) {
                /*
                 * Keep an existing student login's name and email
                 * synchronized with the student record.
                 */
                $studentUser = $student->user;

                $studentUser->name = trim(
                    $validated['first_name'] . ' ' .
                    $validated['last_name']
                );

                $studentUser->email = $validated['email'];
                $studentUser->school_id = $schoolId;

                $studentUser->save();
            }

            /*
             * Update student information.
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

            $student->save();

            /*
             * Remove only the current primary-parent link.
             * Other linked guardians are preserved.
             */
            $student->parents()
                ->newPivotStatement()
                ->where('student_id', $student->id)
                ->where('school_id', $schoolId)
                ->where('is_primary', true)
                ->delete();

            /*
             * If a parent was selected, mark that link as primary.
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
        });

        /*
         * Send the password setup link after the database transaction.
         * A delivery failure must not undo the saved student record.
         */
        $passwordLinkSent = false;

        if ($createdLoginAccount && $studentUser) {
            try {
                $status = Password::sendResetLink([
                    'email' => $studentUser->email,
                ]);

                $passwordLinkSent = $status === Password::RESET_LINK_SENT;
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        if ($createdLoginAccount && $passwordLinkSent) {
            session()->flash(
                'success',
                'Student updated and login account created. A password setup link has been sent to the student email.'
            );
        } elseif ($createdLoginAccount) {
            session()->flash(
                'warning',
                'Student updated and login account created, but the password setup email could not be confirmed. Check your mail configuration and use the password reset process to send the link again.'
            );
        } else {
            session()->flash(
                'success',
                'Student information updated successfully.'
            );
        }

        return redirect()->route('school.students.index');
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $student = Student::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with('user')
            ->findOrFail($this->studentId);

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
