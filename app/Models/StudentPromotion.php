<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPromotion extends Model
{
    use HasFactory, BelongsToSchool;

    public const DECISION_PROMOTE = 'promote';
    public const DECISION_REPEAT = 'repeat';
    public const DECISION_REVIEW = 'review';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_EXECUTED = 'executed';

    protected $fillable = [
        'school_id',
        'student_id',
        'from_enrollment_id',
        'from_academic_session_id',
        'from_class_room_id',
        'to_academic_session_id',
        'to_class_room_id',
        'decision',
        'status',
        'recommendation_reason',
        'approval_note',
        'recommended_by',
        'recommended_at',
        'reviewed_by',
        'reviewed_at',
        'executed_at',
    ];

    protected function casts(): array
    {
        return [
            'school_id' => 'integer',
            'student_id' => 'integer',
            'from_enrollment_id' => 'integer',
            'from_academic_session_id' => 'integer',
            'from_class_room_id' => 'integer',
            'to_academic_session_id' => 'integer',
            'to_class_room_id' => 'integer',
            'recommended_by' => 'integer',
            'reviewed_by' => 'integer',
            'recommended_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'executed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function fromEnrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'from_enrollment_id');
    }

    public function fromAcademicSession(): BelongsTo
    {
        return $this->belongsTo(
            AcademicSession::class,
            'from_academic_session_id'
        );
    }

    public function toAcademicSession(): BelongsTo
    {
        return $this->belongsTo(
            AcademicSession::class,
            'to_academic_session_id'
        );
    }

    public function fromClassRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'from_class_room_id');
    }

    public function toClassRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'to_class_room_id');
    }

    public function recommender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isExecuted(): bool
    {
        return $this->status === self::STATUS_EXECUTED;
    }
}
