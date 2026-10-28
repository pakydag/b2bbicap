<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $setting = Setting::where('key', 'payment_contrassegno_fee')->first();
        if (!$setting || $setting->value === null || $setting->value === '') {
            Setting::updateOrCreate(
                ['key' => 'payment_contrassegno_fee'],
                ['value' => '10.00']
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep settings intact on rollback
    }
};
