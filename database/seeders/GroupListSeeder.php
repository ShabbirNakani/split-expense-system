<?php

namespace Database\Seeders;

use App\Models\GroupList;
use Illuminate\Database\Seeder;

class GroupListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GroupList::factory()->count(10)->create();
    }
}
