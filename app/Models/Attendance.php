<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'month',        // "2026-03"
        'present_days',
        'absent_days',
        'leave_days',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}