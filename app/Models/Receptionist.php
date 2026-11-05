<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receptionist extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'user_id',
        'staff_number',
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'address',
        'qualification',
        'employment_status',
        'hired_at',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_code',
        'payment_notes',
    ];

    protected function casts(): array
    {
        return [
            'hired_at' => 'date',
            'bank_account_number' => 'encrypted',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim(collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])->filter()->implode(' '));
    }
}