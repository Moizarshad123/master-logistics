<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ["customer_head_id", 'name'];

    public function customerHead()
    {
        return $this->belongsTo(CustomerHead::class, 'customer_head_id');
    }
}
