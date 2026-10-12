<?php

namespace App\Lib;

use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TaskLock
{
    public const FREE_DAYS = 15;
    public const WINDOW_DAYS = 7;

    public static function isLocked($user): bool
    {
        return (bool) (self::status($user)['locked'] ?? false);
    }

    public static function status($user): array
    {
        $unlocked = [
            'locked'           => false,
            'phase'            => 'unlocked',
            'approved_at'      => null,
            'free_until'       => null,
            'deadline'         => null,
            'last_joining_at'  => null,
            'referral_link'    => null,
        ];

        if (!$user || !$user->plan_id) {
            return $unlocked;
        }

        $unlocked['referral_link'] = url('/ref/' . ($user->ref ?? ''));

        $approvedAt = self::planApprovedAt($user);
        if (!$approvedAt) {
            return $unlocked;
        }

        $now = Carbon::now();
        $freeUntil = $approvedAt->copy()->addDays(self::FREE_DAYS);

        $base = [
            'locked'          => false,
            'approved_at'     => $approvedAt,
            'free_until'      => $freeUntil,
            'deadline'        => null,
            'last_joining_at' => null,
            'referral_link'   => $unlocked['referral_link'],
        ];

        if ($now->lt($freeUntil)) {
            return array_merge($base, [
                'phase'    => 'free',
                'deadline' => $freeUntil,
            ]);
        }

        $lastJoiningAfterFree = self::latestJoiningAfter($user, $freeUntil);
        $windowStart = $lastJoiningAfterFree
            ? $lastJoiningAfterFree->copy()
            : $freeUntil->copy();
        $deadline = $windowStart->copy()->addDays(self::WINDOW_DAYS);

        $base['deadline'] = $deadline;
        $base['last_joining_at'] = $lastJoiningAfterFree;

        if ($now->gte($deadline)) {
            return array_merge($base, [
                'locked' => true,
                'phase'  => 'locked',
            ]);
        }

        return array_merge($base, [
            'phase' => $lastJoiningAfterFree ? 'unlocked' : 'joining_window',
        ]);
    }

    public static function planApprovedAt($user): ?Carbon
    {
        $deposit = Deposit::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('updated_at')
            ->first(['updated_at']);

        if ($deposit && $deposit->updated_at) {
            return Carbon::parse($deposit->updated_at);
        }

        $subscribe = Transaction::where('user_id', $user->id)
            ->where('remark', 'subscribe_plan')
            ->orderBy('created_at')
            ->first(['created_at']);

        if ($subscribe && $subscribe->created_at) {
            return Carbon::parse($subscribe->created_at);
        }

        return null;
    }

    public static function joiningDates($user): Collection
    {
        $referredIds = User::where('ref_by', $user->id)->pluck('id');
        if ($referredIds->isEmpty()) {
            return collect();
        }

        return Deposit::where('status', 1)
            ->whereIn('user_id', $referredIds)
            ->selectRaw('user_id, MIN(updated_at) as joined_at')
            ->groupBy('user_id')
            ->pluck('joined_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date));
    }

    protected static function latestJoiningAfter($user, Carbon $after): ?Carbon
    {
        $dates = self::joiningDates($user)
            ->filter(fn (Carbon $date) => $date->gte($after));

        if ($dates->isEmpty()) {
            return null;
        }

        return $dates->reduce(function ($latest, Carbon $date) {
            return (!$latest || $date->gt($latest)) ? $date : $latest;
        });
    }
}
