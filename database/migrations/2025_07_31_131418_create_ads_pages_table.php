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
        Schema::create('ads_pages', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset = 'utf8mb4';
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        DB::table('ads_pages')->insert([
            ['name' => 'Home', 'slug' => 'home', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Contact Us', 'slug' => 'contact', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Services', 'slug' => 'services', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_pages');
    }
};
