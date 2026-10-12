<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class RewardPageSetting extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'show_levels' => 'boolean',
        'show_on_dashboard' => 'boolean',
    ];

    public static function defaults(): array
    {
        return [
            'page_title' => 'Rewards & Levels',
            'page_subtitle' => 'Track your progress and unlock amazing rewards',
            'tips_title' => 'How to Level Up',
            'tips_content' => 'Level up by completing successful joinings through your referral link. Share your link with family, friends, or other people on social media. When someone joins through your link and completes the required process, it will count toward your level progress.',
            'show_levels' => true,
            'show_on_dashboard' => true,
        ];
    }

    public static function current(): self
    {
        if (!Schema::hasTable((new static)->getTable())) {
            return new static(static::defaults());
        }

        $settings = static::query()->firstOrCreate([], static::defaults());
        $guide = static::defaults()['tips_content'];
        $oldGuide = 'Invite friends using your referral link';
        if (trim((string) $settings->tips_content) === '' || str_contains((string) $settings->tips_content, $oldGuide)) {
            $settings->tips_title = static::defaults()['tips_title'];
            $settings->tips_content = $guide;
            $settings->save();
        }

        return $settings;
    }
}
