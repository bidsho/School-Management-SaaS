<?php

namespace App\Livewire\School\Classes;

use App\Models\ClassRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ClassCreate extends Component
{
    public string $name = '';
    public string $code = '';
    public string $level = '';
    public string $description = '';
    public string $status = 'active';

    public function save()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = (int) $user->school_id;

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('class_rooms', 'code')
                    ->where('school_id', $schoolId),
            ],
            'level' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        ClassRoom::create([
            'school_id' => $schoolId,
            'name' => $validated['name'],
            'code' => $validated['code'] ?: null,
            'level' => $validated['level'] ?: null,
            'description' => $validated['description'] ?: null,
            'status' => $validated['status'],
        ]);

        session()->flash('success', 'Class created successfully.');

        return $this->redirectRoute('school.classes.index');
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        return view('livewire.school.classes.class-create')
            ->layout('components.layouts.school', [
                'title' => 'Create Class',
            ]);
    }
}
