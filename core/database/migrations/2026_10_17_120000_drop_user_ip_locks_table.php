<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('user_ip_locks');
    }

    public function down(): void
    {
        // IP lock was removed. The previous migration can recreate an empty table.
    }
};
