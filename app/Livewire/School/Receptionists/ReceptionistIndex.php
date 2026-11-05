<?php

namespace App\Livewire\School\Receptionists;

use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class ReceptionistIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public bool $showModal = false;

    #[Locked]
    public ?int $editingReceptionistId = null;

    // Login details
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Personal details
    public string $staff_number = '';
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $phone = '';
    public string $address = '';

    // Employment details
    public string $qualification = '';
    public string $employment_status = 'active';
    public string $hired_at = '';

    // Bank details
    public string $bank_name = '';
    public string $bank_account_name = '';
    public string $bank_account_number = '';
    public string $bank_code = '';
    public string $payment_notes = '';

    // Bank account display
    public bool $hasSavedBankAccountNumber = false;
    public string $bankAccountLastFour = '';

    /**
     * Get the authenticated school's ID.
     */
    protected function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user, 403);

        abort_unless(
            $user->hasAnyRole(['School Admin', 'Super Admin']),
            403,
            'You are not authorized to manage Receptionists.'
        );

        abort_unless(
            $user->school_id,
            403,
            'No school is assigned to your account.'
        );

        return (int) $user->school_id;
    }

    /**
     * Get Receptionists belonging to this school.
     */
    protected function receptionistQuery()
    {
        return Receptionist::query()
            ->where('school_id', $this->schoolId())
            ->with('user');
    }

    /**
     * Get the current Receptionist record.
     */
    protected function receptionistRecord(): Receptionist
    {
        abort_unless($this->editingReceptionistId, 404);

        return $this->receptionistQuery()
            ->findOrFail($this->editingReceptionistId);
    }

    /**
     * Reset pagination when searching.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Open create modal.
     */
    public function create(): void
    {
        $this->resetForm();
        $this->resetValidation();

        $this->showModal = true;
    }

    /**
     * Open edit modal.
     */
    public function edit(int $id): void
    {
        $this->resetForm();
        $this->resetValidation();

        $receptionist = $this->receptionistQuery()
            ->findOrFail($id);

        $this->editingReceptionistId = $receptionist->id;

        // Login details
        $this->name = $receptionist->user?->name
            ?? $receptionist->full_name;

        $this->email = $receptionist->user?->email ?? '';

        // Personal details
        $this->staff_number = $receptionist->staff_number ?? '';
        $this->first_name = $receptionist->first_name ?? '';
        $this->middle_name = $receptionist->middle_name ?? '';
        $this->last_name = $receptionist->last_name ?? '';
        $this->phone = $receptionist->phone ?? '';
        $this->address = $receptionist->address ?? '';

        // Employment details
        $this->qualification = $receptionist->qualification ?? '';

        $this->employment_status =
            $receptionist->employment_status ?? 'active';

        $this->hired_at =
            $receptionist->hired_at?->format('Y-m-d') ?? '';

        // Bank details
        $this->bank_name = $receptionist->bank_name ?? '';

        $this->bank_account_name =
            $receptionist->bank_account_name ?? '';

        $this->bank_code = $receptionist->bank_code ?? '';
        $this->payment_notes = $receptionist->payment_notes ?? '';

        // Do not expose the complete saved account number.
        $savedAccountNumber = $receptionist->bank_account_number;

        $this->bank_account_number = '';

        $this->hasSavedBankAccountNumber =
            filled($savedAccountNumber);

        $this->bankAccountLastFour =
            $this->hasSavedBankAccountNumber
                ? substr($savedAccountNumber, -4)
                : '';

        $this->showModal = true;
    }

    /**
     * Validation rules.
     */
    protected function rules(): array
    {
        $schoolId = $this->schoolId();

        $userId = $this->editingReceptionistId
            ? $this->receptionistRecord()->user_id
            : null;

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

            'password' => $this->editingReceptionistId
                ? ['nullable', 'string', 'min:8', 'confirmed']
                : ['required', 'string', 'min:8', 'confirmed'],

            'password_confirmation' => [
                $this->editingReceptionistId ? 'nullable' : 'required',
            ],

            'staff_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('receptionists', 'staff_number')
                    ->where('school_id', $schoolId)
                    ->ignore($this->editingReceptionistId),
            ],

            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],

            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],

            'qualification' => ['nullable', 'string', 'max:255'],

            'employment_status' => [
                'required',
                Rule::in(['active', 'inactive', 'suspended']),
            ],

            'hired_at' => ['nullable', 'date'],

            'bank_name' => ['nullable', 'string', 'max:150'],

            'bank_account_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'bank_account_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]{8,20}$/',
            ],

            'bank_code' => ['nullable', 'string', 'max:30'],

            'payment_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Save Receptionist and login account.
     */
    public function save(): void
    {
        $schoolId = $this->schoolId();

        $editingId = $this->editingReceptionistId;

        $isEditing = (bool) $editingId;

        $validated = $this->validate();

        // Ensure Receptionist role exists.
        $roleExists = Role::query()
            ->where('name', 'Receptionist')
            ->where('guard_name', 'web')
            ->exists();

        if (!$roleExists) {
            throw ValidationException::withMessages([
                'email' => 'Receptionist role does not exist. Please run your role seeder.',
            ]);
        }

        DB::transaction(function () use (
            $schoolId,
            $editingId,
            $validated
        ) {

            /*
             * EDIT EXISTING RECEPTIONIST
             */
            if ($editingId) {

                $receptionist = Receptionist::query()
                    ->where('school_id', $schoolId)
                    ->lockForUpdate()
                    ->findOrFail($editingId);

                /*
                 * IMPORTANT FIX:
                 *
                 * Find the linked login using user_id only.
                 * Do not filter by school_id here.
                 *
                 * This allows us to repair an existing account
                 * whose school_id was previously incorrect.
                 */
                $user = null;

                if ($receptionist->user_id) {
                    $user = User::query()
                        ->lockForUpdate()
                        ->find($receptionist->user_id);
                }

                if ($user) {

                    // Repair the school assignment.
                    $user->school_id = $schoolId;

                    $user->name = $validated['name'];
                    $user->email = $validated['email'];

                    if ($user->isDirty('email')) {
                        $user->email_verified_at = null;
                    }

                    // Update password only when supplied.
                    if (filled($validated['password'])) {
                        $user->password = Hash::make(
                            $validated['password']
                        );
                    }

                    $user->save();

                    // Ensure Receptionist role exists on this account.
                    if (!$user->hasRole('Receptionist')) {
                        $user->assignRole('Receptionist');
                    }

                } else {

                    /*
                     * The linked login no longer exists.
                     * Create a replacement account.
                     */

                    $existingUser = User::query()
                        ->where('email', $validated['email'])
                        ->lockForUpdate()
                        ->first();

                    if ($existingUser) {
                        throw ValidationException::withMessages([
                            'email' => 'This email already belongs to another account.',
                        ]);
                    }

                    $user = User::create([
                        'school_id' => $schoolId,
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'password' => Hash::make(
                            filled($validated['password'])
                                ? $validated['password']
                                : bin2hex(random_bytes(32))
                        ),
                    ]);

                    $user->assignRole('Receptionist');
                }

                /*
                 * Update both relationships consistently.
                 */
                $profileData = $this->receptionistData($validated);

                $profileData['school_id'] = $schoolId;
                $profileData['user_id'] = $user->id;

                $receptionist->update($profileData);

            } else {

                /*
                 * CREATE NEW RECEPTIONIST
                 */

                // Check that the email is not already registered.
                $existingUser = User::query()
                    ->where('email', $validated['email'])
                    ->lockForUpdate()
                    ->exists();

                if ($existingUser) {
                    throw ValidationException::withMessages([
                        'email' => 'This email address is already registered.',
                    ]);
                }

                /*
                 * Create login account.
                 *
                 * school_id comes from the authenticated
                 * School Admin's school.
                 */
                $user = User::create([
                    'school_id' => $schoolId,
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);

                // Automatically assign Receptionist role.
                $user->assignRole('Receptionist');

                /*
                 * Create Receptionist profile.
                 *
                 * Both records receive exactly the same school_id.
                 */
                $profileData = $this->receptionistData($validated);

                $profileData['school_id'] = $schoolId;
                $profileData['user_id'] = $user->id;

                Receptionist::create($profileData);
            }
        });

        $this->closeModal();

        session()->flash(
            'success',
            $isEditing
                ? 'Receptionist updated successfully.'
                : 'Receptionist account created successfully.'
        );

        $this->resetPage();
    }

    /**
     * Prepare Receptionist profile data.
     */
    protected function receptionistData(array $data): array
    {
        $profile = [
            'staff_number' => $data['staff_number'],

            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?: null,
            'last_name' => $data['last_name'],

            'phone' => $data['phone'] ?: null,
            'address' => $data['address'] ?: null,

            'qualification' => $data['qualification'] ?: null,

            'employment_status' => $data['employment_status'],

            'hired_at' => $data['hired_at'] ?: null,

            'bank_name' => $data['bank_name'] ?: null,

            'bank_account_name' => $data['bank_account_name'] ?: null,

            'bank_code' => $data['bank_code'] ?: null,

            'payment_notes' => $data['payment_notes'] ?: null,
        ];

        /*
         * Keep the existing encrypted account number
         * if the edit field is left blank.
         */
        if (filled($data['bank_account_number'])) {

            $profile['bank_account_number'] =
                $data['bank_account_number'];

        } elseif (!$this->editingReceptionistId) {

            $profile['bank_account_number'] = null;
        }

        return $profile;
    }

    /**
     * Delete Receptionist and linked login account.
     */
    public function delete(int $id): void
    {
        $receptionist = $this->receptionistQuery()
            ->findOrFail($id);

        DB::transaction(function () use ($receptionist) {

            /*
             * Find linked account by ID only.
             * This also handles previously mismatched school IDs.
             */
            $user = null;

            if ($receptionist->user_id) {
                $user = User::query()
                    ->lockForUpdate()
                    ->find($receptionist->user_id);
            }

            $receptionist->delete();

            if ($user) {
                $user->delete();
            }
        });

        session()->flash(
            'success',
            'Receptionist and linked login account deleted successfully.'
        );

        $this->resetPage();
    }

    /**
     * Close modal.
     */
    public function closeModal(): void
    {
        $this->showModal = false;

        $this->resetForm();

        $this->resetValidation();
    }

    /**
     * Reset form fields.
     */
    protected function resetForm(): void
    {
        $this->reset([
            'editingReceptionistId',

            'name',
            'email',
            'password',
            'password_confirmation',

            'staff_number',
            'first_name',
            'middle_name',
            'last_name',
            'phone',
            'address',

            'qualification',
            'employment_status',
            'hired_at',

            'bank_name',
            'bank_account_name',
            'bank_account_number',
            'bank_code',
            'payment_notes',

            'hasSavedBankAccountNumber',
            'bankAccountLastFour',
        ]);

        $this->employment_status = 'active';
    }

    /**
     * Render Receptionist management page.
     */
    public function render(): View
    {
        $receptionists = Receptionist::query()
            ->where('school_id', $this->schoolId())
            ->with('user')
            ->when($this->search, function ($query) {

                $search = '%' . $this->search . '%';

                $query->where(function ($q) use ($search) {

                    $q->where('first_name', 'like', $search)
                        ->orWhere('middle_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('staff_number', 'like', $search)

                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('email', 'like', $search);
                        });
                });
            })
            ->latest()
            ->paginate(10);

        return view(
            'livewire.school.receptionists.receptionist-index',
            compact('receptionists')
        )->layout('components.layouts.school');
    }
}
