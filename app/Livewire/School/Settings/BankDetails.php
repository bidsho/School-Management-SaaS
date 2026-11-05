<?php

namespace App\Livewire\School\Settings;

use App\Models\School;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class BankDetails extends Component
{
    use WithFileUploads;

    public string $schoolName = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    public string $bank_name = '';

    public string $bank_account_name = '';

    public string $bank_account_number = '';

    public string $bank_payment_instructions = '';

    public $logo = null;

    public ?string $currentLogo = null;

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user, 403);

        abort_unless(
            $user->hasAnyRole(['School Admin', 'Super Admin']),
            403,
            'Only authorized administrators can manage school settings.'
        );

        abort_unless(
            $user->school_id,
            403,
            'No school is assigned to this account.'
        );

        $school = School::query()
            ->whereKey($user->school_id)
            ->firstOrFail();

        $this->schoolName = $school->name ?? '';

        $settings = SchoolSetting::query()
            ->where('school_id', $school->id)
            ->first();

        if (! $settings) {
            return;
        }

        $this->email = $settings->email ?? '';
        $this->phone = $settings->phone ?? '';
        $this->address = $settings->address ?? '';

        $this->currentLogo = $settings->logo;

        $this->bank_name = $settings->bank_name ?? '';
        $this->bank_account_name = $settings->bank_account_name ?? '';
        $this->bank_account_number = $settings->bank_account_number ?? '';

        $this->bank_payment_instructions =
            $settings->bank_payment_instructions ?? '';
    }

    private function authorizeSettingsManager(): int
    {
        $user = Auth::user();

        abort_unless($user, 403);

        abort_unless(
            $user->hasAnyRole(['School Admin', 'Super Admin']),
            403,
            'You are not authorized to manage school settings.'
        );

        abort_unless(
            $user->school_id,
            403,
            'No school is assigned to this account.'
        );

        return (int) $user->school_id;
    }

    protected function rules(): array
    {
        return [
            'schoolName' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'bank_name' => [
                'required',
                'string',
                'max:150',
            ],

            'bank_account_name' => [
                'required',
                'string',
                'max:150',
            ],

            'bank_account_number' => [
                'required',
                'string',
                'max:30',
                'regex:/^[0-9 -]+$/',
            ],

            'bank_payment_instructions' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function updatedLogo(): void
    {
        $this->authorizeSettingsManager();

        $this->validateOnly('logo');
    }

    public function save(): void
    {
        $schoolId = $this->authorizeSettingsManager();

        $validated = $this->validate();

        $oldLogo = null;
        $newLogoPath = null;

        // Store a newly uploaded logo before saving the settings.
        if ($this->logo) {
            $newLogoPath = $this->logo->store(
                'school-logos/' . $schoolId,
                'public'
            );

            if (! $newLogoPath) {
                throw ValidationException::withMessages([
                    'logo' => 'The school logo could not be uploaded. Please try again.',
                ]);
            }
        }

        try {
            DB::transaction(function () use (
                $schoolId,
                $validated,
                $newLogoPath,
                &$oldLogo
            ): void {
                $school = School::query()
                    ->whereKey($schoolId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $settings = SchoolSetting::query()
                    ->where('school_id', $schoolId)
                    ->lockForUpdate()
                    ->first();

                if (! $settings) {
                    $settings = new SchoolSetting();
                    $settings->school_id = $schoolId;
                }

                $oldLogo = $settings->logo;

                // School name belongs to the schools table.
                $school->name = $validated['schoolName'];
                $school->save();

                // Contact and payment settings belong to school_settings.
                $settings->email = $validated['email'] ?? null;
                $settings->phone = $validated['phone'] ?? null;
                $settings->address = $validated['address'] ?? null;

                if ($newLogoPath) {
                    $settings->logo = $newLogoPath;
                }

                $settings->bank_name = $validated['bank_name'];
                $settings->bank_account_name = $validated['bank_account_name'];
                $settings->bank_account_number =
                    $validated['bank_account_number'];

                $settings->bank_payment_instructions =
                    $validated['bank_payment_instructions'] ?? null;

                $settings->save();
            });
        } catch (\Throwable $exception) {
            // Remove the newly uploaded file if the database operation fails.
            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }

            throw $exception;
        }

        // Delete the previous logo only after the database transaction succeeds.
        if (
            $newLogoPath &&
            $oldLogo &&
            $oldLogo !== $newLogoPath
        ) {
            Storage::disk('public')->delete($oldLogo);
        }

        $this->currentLogo = $newLogoPath ?? $this->currentLogo;
        $this->logo = null;

        $this->resetValidation();

        session()->flash(
            'success',
            'School profile and bank payment details saved successfully.'
        );
    }

    public function removeLogo(): void
    {
        $schoolId = $this->authorizeSettingsManager();

        $oldLogo = null;

        DB::transaction(function () use ($schoolId, &$oldLogo): void {
            $settings = SchoolSetting::query()
                ->where('school_id', $schoolId)
                ->lockForUpdate()
                ->first();

            if (! $settings || ! $settings->logo) {
                return;
            }

            $oldLogo = $settings->logo;

            $settings->logo = null;
            $settings->save();
        });

        if ($oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        $this->currentLogo = null;
        $this->logo = null;

        $this->resetValidation('logo');

        session()->flash('success', 'School logo removed successfully.');
    }

    public function render()
    {
        $schoolId = $this->authorizeSettingsManager();

        $settings = SchoolSetting::query()
            ->where('school_id', $schoolId)
            ->first();

        $currentLogoUrl = $settings?->logo
            ? Storage::disk('public')->url($settings->logo)
            : null;

        return view('livewire.school.settings.bank-details', [
            'currentLogoUrl' => $currentLogoUrl,
        ])->layout('components.layouts.school');
    }
}
