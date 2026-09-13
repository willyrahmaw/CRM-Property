<?php

use App\Enums\BookingStatus;
use App\Enums\CommissionStatus;
use App\Enums\MortgageStatus;
use App\Enums\PaymentScheme;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
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
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUuid('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->string('nik')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('occupation')->nullable();
            $table->string('npwp')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'name']);
            $table->index('phone');
            $table->index('nik');
        });

        Schema::create('customer_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignUuid('uploaded_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('document_type'); // ktp, kk, npwp, slip_gaji, rekening_koran, etc.
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->timestamps();

            $table->index(['customer_id', 'document_type']);
        });

        Schema::create('negotiations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignUuid('property_unit_id')->constrained('property_units')->cascadeOnDelete();
            $table->foreignUuid('sales_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('initial_price', 15, 2);
            $table->decimal('customer_offer_price', 15, 2);
            $table->decimal('final_price', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->string('promo_description')->nullable();
            $table->string('approval_status')->default('pending'); // pending, approved, rejected
            $table->foreignUuid('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'property_unit_id']);
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUuid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignUuid('property_unit_id')->constrained('property_units')->cascadeOnDelete();
            $table->foreignUuid('sales_id')->constrained('users')->cascadeOnDelete();
            $table->string('booking_number')->unique(); // e.g. "BKG-202603-0001"
            $table->date('booking_date');
            $table->decimal('booking_fee', 15, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('final_price', 15, 2);
            $table->string('payment_scheme')->default(PaymentScheme::KPR->value);
            $table->string('status')->default(BookingStatus::PENDING->value);
            $table->foreignUuid('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['property_unit_id', 'status']);
            $table->index(['sales_id', 'status']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignUuid('verified_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_number')->unique(); // e.g. "PAY-202603-0001"
            $table->string('payment_type')->default(PaymentType::BOOKING_FEE->value);
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method')->default('Transfer Bank');
            $table->string('reference_number')->nullable();
            $table->string('proof_path')->nullable();
            $table->string('status')->default(PaymentStatus::PENDING->value);
            $table->dateTime('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
            $table->index('payment_type');
        });

        Schema::create('mortgages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('booking_id')->unique()->constrained('bookings')->cascadeOnDelete();
            $table->string('bank_name');
            $table->decimal('submission_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->integer('tenor_years')->default(15);
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->decimal('estimated_installment', 15, 2)->nullable();
            $table->string('status')->default(MortgageStatus::DRAFT->value);
            $table->date('application_date')->nullable();
            $table->date('appraisal_date')->nullable();
            $table->date('sp3k_date')->nullable();
            $table->date('contract_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['bank_name', 'status']);
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('beneficiary_type'); // sales, team_leader, agency, referral
            $table->decimal('selling_price', 15, 2);
            $table->decimal('percentage', 5, 2);
            $table->decimal('amount', 15, 2);
            $table->string('status')->default(CommissionStatus::PENDING->value);
            $table->foreignUuid('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('paid_at')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // e.g. "booking_approved", "unit_status_changed"
            $table->string('entity_type');
            $table->string('entity_id')->nullable();
            $table->json('before_values')->nullable();
            $table->json('after_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['company_id', 'action']);
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('mortgages');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('negotiations');
        Schema::dropIfExists('customer_documents');
        Schema::dropIfExists('customers');
    }
};
