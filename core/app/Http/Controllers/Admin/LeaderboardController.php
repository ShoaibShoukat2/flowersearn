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

        $users = $this->boardQuery()
            ->with('plan:id,name')
            ->where('users.plan_id', '>', 0);

        if ($request->filled('plan_id')) {
            $planId = (int) $request->plan_id;
            if ($plans->contains('id', $planId)) {
                $users->where('users.plan_id', $planId);
            }
        }

        $username = trim((string) $request->input('username', ''));
        if ($username !== '') {
            $users->where(function ($query) use ($username) {
                $like = '%' . $username . '%';
                $query->where('users.username', 'like', $like)
                    ->orWhere('users.first_name', 'like', $like)
                    ->orWhere('users.last_name', 'like', $like);
                if (Schema::hasColumn('users', 'firstname')) {
                    $query->orWhere('users.firstname', 'like', $like)
                        ->orWhere('users.lastname', 'like', $like);
                }
            });
        }

        $email = trim((string) $request->input('email', ''));
        if ($email !== '') {
            $users->where('users.email', 'like', '%' . $email . '%');
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

        $levelSummary = $this->levelSummary(clone $users);
        $levelOptions = range(max(array_keys($levelSummary) ?: [8]), 0);

        if ($request->filled('level') && $request->level !== '') {
            $level = (int) $request->level;
            if ($level >= 0 && $level <= 30) {
                $users->whereRaw($this->levelExpression() . ' = ?', [$level]);
            }
        }

        $sort = (string) $request->input('sort', '');
        if (!in_array($sort, ['level', 'earnings', 'team'], true)) {
            $sort = '';
        }

        if ($sort === 'earnings') {
            $users->orderByDesc('board_earnings')->orderByDesc('board_level')->orderByDesc('board_team')->orderBy('users.id');
        } elseif ($sort === 'team') {
            $users->orderByDesc('board_team')->orderByDesc('board_level')->orderByDesc('board_earnings')->orderBy('users.id');
        } else {
            $users->orderByDesc('board_level')->orderByDesc('board_active')->orderByDesc('board_earnings')->orderBy('users.id');
        }

        $users = $users->paginate(getPaginate())->withQueryString();

        return view('admin.leaderboard.index', compact(
            'pageTitle',
            'users',
            'plans',
            'levels',
            'levelSummary',
            'levelOptions',
            'sort',
            'username',
            'email'
        ));
    }

    public function show($id)
    {
        $user = User::with(['plan', 'refBy'])->findOrFail($id);
        $pageTitle = 'Leaderboard - ' . memberDisplayName($user);

        $activeTeam = successfulJoiningCount($user->id);
        $totalTeam = User::where('ref_by', $user->id)->count();
        $earnings = $this->earningsForUser($user->id);
        $displayLevel = max(levelFromJoinings($activeTeam), (int) $user->level);
        $upliner = $user->refBy;
        $approvalDeposit = Deposit::where('user_id', $user->id)->where('status', 1)->orderBy('id')->first();
        $paymentDeposit = $approvalDeposit;
        if (!$paymentDeposit) {
            $paymentDeposit = Deposit::where('user_id', $user->id)->where('status', '!=', 0)->orderByDesc('id')->first();
        }
        if ($paymentDeposit) {
            $paymentDeposit->load('gateway');
        }

        $displayName = memberDisplayName($user);
        $uplinerName = $upliner ? memberDisplayName($upliner) : null;

        return view('admin.leaderboard.show', compact(
            'pageTitle',
            'user',
            'displayName',
            'displayLevel',
            'activeTeam',
            'totalTeam',
            'earnings',
            'upliner',
            'uplinerName',
            'approvalDeposit',
            'paymentDeposit'
        ));
    }

    protected function boardQuery()
    {
        $teamSub = DB::table('users')
            ->select('ref_by', DB::raw('COUNT(*) as team_count'))
            ->where('ref_by', '>', 0)
            ->groupBy('ref_by');

        $activeSub = DB::table('users as team_members')
            ->join('deposits as team_deposits', function ($join) {
                $join->on('team_deposits.user_id', '=', 'team_members.id')
                    ->where('team_deposits.status', '=', 1);
            })
            ->select('team_members.ref_by', DB::raw('COUNT(DISTINCT team_members.id) as active_count'))
            ->where('team_members.ref_by', '>', 0)
            ->groupBy('team_members.ref_by');

        $earnSub = DB::table('transactions')
            ->select('user_id', DB::raw('SUM(amount) as earn_total'))
            ->where('trx_type', '+')
            ->whereIn('remark', ['referral_commission', 'package_upgrade_referral_commission', 'ptc_earn', 'flowers_bonus'])
            ->groupBy('user_id');

        $query = User::query()
            ->select('users.*')
            ->leftJoinSub($teamSub, 'team_stats', 'team_stats.ref_by', '=', 'users.id')
            ->leftJoinSub($activeSub, 'active_stats', 'active_stats.ref_by', '=', 'users.id')
            ->leftJoinSub($earnSub, 'earn_stats', 'earn_stats.user_id', '=', 'users.id');

        $earnings = 'COALESCE(earn_stats.earn_total, 0)';
        if (Schema::hasTable('ptc_earns')) {
            $flowerSub = DB::table('ptc_earns')
                ->select('user_id', DB::raw("COALESCE(SUM(CASE WHEN remark IN ('ptc_earn_sub','ptc_earn_subtract') THEN -ptc_amount ELSE ptc_amount END), 0) as net_amount"))
                ->groupBy('user_id');
            $query->leftJoinSub($flowerSub, 'flower_stats', 'flower_stats.user_id', '=', 'users.id');
            $earnings .= ' + COALESCE(flower_stats.net_amount, 0)';
        }

        return $query
            ->selectRaw('COALESCE(team_stats.team_count, 0) as board_team')
            ->selectRaw('COALESCE(active_stats.active_count, 0) as board_active')
            ->selectRaw('(' . $earnings . ') as board_earnings')
            ->selectRaw($this->levelExpression() . ' as board_level');
    }

    protected function levelExpression(): string
    {
        $levels = defaultJoiningLevels();
        krsort($levels);
        $sql = 'CASE';
        foreach ($levels as $level => $data) {
            $sql .= ' WHEN COALESCE(active_stats.active_count, 0) >= ' . (int) $data['referrals_required'] . ' THEN ' . (int) $level;
        }
        $sql .= ' ELSE 0 END';

        return 'GREATEST((' . $sql . '), COALESCE(users.level, 0))';
    }

    protected function levelSummary($query): array
    {
        $query->getQuery()->columns = null;
        $query->getQuery()->orders = null;
        $expression = $this->levelExpression();

        $rows = $query->toBase()
            ->selectRaw($expression . ' as summary_level, COUNT(*) as total')
            ->groupByRaw($expression)
            ->get();

        $counts = [];
        $highest = 8;
        foreach ($rows as $row) {
            $level = (int) $row->summary_level;
            $counts[$level] = (int) $row->total;
            if ($level > $highest) {
                $highest = $level;
            }
        }

        $summary = [];
        for ($level = 1; $level <= $highest; $level++) {
            $summary[$level] = $counts[$level] ?? 0;
        }
        if (!empty($counts[0])) {
            $summary[0] = $counts[0];
        }

        return $summary;
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
