<?php

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     *
     */
    protected $model = Expense::class;
    public function definition()
    {
        return [
            'title' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),
            'amount' => $this->faker->numberBetween(1, 10000),
            'user_id' => $this->faker->numberBetween(1, 20),
            'group_list_id' => $this->faker->numberBetween(1, 20),
            'expense_date' => $this->faker->date(),
        ];
    }
}
