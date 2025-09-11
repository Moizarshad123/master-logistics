<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        "trip_id",
        "start_date",
        "end_date",
        "from_destination",
        "to_destination",
        "material",
        "total_bags",
        "loading_labour",
        "unloading_labour",
        "rent",
        "advance",
        "weight",
        "status"
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
