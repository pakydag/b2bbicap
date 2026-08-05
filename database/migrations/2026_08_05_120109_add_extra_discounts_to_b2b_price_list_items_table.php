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
        Schema::table('b2b_price_list_items', function (Blueprint $table) {
            $table->decimal('discount_2', 10, 2)->nullable()->after('discount_value');
            $table->decimal('discount_3', 10, 2)->nullable()->after('discount_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('b2b_price_list_items', function (Blueprint $table) {
            $table->dropColumn(['discount_2', 'discount_3']);
        });
    }
};
