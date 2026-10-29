<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            $table->string('corriere', 100)->nullable()->after('spedizione_nazione');
            $table->string('tracking_number', 100)->nullable()->after('corriere');
            $table->string('tracking_url', 500)->nullable()->after('tracking_number');
            $table->timestamp('spedito_il')->nullable()->after('tracking_url');
        });
    }

    public function down(): void
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            $table->dropColumn(['corriere', 'tracking_number', 'tracking_url', 'spedito_il']);
        });
    }
};

