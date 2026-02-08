<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diesel extends Model
{
    use HasFactory;
    protected $fillable = [
        'vehicle_id',
        'trip_id',
        'type',
        'date',
        'time',
        'litres',
        'source',
        'per_litre_amount',
        'total_amount',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }
}
