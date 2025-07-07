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
        Schema::create('default_translator_assignments', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->id();
            $table->unsignedBigInteger('from_language_id');
            $table->unsignedBigInteger('to_language_id');
            $table->unsignedBigInteger('translator_id');
            $table->unsignedBigInteger('assigned_by')->nullable(); // admin ID
            $table->timestamp('assigned_at')->useCurrent();

            $table->unique(['from_language_id', 'to_language_id']);

            $table->foreign('from_language_id')->references('id')->on('translation_languages');
            $table->foreign('to_language_id')->references('id')->on('translation_languages');
            $table->foreign('translator_id')->references('id')->on('translators');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_translator_assignments');
    }
};
