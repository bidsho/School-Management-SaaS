<?php

namespace App\Models\Concerns;

use App\Models\School;
use App\Services\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;

trait BelongsToSchool
{
    public static function bootBelongsToSchool(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            $user = Auth::user();

            // Console commands, migrations and unauthenticated public
            // flows are not automatically scoped by this trait.
            if (! $user || $user->isSuperAdmin()) {
                return;
            }

            $tenant = app(TenantContext::class);
            $schoolId = $tenant->schoolId();

            // Fail closed if an authenticated school user has no tenant.
            if (! $schoolId || (int) $user->school_id !== (int) $schoolId) {
                $builder->whereRaw('1 = 0');

                return;
            }

            $builder->where(
                $builder->getModel()->qualifyColumn('school_id'),
                $schoolId
            );
        });

        static::creating(function (Model $model) {
            $user = Auth::user();

            if (! $user || $user->isSuperAdmin()) {
                // Trusted platform-level services may explicitly set
                // school_id when creating tenant records.
                return;
            }

            $schoolId = app(TenantContext::class)->schoolId();

            if (! $schoolId || (int) $user->school_id !== (int) $schoolId) {
                throw new AuthorizationException(
                    'A valid school tenant is required.'
                );
            }

            // Never trust school_id from a form or request.
            $model->school_id = $schoolId;
        });

        static::updating(function (Model $model) {
            $user = Auth::user();

            if (! $user || $user->isSuperAdmin()) {
                return;
            }

            $schoolId = app(TenantContext::class)->schoolId();

            if (
                ! $schoolId
                || (int) $model->getOriginal('school_id') !== (int) $schoolId
                || (int) $model->school_id !== (int) $schoolId
            ) {
                throw new AuthorizationException(
                    'You cannot move or update a record outside your school.'
                );
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}