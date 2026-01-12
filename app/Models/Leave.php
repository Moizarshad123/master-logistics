<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;
    protected $fillable = [
        'driver_id','from_date','to_date','reason','status'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
