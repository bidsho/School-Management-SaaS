<?php

namespace App\Livewire\School\Parents;

use App\Models\SchoolParent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use App\Livewire\Concerns\UsesPortalLayout;

class ParentEdit extends Component
{
    use UsesPortalLayout;
    #[Locked]
    public int $parentId;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $occupation = '';

    private function schoolId(): int
    {
        $user = Auth::user();

        abort_unless($user && $user->school_id, 403);

        return (int) $user->school_id;
    }

    public function mount(int $parentId): void
    {
        abort_unless(
            Auth::user()?->can('edit parents'),
            403,
            'You do not have permission to edit parents.'
        );

        $parent = $this->findParent($parentId);

        $this->parentId = $parent->id;
        $this->first_name = $parent->first_name;
        $this->last_name = $parent->last_name;
        $this->email = $parent->email ?? '';
        $this->phone = $parent->phone ?? '';
        $this->address = $parent->address ?? '';
        $this->occupation = $parent->occupation ?? '';
    }

    private function findParent(int $parentId): SchoolParent
    {
        return SchoolParent::withoutGlobalScopes()
            ->where('school_id', $this->schoolId())
            ->findOrFail($parentId);
    }

    protected function rules(): array
    {
        $parent = $this->findParent($this->parentId);

        return [
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',

                // Login emails must be unique across all users.
                Rule::unique('users', 'email')
                    ->ignore($parent->user_id),

                // Parent profile email must also be unique
                // within the current school.
                Rule::unique('parents', 'email')
                    ->where('school_id', $this->schoolId())
                    ->ignore($parent->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'occupation' => [
                'nullable',
                'string',
                'max:150',
            ],
        ];
    }

    public function update(): void
    {
        abort_unless(
            Auth::user()?->can('edit parents'),
            403,
            'You do not have permission to edit parents.'
        );

        $validated = $this->validate();

        // Re-fetch the profile using the authenticated user's school.
        $parent = $this->findParent($this->parentId);

        $email = mb_strtolower(trim($validated['email']));

        DB::transaction(function () use ($parent, $validated, $email) {
            // A parent with a login account must keep the two email
            // addresses synchronized.
            if ($parent->user_id) {
                $account = $parent->user()
                    ->where('school_id', $this->schoolId())
                    ->first();

                if (! $account) {
                    throw ValidationException::withMessages([
                        'email' => 'The linked parent login account could not be found. Please contact your system administrator.',
                    ]);
                }

                $account->email = $email;
                $account->name = trim(
                    $validated['first_name'].' '.$validated['last_name']
                );
                $account->save();
            }

            $parent->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $email,
                'phone' => $validated['phone'] ?: null,
                'address' => $validated['address'] ?: null,
                'occupation' => $validated['occupation'] ?: null,
            ]);
        });

        session()->flash(
            'success',
            'Parent details and login information updated successfully.'
        );

        $this->redirectRoute('school.parents.index');
    }

    public function render()
    {
        return view('livewire.school.parents.parent-edit')
            ->layout($this->portalLayout());
    }
}
