<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StatamicAuthTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('users', 'super')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('super')->default(false);
            });
        }

        if (! Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('avatar')->nullable();
            });
        }

        if (! Schema::hasColumn('users', 'preferences')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('preferences')->nullable();
            });
        }

        if (! Schema::hasColumn('users', 'last_login')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_login')->nullable();
            });
        }

        if (Schema::hasColumn('users', 'password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('password')->nullable()->change();
            });
        }

        Schema::create('role_user', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role_id');
        });

        Schema::create('group_user', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('group_id');
        });

        Schema::create('password_activation_tokens', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
     public function down()
     {
         if (Schema::hasColumn('users', 'super')) {
             Schema::table('users', function (Blueprint $table) {
                 $table->dropColumn('super');
             });
         }

         if (Schema::hasColumn('users', 'avatar')) {
             Schema::table('users', function (Blueprint $table) {
                 $table->dropColumn('avatar');
             });
         }

         if (Schema::hasColumn('users', 'preferences')) {
             Schema::table('users', function (Blueprint $table) {
                 $table->dropColumn('preferences');
             });
         }

         if (Schema::hasColumn('users', 'last_login')) {
             Schema::table('users', function (Blueprint $table) {
                 $table->dropColumn('last_login');
             });
         }

         if (Schema::hasColumn('users', 'password')) {
             Schema::table('users', function (Blueprint $table) {
                 $table->string('password')->nullable(false)->change();
             });
         }

         Schema::dropIfExists('role_user');
         Schema::dropIfExists('group_user');

         Schema::dropIfExists('password_activation_tokens');
     }
}
