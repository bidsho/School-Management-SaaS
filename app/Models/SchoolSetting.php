<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'logo', 'address', 'phone', 'email',
        'grading_configuration', 'settings',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_payment_instructions',
    ];

    protected function casts(): array
    {
        return [
            'grading_configuration' => 'array',
            'settings' => 'array',
        ];
    }
}
