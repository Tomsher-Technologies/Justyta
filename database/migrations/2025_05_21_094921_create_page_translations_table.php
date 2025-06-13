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
        Schema::create('page_translations', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->string('lang');
            $table->string('title')->nullable();       // Optional
            $table->text('description')->nullable();   // Optional
            $table->longText('content')->nullable();   // Optional
            $table->timestamps();

            $table->unique(['page_id', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_translations');
    }
};
