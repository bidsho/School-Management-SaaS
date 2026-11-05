<?php

namespace App\Livewire\School\Terms;

use App\Models\AcademicSession;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class TermCreate extends Component
{
    public string $academic_session_id = '';
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
            'academic_session_id' => [
                'required',
                'integer',
                Rule::exists('academic_sessions', 'id')
                    ->where('school_id', $schoolId),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('terms', 'name')
                    ->where('school_id', $schoolId)
                    ->where('academic_session_id', $this->academic_session_id),
            ],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        // The term dates must fit within the selected academic session.
        $session = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereKey($validated['academic_session_id'])
            ->firstOrFail();

        if (
            ($session->starts_at && $validated['starts_at'] < $session->starts_at->format('Y-m-d'))
            || ($session->ends_at && $validated['ends_at'] > $session->ends_at->format('Y-m-d'))
        ) {
            $this->addError(
                'starts_at',
                'The term dates must fall within the selected academic session.'
            );

            return;
        }

        Term::create([
            'school_id' => $schoolId,
            'academic_session_id' => $session->id,
            'name' => trim($validated['name']),
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'status' => $validated['status'],
        ]);

        session()->flash('success', 'Term created successfully.');

        return $this->redirectRoute('school.terms.index');
    }

    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 401);
        abort_if($user->isSuperAdmin(), 403);
        abort_unless($user->school_id, 403);

        $sessions = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $user->school_id)
            ->orderByDesc('starts_at')
            ->get(['id', 'name', 'starts_at', 'ends_at', 'status']);

        return view('livewire.school.terms.term-create', [
            'sessions' => $sessions,
        ])->layout('components.layouts.school', [
            'title' => 'Create Term',
        ]);
    }
}
