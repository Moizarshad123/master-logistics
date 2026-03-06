<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuance extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'item_id',
        'qty',
        'issue_date',
        'remarks'
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
        return $this->belongsTo(Inventory::class, 'item_id');
    }
}
