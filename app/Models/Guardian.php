<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guardian extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'parents';

    protected $fillable = [
        'school_id', 'first_name', 'last_name', 'email',
        'phone', 'address', 'occupation',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student')
            ->withPivot('relationship', 'is_primary')
            ->withTimestamps();
    }
}
