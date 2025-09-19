<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
    protected $fillable = [
        "trip_no",
        "vehicle_id",
        "driver_id",
        "balance"
    ];

    public function tripDetails()
    {
        return $this->hasMany(TripDetail::class);
    }


    public function tripPayments()
    {
        return $this->hasMany(TripPayment::class);
    }

    public function tripExpenses()
    {
        return $this->hasMany(TripVehicleExpense::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
