<?php

namespace App\Livewire\Platform\Settings;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PlatformSettings extends Component
{
    public string $platformName = 'School Management SaaS';

    public string $supportEmail = '';

    public string $supportPhone = '';

    public string $defaultTimezone = 'Africa/Lagos';

    public bool $schoolRegistrationEnabled = true;

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->can('manage platform settings'),
            403
        );

        $this->platformName = (string) PlatformSetting::getValue(
            'platform_name',
            'School Management SaaS'
        );

        $this->supportEmail = (string) PlatformSetting::getValue(
            'support_email',
            ''
        );

        $this->supportPhone = (string) PlatformSetting::getValue(
            'support_phone',
            ''
        );

        $this->defaultTimezone = (string) PlatformSetting::getValue(
            'default_timezone',
            'Africa/Lagos'
        );

        $this->schoolRegistrationEnabled = filter_var(
            PlatformSetting::getValue('school_registration_enabled', '1'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    public function save(): void
    {
        abort_unless(
            Auth::user()?->can('manage platform settings'),
            403
        );

        $validated = $this->validate([
            'platformName' => ['required', 'string', 'max:150'],
            'supportEmail' => ['nullable', 'email', 'max:255'],
            'supportPhone' => ['nullable', 'string', 'max:30'],
            'defaultTimezone' => [
                'required',
                'string',
                Rule::in(timezone_identifiers_list()),
            ],
            'schoolRegistrationEnabled' => ['required', 'boolean'],
        ]);

        PlatformSetting::setValue(
            'platform_name',
            $validated['platformName']
        );

        PlatformSetting::setValue(
            'support_email',
            $validated['supportEmail'] ?? ''
        );

        PlatformSetting::setValue(
            'support_phone',
            $validated['supportPhone'] ?? ''
        );

        PlatformSetting::setValue(
            'default_timezone',
            $validated['defaultTimezone']
        );

        PlatformSetting::setValue(
            'school_registration_enabled',
            $validated['schoolRegistrationEnabled'] ? '1' : '0'
        );

        session()->flash('success', 'Platform settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.platform.settings.platform-settings')->extends('layouts.super');
    }
}
