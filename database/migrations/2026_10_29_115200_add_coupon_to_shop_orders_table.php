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
        if (Schema::hasTable('shop_orders')) {
            Schema::table('shop_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('shop_orders', 'coupon_codice')) {
                    $table->string('coupon_codice')->nullable()->after('note_admin');
                }
                if (!Schema::hasColumn('shop_orders', 'totale_sconto')) {
                    $table->decimal('totale_sconto', 10, 2)->default(0)->after('coupon_codice');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('shop_orders')) {
            Schema::table('shop_orders', function (Blueprint $table) {
                if (Schema::hasColumn('shop_orders', 'totale_sconto')) {
                    $table->dropColumn('totale_sconto');
                }
                if (Schema::hasColumn('shop_orders', 'coupon_codice')) {
                    $table->dropColumn('coupon_codice');
                }
            });
        }
    }
};

