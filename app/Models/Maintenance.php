<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;
    protected $fillable = ['vehicle_id', 'expense_id', 'amount', 'comments', 'date'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_no');
    }

    public function expense()
    {
        return $this->belongsTo(ExpenseType::class);
    }
}
