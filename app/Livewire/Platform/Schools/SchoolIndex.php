<?php

namespace App\Livewire\Platform\Schools;

use App\Models\School;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SchoolIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function deleteSchool(int $id): void
    {
        $school = School::findOrFail($id);

        // Do not delete schools with associated records.
        if (
            $school->users()->exists()
            || $school->students()->exists()
            || $school->subscriptions()->exists()
        ) {
            session()->flash(
                'error',
                'This school has associated records. Suspend it instead.'
            );

            return;
        }

        DB::transaction(function () use ($school) {
            $school->delete();
        });

        session()->flash('success', 'School deleted successfully.');

        $this->resetPage();
    }

    public function render()
    {
        $schools = School::query()
            ->when($this->search !== '', function ($query) {
                $search = $this->search;

                $query->where(function ($subquery) use ($search) {
                    $subquery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.platform.schools.school-index', [
            'schools' => $schools,
        ])->extends('layouts.super');
    }
}
