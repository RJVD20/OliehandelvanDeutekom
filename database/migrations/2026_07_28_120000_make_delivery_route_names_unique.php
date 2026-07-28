<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $usedNames = [];

        DB::table('delivery_routes')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->get()
            ->each(function (object $route) use (&$usedNames): void {
                $originalName = $route->name;
                $candidate = $originalName;
                $suffix = 2;

                while (isset($usedNames[mb_strtolower($candidate)])) {
                    $candidate = "{$originalName} ({$suffix})";
                    $suffix++;
                }

                if ($candidate !== $originalName) {
                    DB::table('delivery_routes')
                        ->where('id', $route->id)
                        ->update(['name' => $candidate]);
                }

                $usedNames[mb_strtolower($candidate)] = true;
            });

        Schema::table('delivery_routes', function (Blueprint $table) {
            $table->unique('name', 'delivery_routes_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_routes', function (Blueprint $table) {
            $table->dropUnique('delivery_routes_name_unique');
        });
    }
};
