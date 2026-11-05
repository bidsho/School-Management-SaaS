<?php

namespace App\Livewire\Parent;

use App\Models\SchoolParent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ParentProfile extends Component
{
    #[Locked]
    public int $parentId;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $occupation = '';

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasRole('Parent'),
            403
        );

        $parent = $this->getParent();

        $this->parentId = (int) $parent->id;

        $this->first_name = $parent->first_name ?? '';
        $this->last_name = $parent->last_name ?? '';
        $this->email = $parent->email ?? $user->email ?? '';
        $this->phone = $parent->phone ?? '';
        $this->address = $parent->address ?? '';
        $this->occupation = $parent->occupation ?? '';
    }

    private function getParent(): SchoolParent
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->hasRole('Parent') && $user->school_id,
            403
        );

        return SchoolParent::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();
    }

    public function updateProfile(): void
    {
        $user = Auth::user();
        $parent = $this->getParent();

        abort_unless(
            (int) $parent->id === $this->parentId,
            403
        );

        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'occupation' => ['nullable', 'string', 'max:150'],
        ]);

        $parent->update($validated);

        // Keep the login account name and email synchronized.
        $user->name = trim(
            $validated['first_name'] . ' ' . $validated['last_name']
        );

        $user->email = $validated['email'];
        $user->save();

        session()->flash(
            'success',
            'Your profile has been updated successfully.'
        );
    }

    public function render()
    {
        $parent = $this->getParent();

        abort_unless(
            (int) $parent->id === $this->parentId,
            403
        );

        $children = $parent->students()
            ->wherePivot('school_id', Auth::user()->school_id)
            ->where('students.school_id', Auth::user()->school_id)
            ->get();

        return view(
            'livewire.parent.parent-profile',
            compact('parent', 'children')
        )->layout('components.layouts.parent');
    }
}
