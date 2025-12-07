<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'name',
        'description',
        'units',
        'is_general',
    ];

    protected $casts = [
        'is_general' => 'boolean',
    ];

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'course_program');
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class);
    }
}
