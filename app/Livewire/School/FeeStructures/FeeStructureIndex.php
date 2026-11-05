<?php

namespace App\Livewire\School\FeeStructures;

use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\FeeStructure;
use App\Models\Term;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class FeeStructureIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public ?int $academic_session_id = null;

    public ?int $term_id = null;

    public ?int $class_room_id = null;

    public string $name = '';

    public string $description = '';

    public string $amount = '';

    public string $currency = 'NGN';

    public ?string $due_date = null;

    public string $status = 'active';

    public function mount(): void
    {
        $this->authorizeSchoolAdmin();
    }

    private function authorizeSchoolAdmin(): void
    {
        abort_unless(
            Auth::check() &&
            Auth::user()->hasRole('School Admin') &&
            Auth::user()->school_id,
            403,
            'Only a School Admin assigned to a school can manage fee structures.'
        );
    }

    private function schoolId(): int
    {
        return (int) Auth::user()->school_id;
    }

    private function feeQuery(): Builder
    {
        return FeeStructure::query()
            ->where('school_id', $this->schoolId());
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorizeSchoolAdmin();

        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeSchoolAdmin();

        $fee = $this->feeQuery()->findOrFail($id);

        $this->editingId = $fee->id;
        $this->academic_session_id = $fee->academic_session_id;
        $this->term_id = $fee->term_id;
        $this->class_room_id = $fee->class_room_id;
        $this->name = $fee->name;
        $this->description = $fee->description ?? '';
        $this->amount = (string) $fee->amount;
        $this->currency = $fee->currency;
        $this->due_date = $fee->due_date?->format('Y-m-d');
        $this->status = $fee->status;

        $this->resetValidation();
        $this->showForm = true;
    }

    protected function rules(): array
    {
        $schoolId = $this->schoolId();

        return [
            'academic_session_id' => [
                'required',
                'integer',
                Rule::exists('academic_sessions', 'id')
                    ->where('school_id', $schoolId),
            ],

            'term_id' => [
                'required',
                'integer',
                Rule::exists('terms', 'id')
                    ->where('school_id', $schoolId),
            ],

            'class_room_id' => [
                'required',
                'integer',
                Rule::exists('class_rooms', 'id')
                    ->where('school_id', $schoolId),
            ],

            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'currency' => ['required', 'string', 'size:3'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function save(): void
    {
        $this->authorizeSchoolAdmin();

        $data = $this->validate();

        // Only use the authenticated user's school.
        $data['school_id'] = $this->schoolId();

        $data['name'] = trim($data['name']);
        $data['description'] = filled($data['description'])
            ? trim($data['description'])
            : null;

        $data['currency'] = strtoupper(trim($data['currency']));

        if ($this->editingId) {
            $fee = $this->feeQuery()->findOrFail($this->editingId);
            $fee->update($data);

            session()->flash(
                'success',
                'Fee structure updated successfully.'
            );
        } else {
            FeeStructure::create($data);

            session()->flash(
                'success',
                'Fee structure created successfully.'
            );
        }

        $this->resetForm();
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $this->authorizeSchoolAdmin();

        $fee = $this->feeQuery()
            ->withCount('invoiceItems')
            ->findOrFail($id);

        if ($fee->invoice_items_count > 0) {
            session()->flash(
                'error',
                'This fee structure is already linked to invoice items. Deactivate it instead of deleting it.'
            );

            return;
        }

        $fee->delete();

        session()->flash(
            'success',
            'Fee structure deleted successfully.'
        );

        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        $this->authorizeSchoolAdmin();

        $fee = $this->feeQuery()->findOrFail($id);

        $fee->update([
            'status' => $fee->status === 'active'
                ? 'inactive'
                : 'active',
        ]);

        session()->flash(
            'success',
            'Fee structure status updated successfully.'
        );
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'academic_session_id',
            'term_id',
            'class_room_id',
            'name',
            'description',
            'amount',
            'due_date',
        ]);

        $this->currency = 'NGN';
        $this->status = 'active';
        $this->showForm = false;

        $this->resetValidation();
    }

    public function render()
    {
        $schoolId = $this->schoolId();

        $fees = $this->feeQuery()
            ->with(['academicSession', 'term', 'classRoom'])
            ->withCount('invoiceItems')
            ->when(trim($this->search) !== '', function (Builder $query) {
                $search = '%' . trim($this->search) . '%';

                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', $search)
                        ->orWhere('description', 'like', $search)
                        ->orWhere('currency', 'like', $search);
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.school.fee-structures.fee-structure-index', [
            'fees' => $fees,

            'academicSessions' => AcademicSession::query()
                ->where('school_id', $schoolId)
                ->orderByDesc('id')
                ->get(),

            'terms' => Term::query()
                ->where('school_id', $schoolId)
                ->orderBy('id')
                ->get(),

            'classRooms' => ClassRoom::query()
                ->where('school_id', $schoolId)
                ->orderBy('name')
                ->get(),

            'totalFees' => $this->feeQuery()->count(),

            'activeFees' => $this->feeQuery()
                ->where('status', 'active')
                ->count(),

            'inactiveFees' => $this->feeQuery()
                ->where('status', 'inactive')
                ->count(),
        ])->layout('components.layouts.school', [
            'title' => 'Fee Structure Management',
        ]);
    }
}
