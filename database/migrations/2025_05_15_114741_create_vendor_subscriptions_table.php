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
        Schema::create('vendor_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('membership_plan_id')->constrained()->onDelete('set null');

            // Snapshot of features
            $table->decimal('amount', 10, 2)->default(0);
            $table->integer('member_count')->default(0);
            $table->integer('job_post_count')->default(0);
            $table->decimal('en_ar_price', 10, 2)->default(0);
            $table->decimal('for_ar_price', 10, 2)->default(0);
            $table->boolean('live_online')->default(false);
            $table->boolean('specific_law_firm_choice')->default(false);
            $table->boolean('annual_legal_contract')->default(false);
            $table->integer('annual_free_ad_days')->default(0);
            $table->boolean('unlimited_training_applications')->default(false);
            $table->boolean('welcome_gift')->default(false);

            // Dates
            $table->date('subscription_start')->nullable();
            $table->date('subscription_end')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_subscriptions');
    }
};
