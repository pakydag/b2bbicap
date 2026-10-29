<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b2b_products', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        // Popola gli slug per tutti i prodotti già presenti nel DB
        $products = DB::table('b2b_products')->get(['id', 'name']);
        $existingSlugs = [];

        foreach ($products as $p) {
            $baseSlug = Str::slug($p->name);
            if (empty($baseSlug)) {
                $baseSlug = 'prodotto-' . $p->id;
            }
            $slug = $baseSlug;
            $counter = 1;
            while (in_array($slug, $existingSlugs)) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $existingSlugs[] = $slug;

            DB::table('b2b_products')
                ->where('id', $p->id)
                ->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('b2b_products', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};

