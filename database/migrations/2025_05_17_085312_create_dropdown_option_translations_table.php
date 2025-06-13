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
        Schema::create('dropdown_option_translations', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->foreignId('dropdown_option_id')->constrained()->onDelete('cascade');
            $table->string('language_code', 5);
            $table->string('name');
            $table->timestamps();

            $table->unique(['dropdown_option_id', 'language_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dropdown_option_translations');
    }
};
