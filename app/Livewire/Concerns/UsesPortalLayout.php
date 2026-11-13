<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Str;

trait UsesPortalLayout
{
    protected function portalRoutePrefix(): string
    {
        $path = $this->getPortalRequestPath();

        if (Str::startsWith($path, '/receptionist/')) {
            return 'receptionist.';
        }

        if (Str::startsWith($path, '/school/')) {
            return 'school.';
        }

        abort(403, 'Unauthorized portal access.');
    }

    protected function portalLayout(): string
    {
        $prefix = $this->portalRoutePrefix();

        if ($prefix === 'receptionist.') {
            return 'components.layouts.receptionist';
        }

        return 'components.layouts.school';
    }

    protected function getPortalRequestPath(): string
    {
        // Normal page request.
        $routeName = request()->route()?->getName();

        if ($routeName && Str::startsWith($routeName, 'school.')) {
            return '/school/';
        }

        if ($routeName && Str::startsWith($routeName, 'receptionist.')) {
            return '/receptionist/';
        }

        // Livewire AJAX request.
        $referer = request()->header('referer');

        if ($referer) {
            $path = parse_url($referer, PHP_URL_PATH);

            if (is_string($path)) {
                if (Str::startsWith($path, '/school/')) {
                    return $path;
                }

                if (Str::startsWith($path, '/receptionist/')) {
                    return $path;
                }
            }
        }

        abort(403, 'Unauthorized portal access.');
    }
}
