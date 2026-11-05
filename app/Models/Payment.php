<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\BankTransaction;


class Payment extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'invoice_id', 'student_id', 'reference',
        'provider', 'provider_reference', 'amount', 'currency',
        'status', 'paid_at', 'verified_at', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
    public function bankTransaction(): HasOne
{
    return $this->hasOne(BankTransaction::class);
}


    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function receipt(): HasOne { return $this->hasOne(Receipt::class); }
}
