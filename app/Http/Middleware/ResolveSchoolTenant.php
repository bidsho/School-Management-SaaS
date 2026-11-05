<?php

namespace App\Http\Middleware;

use App\Models\School;
use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveSchoolTenant
{
    public function __construct(
        private TenantContext $tenant
    ) {}

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        abort_unless($user, 401);

        // Super Admin accounts must use platform routes.
        abort_if(
            $user->isSuperAdmin(),
            403,
            'Super Admin accounts must use platform routes.'
        );

        // Reject inactive accounts.
        abort_unless(
            $user->isActive(),
            403,
            'Your account is inactive.'
        );

        // Every school user must belong to a school.
        abort_unless(
            $user->school_id,
            403,
            'Your account is not assigned to a school.'
        );

        // Only active schools can access the tenant dashboard.
        $school = School::query()
            ->whereKey($user->school_id)
            ->where('status', 'active')
            ->first();

        abort_unless(
            $school,
            403,
            'Your school is unavailable or inactive.'
        );

        // Establish the current school context.
        $this->tenant->set($school);

        // Make the school available to downstream requests.
        $request->attributes->set('currentSchool', $school);

        return $next($request);
    }
}
