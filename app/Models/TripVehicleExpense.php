<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripVehicleExpense extends Model
{
    use HasFactory;
    protected $fillable = [
        "trip_id",
        "vehicle_id",
        "expense_type_id",
        "expense",
        "expense_from",
        "amount"
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'id', 'vehicle_id');
    }

    public function driver()
    {
        return $this->hasOne(Trip::class, 'id', 'trip_id');
    }
    
    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    public function expenseName()
    {
        return $this->hasOne(ExpenseType::class, 'id', 'expense');
    }
}
