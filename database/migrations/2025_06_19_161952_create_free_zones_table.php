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
        Schema::create('free_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('emirate_id')->nullable(); // Optional: Add foreign key
            $table->boolean('status')->default(1);
            $table->integer('sort_order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('free_zones');
    }
};
