<?php

namespace App\Livewire\School\Subjects;

use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SubjectCreate extends Component
{
    public string $name = '';
    public string $code = '';
    public string $description = '';
    public string $status = 'active';

    public function save()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        abort_unless(
            $user->can('create subjects'),
            403,
            'You do not have permission to create subjects.'
        );

        $schoolId = $user->school_id;

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('subjects', 'code')
                    ->where('school_id', $schoolId),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        Subject::create([
            'school_id' => $schoolId,
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?: null,
            'status' => $validated['status'],
        ]);

        session()->flash('success', 'Subject created successfully.');

        return $this->redirectRoute('school.subjects.index');
    }

    public function render()
    {
        return view('livewire.school.subjects.subject-create')
            ->layout('components.layouts.school', [
                'title' => 'Create Subject',
            ]);
    }
}
