<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Setting> */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'shop_name'       => 'JAK Test Store',
            'shop_address'    => 'Test Street, Accra',
            'shop_phone'      => '0302000000',
            'currency_symbol' => 'GH₵',
            'receipt_footer'  => 'Thank you for shopping with us!',
        ];
    }
}
