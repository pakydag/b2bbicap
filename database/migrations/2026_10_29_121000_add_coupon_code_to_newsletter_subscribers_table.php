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
        if (Schema::hasTable('newsletter_subscribers')) {
            Schema::table('newsletter_subscribers', function (Blueprint $table) {
                if (!Schema::hasColumn('newsletter_subscribers', 'coupon_code')) {
                    $table->string('coupon_code')->nullable()->after('locale');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('newsletter_subscribers')) {
            Schema::table('newsletter_subscribers', function (Blueprint $table) {
                if (Schema::hasColumn('newsletter_subscribers', 'coupon_code')) {
                    $table->dropColumn('coupon_code');
                }
            });
        }
    }
};
