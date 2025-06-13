<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('payment_active')->default(0)->after('status'); // or after any relevant column
            $table->decimal('service_fee', 10, 2)->default(0)->after('payment_active');
            $table->decimal('govt_fee', 10, 2)->default(0)->after('service_fee');
            $table->decimal('tax', 10, 2)->default(0)->after('govt_fee');
            $table->decimal('total_amount', 10, 2)->default(0)->after('tax');
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['payment_active', 'service_fee', 'govt_fee', 'tax','total_amount']);
        });
    }
};
