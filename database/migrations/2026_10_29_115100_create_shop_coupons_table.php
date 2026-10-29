<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('shop_coupons')) {
            Schema::create('shop_coupons', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name')->nullable();
                $table->enum('type', ['percentuale', 'fisso'])->default('percentuale');
                $table->decimal('valore', 10, 2)->default(0);
                $table->decimal('min_spesa', 10, 2)->default(0);
                $table->boolean('sempre_attivo')->default(true); // true = sempre attivo; false = si disattiva dopo l'utilizzo
                $table->boolean('attivo')->default(true);
                $table->unsignedInteger('utilizzi')->default(0);
                $table->timestamp('usato_il')->nullable();
                $table->timestamps();
            });

            // Inserisci coupon predefinito per la newsletter
            DB::table('shop_coupons')->insert([
                'code' => 'welcomebicapshop',
                'name' => 'Sconto Benvenuto Newsletter',
                'type' => 'percentuale',
                'valore' => 5.00,
                'min_spesa' => 100.00,
                'sempre_attivo' => true,
                'attivo' => true,
                'utilizzi' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_coupons');
    }
};
