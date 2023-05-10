<?php

namespace Database\Seeders;

use App\Models\GroupUser;
use Illuminate\Database\Seeder;

class GroupUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GroupUser::factory()->count(10)->create();

    }
}
