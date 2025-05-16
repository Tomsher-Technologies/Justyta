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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('logo')->nullable();
            $table->string('office_address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->string('trade_license')->nullable();
            $table->date('trade_license_expiry')->nullable();
            $table->string('emirates_id_front')->nullable();
            $table->string('emirates_id_back')->nullable();
            $table->date('emirates_id_expiry')->nullable();
            $table->string('residence_visa')->nullable();
            $table->date('residence_visa_expiry')->nullable();
            $table->string('passport')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('card_of_law')->nullable();
            $table->date('card_of_law_expiry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
