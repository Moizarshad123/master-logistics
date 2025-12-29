<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmountPayable extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_id',
        'amount',
        'date',
        'payment_via',
        'other_source',
        'receipt'
    ];

    public function supplier()
    {
        return $this->belongsTo(FuelSupplier::class, 'supplier_id');
    }
}
