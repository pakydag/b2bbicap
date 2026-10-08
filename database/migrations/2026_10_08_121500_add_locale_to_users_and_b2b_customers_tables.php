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
        if (!Schema::hasColumn('users', 'locale')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('locale', 10)->default('it')->after('role');
            });
        }

        if (!Schema::hasColumn('b2b_customers', 'locale')) {
            Schema::table('b2b_customers', function (Blueprint $table) {
                $table->string('locale', 10)->default('it')->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'locale')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('locale');
            });
        }

        if (Schema::hasColumn('b2b_customers', 'locale')) {
            Schema::table('b2b_customers', function (Blueprint $table) {
                $table->dropColumn('locale');
            });
        }
    }
};
