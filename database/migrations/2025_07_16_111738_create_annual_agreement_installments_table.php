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
        Schema::create('annual_agreement_installments', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->id();
            $table->unsignedBigInteger('service_request_id');
            $table->unsignedTinyInteger('installment_no'); // 1, 2, 3, or 4
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->date('due_date')->nullable();
            $table->timestamps();

            $table->foreign('service_request_id')->references('id')->on('service_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_agreement_installments');
    }
};
