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
        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone', 30)->default('Asia/Jakarta')->after('phone');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('timezone', 30)->default('Asia/Jakarta')->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('timezone');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('timezone');
        });
    }
};
