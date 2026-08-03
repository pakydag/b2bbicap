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
        Schema::table('b2b_products', function (Blueprint $table) {
            $table->string('code')->nullable()->index()->after('id');
            $table->json('characteristics')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('b2b_products', function (Blueprint $table) {
            $table->dropColumn(['code', 'characteristics']);
        });
    }
};
