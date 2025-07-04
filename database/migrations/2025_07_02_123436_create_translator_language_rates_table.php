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
        Schema::create('translator_language_rates', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedBigInteger('translator_id');
            $table->unsignedBigInteger('from_language_id');
            $table->unsignedBigInteger('to_language_id');
            $table->decimal('hours_per_page', 5, 2)->default(0.00);
            $table->decimal('admin_amount', 10, 2)->default(0.00);
            $table->decimal('translator_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->storedAs('admin_amount + translator_amount');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->unique(['translator_id', 'from_language_id', 'to_language_id'], 'unique_translator_lang_pair');

            $table->foreign('translator_id')->references('id')->on('translators')->onDelete('cascade');
            $table->foreign('from_language_id')->references('id')->on('translation_languages')->onDelete('cascade');
            $table->foreign('to_language_id')->references('id')->on('translation_languages')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translator_language_rates');
    }
};
