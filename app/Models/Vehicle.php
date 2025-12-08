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
        'route_permit_sindh',
        'route_permit_sindh_expiry',
        'route_permit_punjab',
        'route_permit_punjab_expiry',
        'fitness_certificate',
        'fitness_certificate_expiry',
        'insurance_certificate',
        'insurance_certificate_expiry',
        'tax_token',
        'tax_token_expiry',
        'vehicle_file',
        'image'
    ];

    public function expenseTypes()
    {
        return $this->belongsToMany(ExpenseType::class, 'vehicle_expenses', 'vehicle_id', 'expense_type_id');
    }

    public function wheeler() {
        return $this->hasOne(Wheeler::class, 'id', "vehicle_type");
    }

}
