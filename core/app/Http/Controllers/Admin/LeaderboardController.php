<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'Leaderboard';
        $plans = Plan::orderBy('name')->get(['id', 'name']);
        $levels = defaultJoiningLevels();

        $activeSql = $this->activeTeamSql();
        $teamSql = '(SELECT COUNT(*) FROM users AS team_users WHERE team_users.ref_by = users.id)';
        $levelSql = $this->levelSql($activeSql);
        $earnSql = $this->earningsSql();

        $users = User::query()
            ->select('users.*')
            ->selectRaw($teamSql . ' as board_team')
            ->selectRaw($activeSql . ' as board_active')
            ->selectRaw($earnSql . ' as board_earnings')
            ->selectRaw($levelSql . ' as board_level')
            ->with('plan:id,name')
            ->where('users.plan_id', '>', 0);

        if ($request->filled('plan_id')) {
            $planId = (int) $request->plan_id;
            if ($plans->contains('id', $planId)) {
                $users->where('users.plan_id', $planId);
            }
        }

        if ($request->filled('level') && $request->level !== '') {
            $level = (int) $request->level;
            if ($level >= 0 && $level <= 8) {
                $users->whereRaw($levelSql . ' = ?', [$level]);
            }
        }

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $users->where(function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where('users.email', 'like', $like)
                    ->orWhere('users.username', 'like', $like)
                    ->orWhere('users.first_name', 'like', $like)
                    ->orWhere('users.last_name', 'like', $like);
                if (Schema::hasColumn('users', 'firstname')) {
                    $query->orWhere('users.firstname', 'like', $like)
                        ->orWhere('users.lastname', 'like', $like);
                }
            });
        }

        $range = $this->dateRange($request);
        if ($range) {
            [$start, $end] = $range;
            if ($request->input('date_basis') === 'registration') {
                $users->whereBetween('users.created_at', [$start, $end]);
            } else {
                $users->whereExists(function ($sub) use ($start, $end) {
                    $sub->select(DB::raw(1))
                        ->from('deposits as approval_deposits')
                        ->whereColumn('approval_deposits.user_id', 'users.id')
                        ->where('approval_deposits.status', 1)
                        ->whereRaw('approval_deposits.id = (SELECT MIN(first_deposit.id) FROM deposits AS first_deposit WHERE first_deposit.user_id = users.id AND first_deposit.status = 1)')
                        ->whereBetween('approval_deposits.updated_at', [$start, $end]);
                });
            }
        }

        $sort = $request->input('sort') === 'earnings' ? 'earnings' : 'level';
        if ($sort === 'earnings') {
            $users->orderByDesc('board_earnings')->orderByDesc('board_level')->orderByDesc('board_active')->orderBy('users.id');
        } else {
            $users->orderByDesc('board_level')->orderByDesc('board_active')->orderByDesc('board_earnings')->orderBy('users.id');
        }

        $users = $users->paginate(getPaginate())->withQueryString();

        foreach ($users as $user) {
            if ((int) $user->level === (int) $user->board_level) {
                continue;
            }

            $newLevel = (int) $user->board_level;
            $payload = ['level' => $newLevel];
            $plan = $user->plan ?: Plan::find($user->plan_id);
            if ($plan) {
                $payload['daily_limit'] = dailyLimitForLevel($plan, $newLevel);
            }
            User::where('id', $user->id)->update($payload);
            $user->level = $newLevel;
        }

        return view('admin.leaderboard.index', compact('pageTitle', 'users', 'plans', 'levels', 'sort', 'search'));
    }

    public function show($id)
    {
        $user = User::with(['plan', 'refBy'])->findOrFail($id);
        $user = syncUserLevelAndLimit($user);
        $pageTitle = 'Leaderboard - ' . memberDisplayName($user);

        $activeTeam = successfulJoiningCount($user->id);
        $totalTeam = User::where('ref_by', $user->id)->count();
        $earnings = $this->earningsForUser($user->id);
        $upliner = $user->refBy;
        $approvalDeposit = Deposit::where('user_id', $user->id)->where('status', 1)->orderBy('id')->first();
        $paymentDeposit = $approvalDeposit;
        if (!$paymentDeposit) {
            $paymentDeposit = Deposit::where('user_id', $user->id)->where('status', '!=', 0)->orderByDesc('id')->first();
        }

        $displayName = memberDisplayName($user);
        $uplinerName = $upliner ? memberDisplayName($upliner) : null;

        return view('admin.leaderboard.show', compact(
            'pageTitle',
            'user',
            'displayName',
            'activeTeam',
            'totalTeam',
            'earnings',
            'upliner',
            'uplinerName',
            'approvalDeposit',
            'paymentDeposit'
        ));
    }

    protected function dateRange(Request $request): ?array
    {
        $from = trim((string) $request->input('date_from', ''));
        $to = trim((string) $request->input('date_to', ''));
        if ($from === '' && $to === '') {
            return null;
        }

        try {
            $start = $from !== '' ? Carbon::parse($from)->startOfDay() : Carbon::parse($to)->startOfDay();
            $end = $to !== '' ? Carbon::parse($to)->endOfDay() : Carbon::parse($from)->endOfDay();
        } catch (\Throwable $e) {
            return null;
        }

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }

    protected function activeTeamSql(): string
    {
        return '(SELECT COUNT(DISTINCT active_users.id) FROM users AS active_users INNER JOIN deposits AS active_deposits ON active_deposits.user_id = active_users.id AND active_deposits.status = 1 WHERE active_users.ref_by = users.id)';
    }

    protected function levelSql(string $activeSql): string
    {
        $levels = defaultJoiningLevels();
        krsort($levels);
        $sql = 'CASE';
        foreach ($levels as $level => $data) {
            $sql .= ' WHEN ' . $activeSql . ' >= ' . (int) $data['referrals_required'] . ' THEN ' . (int) $level;
        }
        $sql .= ' ELSE 0 END';

        return $sql;
    }

    protected function earningsSql(): string
    {
        $remarks = "'referral_commission','package_upgrade_referral_commission','ptc_earn','flowers_bonus'";
        $sql = '(COALESCE((SELECT SUM(earn_trx.amount) FROM transactions AS earn_trx WHERE earn_trx.user_id = users.id AND earn_trx.trx_type = "+" AND earn_trx.remark IN (' . $remarks . ')), 0)';
        if (Schema::hasTable('ptc_earns')) {
            $sql .= ' + COALESCE((SELECT SUM(CASE WHEN flower_earn.remark IN (\'ptc_earn_sub\',\'ptc_earn_subtract\') THEN -flower_earn.ptc_amount ELSE flower_earn.ptc_amount END) FROM ptc_earns AS flower_earn WHERE flower_earn.user_id = users.id), 0)';
        }
        $sql .= ')';

        return $sql;
    }

    protected function earningsForUser(int $userId): float
    {
        $main = (float) DB::table('transactions')
            ->where('user_id', $userId)
            ->where('trx_type', '+')
            ->whereIn('remark', ['referral_commission', 'package_upgrade_referral_commission', 'ptc_earn', 'flowers_bonus'])
            ->sum('amount');

        $flowers = 0;
        if (Schema::hasTable('ptc_earns')) {
            $flowersRow = DB::table('ptc_earns')
                ->where('user_id', $userId)
                ->selectRaw('COALESCE(SUM(CASE WHEN remark IN (\'ptc_earn_sub\',\'ptc_earn_subtract\') THEN -ptc_amount ELSE ptc_amount END), 0) as net_amount')
                ->first();
            $flowers = (float) ($flowersRow->net_amount ?? 0);
        }

        return $main + $flowers;
    }
}
