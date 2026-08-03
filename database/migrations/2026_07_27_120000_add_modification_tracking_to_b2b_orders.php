<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b2b_orders', function (Blueprint $table) {
            $table->boolean('is_modified')->default(false)->after('status');
        });

        Schema::table('b2b_order_items', function (Blueprint $table) {
            $table->integer('original_quantity')->nullable()->after('quantity');
            $table->decimal('original_price', 10, 2)->nullable()->after('price');
            $table->boolean('is_modified')->default(false)->after('original_price');
        });
    }

    public function down(): void
    {
        Schema::table('b2b_orders', function (Blueprint $table) {
            $table->dropColumn('is_modified');
        });

        Schema::table('b2b_order_items', function (Blueprint $table) {
            $table->dropColumn(['original_quantity', 'original_price', 'is_modified']);
        });
    }
};
