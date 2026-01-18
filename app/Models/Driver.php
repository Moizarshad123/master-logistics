<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        "salary",
        'cnic_front',
        'cnic_back',
        'cnic_expiry_date',
        'license_expiry_date',
        'driving_license_front',
        'driving_license_back',
        'image',
        'address',
        'status'
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function advanceSalaries()
    {
        return $this->hasMany(AdvanceSalary::class);
    }
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
