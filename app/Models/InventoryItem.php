<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;
        protected $fillable = [
        'name',
        'make',
        'model',
        'unit',
    ];

    public function inventories() {
        return $this->hasMany(Inventory::class, 'item_id');
    }

    public function remainingQty() {
        return $this->inventories()->sum('remaining_qty');
    }
}
