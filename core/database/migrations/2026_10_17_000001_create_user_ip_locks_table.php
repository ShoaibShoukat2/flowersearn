<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

    }

    public function down(): void
    {
        Schema::dropIfExists('user_ip_locks');
    }
};
