<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name')->default('JAK POS');
            $table->string('shop_address')->nullable();
            $table->string('shop_phone')->nullable();
            $table->string('currency_symbol')->default('GH₵');
            $table->string('shop_logo')->nullable();
            $table->timestamps();
        });

        // Insert initial settings
        DB::table('settings')->insert([
            'shop_name' => 'JAK POS',
            'currency_symbol' => 'GH₵',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
