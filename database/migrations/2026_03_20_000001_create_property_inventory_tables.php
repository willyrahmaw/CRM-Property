<?php

use App\Enums\ProjectStatus;
use App\Enums\PropertyUnitStatus;
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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('developer_name')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('description')->nullable();
            $table->json('facilities')->nullable();
            $table->string('siteplan_image')->nullable();
            $table->string('status')->default(ProjectStatus::ACTIVE->value);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
        });

        Schema::create('clusters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');
        });

        Schema::create('property_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name'); // e.g. "Type 45/90 - Aster"
            $table->string('code')->nullable();
            $table->decimal('building_area', 8, 2)->default(0);
            $table->decimal('land_area', 8, 2)->default(0);
            $table->integer('bedrooms')->default(2);
            $table->integer('bathrooms')->default(1);
            $table->integer('floors')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('project_id');
        });

        Schema::create('property_units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cluster_id')->constrained('clusters')->cascadeOnDelete();
            $table->foreignUuid('property_type_id')->nullable()->constrained('property_types')->nullOnDelete();
            $table->string('unit_number');
            $table->string('block')->nullable();
            $table->decimal('land_area', 8, 2);
            $table->decimal('building_area', 8, 2);
            $table->integer('bedrooms')->default(2);
            $table->integer('bathrooms')->default(1);
            $table->integer('floors')->default(1);
            $table->string('direction')->nullable(); // e.g. "Utara", "Selatan"
            $table->decimal('base_price', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->string('status')->default(PropertyUnitStatus::AVAILABLE->value);
            $table->json('siteplan_coordinates')->nullable(); // Polygon or SVG path/coordinates for interactive siteplan
            $table->string('promo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cluster_id', 'status']);
            $table->index(['status', 'selling_price']);
            $table->unique(['cluster_id', 'unit_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_units');
        Schema::dropIfExists('property_types');
        Schema::dropIfExists('clusters');
        Schema::dropIfExists('projects');
    }
};
