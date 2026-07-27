<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('short_description')->nullable()->after('description');
            $table->json('specifications')->nullable()->after('short_description');
        });

        DB::table('products')
            ->whereNotNull('description')
            ->orderBy('id')
            ->eachById(function (object $product) {
                $parts = preg_split('/(?:\r?\n)\s*Specificaties\s*(?:\r?\n)/iu', $product->description, 2);

                if (count($parts) !== 2) {
                    return;
                }

                $contentLines = preg_split('/\r?\n/u', trim($parts[0]));
                $summaryIndexes = collect($contentLines)
                    ->filter(fn (string $line) => trim($line) !== '')
                    ->keys()
                    ->take(2);
                $shortDescription = $summaryIndexes
                    ->map(fn (int $index) => trim($contentLines[$index]))
                    ->implode("\n\n");
                $longDescription = trim(collect($contentLines)
                    ->reject(fn (string $line, int $index) => $summaryIndexes->contains($index))
                    ->implode("\n"));

                $specifications = collect(preg_split('/\r?\n/u', trim($parts[1])))
                    ->map(function (string $line) {
                        if (! str_contains($line, ':')) {
                            return null;
                        }

                        [$name, $value] = array_map('trim', explode(':', $line, 2));

                        return $name !== '' && $value !== ''
                            ? ['name' => $name, 'value' => $value]
                            : null;
                    })
                    ->filter()
                    ->values()
                    ->all();

                DB::table('products')->where('id', $product->id)->update([
                    'short_description' => $shortDescription ?: null,
                    'description' => $longDescription ?: null,
                    'specifications' => $specifications === [] ? null : json_encode($specifications),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['short_description', 'specifications']);
        });
    }
};
