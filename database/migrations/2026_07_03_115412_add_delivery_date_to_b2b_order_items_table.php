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
        Schema::table('b2b_order_items', function (Blueprint $table) {
            $table->string('delivery_date')->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('b2b_order_items', function (Blueprint $table) {
            $table->dropColumn('delivery_date');
        });
    }
};
