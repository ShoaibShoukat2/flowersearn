<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('user_ip_locks');
        Schema::dropIfExists('ip_lock_meta');
    }

    public function down(): void
    {
        // IP lock was removed.
    }
};
