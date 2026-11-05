<?php

namespace App\Livewire\School\Subjects;

use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SubjectEdit extends Component
{
    #[Locked]
    public int $subjectId;

    public string $name = '';
    public string $code = '';
    public string $description = '';
    public string $status = 'active';

    public function mount(int $subjectId): void
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $subject = Subject::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->whereKey($subjectId)
            ->firstOrFail();

        $this->subjectId = $subject->id;
        $this->name = $subject->name;
        $this->code = $subject->code;
        $this->description = $subject->description ?? '';
        $this->status = $subject->status;
    }

    public function save()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        abort_unless(
            $user->can('edit subjects'),
            403,
            'You do not have permission to edit subjects.'
        );

        $schoolId = $user->school_id;

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('subjects', 'code')
                    ->where('school_id', $schoolId)
                    ->ignore($this->subjectId),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $subject = Subject::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereKey($this->subjectId)
            ->firstOrFail();

        $subject->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?: null,
            'status' => $validated['status'],
        ]);

        session()->flash('success', 'Subject updated successfully.');

        return $this->redirectRoute('school.subjects.index');
    }

    public function render()
    {
        return view('livewire.school.subjects.subject-edit')
            ->layout('components.layouts.school', [
                'title' => 'Edit Subject',
            ]);
    }
}
