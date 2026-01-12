<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanInstallment extends Model
{
    use HasFactory;
    protected $fillable = [
        'loan_id',
        'month',
        'year',
        'amount',
        'status',
        'paid_at'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
