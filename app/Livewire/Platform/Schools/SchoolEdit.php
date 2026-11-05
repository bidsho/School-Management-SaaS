<?php

namespace App\Livewire\Platform\Schools;

use App\Models\School;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SchoolEdit extends Component
{
    public School $school;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $status = 'active';
    public string $timezone = 'Africa/Lagos';

    public function mount(School $school): void
    {
        $this->school = $school;

        $this->name = $school->name;
        $this->email = $school->email ?? '';
        $this->phone = $school->phone ?? '';
        $this->address = $school->address ?? '';
        $this->status = $school->status;
        $this->timezone = $school->timezone ?: 'Africa/Lagos';
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('schools', 'email')->ignore($this->school->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in([
                'active',
                'pending',
                'suspended',
            ])],
            'timezone' => ['required', 'timezone'],
        ];
    }

    public function update(): void
    {
        $validated = $this->validate();

        $this->school->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?: null,
            'phone' => $validated['phone'] ?: null,
            'address' => $validated['address'] ?: null,
            'status' => $validated['status'],
            'timezone' => $validated['timezone'],
        ]);

        session()->flash(
            'success',
            'School details updated successfully.'
        );

        $this->redirectRoute('platform.schools.index');
    }

    public function render()
    {
        return view('livewire.platform.schools.school-edit')->extends('layouts.super');
    }
}
