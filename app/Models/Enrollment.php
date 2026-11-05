<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory, BelongsToSchool;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_WITHDRAWN = 'withdrawn';
    public const STATUS_TRANSFERRED = 'transferred';

    protected $fillable = [
        'school_id',
        'student_id',
        'academic_session_id',
        'term_id',
        'class_room_id',
        'section_id',
        'admission_status',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'school_id' => 'integer',
            'student_id' => 'integer',
            'academic_session_id' => 'integer',
            'term_id' => 'integer',
            'class_room_id' => 'integer',
            'section_id' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function isActive(): bool
    {
        return $this->admission_status === self::STATUS_ACTIVE;
    }
}
