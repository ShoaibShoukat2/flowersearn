<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('official_channels')) {
            return;
        }

        Schema::create('official_channels', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('purpose')->nullable();
            $table->text('description')->nullable();
            $table->string('link', 500);
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        if (function_exists('seedOfficialChannelsFromLegacyLinks')) {
            seedOfficialChannelsFromLegacyLinks();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('official_channels');
    }
};
