<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Student extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'admission_number',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'address',
        'photo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Student enrollments.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Parents or guardians linked to this student.
     */
    

// Add these methods inside the Student class.

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function parents(): BelongsToMany
{
    return $this->belongsToMany(
        SchoolParent::class,
        'parent_student',
        'student_id',
        'parent_id'
    )->withPivot([
        'school_id',
        'relationship',
        'is_primary',
    ])->withTimestamps();
}

    /**
     * Student attendance records.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Student examination scores.
     */
    public function examScores(): HasMany
    {
        return $this->hasMany(ExamScore::class);
    }

    /**
     * Student invoices.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Student payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
