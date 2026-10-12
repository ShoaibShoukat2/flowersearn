<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\GatewayCurrency;
use App\Models\Plan;
use App\Models\PlanUpgrade;
use Illuminate\Http\Request;

class PlanUpgradeController extends Controller
{
    public function index()
    {
        ensurePlanUpgradesTable();

        $user = auth()->user()->load('plan');
        if (!(int) $user->plan_id || !$user->plan) {
            $notify[] = ['error', 'Choose and activate a package before upgrading.'];
            return redirect()->route('user.deposit')->withNotify($notify);
        }

        $pageTitle = 'Plan Upgrade';
        $current = $user->plan;
        $plans = Plan::where('status', 1)->orderBy('price')->get()->filter(function ($plan) use ($current) {
            return (float) $plan->price > (float) $current->price;
        })->values();

        $gateways = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1)->where('code', '>=', 1000);
        })->with('method')->orderBy('method_code')->get();

        $pending = PlanUpgrade::with(['fromPlan', 'toPlan'])
            ->where('user_id', $user->id)
            ->where('status', PlanUpgrade::PENDING)
            ->latest('id')
            ->first();

        $history = PlanUpgrade::with(['fromPlan', 'toPlan'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(10)
            ->get();

        return view($this->activeTemplate . 'user.plan_upgrade', compact(
            'pageTitle',
            'user',
            'current',
            'plans',
            'gateways',
            'pending',
            'history'
        ));
    }

    public function submit(Request $request)
    {
        ensurePlanUpgradesTable();

        $user = auth()->user()->load('plan');
        if (!(int) $user->plan_id || !$user->plan) {
            $notify[] = ['error', 'An active package is required before upgrading.'];
            return back()->withNotify($notify);
        }

        if (PlanUpgrade::where('user_id', $user->id)->where('status', PlanUpgrade::PENDING)->exists()) {
            $notify[] = ['error', 'You already have a plan upgrade waiting for approval.'];
            return back()->withNotify($notify);
        }

        $gateways = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1)->where('code', '>=', 1000);
        })->get();

        $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
            'sender_name' => 'required|string|max:100',
            'sender_number' => 'required|string|max:30',
            'screenshot' => 'required|file|mimes:jpg,jpeg,png,webp|max:8192',
            'method_code' => $gateways->count() ? 'required|integer' : 'nullable|integer',
        ], [
            'screenshot.required' => 'Please upload the payment screenshot.',
            'screenshot.mimes' => 'Payment screenshot must be a JPG, PNG, or WEBP image.',
            'screenshot.max' => 'Payment screenshot must be smaller than 8MB.',
        ]);

        $target = Plan::where('status', 1)->findOrFail($request->plan_id);
        $current = $user->plan;
        $remaining = round((float) $target->price - (float) $current->price, 2);

        if ($remaining <= 0 || (int) $target->id === (int) $current->id) {
            $notify[] = ['error', 'Please choose a higher package than your current plan.'];
            return back()->withNotify($notify);
        }

        $methodCode = null;
        if ($request->filled('method_code')) {
            $gateway = $gateways->first(function ($gate) use ($request) {
                return (string) $gate->method_code === (string) $request->method_code;
            });
            if (!$gateway) {
                $notify[] = ['error', 'Please select a valid payment method.'];
                return back()->withNotify($notify);
            }
            $methodCode = $gateway->method_code;
        }

        try {
            $directory = date('Y') . '/' . date('m') . '/' . date('d');
            $path = getFilePath('verify') . '/' . $directory;
            $filename = $directory . '/' . fileUploader($request->screenshot, $path);
        } catch (\Throwable $e) {
            \Log::error('Plan upgrade screenshot upload failed: ' . $e->getMessage());
            $notify[] = ['error', 'Payment screenshot could not be saved. Please use a smaller JPG or PNG and try again.'];
            return back()->withNotify($notify)->withInput();
        }

        $upgrade = PlanUpgrade::create([
            'user_id' => $user->id,
            'from_plan_id' => $current->id,
            'to_plan_id' => $target->id,
            'amount' => $remaining,
            'sender_name' => trim($request->sender_name),
            'sender_number' => trim($request->sender_number),
            'screenshot' => $filename,
            'method_code' => $methodCode,
            'trx' => getTrx(),
            'status' => PlanUpgrade::PENDING,
        ]);

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'Plan upgrade request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.plan.upgrade.pending');
        $adminNotification->save();

        $notify[] = ['success', 'Upgrade payment submitted. It is now pending approval.'];
        return redirect()->route('user.plan.upgrade.index')->withNotify($notify);
    }
}
