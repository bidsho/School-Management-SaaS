<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamScore extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'exam_id', 'student_id', 'subject_id',
        'ca_score', 'exam_score', 'total_score', 'grade',
        'remark', 'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'ca_score' => 'decimal:2',
            'exam_score' => 'decimal:2',
            'total_score' => 'decimal:2',
        ];
    }

    public function exam(): BelongsTo { return $this->belongsTo(Exam::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function examiner(): BelongsTo { return $this->belongsTo(User::class, 'entered_by'); }
}
