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
        Schema::create('membership_plan_translations', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedBigInteger('membership_plan_id');
            $table->string('lang', 10); // e.g., 'en', 'ar', etc.
            $table->string('title')->nullable();
            $table->timestamps();

            $table->foreign('membership_plan_id')->references('id')->on('membership_plans')->onDelete('cascade');
            $table->unique(['membership_plan_id', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_plan_translations');
    }
};
