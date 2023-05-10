<?php

namespace Database\Seeders;

use App\Models\SplitExpense;
use Illuminate\Database\Seeder;

class SplitExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SplitExpense::factory()->count(10)->create();
    }
}
