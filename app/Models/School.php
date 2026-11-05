<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'logo',
        'status',
        'timezone',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Subscriptions
    |--------------------------------------------------------------------------
    */

    // All subscriptions belonging to this school.
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Latest subscription, regardless of status.
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->latestOfMany();
    }

    // Latest active subscription.
    // Uses ordinary ordering instead of ofMany() with a closure.
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->orderByDesc('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Principals
    |--------------------------------------------------------------------------
    */

    public function principals(): HasMany
    {
        return $this->hasMany(Principal::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Receptionists
    |--------------------------------------------------------------------------
    */

    public function receptionists(): HasMany
    {
        return $this->hasMany(Receptionist::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Subscription Access
    |--------------------------------------------------------------------------
    */

    public function hasActiveSubscription(): bool
    {
        $subscription = $this->activeSubscription;

        if (!$subscription) {
            return false;
        }

        // Do not allow expired subscriptions.
        if (
            $subscription->ends_at !== null &&
            $subscription->ends_at->isPast()
        ) {
            return false;
        }

        // Do not allow subscriptions that have not started.
        if (
            $subscription->starts_at !== null &&
            $subscription->starts_at->isFuture()
        ) {
            return false;
        }

        return true;
    }
    public function subscriptionPayments(): HasMany
{
    return $this->hasMany(SubscriptionPayment::class);
}
}
