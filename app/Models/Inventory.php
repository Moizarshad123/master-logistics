<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'qty',
        'unit_price',
        'total_price',
        'purchase_date',
        'vendor',
        'invoice_no',
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
