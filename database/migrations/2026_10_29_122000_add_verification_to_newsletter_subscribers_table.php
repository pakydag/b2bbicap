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
                if (!Schema::hasColumn('newsletter_subscribers', 'verification_token')) {
                    $table->string('verification_token', 64)->nullable()->index()->after('coupon_code');
                }
                if (!Schema::hasColumn('newsletter_subscribers', 'confirmed')) {
                    $table->boolean('confirmed')->default(false)->after('verification_token');
                }
                if (!Schema::hasColumn('newsletter_subscribers', 'confirmed_at')) {
                    $table->timestamp('confirmed_at')->nullable()->after('confirmed');
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
                if (Schema::hasColumn('newsletter_subscribers', 'confirmed_at')) {
                    $table->dropColumn('confirmed_at');
                }
                if (Schema::hasColumn('newsletter_subscribers', 'confirmed')) {
                    $table->dropColumn('confirmed');
                }
                if (Schema::hasColumn('newsletter_subscribers', 'verification_token')) {
                    $table->dropColumn('verification_token');
                }
            });
        }
    }
};

