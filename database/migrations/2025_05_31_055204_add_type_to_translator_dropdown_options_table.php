<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('translator_dropdown_options', function (Blueprint $table) {
            $table->string('type')->after('translator_id')->nullable();;
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('translator_dropdown_options', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
