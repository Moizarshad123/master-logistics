<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        "trip_id",
        "customer_id",
        "trip_type",
        "start_date",
        "end_date",
        "from_destination",
        "to_destination",
        "material",
        "material_type",
        "total_bags",
        "weekly_labour",
        "baloch_labour",
        "baloch_labour_rate",
        "no_of_labour",
        "rent",
        "comments",
        "weight",
        "status"
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function from_dest()
    {
        return $this->hasOne(Destination::class, 'id', 'from_destination');
    }

    public function to_dest()
    {
        return $this->hasOne(Destination::class, 'id', 'to_destination');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
}
