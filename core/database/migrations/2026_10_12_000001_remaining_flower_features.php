<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('deposits') && !Schema::hasColumn('deposits', 'channels_joined_at')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->timestamp('channels_joined_at')->nullable();
            });
        }

        if (Schema::hasTable('plan_upgrades')) {
            Schema::table('plan_upgrades', function (Blueprint $table) {
                if (!Schema::hasColumn('plan_upgrades', 'commission_given')) {
                    $table->boolean('commission_given')->default(false);
                }
                if (!Schema::hasColumn('plan_upgrades', 'commission_amount')) {
                    $table->decimal('commission_amount', 28, 8)->default(0);
                }
                if (!Schema::hasColumn('plan_upgrades', 'upliner_id')) {
                    $table->unsignedBigInteger('upliner_id')->nullable();
                }
            });
        }

        if (!Schema::hasTable('contact_admins')) {
            Schema::create('contact_admins', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('category');
                $table->text('instructions')->nullable();
                $table->string('contact_number')->nullable();
                $table->string('whatsapp_number')->nullable();
                $table->string('bot_link', 500)->nullable();
                $table->string('contact_type', 20)->default('whatsapp');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('deposits') && Schema::hasColumn('deposits', 'channels_joined_at')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->dropColumn('channels_joined_at');
            });
        }

        if (Schema::hasTable('plan_upgrades')) {
            Schema::table('plan_upgrades', function (Blueprint $table) {
                foreach (['commission_given', 'commission_amount', 'upliner_id'] as $column) {
                    if (Schema::hasColumn('plan_upgrades', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::dropIfExists('contact_admins');
    }
};
