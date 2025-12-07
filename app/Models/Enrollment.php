<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_section_id',
        'status',
        'grade',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function courseSection()
    {
        return $this->belongsTo(CourseSection::class);
    }

    public function scopePendingPayment($query)
    {
        return $query->where('status', 'pending_payment');
    }

    public function scopeEnrolled($query)
    {
        return $query->where('status', 'enrolled');
    }

    public function scopeDropped($query)
    {
        return $query->where('status', 'dropped');
    }
}
