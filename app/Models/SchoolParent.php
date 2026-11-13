<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolParent extends Model
{
    use HasFactory, BelongsToSchool;

    /**
     * Database table.
     */
    protected $table = 'parents';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'school_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'occupation',
    ];

    /**
     * Parent belongs to a school.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Parent may have a login account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Parent can have multiple students.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'parent_student',
            'parent_id',
            'student_id'
        )
        ->withPivot([
            'school_id',
            'relationship',
            'is_primary',
        ])
        ->withTimestamps();
    }

    /**
     * Get parent's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
