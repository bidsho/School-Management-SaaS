<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use App\Services\TenantContext;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isActive()
            && $user->can('view students')
            && $this->hasSchoolContext($user);
    }

    public function view(User $user, Student $student): bool
    {
        return $this->canAccessStudent($user, $student)
            && $user->can('view students');
    }

    public function create(User $user): bool
    {
        return $user->isActive()
            && $user->can('manage students')
            && $this->hasSchoolContext($user);
    }

    public function update(User $user, Student $student): bool
    {
        return $this->canAccessStudent($user, $student)
            && $user->can('manage students');
    }

    public function delete(User $user, Student $student): bool
    {
        return $this->canAccessStudent($user, $student)
            && $user->can('manage students');
    }

    private function canAccessStudent(
        User $user,
        Student $student
    ): bool {
        return $user->isActive()
            && $this->hasSchoolContext($user)
            && (int) $student->school_id === (int) $user->school_id;
    }

    private function hasSchoolContext(User $user): bool
    {
        if ($user->isSuperAdmin() || ! $user->school_id) {
            return false;
        }

        return (int) app(TenantContext::class)->schoolId()
            === (int) $user->school_id;
    }
}