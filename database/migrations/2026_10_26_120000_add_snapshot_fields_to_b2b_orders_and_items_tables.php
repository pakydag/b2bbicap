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
        Schema::table('b2b_orders', function (Blueprint $table) {
            $table->string('customer_business_name')->nullable()->after('b2b_customer_id');
            $table->string('customer_code')->nullable()->after('customer_business_name');
            $table->string('customer_vat_number')->nullable()->after('customer_code');
            $table->string('customer_contact_name')->nullable()->after('customer_vat_number');
            $table->string('customer_phone')->nullable()->after('customer_contact_name');
            $table->string('customer_email')->nullable()->after('customer_phone');
            $table->string('customer_porto')->nullable()->after('customer_email');
            $table->string('agent_name')->nullable()->after('agent_id');
            $table->string('price_list_name')->nullable()->after('customer_porto');
            $table->json('price_list_snapshot')->nullable()->after('price_list_name');
        });

        Schema::table('b2b_order_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('b2b_product_id');
            $table->string('product_code')->nullable()->after('product_name');
            $table->string('brand_name')->nullable()->after('product_code');
            $table->string('variant_size')->nullable()->after('b2b_product_variant_id');
            $table->string('variant_color')->nullable()->after('variant_size');
            $table->decimal('base_price', 10, 2)->nullable()->after('variant_color');
            $table->string('discount_rule_summary')->nullable()->after('base_price');
            $table->string('discount_type')->nullable()->after('discount_rule_summary');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
        });

        // Make product and variant IDs nullable with nullOnDelete to preserve orders if products are removed
        try {
            Schema::table('b2b_order_items', function (Blueprint $table) {
                $table->dropForeign(['b2b_product_id']);
                $table->dropForeign(['b2b_product_variant_id']);
            });

            Schema::table('b2b_order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('b2b_product_id')->nullable()->change();
                $table->unsignedBigInteger('b2b_product_variant_id')->nullable()->change();
                $table->foreign('b2b_product_id')->references('id')->on('b2b_products')->nullOnDelete();
                $table->foreign('b2b_product_variant_id')->references('id')->on('b2b_product_variants')->nullOnDelete();
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Could not alter FK constraints on b2b_order_items: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('b2b_order_items', function (Blueprint $table) {
            $table->dropColumn([
                'product_name',
                'product_code',
                'brand_name',
                'variant_size',
                'variant_color',
                'base_price',
                'discount_rule_summary',
                'discount_type',
                'discount_value',
            ]);
        });

        Schema::table('b2b_orders', function (Blueprint $table) {
            $table->dropColumn([
                'customer_business_name',
                'customer_code',
                'customer_vat_number',
                'customer_contact_name',
                'customer_phone',
                'customer_email',
                'customer_porto',
                'agent_name',
                'price_list_name',
                'price_list_snapshot',
            ]);
        });
    }
};
