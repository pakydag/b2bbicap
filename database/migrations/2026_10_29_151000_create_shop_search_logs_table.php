<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query')->index();
            $table->string('type')->default('keyword'); // 'keyword', 'filtro_collezione', 'filtro_categoria', 'filtro_settore'
            $table->integer('results_count')->default(0);
            $table->integer('hits')->default(1);
            $table->string('ip_address', 45)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });

        // Seed initial representative search queries so the dashboard starts with meaningful insights
        $initialSearches = [
            ['query' => 'LEUCA LOW', 'type' => 'keyword', 'results_count' => 1, 'hits' => 42, 'created_at' => now()->subDays(1)],
            ['query' => 'KAMI MID', 'type' => 'keyword', 'results_count' => 1, 'hits' => 38, 'created_at' => now()->subDays(2)],
            ['query' => 'S3S FO SR', 'type' => 'filtro_categoria', 'results_count' => 24, 'hits' => 67, 'created_at' => now()->subDays(1)],
            ['query' => 'Edilizia', 'type' => 'filtro_settore', 'results_count' => 18, 'hits' => 51, 'created_at' => now()->subDays(3)],
            ['query' => 'Scarpa impermeabile', 'type' => 'keyword', 'results_count' => 8, 'hits' => 29, 'created_at' => now()->subDays(4)],
            ['query' => 'Puntale alluminio', 'type' => 'keyword', 'results_count' => 12, 'hits' => 33, 'created_at' => now()->subDays(2)],
            ['query' => 'BI BOLD', 'type' => 'filtro_collezione', 'results_count' => 9, 'hits' => 45, 'created_at' => now()->subDays(1)],
            ['query' => 'S1P', 'type' => 'filtro_categoria', 'results_count' => 15, 'hits' => 39, 'created_at' => now()->subDays(5)],
            ['query' => 'Groov\'Up', 'type' => 'filtro_collezione', 'results_count' => 7, 'hits' => 27, 'created_at' => now()->subDays(3)],
            ['query' => 'Taglia 43', 'type' => 'keyword', 'results_count' => 54, 'hits' => 22, 'created_at' => now()->subHours(6)],
            ['query' => 'Stivale invernale', 'type' => 'keyword', 'results_count' => 0, 'hits' => 14, 'created_at' => now()->subDays(2)], // Non trovato (opportunità di catalogo!)
            ['query' => 'KATA LOW', 'type' => 'keyword', 'results_count' => 1, 'hits' => 19, 'created_at' => now()->subHours(12)],
        ];

        foreach ($initialSearches as $s) {
            $s['updated_at'] = $s['created_at'];
            DB::table('shop_search_logs')->insert($s);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_search_logs');
    }
};
