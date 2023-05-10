<?php

namespace Database\Factories;

use App\Models\SplitExpense;
use Illuminate\Database\Eloquent\Factories\Factory;

class SplitExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = SplitExpense::class;
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 20),
            'receiver_id' => $this->faker->numberBetween(1, 20),
            'expense_id' => $this->faker->numberBetween(1, 20),
            'group_list_id' => $this->faker->numberBetween(1, 20),
            'amount' => $this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 5000),
            'status' => $this->faker->randomElement($array = array('owe', 'pay')),
            'is_Settled' => $this->faker->randomElement($array = array('Settled', 'notSettled')),
        ];
    }
    // 'status' => $this->faker->randomElements($array = array('owe', 'pay'), $count = 1), // array('c')
    // 'is_Settled' => $this->faker->randomElements($array = array('Settled', 'notSettled'), $count = 1) // array('c')
    // $this->faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = NULL),
}
