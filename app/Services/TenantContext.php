<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class TenantContext
{
    private ?School $school = null;

    public function set(School $school): void
    {
        $this->school = $school;
    }

    public function school(): ?School
    {
        return $this->school;
    }

    public function schoolId(): ?int
    {
        return $this->school?->getKey();
    }

    public function requireSchool(): School
    {
        if (! $this->school) {
            throw new AuthorizationException(
                'No school tenant is available for this request.'
            );
        }

        return $this->school;
    }

    public function assertUserBelongsToSchool(User $user): void
    {
        if (
            $user->isSuperAdmin()
            || ! $this->school
            || (int) $user->school_id !== (int) $this->school->id
        ) {
            throw new AuthorizationException(
                'You are not authorized to access this school.'
            );
        }
    }
}
