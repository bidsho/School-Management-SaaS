<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeStructure extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'academic_session_id', 'term_id',
        'class_room_id', 'name', 'description',
        'amount', 'currency', 'due_date', 'status',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'due_date' => 'date'];
    }

    public function academicSession(): BelongsTo { return $this->belongsTo(AcademicSession::class); }
    public function term(): BelongsTo { return $this->belongsTo(Term::class); }
    public function classRoom(): BelongsTo { return $this->belongsTo(ClassRoom::class); }
    public function invoiceItems(): HasMany { return $this->hasMany(InvoiceItem::class); }
}
