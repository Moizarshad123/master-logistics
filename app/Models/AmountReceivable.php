<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmountReceivable extends Model
{
    use HasFactory;
    protected $fillable = [
        'trip_id',
        'customer_id',
        'payment_type',
        'other_source',
        'amount',
        "date",
        'receipt'
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
