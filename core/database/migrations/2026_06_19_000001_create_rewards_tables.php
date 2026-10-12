<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reward_page_settings')) {
            Schema::create('reward_page_settings', function (Blueprint $table) {
                $table->id();
                $table->string('page_title')->default('Rewards & Levels');
                $table->string('page_subtitle')->nullable();
                $table->string('tips_title')->nullable();
                $table->text('tips_content')->nullable();
                $table->boolean('show_levels')->default(true);
                $table->boolean('show_on_dashboard')->default(true);
                $table->timestamps();
            });

            DB::table('reward_page_settings')->insert([
                'page_title' => 'Rewards & Levels',
                'page_subtitle' => 'Track your progress and unlock amazing rewards',
                'tips_title' => 'How to Level Up',
                'tips_content' => "• Invite friends using your referral link\n• Ensure your referrals make successful deposits\n• Complete daily tasks to earn more\n• Join our official channel for bonus updates",
                'show_levels' => true,
                'show_on_dashboard' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!Schema::hasTable('reward_items')) {
            Schema::create('reward_items', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('badge')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('status')->default(true);
                $table->timestamps();
            });

            DB::table('reward_items')->insert([
                [
                    'title' => 'First Task Bonus',
                    'description' => 'Get Rs 50 extra on your very first completed task.',
                    'icon' => '🎁',
                    'badge' => 'Rs 50',
                    'sort_order' => 1,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Daily Task Reward',
                    'description' => 'Earn Rs 5 on every task you complete each day.',
                    'icon' => '✅',
                    'badge' => 'Rs 5',
                    'sort_order' => 2,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Referral Rewards',
                    'description' => 'Invite active members and unlock higher levels with more benefits.',
                    'icon' => '👥',
                    'badge' => 'Level Up',
                    'sort_order' => 3,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_items');
        Schema::dropIfExists('reward_page_settings');
    }
};
