<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Treasury extends Model
{
    protected $fillable = [
        'school_id',
        'user_id',
        'employee_number',
        'phone',
        'job_title',
        'status',
        'date_joined',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_joined' => 'date',
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
}