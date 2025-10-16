<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseSheet extends Model
{
    use HasFactory;
    protected $fillable = [
        "station",
        "per_ton_rate",
        'type'
    ];
}
