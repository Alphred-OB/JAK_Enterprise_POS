<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Admin
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@jakpos.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);

        // Sample Categories
        $tissues = \App\Models\Category::create(['name' => 'Tissues', 'description' => 'Soft tissues and rolls']);
        $diapers = \App\Models\Category::create(['name' => 'Diapers', 'description' => 'Baby and adult diapers']);
        $disposables = \App\Models\Category::create(['name' => 'Disposables', 'description' => 'Cups, plates, and cutlery']);

        // Sample Products
        \App\Models\Product::create([
            'category_id' => $tissues->id,
            'name' => 'SoftCare 2-Ply Tissue',
            'sku' => 'TIS-001',
            'barcode' => '1234567890123',
            'cost_price' => 5.00,
            'selling_price' => 8.50,
            'wholesale_price' => 7.00,
            'stock_quantity' => 100,
        ]);

        \App\Models\Product::create([
            'category_id' => $diapers->id,
            'name' => 'Pampers Size 4 (40pcs)',
            'sku' => 'DIA-001',
            'barcode' => '9876543210987',
            'cost_price' => 45.00,
            'selling_price' => 65.00,
            'wholesale_price' => 60.00,
            'stock_quantity' => 50,
        ]);

        \App\Models\Product::create([
            'category_id' => $disposables->id,
            'name' => 'Plastic Cups (50pcs)',
            'sku' => 'DSP-001',
            'barcode' => '5556667778889',
            'cost_price' => 10.00,
            'selling_price' => 15.00,
            'wholesale_price' => 13.00,
            'stock_quantity' => 200,
        ]);
    }
}
