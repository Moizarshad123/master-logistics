<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'role_id'  => 1,
            'name'     => 'Master Logistics Admin',
            'email'    => 'admin@admin.com',
            'password' => Hash::make('admin@123'),
            'status'   => 1
        ]);
    }
}
