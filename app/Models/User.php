<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;


/**
 * @property int $id
 * @property int|null $school_id
 * @property string $name
 * @property string $email
 * @property string|null $status
 * @property Carbon|null $email_verified_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden([
    'password',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'remember_token',
])]
class User extends Authenticatable implements  PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;
    use PasskeyAuthenticatable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * Spatie Permission uses the web guard by default.
     */
    protected string $guard_name = 'web';

    /**
     * Cast model attributes.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials.
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1)
                . Str::substr($initials, -1)
            : $initials;
    }

    /**
     * School that owns this user's account.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Notifications belonging to this user.
     */
    public function schoolNotifications(): HasMany
    {
        return $this->hasMany(SchoolNotification::class);
    }

    /**
     * Teacher profile associated with this user.
     */
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Announcements created by this user.
     */
    public function createdAnnouncements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }
    public function receptionist(): HasOne
{
    return $this->hasOne(Receptionist::class);
}
public function principal(): HasOne
{
    return $this->hasOne(Principal::class);
}

    /**
     * Check whether this user is a platform Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Check whether this user account is active.
     */
    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }
    public function studentProfile(): HasOne
{
    return $this->hasOne(Student::class);
}

public function parentProfile(): HasOne
{
    return $this->hasOne(SchoolParent::class);
}

public function treasury()
{
    return $this->hasOne(\App\Models\Treasury::class);
}

public function isTreasury(): bool
{
    return $this->hasRole('Treasury');
}

}
