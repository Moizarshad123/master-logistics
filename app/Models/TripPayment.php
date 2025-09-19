<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripPayment extends Model
{
    use HasFactory;
    protected $fillable = [
        "trip_id",
        "driver_id",
        "payment_type",
        "amount",
        "date",
        "comments"
    ];
}
