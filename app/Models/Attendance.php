<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'attendance';

    protected $fillable = [
        'school_id', 'student_id', 'class_room_id', 'section_id',
        'academic_session_id', 'term_id', 'attendance_date',
        'status', 'marked_by', 'remarks',
    ];

    protected function casts(): array
    {
        return ['attendance_date' => 'date'];
    }

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function classRoom(): BelongsTo { return $this->belongsTo(ClassRoom::class); }
    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
    public function academicSession(): BelongsTo { return $this->belongsTo(AcademicSession::class); }
    public function term(): BelongsTo { return $this->belongsTo(Term::class); }
    public function marker(): BelongsTo { return $this->belongsTo(User::class, 'marked_by'); }
}
