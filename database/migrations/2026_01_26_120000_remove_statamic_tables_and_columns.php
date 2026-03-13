<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('role_user')) {
            Schema::dropIfExists('role_user');
        }

        if (Schema::hasTable('group_user')) {
            Schema::dropIfExists('group_user');
        }

        if (Schema::hasTable('password_activation_tokens')) {
            Schema::dropIfExists('password_activation_tokens');
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'super')) {
                    $table->dropColumn('super');
                }
                if (Schema::hasColumn('users', 'avatar')) {
                    $table->dropColumn('avatar');
                }
                if (Schema::hasColumn('users', 'preferences')) {
                    $table->dropColumn('preferences');
                }
                if (Schema::hasColumn('users', 'last_login')) {
                    $table->dropColumn('last_login');
                }
            });
        }
    }

    public function down(): void
    {
        // No rollback for Statamic cleanup.
    }
};
