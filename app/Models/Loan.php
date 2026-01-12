<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $fillable = [
        'driver_id',
        'tenure_from',
        'tenure_to',
        'amount',
        'total_months',
        'monthly_installment',
        'status'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function installments()
    {
        return $this->hasMany(LoanInstallment::class);
    }
}
