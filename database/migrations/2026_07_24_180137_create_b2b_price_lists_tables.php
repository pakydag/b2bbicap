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
        Schema::create('b2b_price_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('general_discount_percent', 5, 2)->default(0.00);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('b2b_price_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('b2b_price_list_id')->constrained('b2b_price_lists')->onDelete('cascade');
            $table->foreignId('b2b_product_id')->nullable()->constrained('b2b_products')->onDelete('cascade');
            $table->integer('min_quantity')->default(1);
            $table->integer('max_quantity')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed_price'])->default('percentage');
            $table->decimal('discount_value', 10, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::table('b2b_customers', function (Blueprint $table) {
            $table->foreignId('b2b_price_list_id')->nullable()->after('payment_condition_id')->constrained('b2b_price_lists')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('b2b_customers', function (Blueprint $table) {
            $table->dropForeign(['b2b_price_list_id']);
            $table->dropColumn('b2b_price_list_id');
        });

        Schema::dropIfExists('b2b_price_list_items');
        Schema::dropIfExists('b2b_price_lists');
    }
};
