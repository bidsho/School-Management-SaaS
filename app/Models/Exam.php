<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'term_id',
        'class_room_id',
        'subject_id',
        'name',
        'type',
        'exam_date',
        'total_marks',
        'status',
        'created_by',

        // Results approval workflow
        'results_status',
        'results_submitted_by',
        'results_submitted_at',
        'results_reviewed_by',
        'results_reviewed_at',
        'results_review_note',
    ];

    protected function casts(): array
    {
        return [
            'school_id' => 'integer',
            'academic_session_id' => 'integer',
            'term_id' => 'integer',
            'class_room_id' => 'integer',
            'subject_id' => 'integer',
            'created_by' => 'integer',
            'results_submitted_by' => 'integer',
            'results_reviewed_by' => 'integer',

            'exam_date' => 'date',
            'total_marks' => 'decimal:2',
            'results_submitted_at' => 'datetime',
            'results_reviewed_at' => 'datetime',
        ];
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

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(ExamScore::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function resultsSubmitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'results_submitted_by');
    }

    public function resultsReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'results_reviewed_by');
    }
}
