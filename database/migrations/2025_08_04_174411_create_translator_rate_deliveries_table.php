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
        Schema::create('translator_rate_deliveries', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->id();
            $table->foreignId('rate_id')->constrained('translator_language_rates')->onDelete('cascade');
            $table->enum('priority_type', ['normal', 'urgent']);
            $table->enum('delivery_type', ['email', 'physical']);
            $table->decimal('delivery_amount', 8, 2)->nullable();
            $table->decimal('admin_amount', 8, 2);
            $table->decimal('translator_amount', 8, 2);
            $table->decimal('tax', 8, 2)->default(0);
            $table->decimal('total_amount', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translator_rate_deliveries');
    }
};
