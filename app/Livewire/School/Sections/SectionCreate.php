<?php

namespace App\Livewire\School\Sections;

use App\Models\ClassRoom;
use App\Models\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SectionCreate extends Component
{
    public string $class_room_id = '';
    public string $name = '';
    public string $capacity = '';
    public string $status = 'active';

    public function save()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = (int) $user->school_id;

        $validated = $this->validate([
            'class_room_id' => [
                'required',
                'integer',
                Rule::exists('class_rooms', 'id')
                    ->where('school_id', $schoolId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        Section::create([
            'school_id' => $schoolId,
            'class_room_id' => $validated['class_room_id'],
            'name' => trim($validated['name']),
            'capacity' => $validated['capacity'] ?: null,
            'status' => $validated['status'],
        ]);

        session()->flash('success', 'Section created successfully.');

        return $this->redirectRoute('school.sections.index');
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $classes = ClassRoom::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.school.sections.section-create', [
            'classes' => $classes,
        ])->layout('components.layouts.school', [
            'title' => 'Create Section',
        ]);
    }
}
