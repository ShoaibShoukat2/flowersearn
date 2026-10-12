<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionLog;
use App\Models\Plan;
use App\Models\PlanUpgrade;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanUpgradeController extends Controller
{
    public function __construct()
    {
        ensurePlanUpgradesTable();
    }

    public function dashboard()
    {
        $pageTitle = 'Plan Upgrade';
        $today = Carbon::today();

        $approvedToday = PlanUpgrade::approved()->whereDate('reviewed_at', $today);

        $stats = [
            'today_users' => (clone $approvedToday)->count(),
            'today_received' => (clone $approvedToday)->sum('amount'),
            'pending' => PlanUpgrade::pending()->count(),
            'rejected' => PlanUpgrade::rejected()->count(),
        ];

        return view('admin.plan_upgrade.dashboard', compact('pageTitle', 'stats', 'today'));
    }

    public function pending(Request $request)
    {
        $pageTitle = 'Pending Plan Upgrades';
        $upgrades = $this->upgradeList(PlanUpgrade::PENDING, $request);

        return view('admin.plan_upgrade.list', compact('pageTitle', 'upgrades'));
    }

    public function approved(Request $request)
    {
        $pageTitle = 'Approved Plan Upgrades';
        $upgrades = $this->upgradeList(PlanUpgrade::APPROVED, $request);

        return view('admin.plan_upgrade.list', compact('pageTitle', 'upgrades'));
    }

    public function rejected(Request $request)
    {
        $pageTitle = 'Rejected Plan Upgrades';
        $upgrades = $this->upgradeList(PlanUpgrade::REJECTED, $request);

        return view('admin.plan_upgrade.list', compact('pageTitle', 'upgrades'));
    }

    public function details($id)
    {
        $upgrade = PlanUpgrade::with(['user.refBy', 'fromPlan', 'toPlan', 'gateway'])->findOrFail($id);
        $pageTitle = 'Plan Upgrade Details';
        $upliner = optional($upgrade->user)->refBy;
        $commissionPreview = round(((float) $upgrade->amount * 20) / 100, 8);

        return view('admin.plan_upgrade.detail', compact('pageTitle', 'upgrade', 'upliner', 'commissionPreview'));
    }

    public function approve(Request $request, $id)
    {
        $upgrade = PlanUpgrade::with(['fromPlan', 'toPlan', 'user'])
            ->where('id', $id)
            ->where('status', PlanUpgrade::PENDING)
            ->firstOrFail();
        $plan = Plan::find($upgrade->to_plan_id);

        if (!$plan) {
            $notify[] = ['error', 'The selected upgrade plan no longer exists.'];
            return back()->withNotify($notify);
        }

        $giveCommission = $request->boolean('give_commission');
        $commissionCredited = false;

        try {
            DB::transaction(function () use ($upgrade, $plan, $giveCommission, &$commissionCredited) {
                $locked = PlanUpgrade::where('id', $upgrade->id)
                    ->where('status', PlanUpgrade::PENDING)
                    ->lockForUpdate()
                    ->firstOrFail();

                $user = User::where('id', $locked->user_id)->lockForUpdate()->firstOrFail();

                // Existing plan assignment only. Upgrade referral commission is separate and optional.
                assignPlanToUser($user, $plan);

                $commissionAmount = 0;
                $uplinerId = null;

                if ($giveCommission && $user->ref_by) {
                    $upliner = User::where('id', $user->ref_by)->lockForUpdate()->first();
                    $commissionAmount = round(((float) $locked->amount * 20) / 100, 8);

                    if ($upliner && $commissionAmount > 0) {
                        $upliner->balance += $commissionAmount;
                        $upliner->save();

                        $fromName = optional($upgrade->fromPlan)->name ?: 'Previous';
                        $toName = $plan->name;
                        $trx = $locked->trx ?: getTrx();
                        $locked->trx = $trx;
                        $details = 'Package Upgrade Referral Commission from ' . $user->email . ' | ' . $fromName . ' → ' . $toName;

                        Transaction::insert([[
                            'user_id' => $upliner->id,
                            'amount' => $commissionAmount,
                            'post_balance' => $upliner->balance,
                            'charge' => 0,
                            'trx_type' => '+',
                            'details' => $details,
                            'remark' => 'package_upgrade_referral_commission',
                            'trx' => $trx,
                            'created_at' => now(),
                        ]]);

                        CommissionLog::insert([[
                            'to_id' => $upliner->id,
                            'from_id' => $user->id,
                            'level' => 1,
                            'amount' => $commissionAmount,
                            'details' => $details,
                            'type' => 'package_upgrade_referral_commission',
                            'trx' => $trx,
                            'created_at' => now(),
                        ]]);

                        $uplinerId = $upliner->id;
                        $commissionCredited = true;

                        try {
                            notify($upliner, 'REFERRAL_COMMISSION', [
                                'amount' => showAmount($commissionAmount),
                                'post_balance' => showAmount($upliner->balance),
                                'trx' => $trx,
                                'level' => 'Direct',
                                'type' => 'Package Upgrade Referral Commission',
                            ]);
                        } catch (\Throwable $e) {
                            \Log::error('Package upgrade commission notify failed: ' . $e->getMessage(), [
                                'upgrade_id' => $locked->id,
                                'upliner_id' => $upliner->id,
                            ]);
                        }
                    } else {
                        $commissionAmount = 0;
                    }
                }

                $locked->status = PlanUpgrade::APPROVED;
                $locked->admin_feedback = null;
                $locked->reviewed_at = now();
                $locked->commission_given = $uplinerId ? 1 : 0;
                $locked->commission_amount = $commissionAmount;
                $locked->upliner_id = $uplinerId;
                $locked->save();
            });
        } catch (\Throwable $e) {
            \Log::error('Plan upgrade approval failed: ' . $e->getMessage(), [
                'upgrade_id' => $upgrade->id,
            ]);
            $notify[] = ['error', 'Upgrade approval failed. Please try again.'];
            return back()->withNotify($notify);
        }

        if ($commissionCredited) {
            $notify[] = ['success', 'Plan upgrade approved. 20% package upgrade referral commission was credited to the direct upliner.'];
        } elseif ($giveCommission && !$upgrade->user->ref_by) {
            $notify[] = ['success', 'Plan upgrade approved. This user has no direct upliner, so no referral commission was credited.'];
        } else {
            $notify[] = ['success', 'Plan upgrade approved. The user is now on the new plan. No upliner commission was credited.'];
        }

        return back()->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'message' => 'required|string|max:255',
        ]);

        $upgrade = PlanUpgrade::where('id', $request->id)->where('status', PlanUpgrade::PENDING)->firstOrFail();
        $upgrade->status = PlanUpgrade::REJECTED;
        $upgrade->admin_feedback = $request->message;
        $upgrade->reviewed_at = now();
        $upgrade->save();

        $notify[] = ['success', 'Plan upgrade rejected. The user stays on the current plan.'];
        return back()->withNotify($notify);
    }

    protected function upgradeList(int $status, Request $request)
    {
        $query = PlanUpgrade::with(['user', 'fromPlan', 'toPlan', 'gateway'])
            ->where('status', $status);

        if ($request->filled('email')) {
            $email = trim($request->email);
            $query->whereHas('user', function ($user) use ($email) {
                $user->where('email', 'like', '%' . $email . '%')
                    ->orWhere('username', 'like', '%' . $email . '%')
                    ->orWhere('first_name', 'like', '%' . $email . '%')
                    ->orWhere('last_name', 'like', '%' . $email . '%');
            });
        }

        if ($request->filled('date')) {
            $column = $request->date_on === 'reviewed' ? 'reviewed_at' : 'created_at';
            $query->whereDate($column, Carbon::parse($request->date)->toDateString());
        }

        return $query->orderByDesc('id')->paginate(getPaginate())->withQueryString();
    }
}
