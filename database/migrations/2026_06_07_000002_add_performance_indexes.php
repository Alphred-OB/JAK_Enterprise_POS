<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // activities: user_id foreign key index exists; add action + created_at
        Schema::table('activities', function (Blueprint $table) {
            $table->index('action');
            $table->index('created_at');
        });

        // products: category_id foreign key index exists; add is_active
        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active');
        });

        // users: only email unique exists; add role + is_active for CheckRole queries
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('is_active');
        });

        // shifts: user_id foreign key index exists; add status + created_at
        Schema::table('shifts', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
        });

        // sale_items: sale_id + product_id already indexed; add status + created_at
        Schema::table('sale_items', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['action']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
    }
};
