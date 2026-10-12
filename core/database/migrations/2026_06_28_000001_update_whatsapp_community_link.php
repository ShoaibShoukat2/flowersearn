<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('general_settings', 'whatsapp_community_link')) {
            return;
        }

        DB::table('general_settings')->update([
            'whatsapp_community_link' => 'https://chat.whatsapp.com/F0W9Ay9wXSp6J4K6vS8Qu2',
        ]);

        Cache::forget('GeneralSetting');
    }

    public function down(): void
    {
        if (!Schema::hasColumn('general_settings', 'whatsapp_community_link')) {
            return;
        }

        DB::table('general_settings')->update([
            'whatsapp_community_link' => 'https://chat.whatsapp.com/BaGIEFk1YpQCkFzq06mbuM',
        ]);

        Cache::forget('GeneralSetting');
    }
};
