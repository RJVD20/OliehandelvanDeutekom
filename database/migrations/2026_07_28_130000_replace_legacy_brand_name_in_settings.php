<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')
            ->select(['id', 'key', 'value'])
            ->where(function ($query) {
                $query->where('value', 'like', '%Oliehandel van Deutekom%')
                    ->orWhere('value', 'like', '%info@oliehandelvandeutekom.nl%')
                    ->orWhere('value', 'like', '%oliehandelvandeutekom.nl%');
            })
            ->orderBy('id')
            ->each(function ($setting) {
                $value = str_replace(
                    [
                        'Oliehandel van Deutekom',
                        'info@oliehandelvandeutekom.nl',
                        'oliehandelvandeutekom.nl',
                    ],
                    [
                        'Kachelvloeistof.nl',
                        'info@kachelvloeistof.nl',
                        'kachelvloeistof.nl',
                    ],
                    (string) $setting->value,
                );

                DB::table('settings')
                    ->where('id', $setting->id)
                    ->update(['value' => $value, 'updated_at' => now()]);

                Cache::forget('setting:' . $setting->key);
            });
    }

    public function down(): void
    {
        // Bewust niet teruggedraaid: CMS-inhoud kan na deze migratie handmatig zijn aangepast.
    }
};
