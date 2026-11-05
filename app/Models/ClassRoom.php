<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassRoom extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'class_rooms';

    protected $fillable = [
        'school_id', 'name', 'code', 'level', 'description', 'status',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
   

public function classHeadTeacherAssignments(): HasMany
{
    return $this->hasMany(ClassHeadTeacher::class, 'class_room_id');
}
}
