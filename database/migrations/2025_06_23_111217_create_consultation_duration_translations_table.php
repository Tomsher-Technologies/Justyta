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
        Schema::create('consultation_duration_translations', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedBigInteger('consultation_duration_id');
            $table->string('lang', 5);
            $table->string('name');
            $table->timestamps();

            // Shorter unique constraint name
            $table->unique(['consultation_duration_id', 'lang'], 'uniq_cd_trans_duration_lang');

            $table->foreign('consultation_duration_id', 'fk_cd_trans_duration_id')
                ->references('id')->on('consultation_durations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_duration_translations');
    }
};
