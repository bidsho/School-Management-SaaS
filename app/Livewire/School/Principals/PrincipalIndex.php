<?php

namespace App\Livewire\School\Principals;

use App\Models\Principal;
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

class PrincipalIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public bool $showModal = false;

    #[Locked]
    public ?int $editingPrincipalId = null;

    // Login information
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Personal information
    public string $staff_number = '';
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $phone = '';
    public string $gender = '';
    public string $date_of_birth = '';
    public string $address = '';

    // Employment information
    public string $qualification = '';
    public string $employment_status = 'active';
    public string $hired_at = '';

    // Bank information
    public string $bank_name = '';
    public string $bank_account_name = '';
    public string $bank_account_number = '';
    public string $bank_code = '';
    public string $payment_notes = '';

    // Bank account display information
    public bool $hasSavedBankAccountNumber = false;
    public string $bankAccountLastFour = '';

    /**
     * Get the authenticated user's school ID.
     *
     * School Admin and Principal can manage Principal records.
     * Super Admin uses the platform routes.
     */
    protected function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user, 403);

        abort_unless(
            $user->hasAnyRole(['School Admin', 'Principal']),
            403,
            'You are not authorized to manage Principals.'
        );

        abort_unless(
            $user->school_id,
            403,
            'No school is assigned to your account.'
        );

        return (int) $user->school_id;
    }

    /**
     * Get Principals belonging only to the authenticated school.
     */
    protected function principalQuery()
    {
        return Principal::query()
            ->where('school_id', $this->schoolId())
            ->with('user');
    }

    /**
     * Get the Principal currently being edited.
     */
    protected function principalRecord(): Principal
    {
        abort_unless($this->editingPrincipalId, 404);

        return $this->principalQuery()
            ->findOrFail($this->editingPrincipalId);
    }

    /**
     * Reset pagination when searching.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Open the create modal.
     */
    public function create(): void
    {
        $this->resetForm();
        $this->resetValidation();

        $this->showModal = true;
    }

    /**
     * Open the edit modal.
     */
    public function edit(int $id): void
    {
        $this->resetForm();
        $this->resetValidation();

        $principal = $this->principalQuery()
            ->findOrFail($id);

        $this->editingPrincipalId = $principal->id;

        // Login information
        $this->name = $principal->user?->name
            ?? $principal->full_name;

        $this->email = $principal->user?->email ?? '';

        // Personal information
        $this->staff_number = $principal->staff_number ?? '';
        $this->first_name = $principal->first_name ?? '';
        $this->middle_name = $principal->middle_name ?? '';
        $this->last_name = $principal->last_name ?? '';
        $this->phone = $principal->phone ?? '';
        $this->gender = $principal->gender ?? '';

        $this->date_of_birth = $principal->date_of_birth
            ? $principal->date_of_birth->format('Y-m-d')
            : '';

        $this->address = $principal->address ?? '';

        // Employment information
        $this->qualification = $principal->qualification ?? '';

        $this->employment_status =
            $principal->employment_status ?? 'active';

        $this->hired_at = $principal->hired_at
            ? $principal->hired_at->format('Y-m-d')
            : '';

        // Bank information
        $this->bank_name = $principal->bank_name ?? '';
        $this->bank_account_name = $principal->bank_account_name ?? '';
        $this->bank_code = $principal->bank_code ?? '';
        $this->payment_notes = $principal->payment_notes ?? '';

        // Never expose the saved account number to Livewire state.
        $this->bank_account_number = '';

        // Only show whether an account exists and its final four digits.
        $savedAccountNumber = $principal->bank_account_number;

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

        $userId = $this->editingPrincipalId
            ? $this->principalRecord()->user_id
            : null;

        return [
            // Login information
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

            'password' => $this->editingPrincipalId
                ? ['nullable', 'string', 'min:8', 'confirmed']
                : ['required', 'string', 'min:8', 'confirmed'],

            'password_confirmation' => [
                $this->editingPrincipalId ? 'nullable' : 'required',
                'string',
            ],

            // Personal information
            'staff_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('principals', 'staff_number')
                    ->where('school_id', $schoolId)
                    ->ignore($this->editingPrincipalId),
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

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'gender' => [
                'nullable',
                Rule::in(['male', 'female', 'other']),
            ],

            'date_of_birth' => [
                'nullable',
                'date',
            ],

            'address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            // Employment information
            'qualification' => [
                'nullable',
                'string',
                'max:255',
            ],

            'employment_status' => [
                'required',
                Rule::in(['active', 'inactive', 'suspended']),
            ],

            'hired_at' => [
                'nullable',
                'date',
            ],

            // Bank information
            'bank_name' => [
                'nullable',
                'string',
                'max:150',
            ],

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

            'bank_code' => [
                'nullable',
                'string',
                'max:30',
            ],

            'payment_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Create or update a Principal.
     */
    public function save(): void
    {
        $schoolId = $this->schoolId();

        // Capture before closeModal resets the editing ID.
        $isEditing = (bool) $this->editingPrincipalId;

        $validated = $this->validate();

        // Confirm the Principal role exists.
        $roleExists = Role::query()
            ->where('name', 'Principal')
            ->where('guard_name', 'web')
            ->exists();

        if (! $roleExists) {
            throw ValidationException::withMessages([
                'email' => 'The Principal role does not exist. Please run your role seeder.',
            ]);
        }

        DB::transaction(function () use (
            $schoolId,
            $validated,
            $isEditing
        ) {
            if ($isEditing) {
                /*
                 * UPDATE EXISTING PRINCIPAL
                 */

                $principal = $this->principalQuery()
                    ->lockForUpdate()
                    ->findOrFail($this->editingPrincipalId);

                // Find the login account within this school.
                $user = User::query()
                    ->where('school_id', $schoolId)
                    ->lockForUpdate()
                    ->findOrFail($principal->user_id);

                // Update login information.
                $user->name = $validated['name'];
                $user->email = $validated['email'];

                // Require verification again if email changes.
                if ($user->isDirty('email')) {
                    $user->email_verified_at = null;
                }

                // Change password only when a new one is supplied.
                if (filled($validated['password'])) {
                    $user->password = Hash::make(
                        $validated['password']
                    );
                }

                $user->save();

                // Ensure the correct role remains assigned.
                if (! $user->hasRole('Principal')) {
                    $user->assignRole('Principal');
                }

                // Update Principal profile.
                $principal->update(
                    $this->principalData($validated)
                );

            } else {
                /*
                 * CREATE NEW PRINCIPAL
                 */

                // Create the login account without relying on fillable.
                $user = new User();

                $user->school_id = $schoolId;
                $user->name = $validated['name'];
                $user->email = $validated['email'];
                $user->password = Hash::make(
                    $validated['password']
                );

                $user->save();

                // Assign Principal role.
                $user->assignRole('Principal');

                // Create the Principal profile.
                Principal::create([
                    'school_id' => $schoolId,
                    'user_id' => $user->id,
                    ...$this->principalData($validated),
                ]);
            }
        });

        $this->closeModal();

        session()->flash(
            'success',
            $isEditing
                ? 'Principal updated successfully.'
                : 'Principal account created successfully.'
        );

        $this->resetPage();
    }

    /**
     * Prepare Principal information for database storage.
     */
    protected function principalData(array $data): array
    {
        $principalData = [
            'staff_number' => $data['staff_number'],

            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?: null,
            'last_name' => $data['last_name'],

            'phone' => $data['phone'] ?: null,
            'gender' => $data['gender'] ?: null,
            'date_of_birth' => $data['date_of_birth'] ?: null,
            'address' => $data['address'] ?: null,

            'qualification' => $data['qualification'] ?: null,
            'employment_status' => $data['employment_status'],
            'hired_at' => $data['hired_at'] ?: null,

            'bank_name' => $data['bank_name'] ?: null,
            'bank_account_name' => $data['bank_account_name'] ?: null,
            'bank_code' => $data['bank_code'] ?: null,
            'payment_notes' => $data['payment_notes'] ?: null,
        ];

        // Replace the account number only when a new one is supplied.
        if (filled($data['bank_account_number'])) {
            $principalData['bank_account_number'] =
                $data['bank_account_number'];

        } elseif (! $this->editingPrincipalId) {
            $principalData['bank_account_number'] = null;
        }

        return $principalData;
    }

    /**
     * Delete a Principal and the linked login account.
     */
    public function delete(int $id): void
    {
        $schoolId = $this->schoolId();

        $principal = $this->principalQuery()
            ->findOrFail($id);

        DB::transaction(function () use ($principal, $schoolId) {
            $user = User::query()
                ->where('school_id', $schoolId)
                ->findOrFail($principal->user_id);

            $principal->delete();

            $user->delete();
        });

        session()->flash(
            'success',
            'Principal and login account deleted successfully.'
        );

        $this->resetPage();
    }

    /**
     * Close the modal.
     */
    public function closeModal(): void
    {
        $this->showModal = false;

        $this->resetForm();

        $this->resetValidation();
    }

    /**
     * Reset all form fields.
     */
    protected function resetForm(): void
    {
        $this->reset([
            'editingPrincipalId',

            'name',
            'email',
            'password',
            'password_confirmation',

            'staff_number',
            'first_name',
            'middle_name',
            'last_name',
            'phone',
            'gender',
            'date_of_birth',
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
     * Render Principal management page.
     */
    public function render(): View
    {
        $schoolId = $this->schoolId();

        $principals = Principal::query()
            ->where('school_id', $schoolId)
            ->with('user')

            ->when($this->search, function ($query) {
                $search = '%' . trim($this->search) . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', $search)
                        ->orWhere('middle_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('staff_number', 'like', $search)

                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('email', 'like', $search)
                                ->orWhere('name', 'like', $search);
                        });
                });
            })

            ->latest()
            ->paginate(10);

        return view(
            'livewire.school.principals.principal-index',
            compact('principals')
        )->layout('components.layouts.school');
    }
}
