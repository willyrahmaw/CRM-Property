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
        Schema::table('property_units', function (Blueprint $table) {
            $table->string('dimension', 50)->nullable()->after('land_area');
            $table->unsignedTinyInteger('carports')->default(1)->after('floors');
            $table->string('electricity', 50)->nullable()->after('direction');
            $table->string('water_source', 100)->nullable()->after('electricity');
            $table->string('certificate_type', 50)->nullable()->after('water_source');
            $table->json('building_specs')->nullable()->after('certificate_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_units', function (Blueprint $table) {
            $table->dropColumn([
                'dimension',
                'carports',
                'electricity',
                'water_source',
                'certificate_type',
                'building_specs',
            ]);
        });
    }
};
