<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PtcView;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EarningsController extends Controller
{
    public function tasks(Request $request)
    {
        $pageTitle = 'Task Earnings';
        $plans = Plan::orderBy('price')->get();
        $date = $request->has('date') ? $request->date : Carbon::today()->toDateString();

        $base = PtcView::query()
            ->join('users', 'users.id', '=', 'ptc_views.user_id')
            ->leftJoin('plans', 'plans.id', '=', 'users.plan_id');

        $this->applyTaskFilters($base, $request, $date);

        $filteredAmount = (clone $base)->sum('ptc_views.amount');
        $filteredTasks = (clone $base)->count();

        $grouped = (clone $base)
            ->selectRaw('ptc_views.user_id, users.email, users.username, users.first_name, users.last_name, users.level, users.plan_id, plans.name as plan_name, DATE(ptc_views.view_date) as earn_date, ptc_views.amount as per_task, COUNT(*) as task_count, SUM(ptc_views.amount) as total_earning, MAX(ptc_views.view_date) as earned_at')
            ->groupBy(
                'ptc_views.user_id',
                'users.email',
                'users.username',
                'users.first_name',
                'users.last_name',
                'users.level',
                'users.plan_id',
                'plans.name',
                DB::raw('DATE(ptc_views.view_date)'),
                'ptc_views.amount'
            );

        $records = DB::query()
            ->fromSub($grouped, 'task_earnings')
            ->orderByDesc('earned_at')
            ->paginate(getPaginate())
            ->withQueryString();

        return view('admin.earnings.tasks', compact(
            'pageTitle',
            'plans',
            'records',
            'filteredAmount',
            'filteredTasks',
            'date'
        ));
    }

    public function manual(Request $request)
    {
        $pageTitle = 'Manual Balance Add';
        $date = $request->has('date') ? $request->date : Carbon::today()->toDateString();

        $query = Transaction::with('user')
            ->where('remark', 'balance_add')
            ->where('trx_type', '+');

        if ($date) {
            $query->whereDate('created_at', Carbon::parse($date)->toDateString());
        }

        if ($request->filled('email')) {
            $email = trim($request->email);
            $query->whereHas('user', function ($user) use ($email) {
                $user->where('email', 'like', '%' . $email . '%');
            });
        }

        $filteredAmount = (clone $query)->sum('amount');
        $records = $query->orderByDesc('id')->paginate(getPaginate())->withQueryString();

        return view('admin.earnings.manual', compact('pageTitle', 'records', 'filteredAmount', 'date'));
    }

    protected function applyTaskFilters($query, Request $request, $date): void
    {
        if ($date) {
            $query->whereDate('ptc_views.view_date', Carbon::parse($date)->toDateString());
        }

        if ($request->filled('plan_id')) {
            $query->where('users.plan_id', (int) $request->plan_id);
        }

        if ($request->has('level') && $request->level !== '' && $request->level !== null) {
            $query->where('users.level', (int) $request->level);
        }

        if ($request->filled('email')) {
            $email = trim($request->email);
            $query->where('users.email', 'like', '%' . $email . '%');
        }
    }
}
