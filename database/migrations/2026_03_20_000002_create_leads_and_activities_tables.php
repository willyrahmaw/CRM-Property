<?php

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\LeadTemperature;
use App\Enums\SiteVisitStatus;
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
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUuid('assigned_sales_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code')->unique(); // e.g. "LD-202603-0001"
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('source')->default(LeadSource::MANUAL->value);
            $table->string('campaign')->nullable();
            $table->decimal('budget_min', 15, 2)->default(0);
            $table->decimal('budget_max', 15, 2)->default(0);
            $table->foreignUuid('interested_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('property_type_interest')->nullable();
            $table->string('status')->default(LeadStatus::NEW->value);
            $table->string('temperature')->default(LeadTemperature::COLD->value);
            $table->integer('score')->default(0);
            $table->integer('purchase_target_days')->nullable(); // Target pembelian dalam berapa hari
            $table->string('lost_reason')->nullable(); // Wajib diisi jika status == LOST
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['assigned_sales_id', 'status']);
            $table->index(['temperature', 'score']);
            $table->index('phone');
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('activity_type'); // whatsapp, call, meeting, site_visit, email, note
            $table->dateTime('activity_date');
            $table->text('result')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('next_follow_up_date')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'activity_date']);
            $table->index(['user_id', 'next_follow_up_date']);
        });

        Schema::create('site_visits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete(); // Sales PIC
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignUuid('property_unit_id')->nullable()->constrained('property_units')->nullOnDelete();
            $table->dateTime('visit_date');
            $table->string('status')->default(SiteVisitStatus::SCHEDULED->value);
            $table->text('result')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'visit_date']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_visits');
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('leads');
    }
};
