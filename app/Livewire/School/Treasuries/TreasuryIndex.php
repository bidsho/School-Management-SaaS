<?php

namespace App\Livewire\School\Treasuries;

use App\Models\Treasury;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class TreasuryIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $email = '';
    public string $employee_number = '';
    public string $phone = '';
    public string $job_title = 'Treasury Officer';
    public string $status = 'active';
    public string $date_joined = '';
    public string $notes = '';

    /**
     * Ensure the current user is a School Admin
     * and belongs to a school.
     */
    public function mount(): void
    {
        $this->authorizeSchoolAdmin();

        abort_unless(
            Auth::user()->school_id,
            403,
            'Your account is not assigned to a school.'
        );
    }

    /**
     * Validation rules.
     */
    protected function rules(): array
    {
        $schoolId = Auth::user()->school_id;

        $userId = null;

        if ($this->editingId) {
            $userId = $this->findTreasury($this->editingId)->user_id;
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'employee_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('treasuries', 'employee_number')
                    ->where('school_id', $schoolId)
                    ->ignore($this->editingId),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'job_title' => [
                'required',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],

            'date_joined' => [
                'nullable',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    /**
     * Open the create form.
     */
    public function create(): void
    {
        $this->authorizeSchoolAdmin();

        $this->resetForm();
        $this->showForm = true;
    }

    /**
     * Open the edit form.
     */
    public function edit(int $id): void
    {
        $this->authorizeSchoolAdmin();

        $treasury = $this->findTreasury($id);

        $this->editingId = $treasury->id;
        $this->name = $treasury->user->name;
        $this->email = $treasury->user->email;
        $this->employee_number = $treasury->employee_number;
        $this->phone = $treasury->phone ?? '';
        $this->job_title = $treasury->job_title;
        $this->status = $treasury->status;
        $this->date_joined =
            $treasury->date_joined?->format('Y-m-d') ?? '';
        $this->notes = $treasury->notes ?? '';

        $this->showForm = true;

        $this->resetValidation();
    }

    /**
     * Create or update Treasury staff.
     */
    public function save(): void
    {
        $this->authorizeSchoolAdmin();

        $schoolId = Auth::user()->school_id;

        abort_unless(
            $schoolId,
            403,
            'Your account is not assigned to a school.'
        );

        $data = $this->validate();

        /*
        |--------------------------------------------------------------------------
        | Update existing Treasury staff
        |--------------------------------------------------------------------------
        */
        if ($this->editingId) {
            $treasury = $this->findTreasury($this->editingId);

            DB::transaction(function () use ($treasury, $data) {
                $user = $treasury->user;

                $user->name = $data['name'];
                $user->email = $data['email'];

                // Keep the account role consistent.
                $user->role = 'treasury';

                // Match the login status to the staff status.
                $user->status = $data['status'];

                $user->save();

                // Ensure the correct Spatie role is present.
                Role::findOrCreate('Treasury', 'web');

                if (! $user->hasRole('Treasury')) {
                    $user->assignRole('Treasury');
                }

                $treasury->update([
                    'employee_number' => $data['employee_number'],
                    'phone' => $data['phone'] ?: null,
                    'job_title' => $data['job_title'],
                    'status' => $data['status'],
                    'date_joined' => $data['date_joined'] ?: null,
                    'notes' => $data['notes'] ?: null,
                ]);
            });

            session()->flash(
                'success',
                'Treasury staff updated successfully.'
            );
        } else {
            /*
            |--------------------------------------------------------------------------
            | Create a new Treasury staff account
            |--------------------------------------------------------------------------
            */
            $user = DB::transaction(function () use ($data, $schoolId) {
                // Make sure the Spatie role exists.
                Role::findOrCreate('Treasury', 'web');

                // Create the login account.
                $user = new User();

                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->school_id = $schoolId;
                $user->role = 'treasury';
                $user->status = $data['status'];
                $user->password = Hash::make(Str::random(48));

                $user->save();

                // Assign the Spatie permission role.
                $user->assignRole('Treasury');

                // Create the school-specific Treasury staff profile.
                Treasury::create([
                    'school_id' => $schoolId,
                    'user_id' => $user->id,
                    'employee_number' => $data['employee_number'],
                    'phone' => $data['phone'] ?: null,
                    'job_title' => $data['job_title'],
                    'status' => $data['status'],
                    'date_joined' => $data['date_joined'] ?: null,
                    'notes' => $data['notes'] ?: null,
                ]);

                return $user;
            });

            /*
             * Send the password setup link after the transaction commits.
             */
            $result = Password::sendResetLink([
                'email' => $user->email,
            ]);

            if ($result === Password::RESET_LINK_SENT) {
                session()->flash(
                    'success',
                    'Treasury staff created successfully. A password setup link was sent by email.'
                );
            } else {
                session()->flash(
                    'warning',
                    'Treasury staff was created, but the password setup email could not be sent. Check your mail configuration and use Setup Link to retry.'
                );
            }
        }

        $this->resetForm();
        $this->resetPage();
    }

    /**
     * Delete the Treasury profile and login account.
     */
    public function delete(int $id): void
    {
        $this->authorizeSchoolAdmin();

        $treasury = $this->findTreasury($id);
        $user = $treasury->user;

        DB::transaction(function () use ($treasury, $user) {
            $treasury->delete();

            // Remove this user's Spatie role assignments.
            $user->syncRoles([]);

            $user->delete();
        });

        session()->flash(
            'success',
            'Treasury staff and login account deleted successfully.'
        );

        $this->resetPage();
    }

    /**
     * Resend the password setup link.
     */
    public function resendSetupLink(int $id): void
    {
        $this->authorizeSchoolAdmin();

        $treasury = $this->findTreasury($id);

        $result = Password::sendResetLink([
            'email' => $treasury->user->email,
        ]);

        if ($result === Password::RESET_LINK_SENT) {
            session()->flash(
                'success',
                'Password setup link sent successfully.'
            );
        } else {
            session()->flash(
                'warning',
                'Could not send the setup link. Check your mail configuration and try again.'
            );
        }
    }

    /**
     * Reset pagination when the search changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Find Treasury staff belonging to the current school only.
     */
    private function findTreasury(int $id): Treasury
    {
        return Treasury::with('user')
            ->where('school_id', Auth::user()->school_id)
            ->findOrFail($id);
    }

    /**
     * Restrict staff management to School Admin.
     */
    private function authorizeSchoolAdmin(): void
    {
        abort_unless(
            Auth::user()?->hasRole('School Admin'),
            403,
            'You are not authorized to manage Treasury staff.'
        );
    }

    /**
     * Clear the form and validation errors.
     */
    public function resetForm(): void
    {
        $this->reset([
            'showForm',
            'editingId',
            'name',
            'email',
            'employee_number',
            'phone',
            'job_title',
            'status',
            'date_joined',
            'notes',
        ]);

        $this->job_title = 'Treasury Officer';
        $this->status = 'active';

        $this->resetValidation();
    }

    /**
     * Render the Treasury management page.
     */
    public function render()
    {
        $this->authorizeSchoolAdmin();

        $treasuries = Treasury::with('user')
            ->where('school_id', Auth::user()->school_id)
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where(
                        'employee_number',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'job_title',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'email',
                            'like',
                            "%{$search}%"
                        );
                    });
                });
            })
            ->latest()
            ->paginate(10);

        return view(
            'livewire.school.treasuries.treasury-index',
            [
                'treasuries' => $treasuries,
            ]
        )->layout('components.layouts.school', [
            'title' => 'Treasury Management',
        ]);
    }
}
