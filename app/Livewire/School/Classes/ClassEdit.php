<?php

namespace App\Livewire\School\Classes;

use App\Models\ClassRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ClassEdit extends Component
{
    #[Locked]
    public int $classId;

    public string $name = '';
    public string $code = '';
    public string $level = '';
    public string $description = '';
    public string $status = 'active';

    public function mount(int $classId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $class = ClassRoom::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->whereKey($classId)
            ->firstOrFail();

        $this->classId = $class->id;
        $this->name = $class->name;
        $this->code = $class->code ?? '';
        $this->level = $class->level ?? '';
        $this->description = $class->description ?? '';
        $this->status = $class->status ?? 'active';
    }

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
                    ->where('school_id', $schoolId)
                    ->ignore($this->classId),
            ],
            'level' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $class = ClassRoom::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereKey($this->classId)
            ->firstOrFail();

        $class->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?: null,
            'level' => $validated['level'] ?: null,
            'description' => $validated['description'] ?: null,
            'status' => $validated['status'],
        ]);

        session()->flash('success', 'Class updated successfully.');

        return $this->redirectRoute('school.classes.index');
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        return view('livewire.school.classes.class-edit')
            ->layout('components.layouts.school', [
                'title' => 'Edit Class',
            ]);
    }
}
