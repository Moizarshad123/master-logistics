<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'price',
        'purchase_date',
        'qty',
        'remaining_qty'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'price'         => 'decimal:2',
    ];

    public function item() {
        return $this->hasOne(InventoryItem::class, 'id', 'item_id');
    }
}
