<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'professor_id',
        'semester_id',
        'section_code',
        'max_students',
        'schedule',
        'room',
        'grades_visible',
    ];

    protected $casts = [
        'grades_visible' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_section_id', 'student_id')
                    ->withPivot('status', 'grade')
                    ->withTimestamps();
    }

    public function getFullCodeAttribute()
    {
        return $this->course->course_code . '(' . $this->section_code . ')';
    }

    public function getEnrolledCountAttribute()
    {
        return $this->enrollments()->whereIn('status', ['enrolled', 'pending_payment'])->count();
    }

    public function hasAvailableSlots()
    {
        return $this->enrolled_count < $this->max_students;
    }
}
