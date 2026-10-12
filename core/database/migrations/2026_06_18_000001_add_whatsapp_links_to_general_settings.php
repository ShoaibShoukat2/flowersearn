<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('general_settings', 'whatsapp_community_link')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->string('whatsapp_community_link', 500)->nullable()->after('secondary_color');
            });
        }

        if (!Schema::hasColumn('general_settings', 'whatsapp_channel_link')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->string('whatsapp_channel_link', 500)->nullable()->after('whatsapp_community_link');
            });
        }

        if (Schema::hasColumn('general_settings', 'whatsapp_community_link')) {
            \DB::table('general_settings')->whereNull('whatsapp_community_link')->update([
                'whatsapp_community_link' => 'https://chat.whatsapp.com/F0W9Ay9wXSp6J4K6vS8Qu2',
            ]);
        }

        if (Schema::hasColumn('general_settings', 'whatsapp_channel_link')) {
            \DB::table('general_settings')->whereNull('whatsapp_channel_link')->update([
                'whatsapp_channel_link' => 'https://whatsapp.com/channel/0029VbD43xPDjiOXXUicht3V',
            ]);
        }

        \Illuminate\Support\Facades\Cache::forget('GeneralSetting');
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_community_link', 'whatsapp_channel_link']);
        });
    }
};
