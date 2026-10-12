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

        $transactions = Transaction::with('user')->orderBy('id', 'desc');

        if ($request->search) {
            $search = $request->search;
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('trx', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($user) use ($search) {
                      $user->where('username', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->type) {
            $transactions = $transactions->where('trx_type', $request->type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark', $request->remark);
        }

        $transactions = $transactions->paginate(getPaginate());
        $this->attachReferralSources($transactions);

        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'remarks'));
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
