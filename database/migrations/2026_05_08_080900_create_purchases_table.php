<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->char('supplier_id', 36);
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');
            $table->char('user_id', 36);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->string('reference_number')->unique();
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->enum('status', ['received', 'pending'])->default('received');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
