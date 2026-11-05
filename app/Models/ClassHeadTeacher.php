<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassHeadTeacher extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'teacher_id',
        'class_room_id',
        'academic_session_id',
        'assigned_by',
        'status',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'school_id' => 'integer',
            'teacher_id' => 'integer',
            'class_room_id' => 'integer',
            'academic_session_id' => 'integer',
            'assigned_by' => 'integer',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
