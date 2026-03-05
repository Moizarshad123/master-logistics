<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuance extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'inventory_id',
        'qty',
        'issue_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
