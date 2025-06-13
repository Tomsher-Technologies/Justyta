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
        Schema::create('translators', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ref_no')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('company_name')->nullable();
            $table->string('emirate_id')->nullable();
            $table->string('image')->nullable();
            $table->string('country')->nullable();
            $table->string('trade_license')->nullable();
            $table->date('trade_license_expiry')->nullable();
            $table->string('emirates_id_front')->nullable();
            $table->string('emirates_id_back')->nullable();
            $table->date('emirates_id_expiry')->nullable();
            $table->string('residence_visa')->nullable();
            $table->date('residence_visa_expiry')->nullable();
            $table->string('passport')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translators');
    }
};
