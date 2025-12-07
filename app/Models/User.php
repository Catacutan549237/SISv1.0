<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'program_id',
        'student_id',
        'year_level',
        'must_change_password',
        'temp_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function courseSections()
    {
        return $this->hasMany(CourseSection::class, 'professor_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id');
    }

    // Role checking methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isProfessor()
    {
        return $this->role === 'professor';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    // Get current semester enrollments
    public function currentEnrollments()
    {
        $currentSemester = Semester::current();
        if (!$currentSemester) {
            return collect();
        }

        return $this->enrollments()
            ->whereHas('courseSection', function($query) use ($currentSemester) {
                $query->where('semester_id', $currentSemester->id);
            })
            ->with('courseSection.course')
            ->get();
    }

    // Calculate total enrolled units for current semester
    public function getTotalEnrolledUnits($semesterId = null)
    {
        if (!$semesterId) {
            $currentSemester = Semester::current();
            $semesterId = $currentSemester ? $currentSemester->id : null;
        }

        if (!$semesterId) {
            return 0;
        }

        return $this->enrollments()
            ->whereIn('status', ['enrolled', 'pending_payment'])
            ->whereHas('courseSection', function($query) use ($semesterId) {
                $query->where('semester_id', $semesterId);
            })
            ->with('courseSection.course')
            ->get()
            ->sum(function($enrollment) {
                return $enrollment->courseSection->course->units;
            });
    }
}
