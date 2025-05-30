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
        Schema::create('lawyers', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedBigInteger('lawfirm_id');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth')->nullable();
            $table->string('emirate_id')->nullable();
            $table->string('nationality')->nullable();
            $table->unsignedBigInteger('years_of_experience')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('emirate_id_front')->nullable();
            $table->string('emirate_id_back')->nullable();
            $table->date('emirate_id_expiry')->nullable();
            $table->string('passport')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('residence_visa')->nullable();
            $table->date('residence_visa_expiry')->nullable();
            $table->string('bar_card')->nullable();
            $table->date('bar_card_expiry')->nullable();
            $table->string('practicing_lawyer_card')->nullable();
            $table->date('practicing_lawyer_card_expiry')->nullable();
            $table->timestamps();

            $table->foreign('lawfirm_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('years_of_experience')->references('id')->on('dropdown_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lawyers');
    }
};
