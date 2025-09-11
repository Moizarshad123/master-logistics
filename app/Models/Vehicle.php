<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $fillable = [
        'vehicle_no',
        'chachis_no',
        'engine_no',
        'vehicle_type',
        'make',
        'model',
        'image',
    ];

    public function expenseTypes()
    {
        return $this->belongsToMany(ExpenseType::class, 'vehicle_expenses', 'vehicle_id', 'expense_type_id');
    }

    public function wheeler() {
        return $this->hasOne(Wheeler::class, 'id', "vehicle_type");
    }

}
