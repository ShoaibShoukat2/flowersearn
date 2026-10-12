<?php

namespace App\Http\Controllers\Admin;

use App\Models\Deposit;
use App\Models\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    public function pending()
    {
    
        $pageTitle = 'Pending Deposits';
        $deposits = $this->depositData('pending');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }


    public function approved()
    {
        $pageTitle = 'Approved Deposits';
        $deposits = $this->depositData('approved');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function todayReport(Request $request)
    {
        $pageTitle = 'Today Approved Users';
        $reportDate = $request->date ? Carbon::parse($request->date) : Carbon::today();
        $plans = Plan::orderBy('price')->get();
        $selectedPlan = $request->filled('plan_id')
            ? $plans->firstWhere('id', (int) $request->plan_id)
            : null;

        $dayColumns = ['id', 'amount'];
        if (\Illuminate\Support\Facades\Schema::hasColumn('deposits', 'plan_id')) {
            $dayColumns[] = 'plan_id';
        }
        $dayDeposits = Deposit::approved()
            ->whereDate('updated_at', $reportDate)
            ->get($dayColumns);

        $planSummary = $plans->map(function ($plan) use ($dayDeposits) {
            $matched = $dayDeposits->filter(function ($deposit) use ($plan) {
                return depositMatchesPlan($deposit, $plan);
            });

            return (object) [
                'plan' => $plan,
                'count' => $matched->count(),
                'amount' => $matched->sum('amount'),
            ];
        });

        $filtered = Deposit::approved()
            ->with(['user.plan', 'gateway'])
            ->whereDate('updated_at', $reportDate);

        if ($selectedPlan) {
            constrainDepositToPlan($filtered, $selectedPlan);
        }

        if ($request->filled('email')) {
            $email = trim($request->email);
            $filtered->whereHas('user', function ($user) use ($email) {
                $user->where('email', 'like', '%' . $email . '%');
            });
        }

        $summary = [
            'received_amount' => (clone $filtered)->sum('amount'),
            'approved_count'  => (clone $filtered)->count(),
            'total_charge'    => (clone $filtered)->sum('charge'),
        ];

        $deposits = $filtered->orderByDesc('updated_at')->paginate(getPaginate())->withQueryString();

        return view('admin.deposit.today_report', compact(
            'pageTitle',
            'deposits',
            'summary',
            'reportDate',
            'plans',
            'planSummary',
            'selectedPlan'
        ));
    }

    public function successful()
    {
        $pageTitle = 'Successful Deposits';
        $deposits = $this->depositData('successful');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function rejected(Request $request)
    {
        $pageTitle = 'Rejected Users';
        $plans = Plan::orderBy('price')->get();
        $selectedPlan = $request->filled('plan_id')
            ? $plans->firstWhere('id', (int) $request->plan_id)
            : null;

        $deposits = Deposit::rejected()->with(['user.plan', 'gateway']);

        $search = trim((string) $request->input('search', ''));
        if ($search === '' && $request->filled('email')) {
            $search = trim((string) $request->email);
        }
        if ($search === '' && $request->filled('user')) {
            $search = trim((string) $request->user);
        }

        if ($search !== '') {
            $compact = preg_replace('/\s+/', '', $search);
            $deposits->whereHas('user', function ($user) use ($search, $compact) {
                $user->where('email', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$compact%")
                    ->orWhere('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%");
            });
        }

        if ($request->filled('date')) {
            $deposits->whereDate('updated_at', Carbon::parse($request->date)->toDateString());
        }

        $planSummary = $plans->map(function ($plan) use ($deposits) {
            $matched = clone $deposits;
            constrainDepositToPlan($matched, $plan);

            return (object) [
                'plan' => $plan,
                'count' => $matched->count(),
            ];
        });

        if ($selectedPlan) {
            constrainDepositToPlan($deposits, $selectedPlan);
        }

        $deposits = $deposits->orderByDesc('updated_at')->paginate(getPaginate())->withQueryString();

        return view('admin.deposit.rejected', compact('pageTitle', 'deposits', 'plans', 'planSummary', 'selectedPlan', 'search'));
    }

    public function reApproveList()
    {
        return redirect()->route('admin.deposit.rejected');
    }

    public function initiated()
    {
        $pageTitle = 'Initiated Deposits';
        $deposits = $this->depositData('initiated');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function deposit()
    {
        $pageTitle = 'Deposit History';
        $depositData = $this->depositData($scope = null, $summery = true);
        $deposits = $depositData['data'];
        $summery = $depositData['summery'];
        $successful = $summery['successful'];
        $pending = $summery['pending'];
        $rejected = $summery['rejected'];
        $initiated = $summery['initiated'];
        return view('admin.deposit.log', compact('pageTitle', 'deposits','successful','pending','rejected','initiated'));
    }

    protected function depositData($scope = null,$summery = false)
    {
        if ($scope) {
            $deposits = Deposit::$scope()->with(['user', 'gateway']);
        }else{
            $deposits = Deposit::with(['user', 'gateway']);
        }
        
        $request = request();
        $deposits = applyAdminTrxUserSearch($deposits, $request->search);


        //date search
        if($request->date) {
            $date = explode('-',$request->date);
            $request->merge([
                'start_date'=> trim(@$date[0]),
                'end_date'  => trim(@$date[1])
            ]);
            $request->validate([
                'start_date'    => 'required|date_format:m/d/Y',
                'end_date'      => 'nullable|date_format:m/d/Y'
            ]);
            if($request->end_date) {
                $endDate = Carbon::parse($request->end_date)->addHours(23)->addMinutes(59)->addSecond(59);
                $deposits   = $deposits->whereBetween('created_at', [Carbon::parse($request->start_date), $endDate]);
            }else{
                $deposits   = $deposits->whereDate('created_at', Carbon::parse($request->start_date));
            }
        }

        //vai method
        if ($request->method) {
            $method = Gateway::where('alias',$request->method)->firstOrFail();
            $deposits = $deposits->where('method_code',$method->code);
        }

        if (!$summery) {
            return $deposits->orderBy('id','desc')->paginate(getPaginate());
        }else{
            $successful = clone $deposits;
            $pending = clone $deposits;
            $rejected = clone $deposits;
            $initiated = clone $deposits;

            $successfulSummery = $successful->where('status',1)->sum('amount');
            $pendingSummery = $pending->where('status',2)->sum('amount');
            $rejectedSummery = $rejected->where('status',3)->sum('amount');
            $initiatedSummery = $initiated->where('status',0)->sum('amount');

            return [
                'data'=>$deposits->orderBy('id','desc')->paginate(getPaginate()),
                'summery'=>[
                    'successful'=>$successfulSummery,
                    'pending'=>$pendingSummery,
                    'rejected'=>$rejectedSummery,
                    'initiated'=>$initiatedSummery,
                ]
            ];
        }
    }

    public function details($id)
    {
        $general = gs();
        $deposit = Deposit::where('id', $id)->with(['user', 'gateway'])->firstOrFail();
        $pageTitle = $deposit->user->username.' requested ' . showAmount($deposit->amount) . ' '.$general->cur_text;
        $details = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
        return view('admin.deposit.detail', compact('pageTitle', 'deposit','details'));
    }


    public function approve($id)
    {
        $deposit = Deposit::where('id', $id)->where('status', 2)->firstOrFail();
        $plan = resolvePlanForDeposit($deposit);

        if (!$plan) {
            $notify[] = ['error', 'No matching package found for this joining amount. Please edit the deposit and select a plan, then approve again.'];
            return back()->withNotify($notify);
        }

        try {
            DB::transaction(function () use ($deposit, $plan) {
                completeJoiningApproval($deposit, $plan, true);
            });
        } catch (\Throwable $e) {
            \Log::error('Deposit approval failed: ' . $e->getMessage(), [
                'deposit_id' => $deposit->id,
                'exception' => $e,
            ]);
            $notify[] = ['error', 'Approval failed. Please try again.'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Deposit request approved successfully'];
        return redirect()->route('admin.deposit.pending')->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'message' => 'required|string|max:255'
        ]);
        $deposit = Deposit::where('id',$request->id)->where('status',2)->firstOrFail();

        $deposit->admin_feedback = $request->message;
        $deposit->status = 3;
        $deposit->save();

        $user = User::findOrFail($deposit->user_id);
        $user->pv = 're';
        $user->save();

        try {
            notify($deposit->user, 'DEPOSIT_REJECT', [
                'method_name' => $deposit->gatewayCurrency()->name,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amo),
                'amount' => showAmount($deposit->amount),
                'charge' => showAmount($deposit->charge),
                'rate' => showAmount($deposit->rate),
                'trx' => $deposit->trx,
                'rejection_message' => $request->message
            ]);
        } catch (\Throwable $e) {
            \Log::error('Deposit reject notify failed: ' . $e->getMessage(), [
                'deposit_id' => $deposit->id,
            ]);
        }

        $notify[] = ['success', 'Deposit request rejected successfully'];
        return  to_route('admin.deposit.pending')->withNotify($notify);

    }

    public function reApprove($id)
    {
        $deposit = Deposit::where('id', $id)->where('status', 3)->firstOrFail();
        $plan = resolvePlanForDeposit($deposit);

        if (!$plan) {
            $notify[] = ['error', 'No matching package found for this joining amount. Please select a plan first.'];
            return back()->withNotify($notify);
        }

        try {
            DB::transaction(function () use ($deposit, $plan) {
                $deposit->admin_feedback = null;
                $deposit->status = 2;
                $deposit->save();
                completeJoiningApproval($deposit, $plan, true);
            });
        } catch (\Throwable $e) {
            \Log::error('Deposit re-approval failed: ' . $e->getMessage(), [
                'deposit_id' => $deposit->id,
                'exception' => $e,
            ]);
            $notify[] = ['error', 'Re-approval failed. Please try again.'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Rejected user re-approved successfully'];
        return back()->withNotify($notify);
    }
    
    public function edit($id)
    {
        $plans = Plan::all();
        $general = gs();
        $deposit = Deposit::where('id', $id)->with(['user', 'gateway'])->firstOrFail();
        $pageTitle = $deposit->user->username.' requested ' . showAmount($deposit->amount) . ' '.$general->cur_text;
        $details = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
        return view('admin.deposit.edit_deposit', compact('pageTitle', 'deposit','details', 'plans'));
    }
    
    public function update(Request $request, $id)
    {
        $deposit = Deposit::where('id', $id)->where('status', 2)->firstOrFail();
        $plan = Plan::where('id', $request->plan_id)->first();

        if (!$plan) {
            $notify[] = ['error', 'Please select a valid package before approving.'];
            return back()->withNotify($notify);
        }

        try {
            DB::transaction(function () use ($deposit, $plan) {
                $deposit->amount = $plan->price;
                $deposit->final_amo = $plan->price;
                if (\Illuminate\Support\Facades\Schema::hasColumn('deposits', 'plan_id')) {
                    $deposit->plan_id = $plan->id;
                }
                $deposit->save();
                completeJoiningApproval($deposit, $plan, true);
            });
        } catch (\Throwable $e) {
            \Log::error('Deposit update/approval failed: ' . $e->getMessage(), [
                'deposit_id' => $deposit->id,
                'exception' => $e,
            ]);
            $notify[] = ['error', 'Approval failed. Please try again.'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Deposit request approved successfully'];
        return redirect()->route('admin.deposit.pending')->withNotify($notify);
    }
    
}
