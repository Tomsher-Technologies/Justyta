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
        Schema::create('public_prosecution_translations', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedBigInteger('public_prosecution_id');
            $table->string('lang', 10); // e.g., 'en', 'ar'
            $table->string('name');
            $table->timestamps();

            $table->foreign('public_prosecution_id')
                  ->references('id')
                  ->on('public_prosecutions')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_prosecution_translations');
    }
};
