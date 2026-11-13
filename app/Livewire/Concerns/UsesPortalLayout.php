<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Str;

trait UsesPortalLayout
{
    /**
     * Determine the current portal route prefix.
     */
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

    /**
     * Return the correct portal layout.
     */
    protected function portalLayout(): string
    {
        return match ($this->portalRoutePrefix()) {
            'receptionist.' => 'components.layouts.receptionist',
            'school.' => 'components.layouts.school',
            default => abort(403, 'Unauthorized portal access.'),
        };
    }

    /**
     * Resolve the original portal page path.
     *
     * Livewire AJAX requests use the Referer header because
     * their current route is the Livewire update endpoint.
     */
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
                if (
                    Str::startsWith($path, '/school/') ||
                    Str::startsWith($path, '/receptionist/')
                ) {
                    return $path;
                }
            }
        }

        abort(403, 'Unauthorized portal access.');
    }
}
