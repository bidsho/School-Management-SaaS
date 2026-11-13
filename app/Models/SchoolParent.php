<?php

namespace App\Livewire\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait UsesPortalLayout
{
    protected function portalRouteName(): ?string
    {
        // Normal page request.
        $routeName = request()->route()?->getName();

        if ($routeName && (
            Str::startsWith($routeName, 'school.') ||
            Str::startsWith($routeName, 'receptionist.')
        )) {
            return $routeName;
        }

        // Livewire AJAX request: recover the original page
        // from the same-origin Referer header.
        $referer = request()->headers->get('referer');

        if (!$referer) {
            return null;
        }

        $refererParts = parse_url($referer);

        if (
            !is_array($refererParts) ||
            empty($refererParts['host']) ||
            strtolower($refererParts['host']) !==
                strtolower(request()->getHost())
        ) {
            return null;
        }

        $path = $refererParts['path'] ?? '/';

        try {
            $refererRequest = Request::create($path, 'GET');

            $matchedRoute = Route::getRoutes()->match($refererRequest);

            return $matchedRoute->getName();
        } catch (NotFoundHttpException $exception) {
            return null;
        }
    }

    protected function portalLayout(): string
    {
        $routeName = $this->portalRouteName();

        if ($routeName && Str::startsWith($routeName, 'receptionist.')) {
            return 'components.layouts.receptionist';
        }

        if ($routeName && Str::startsWith($routeName, 'school.')) {
            return 'components.layouts.school';
        }

        abort(403, 'Unauthorized portal access.');
    }

    protected function portalRoutePrefix(): string
    {
        $routeName = $this->portalRouteName();

        if ($routeName && Str::startsWith($routeName, 'receptionist.')) {
            return 'receptionist.';
        }

        if ($routeName && Str::startsWith($routeName, 'school.')) {
            return 'school.';
        }

        abort(403, 'Unauthorized portal access.');
    }
}
