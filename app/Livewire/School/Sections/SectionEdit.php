<?php

namespace App\Livewire\School\Sections;

use App\Models\ClassRoom;
use App\Models\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SectionEdit extends Component
{
    #[Locked]
    public int $sectionId;

    public string $class_room_id = '';
    public string $name = '';
    public string $capacity = '';
    public string $status = 'active';

    public function mount(int $sectionId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $section = Section::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->whereKey($sectionId)
            ->firstOrFail();

        $this->sectionId = $section->id;
        $this->class_room_id = (string) $section->class_room_id;
        $this->name = $section->name;
        $this->capacity = $section->capacity !== null
            ? (string) $section->capacity
            : '';
        $this->status = $section->status ?? 'active';
    }

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

        $section = Section::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereKey($this->sectionId)
            ->firstOrFail();

        $section->update([
            'class_room_id' => $validated['class_room_id'],
            'name' => trim($validated['name']),
            'capacity' => $validated['capacity'] ?: null,
            'status' => $validated['status'],
        ]);

        session()->flash('success', 'Section updated successfully.');

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
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.school.sections.section-edit', [
            'classes' => $classes,
        ])->layout('components.layouts.school', [
            'title' => 'Edit Section',
        ]);
    }
}
