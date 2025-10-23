<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    use HasFactory;
    protected $fillable = [
        "category_id",
        "name"
    ];

    public function category() {
        return $this->hasOne(ExpenseCategory::class, 'id', "category_id");
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_expenses', 'expense_type_id', 'vehicle_id');
    }
}
