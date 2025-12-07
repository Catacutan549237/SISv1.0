<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'semester_id',
        'total_amount',
        'amount_paid',
        'balance',
        'status',
        'payment_method',
        'reference_number',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function updateBalance()
    {
        $this->balance = $this->total_amount - $this->amount_paid;
        
        if ($this->balance <= 0) {
            $this->status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'pending';
        }
        
        $this->save();
    }
}
