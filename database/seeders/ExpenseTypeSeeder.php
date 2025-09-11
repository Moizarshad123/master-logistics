<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseType;

class ExpenseTypeSeeder extends Seeder
{
    public function run()
    {
        $expenseTypes = [
            'Oil/Filter',
            'Toll Tax',
            'Excise',
            'Extra Work',
            'Challan',
            'Phone Card',
            'Loading Labour 1',
            'Loading Labour 2',
            'Weight',
            'Meal',
            'Police',
            'Bilty',
            'Security',
            'Greasing/Service',
            'Air/Jali',
            'Electrician',
            'Tyre Punc/Air',
            'Unloading Labour 1',
            'Unloading Labour 2',
            'Unloading Labour 3',
            'Egg Reward',
            'Petroling',
            'Easypaisa',
            'Medical',
            'Wanda Reward',
            'Sample Reward'
        ];

        foreach ($expenseTypes as $type) {
            ExpenseType::firstOrCreate(['name' => $type]);
        }

        
    }
}
