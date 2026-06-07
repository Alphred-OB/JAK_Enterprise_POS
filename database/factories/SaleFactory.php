<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Sale> */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 5, 500);
        $discount = 0;
        $total    = $subtotal;

        return [
            'user_id'        => User::factory()->cashier(),
            'shift_id'       => Shift::factory(),
            'customer_id'    => null,
            'receipt_number' => 'REC-' . strtoupper(Str::random(8)),
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'total'          => $total,
            'cash_received'  => $total,
            'change_amount'  => 0,
            'payment_method' => 'cash',
            'status'         => 'completed',
        ];
    }
}
