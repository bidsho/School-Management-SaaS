<?php

namespace App\Livewire\Concerns;

trait UsesPortalLayout
{
    protected function portalLayout(): string
    {
        if (request()->routeIs('receptionist.*')) {
            return 'components.layouts.receptionist';
        }

        if (request()->routeIs('school.*')) {
            return 'components.layouts.school';
        }

        abort(403, 'Unauthorized portal access.');
    }

    protected function portalRoutePrefix(): string
    {
        if (request()->routeIs('receptionist.*')) {
            return 'receptionist.';
        }

        if (request()->routeIs('school.*')) {
            return 'school.';
        }

        abort(403, 'Unauthorized portal access.');
    }
}
