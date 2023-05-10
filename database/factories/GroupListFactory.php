<?php

namespace Database\Factories;

use App\Models\GroupList;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = GroupList::class;

    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 20),
            'title' => $this->faker->sentence($nbWords = 3, $variableNbWords = true),
            'discription' => $this->faker->sentence($nbWords = 8, $variableNbWords = true),
            'total_members' => $this->faker->numberBetween(1, 10),
        ];
    }
}
