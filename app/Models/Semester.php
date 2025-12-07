<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function courseSections()
    {
        return $this->hasMany(CourseSection::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function current()
    {
        return static::where('is_current', true)->first();
    }
}
