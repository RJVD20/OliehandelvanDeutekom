<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->whereNotNull('specifications')
            ->orderBy('id')
            ->eachById(function (object $product) {
                if (mb_strlen($product->short_description ?? '') <= 500) {
                    return;
                }

                $lines = preg_split('/\r?\n/u', trim($product->short_description));
                $summaryIndexes = collect($lines)
                    ->filter(fn (string $line) => trim($line) !== '')
                    ->keys()
                    ->take(2);
                $summary = $summaryIndexes
                    ->map(fn (int $index) => trim($lines[$index]))
                    ->implode("\n\n");
                $remainder = trim(collect($lines)
                    ->reject(fn (string $line, int $index) => $summaryIndexes->contains($index))
                    ->implode("\n"));

                DB::table('products')->where('id', $product->id)->update([
                    'short_description' => $summary,
                    'description' => trim(implode("\n\n", array_filter([
                        $remainder,
                        $product->description,
                    ]))),
                ]);
            });
    }

    public function down(): void
    {
        // De oorspronkelijke tekst blijft inhoudelijk behouden; alleen de verdeling is gecorrigeerd.
    }
};
