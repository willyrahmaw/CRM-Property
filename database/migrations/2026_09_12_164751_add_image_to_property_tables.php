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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('image')->nullable()->after('siteplan_image');
        });

        Schema::table('property_types', function (Blueprint $table) {
            $table->string('image')->nullable()->after('floors');
        });

        Schema::table('property_units', function (Blueprint $table) {
            $table->string('image')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('property_types', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('property_units', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
