<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'user_id', 'staff_number', 'first_name',
        'middle_name', 'last_name', 'phone', 'address',
        'qualification', 'employment_status', 'hired_at' ,'bank_name',
    'bank_account_name',
    'bank_account_number',
    'bank_code',
    'payment_notes',
    ];

    protected function casts(): array
    {
        return ['hired_at' => 'date'];
    }

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function assignmentsGiven(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function timetableEntries(): HasMany
    {
        return $this->hasMany(TimetableEntry::class);
    }

   

public function classHeadTeacherAssignments(): HasMany
{
    return $this->hasMany(ClassHeadTeacher::class, 'teacher_id');
}
}
