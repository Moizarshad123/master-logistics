<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSweetner extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_id',
        'total_litres',
        'fuel_type',
        'per_litre_price',
        'total_amount',
        'date',
        'receiving_receipt',
        'delivery_challan'
    ];

    public function supplier()
    {
        return $this->belongsTo(FuelSupplier::class, 'supplier_id');
    }
}
