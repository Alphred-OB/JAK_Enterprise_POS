<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Widen the column from varchar(4) to varchar(255) to hold bcrypt hashes
        Schema::table('users', function (Blueprint $table) {
            $table->string('pin_code', 255)->nullable()->change();
        });

        // Hash all existing plaintext PINs (≤6 chars means plaintext)
        // Use lazy/chunk to avoid loading the entire users table into memory.
        DB::table('users')->whereNotNull('pin_code')->lazyById(100, 'id')->each(function ($user) {
            if (strlen($user->pin_code) <= 6) {
                DB::table('users')->where('id', $user->id)->update([
                    'pin_code' => Hash::make($user->pin_code),
                ]);
            }
        });
    }

    public function down(): void
    {
        // Cannot reverse a one-way hash — PINs must be reset manually if rolled back
        Schema::table('users', function (Blueprint $table) {
            $table->string('pin_code', 4)->nullable()->change();
        });
    }
};
