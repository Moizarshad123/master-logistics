<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overhead extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'expense_type_id',
        'driver_id',
        'amount',
        'date',
        'comment',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
