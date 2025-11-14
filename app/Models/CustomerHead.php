<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerHead extends Model
{
    use HasFactory;
    protected $fillable = ['customer_head_id', 'name'];


    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
