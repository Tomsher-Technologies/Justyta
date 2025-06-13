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
        Schema::create('translator_dropdown_options', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedBigInteger('translator_id');
            $table->unsignedBigInteger('dropdown_option_id');
            $table->foreign('translator_id')->references('id')->on('translators')->onDelete('cascade');
            $table->foreign('dropdown_option_id')->references('id')->on('dropdown_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translator_dropdown_options');
    }
};
