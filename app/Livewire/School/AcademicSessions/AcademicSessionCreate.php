<?php

namespace App\Livewire\School\AcademicSessions;

use App\Models\AcademicSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AcademicSessionCreate extends Component
{
    public string $name = '';
    public string $starts_at = '';
    public string $ends_at = '';
    public string $status = 'active';

    public function save()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $schoolId = (int) $user->school_id;

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('academic_sessions', 'name')
                    ->where('school_id', $schoolId),
            ],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        AcademicSession::create([
            'school_id' => $schoolId,
            'name' => trim($validated['name']),
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'status' => $validated['status'],
        ]);

        session()->flash('success', 'Academic session created successfully.');

        return $this->redirectRoute('school.academic-sessions.index');
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        return view('livewire.school.academic-sessions.academic-session-create')
            ->layout('components.layouts.school', [
                'title' => 'Create Academic Session',
            ]);
    }
}
