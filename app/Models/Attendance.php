<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = ['driver_id', 'date', 'status'];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
