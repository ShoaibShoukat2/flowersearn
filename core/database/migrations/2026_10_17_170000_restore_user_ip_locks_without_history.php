<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_ip_locks')) {
            Schema::create('user_ip_locks', function (Blueprint $table) {
                $table->id();
                $table->string('ip', 45)->unique();
                $table->unsignedBigInteger('user_id')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ip_lock_meta')) {
            Schema::create('ip_lock_meta', function (Blueprint $table) {
                $table->id();
                $table->dateTime('enforced_at');
                $table->unsignedBigInteger('grandfather_user_id')->default(0);
            });
        }

        // Every account that already exists keeps its login. The cutoff is the
        // highest user id at the moment this protection starts, so older
        // accounts are never blocked. Old login history is not imported.
        if (Schema::hasTable('ip_lock_meta') && DB::table('ip_lock_meta')->count() === 0) {
            DB::table('ip_lock_meta')->insert([
                'enforced_at' => now(),
                'grandfather_user_id' => (int) (Schema::hasTable('users') ? DB::table('users')->max('id') : 0),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_ip_locks');
        Schema::dropIfExists('ip_lock_meta');
    }
};
