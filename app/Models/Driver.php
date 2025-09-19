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
        'driving_license_front',
        'driving_license_back',
        'image',
        'address'
    ];
}
