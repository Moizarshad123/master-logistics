<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diesel extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'date',
        'time',
        'litres',
        'per_litre_amount',
        'total_amount',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
