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
        Schema::table('activities', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->change();
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->text('receipt_footer')->nullable()->after('shop_logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('receipt_footer');
        });
    }
};
