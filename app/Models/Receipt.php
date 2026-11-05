<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'invoice_id', 'payment_id',
        'receipt_number', 'issued_at', 'file_path',
    ];

    protected function casts(): array
    {
        return ['issued_at' => 'datetime'];
    }

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
     public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
