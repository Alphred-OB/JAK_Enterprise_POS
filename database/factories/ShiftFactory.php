<?php

namespace Database\Factories;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Shift> */
class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'opening_cash' => fake()->randomFloat(2, 50, 500),
            'status'       => 'open',
            'opened_at'    => now(),
        ];
    }

    public function closed(): static
    {
        return $this->state([
            'status'    => 'closed',
            'closed_at' => now(),
        ]);
    }
}
