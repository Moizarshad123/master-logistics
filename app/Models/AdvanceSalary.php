<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceSalary extends Model
{
    use HasFactory;
    protected $fillable = [
        'driver_id',
        'month',
        'year',
        'amount'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
