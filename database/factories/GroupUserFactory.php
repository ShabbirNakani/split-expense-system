<?php

namespace Database\Factories;

use App\Models\GroupUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = GroupUser::class;

    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 20),
            'group_list_id' => $this->faker->numberBetween(1, 20),
        ];
    }
}
