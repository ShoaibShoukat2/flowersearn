<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\PtcView;
use App\Models\CommissionLog;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transaction(Request $request)
    {
        $pageTitle = 'Transaction Logs';

        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $remarkLabels = $this->transactionRemarkLabels();
        $categories = [
            'credit' => 'Balance Added / Credit',
            'debit' => 'Balance Deducted / Debit',
            'referral' => 'Referral Commission',
            'task' => 'Task Earnings',
            'withdraw' => 'Withdrawals',
        ];

        $transactions = Transaction::with('user')->orderBy('id', 'desc');

        if ($request->search) {
            $search = trim($request->search);
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('trx', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($user) use ($search) {
                      $user->where('username', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $category = (string) $request->input('category', '');
        if ($category === 'credit') {
            $transactions->where('trx_type', '+');
        } elseif ($category === 'debit') {
            $transactions->where('trx_type', '-');
        } elseif ($category === 'referral') {
            $transactions->whereIn('remark', ['referral_commission', 'package_upgrade_referral_commission']);
        } elseif ($category === 'task') {
            $transactions->whereIn('remark', ['ptc_earn', 'ptc_earn_subtract', 'ptc_earn_sub', 'flowers_bonus']);
        } elseif ($category === 'withdraw') {
            $transactions->whereIn('remark', ['withdraw', 'withdraw_reject']);
        }

        if (in_array($request->type, ['+', '-'], true)) {
            $transactions->where('trx_type', $request->type);
        }

        if ($request->filled('remark')) {
            $transactions->where('remark', $request->remark);
        }

        $range = parseAdminDateRange($request->date);
        if ($range) {
            $transactions->whereBetween('created_at', $range);
        }

        $transactions = $transactions->paginate(getPaginate())->withQueryString();
        $this->attachReferralSources($transactions);

        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'remarks', 'remarkLabels', 'categories'));
    }

    protected function transactionRemarkLabels(): array
    {
        return [
            'ptc_earn' => 'Task Earnings',
            'ptc_earn_subtract' => 'Task Adjustment',
            'ptc_earn_sub' => 'Task Adjustment',
            'flowers_bonus' => 'Flowers Bonus',
            'balance_add' => 'Balance Added',
            'balance_subtract' => 'Balance Deducted',
            'balance_received' => 'Balance Received',
            'balance_transfer' => 'Balance Transfer',
            'referral_commission' => 'Referral Commission',
            'package_upgrade_referral_commission' => 'Package Upgrade Referral Commission',
            'deposit' => 'Deposit',
            'withdraw' => 'Withdrawal',
            'withdraw_reject' => 'Withdrawal Rejected',
            'subscribe_plan' => 'Plan Purchase',
            'registration_bonus' => 'Registration Bonus',
        ];
    }

    protected function attachReferralSources($transactions)
    {
        $rows = $transactions->getCollection();
        $targets = $rows->filter(function ($trx) {
            return in_array($trx->remark, ['referral_commission', 'package_upgrade_referral_commission'], true);
        });

        if ($targets->isEmpty()) {
            return;
        }

        $trxCodes = $targets->pluck('trx')->filter()->unique()->values();
        $logs = collect();
        if ($trxCodes->isNotEmpty()) {
            $logs = CommissionLog::with('userFrom')
                ->whereIn('trx', $trxCodes)
                ->get()
                ->groupBy(function ($log) {
                    return $log->trx . '|' . $log->to_id;
                });
        }

        $missingTokens = [];
        foreach ($targets as $trx) {
            $log = optional($logs->get($trx->trx . '|' . $trx->user_id))->first();
            if ($log && $log->userFrom) {
                continue;
            }
            if (preg_match('/from\s+([^\s|]+)/i', (string) $trx->details, $match)) {
                $missingTokens[$trx->id] = rtrim($match[1], '.,');
            }
        }

        $usersByToken = [];
        if ($missingTokens) {
            $tokens = array_values(array_unique($missingTokens));
            $found = User::query()
                ->where(function ($query) use ($tokens) {
                    $query->whereIn('email', $tokens)->orWhereIn('username', $tokens);
                })
                ->get(['id', 'email', 'username']);
            foreach ($found as $user) {
                $usersByToken[strtolower($user->email)] = $user;
                $usersByToken[strtolower($user->username)] = $user;
            }
        }

        $rows->transform(function ($trx) use ($logs, $missingTokens, $usersByToken) {
            if (!in_array($trx->remark, ['referral_commission', 'package_upgrade_referral_commission'], true)) {
                return $trx;
            }

            $group = $logs->get($trx->trx . '|' . $trx->user_id);
            $log = $group ? $group->first() : null;
            $from = $log ? $log->userFrom : null;
            $label = null;
            if (!$from && isset($missingTokens[$trx->id])) {
                $label = $missingTokens[$trx->id];
                $from = $usersByToken[strtolower($label)] ?? null;
            }

            $upgrade = null;
            if ($trx->remark === 'package_upgrade_referral_commission') {
                $parts = explode(' | ', (string) $trx->details, 2);
                $upgrade = isset($parts[1]) ? trim($parts[1]) : null;
            }

            $trx->setAttribute('referral_source', $from);
            $trx->setAttribute('referral_from_label', $from ? null : $label);
            $trx->setAttribute('referral_upgrade', $upgrade);

            return $trx;
        });

        $transactions->setCollection($rows);
    }

    public function loginHistory(Request $request)
    {
        $pageTitle = 'User Login History';
        $loginLogs = UserLogin::with('user')->orderBy('id', 'desc');
        
        if ($request->search) {
            $search = $request->search;
            $loginLogs = $loginLogs->whereHas('user', function ($user) use ($search) {
                $user->where('username', 'like', "%{$search}%");
            });
        }
        
        $loginLogs = $loginLogs->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip)
    {
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = UserLogin::where('user_ip', $ip)->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs', 'ip'));
    }

    public function notificationHistory(Request $request)
    {
        $pageTitle = 'Notification History';
        $logs = NotificationLog::with('user')->orderBy('id', 'desc');
        
        if ($request->search) {
            $search = $request->search;
            $logs = $logs->whereHas('user', function ($user) use ($search) {
                $user->where('username', 'like', "%{$search}%");
            });
        }
        
        $logs = $logs->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs'));
    }

    public function emailDetails($id)
    {
        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle', 'email'));
    }

    public function ptcview(Request $request)
    {
        $pageTitle = 'PTC View Logs';
        $logs = PtcView::with('user', 'ptc')->orderBy('id', 'desc');

        if ($request->search) {
            $logs = $logs->whereHas('user', function ($user) use ($request) {
                $user->where('username', 'like', "%{$request->search}%");
            });
        }

        $logs = $logs->paginate(getPaginate());

        return view('admin.reports.ptc_views', compact('pageTitle', 'logs'));
    }

    public function commissions(Request $request)
    {
        $pageTitle = 'Referral Commissions';
        $logs = CommissionLog::with('user', 'fromUser')->orderBy('id', 'desc');

        if ($request->search) {
            $logs = $logs->whereHas('user', function ($user) use ($request) {
                $user->where('username', 'like', "%{$request->search}%");
            });
        }

        $logs = $logs->paginate(getPaginate());

        return view('admin.reports.commissions', compact('pageTitle', 'logs'));
    }
}
