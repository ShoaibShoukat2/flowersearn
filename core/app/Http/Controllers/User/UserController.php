<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\CommissionLog;
use App\Models\Deposit;
use App\Models\Form;
use App\Models\Plan;
use App\Models\PtcView;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function pending()
    {
        // Redirect to login if user not logged in
        if (!Auth::check()) {
            return redirect()->route('user.login');
        }

        ensureDepositChannelJoinColumn();
    
        $user = Auth::user()->fresh();

        $approvedDeposit = Deposit::where('user_id', $user->id)->where('status', 1)->latest('id')->first();
        if ($approvedDeposit && (int) $user->plan_id === 0) {
            $plan = resolvePlanForDeposit($approvedDeposit, $user);
            if ($plan) {
                assignPlanToUser($user, $plan);
                $user->refresh();
                levelCommission($user, $approvedDeposit->amount, 'plan_subscribe_commission', $approvedDeposit->trx);
                updateReferrerLevelAndLimit($user->id);
            }
        }

        if ($user && preg_match('/^\d+p$/i', trim((string) $user->pv)) && (int) $user->plan_id === 0) {
            $plan = resolvePlanForDeposit($approvedDeposit ?: Deposit::where('user_id', $user->id)->latest('id')->first() ?: new Deposit(['user_id' => $user->id]), $user);
            if ($plan) {
                $pendingOrApproved = Deposit::where('user_id', $user->id)->whereIn('status', [1, 2])->latest('id')->first();
                if ($pendingOrApproved && (int) $pendingOrApproved->status === 2) {
                    completeJoiningApproval($pendingOrApproved, $plan, true);
                } else {
                    assignPlanToUser($user, $plan);
                    if ($pendingOrApproved && (int) $pendingOrApproved->status === 1) {
                        $user->refresh();
                        levelCommission($user, $plan->price, 'plan_subscribe_commission', $pendingOrApproved->trx);
                        updateReferrerLevelAndLimit($user->id);
                    }
                }
            }
        }

        $user = $user->fresh();
        repairSignupPassword($user);
        $user->refresh();

        $isApproved = (int) $user->plan_id !== 0 || preg_match('/^\d+a$/i', trim((string) $user->pv));
        $isRejected = !$isApproved && isAccountRejected($user);
        $rejectedDeposit = $isRejected
            ? Deposit::where('user_id', $user->id)->where('status', 3)->latest('created_at')->first()
            : null;
        $pendingDeposit = !$isRejected
            ? Deposit::where('user_id', $user->id)->where('status', 2)->latest('created_at')->first()
            : null;

        $loginEmail = $user->email;
        $loginUsername = $user->username;
        $loginPassword = $isApproved ? signupPasswordFor($user) : null;

        return view(base64_decode('dGVtcGxhdGVzLmJhc2ljLnVzZXIucGVuZGluZw=='), compact(
            'isRejected',
            'rejectedDeposit',
            'pendingDeposit',
            'isApproved',
            'loginEmail',
            'loginUsername',
            'loginPassword'
        ));
    }

    public function confirmChannels()
    {
        if (!Auth::check()) {
            return redirect()->route('user.login');
        }

        ensureDepositChannelJoinColumn();

        $deposit = Deposit::where('user_id', Auth::id())->where('status', 2)->latest('id')->first();
        if ($deposit && empty($deposit->channels_joined_at)) {
            $deposit->channels_joined_at = now();
            $deposit->save();
        }

        return redirect()->route('user.pending');
    }

    public function home()
    {
    
        $pageTitle = base64_decode('RGFzaGJvYXJk');
        $ptc = PtcView::where(base64_decode('dXNlcl9pZA=='), auth()->user()->id)
            ->get([base64_decode('dmlld19kYXRl'), base64_decode('YW1vdW50')]);

        $chart[base64_decode('Y2xpY2s=')] = $ptc->groupBy(base64_decode('dmlld19kYXRl'))
            ->map(function ($item, $key) {
                return collect($item)->count();
            })->sort()->reverse()->take(7)->toArray();

        $chart[base64_decode('YW1vdW50')] = $ptc->groupBy(base64_decode('dmR0'))
            ->map(function ($item, $key) {
                return collect($item)->sum(base64_decode('YW1vdW50'));
            })->sort()->reverse()->take(7)->toArray();

        $user = syncUserLevelAndLimit(auth()->user());
        return view($this->activeTemplate . base64_decode('dXNlci5kYXNoYm9hcmQ='), compact(
            base64_decode('cGFnZVRpdGxl'),
            base64_decode('Y2hhcnQ='),
            base64_decode('dXNlcg==')
        ));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = base64_decode('RGVwb3NpdCBIaXN0b3J5');
        $deposits = auth()->user()->deposits();

        if ($request->search) {
            $deposits = $deposits->where(base64_decode('dHJ4'), $request->search);
        }

        $deposits = $deposits->with([base64_decode('Z2F0ZXdheQ==')])
            ->orderBy(base64_decode('aWQ='), base64_decode('ZGVzYw=='))
            ->paginate(getPaginate());

        return view($this->activeTemplate . base64_decode('dXNlci5kZXBvc2l0X2hpc3Rvcnk='), compact(
            base64_decode('cGFnZVRpdGxl'),
            base64_decode('ZGVwb3NpdHM=')
        ));
    }

    public function show2faForm()
    {
        $general = gs();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . base64_decode('QA==') . $general->site_name, $secret);
        $pageTitle = base64_decode('MkZBIFNldHRpbmc=');

        return view($this->activeTemplate . base64_decode('dXNlci50d29mYWN0b3I='), compact(
            base64_decode('cGFnZVRpdGxl'),
            base64_decode('c2VjcmV0'),
            base64_decode('cXJDb2RlVXJs'),
            'general'
        ));
    }

    public function generateQRCode(Request $request)
    {
        $secret = $request->get('secret');
        $username = auth()->user()->username;
        $siteName = gs()->site_name;
        
        if (!$secret) {
            return response()->json(['error' => 'Secret is required'], 400);
        }
        
        // Create OTP URL
        $otpUrl = 'otpauth://totp/' . urlencode($username . '@' . $siteName) . '?secret=' . $secret . '&issuer=' . urlencode($siteName);
        
        // Return multiple QR code URLs as fallbacks
        $qrUrls = [
            'qrserver' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($otpUrl),
            'google' => 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($otpUrl),
            'quickchart' => 'https://quickchart.io/qr?text=' . urlencode($otpUrl) . '&size=200'
        ];
        
        return response()->json([
            'urls' => $qrUrls,
            'otpUrl' => $otpUrl
        ]);
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            base64_decode('a2V5') => base64_decode('cmVxdWlyZWQ='),
            base64_decode('Y29kZQ==') => base64_decode('cmVxdWlyZWQ=')
        ]);

        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $notify[] = [base64_decode('c3VjY2Vzcw=='), base64_decode('R29vZ2xlIGF1dGhlbnRpY2F0b3IgYWN0aXZhdGVkIHN1Y2Nlc3NmdWxseQ==')];
            return back()->withNotify($notify);
        }

        $notify[] = [base64_decode('ZXJyb3I='), base64_decode('V3JvbmcgdmVyaWZpY2F0aW9uIGNvZGU=')];
        return back()->withNotify($notify);
    }

    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            base64_decode('Y29kZQ==') => base64_decode('cmVxdWlyZWQ=')
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $notify[] = [base64_decode('c3VjY2Vzcw=='), base64_decode('VHdvIGZhY3RvciBhdXRoZW50aWNhdG9yIGRlYWN0aXZhdGVkIHN1Y2Nlc3NmdWxseQ==')];
        } else {
            $notify[] = [base64_decode('ZXJyb3I='), base64_decode('V3JvbmcgdmVyaWZpY2F0aW9uIGNvZGU=')];
        }

        return back()->withNotify($notify);
    }

    public function transactions(Request $request)
    {
        $pageTitle = 'Transaction History';

        $transactions = Transaction::where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view($this->activeTemplate . 'user.transactions', compact('pageTitle', 'transactions'));
    }

    public function withdrawHistory()
    {
        $pageTitle = 'Withdrawal History';

        $withdraws = Withdrawal::with('method')
            ->where('user_id', auth()->id())
            ->where('status', '!=', 0)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $summary = [
            'count'    => $withdraws->count(),
            'approved' => $withdraws->where('status', 1)->sum('amount'),
            'pending'  => $withdraws->where('status', 2)->sum('amount'),
            'rejected' => $withdraws->where('status', 3)->sum('amount'),
        ];

        $groupedWithdraws = $withdraws->groupBy(function ($w) {
            return optional($w->created_at)->format('Y-m-d') ?: 'unknown';
        });

        return view('templates.basic.user.withdraw.log', compact('pageTitle', 'withdraws', 'summary', 'groupedWithdraws'));
    }

    public function kycForm()
    {
        if (auth()->user()->kv == 2) {
            $notify[] = [base64_decode('ZXJyb3I='), base64_decode('WW91ciBLWUMgaXMgdW5kZXIgcmV2aWV3')];
            return to_route(base64_decode('dXNlci5ob21l'))->withNotify($notify);
        }
        if (auth()->user()->kv == 1) {
            $notify[] = [base64_decode('ZXJyb3I='), base64_decode('WW91IGFyZSBhbHJlYWR5IEtZQyB2ZXJpZmllZA==')];
            return to_route(base64_decode('dXNlci5ob21l'))->withNotify($notify);
        }

        $pageTitle = base64_decode('S1lDIEZvcm0=');
        $form = Form::where(base64_decode('YWN0'), base64_decode('a3lj'))->first();
        return view($this->activeTemplate . base64_decode('dXNlci5reWMuZm9ybQ=='), compact(
            base64_decode('cGFnZVRpdGxl'),
            base64_decode('Zm9ybQ==')
        ));
    }

    public function kycData()
    {
        $user = auth()->user();
        $pageTitle = base64_decode('S1lDIERhdGE=');
        return view($this->activeTemplate . base64_decode('dXNlci5reWMuaW5mbw=='), compact(
            base64_decode('cGFnZVRpdGxl'),
            base64_decode('dXNlcg==')
        ));
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where(base64_decode('YWN0'), base64_decode('a3lj'))->first();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $user = auth()->user();
        $user->kyc_data = $userData;
        $user->kv = 2;
        $user->save();

        $notify[] = [base64_decode('c3VjY2Vzcw=='), base64_decode('S1lDIGRhdGEgc3VibWl0dGVkIHN1Y2Nlc3NmdWxseQ==')];
        return to_route(base64_decode('dXNlci5ob21l'))->withNotify($notify);
    }

    public function attachmentDownload($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general = gs();
        $title = slug($general->site_name) . base64_decode('LSBhdHRhY2htZW50cy4=') . $extension;
        $mimetype = mime_content_type($filePath);

        header(base64_decode('Q29udGVudC1EaXNwb3NpdGlvbjogYXR0YWNobWVudDsgZmlsZW5hbWU9Ig==') . $title);
        header(base64_decode('Q29udGVudC1UeXBlOiA=') . $mimetype);
        return readfile($filePath);
    }

    public function userData()
    {
        $user = auth()->user();
        if ($user->reg_step == 1) {
            return to_route(base64_decode('dXNlci5ob21l'));
        }

        $pageTitle = base64_decode('VXNlciBEYXRh');
        return view($this->activeTemplate . base64_decode('dXNlci51c2VyX2RhdGE='), compact(
            base64_decode('cGFnZVRpdGxl'),
            base64_decode('dXNlcg==')
        ));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->reg_step == 1) {
            return to_route(base64_decode('dXNlci5ob21l'));
        }

        $request->validate([
            base64_decode('Zmlyc3RuYW1l') => base64_decode('cmVxdWlyZWQ='),
            base64_decode('bGFzdG5hbWU=') => base64_decode('cmVxdWlyZWQ=')
        ]);

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->address = [
            base64_decode('Y291bnRyeQ==') => @$user->address->country,
            base64_decode('YWRkcmVzcw==') => $request->address,
            base64_decode('c3RhdGU=') => $request->state,
            base64_decode('emlw') => $request->zip,
            base64_decode('Y2l0eQ==') => $request->city
        ];
        $user->reg_step = 1;
        $user->save();

        $general = gs();
        if ($general->registration_bonus > 0) {
            $user->balance += $general->registration_bonus;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $general->registration_bonus;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = base64_decode('Kw==');
            $transaction->details = base64_decode('UmVnaXN0cmF0aW9uIEJvbnVz');
            $transaction->remark = base64_decode('cmVnaXN0cmF0aW9uX2JvbnVz');
            $transaction->trx = getTrx();
            $transaction->save();
        }

        $plan = Plan::where(base64_decode('c3RhdHVz'), 1)->find($general->default_plan);
        if ($plan) {
            $user->expire_date = now()->addDays($plan->validity);
            $user->plan_id = $plan->id;
            $user->save();
            syncUserLevelAndLimit($user);
        }

        $notify[] = [base64_decode('c3VjY2Vzcw=='), base64_decode('UmVnaXN0cmF0aW9uIHByb2Nlc3MgY29tcGxldGVkIHN1Y2Nlc3NmdWxseQ==')];
        return to_route(base64_decode('dXNlci5ob21l'))->withNotify($notify);
    }

    public function buyPlan(Request $request)
    {
        $request->validate([
            base64_decode('aWQ=') => base64_decode('cmVxdWlyZWQ=')
        ]);

        $plan = Plan::where(base64_decode('c3RhdHVz'), 1)->findOrFail($request->id);
        $user = auth()->user();

        if ($plan->price > $user->balance) {
            $notify[] = [base64_decode('ZXJyb3I='), base64_decode('T29wcyEgWW91XCd2ZSBubyBzdWZmaWNpZW50IGJhbGFuY2U=')];
            return back()->withNotify($notify);
        }

        if ($user->runningPlan && $user->plan_id == $plan->id) {
            $notify[] = [base64_decode('ZXJyb3I='), base64_decode('WW91IGNvdWxkblwndCBzdWJzY3JpYmUgY3VycmVudCBwYWNrYWdlIHRpbGwgZXhwaXJlZA==')];
            return back()->withNotify($notify);
        }

        $user->balance -= $plan->price;
        $user->expire_date = now()->addDays($plan->validity);
        $user->plan_id = $plan->id;
        $user->save();
        syncUserLevelAndLimit($user);

        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $plan->price;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx_type = base64_decode('LQ==');
        $transaction->details = base64_decode('U3Vic2NyaWJlIA==') . $plan->name . base64_decode('IFBsYW4=');
        $transaction->trx = $trx;
        $transaction->remark = base64_decode('c3Vic2NyaWJlX3BsYW4=');
        $transaction->save();

        levelCommission($user, $plan->price, base64_decode('cGxhbl9zdWJzY3JpYmVfY29tbWlzc2lvbg=='), $trx);
        notify($user, base64_decode('QlVZX1BMQU4='), [
            base64_decode('cGxhbl9uYW1l') => $plan->name,
            base64_decode('YW1vdW50') => showAmount($plan->price),
            base64_decode('dHJ4') => $trx,
            base64_decode('cG9zdF9iYWxhbmNl') => showAmount($user->balance)
        ]);

        $notify[] = [base64_decode('c3VjY2Vzcw=='), base64_decode('WW91IGhhdmUgc3Vic2NyaWJlZCB0byB0aGUgcGxhbiBzdWNjZXNzZnVsbHk=')];
        return back()->withNotify($notify);
    }

    public function commissions(Request $request)
    {
        $pageTitle = base64_decode('Q29tbWlzc2lvbnM=');
        $commissions = CommissionLog::where(base64_decode('dG9faWQ='), auth()->id());

        if ($request->search) {
            $search = request()->search;
            $commissions = $commissions->where(function ($q) use ($search) {
                $q->where(base64_decode('dHJ4'), base64_decode('bGlrZQ=='), "%$search%")
                    ->orWhereHas(base64_decode('dXNlckZyb20='), function ($user) use ($search) {
                        $user->where(base64_decode('dXNlcm5hbWU='), base64_decode('bGlrZQ=='), "%$search%");
                    });
            });
        }

        if ($request->remark) {
            $commissions = $commissions->where(base64_decode('dHlwZQ=='), $request->remark);
        }
        if ($request->level) {
            $commissions = $commissions->where(base64_decode('bGV2ZWw='), $request->level);
        }

        $commissions = $commissions->with(base64_decode('dXNlckZyb20='))->paginate(getPaginate());
        $levels = Referral::max(base64_decode('bGV2ZWw='));

        return view($this->activeTemplate . base64_decode('dXNlci5jb21taXNzaW9ucw=='), compact(
            base64_decode('cGFnZVRpdGxl'),
            base64_decode('Y29tbWlzc2lvbnM='),
            base64_decode('bGV2ZWxz')
        ));
    }

    public function referredUsers()
    {
        $pageTitle = base64_decode('UmVmZXJyZWQgVXNlcnM=');
        $refUsers = User::where(base64_decode('cmVmX2J5'), auth()->user()->id)->where('plan_id', '!=', 0)
            ->with(base64_decode('cGxhbg=='))
            ->paginate(getPaginate());
        $user = auth()->user();
        
        return view($this->activeTemplate . base64_decode('dXNlci5yZWZlcnJlZA=='), compact(
            base64_decode('cGFnZVRpdGxl'),
            base64_decode('cmVmVXNlcnM='),
            base64_decode('dXNlcg==')
        ));
    }

    public function transfer()
    {
        $pageTitle = base64_decode('VHJhbnNmZXIgQmFsYW5jZQ==');
        $general = gs();
        if ($general->balance_transfer == 0) {
            $notify[] = [base64_decode('ZXJyb3I='), base64_decode('VXNlciBiYWxhbmNlIHRyYW5zZmVyIGN1cnJlbnRseSBkaXNhYmxlZA==')];
            return redirect()->route(base64_decode('dXNlci5ob21l'))->withNotify($notify);
        }

        return view($this->activeTemplate . base64_decode('dXNlci50cmFuc2Zlcl9iYWxhbmNl'), compact(
            base64_decode('cGFnZVRpdGxl')
        ));
    }

    public function transferSubmit(Request $request)
    {
        $request->validate([
            base64_decode('dXNlcm5hbWU=') => base64_decode('cmVxdWlyZWQ='),
            base64_decode('YW1vdW50') => base64_decode('cmVxdWlyZWR8bnVtZXJpY3xndDow')
        ]);

        $user = auth()->user();
        if ($user->username == $request->username) {
            $notify[] = [base64_decode('ZXJyb3I='), base64_decode('WW91IGNhbm5vdCBzZW5kIG1vbmV5IHRvIHlvdXIgd29uIGFjY291bnQ=')];
            return back()->withNotify($notify);
        }

        $receiver = User::where(base64_decode('dXNlcm5hbWU='), $request->username)->first();
        if (!$receiver) {
            $notify[] = [base64_decode('ZXJyb3I='), base64_decode('UmVjZWl2ZXIgbm90IGZvdW5k')];
            return back()->withNotify($notify);
        }

        $general = gs();
        $charge = $general->bt_fixed + ($request->amount * $general->bt_percent) / 100;
        $afterCharge = $request->amount + $charge;

        if ($user->balance < $afterCharge) {
            $notify[] = [base64_decode('ZXJyb3I='), base64_decode('WW91IGhhdmUgbm8gc3VmZmljaWVudCBiYWxhbmNl')];
            return back()->withNotify($notify);
        }

        $user->balance -= $afterCharge;
        $user->save();

        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = getAmount($afterCharge);
        $transaction->charge = $charge;
        $transaction->trx_type = base64_decode('LQ==');
        $transaction->trx = $trx;
        $transaction->details = base64_decode('QmFsYW5jZSB0cmFuc2ZlciB0byA=') . $receiver->username;
        $transaction->remark = base64_decode('YmFsYW5jZV90cmFuc2Zlcg==');
        $transaction->post_balance = getAmount($user->balance);
        $transaction->save();

        $receiver->balance += $request->amount;
        $receiver->save();

        $transaction = new Transaction();
        $transaction->user_id = $receiver->id;
        $transaction->amount = getAmount($request->amount);
        $transaction->charge = 0;
        $transaction->trx_type = base64_decode('Kw==');
        $transaction->trx = $trx;
        $transaction->details = base64_decode('QmFsYW5jZSByZWNlaXZlZCBmcm9tIA==') . $user->username;
        $transaction->remark = base64_decode('YmFsYW5jZV9yZWNlaXZlZA==');
        $transaction->post_balance = getAmount($user->balance);
        $transaction->save();

        notify($user, base64_decode('QkFMQU5DRV9UUkFOU0ZFUg=='), [
            base64_decode('YW1vdW50') => $request->amount,
            base64_decode('Y2hhcmdl') => $charge,
            base64_decode('YWZ0ZXJDaGFyZ2U=') => $afterCharge,
            base64_decode('cG9zdF9iYWxhbmNl') => $user->balance,
            base64_decode('cmVjZWl2ZXI=') => $receiver->username,
            base64_decode('dHJ4') => $trx
        ]);

        notify($receiver, base64_decode('QkFMQU5DRV9SRUNFSVZF'), [
            base64_decode('YW1vdW50') => $request->amount,
            base64_decode('cG9zdF9iYWxhbmNl') => $user->balance,
            base64_decode('c2VuZGVy') => $user->username,
            base64_decode('dHJ4') => $trx
        ]);

        $notify[] = [base64_decode('c3VjY2Vzcw=='), base64_decode('QmFsYW5jZSB0cmFuc2ZlcnJlZCBzdWNjZXNzZnVsbHk=')];
        return back()->withNotify($notify);
    }

    public function contact()
    {
        ensureContactAdminsTable();
        $pageTitle = 'Contact Us';
        $admins = \App\Models\ContactAdmin::orderBy('id')->get();

        return view($this->activeTemplate . 'user.contact', compact('pageTitle', 'admins'));
    }

    public function channels()
    {
        $pageTitle = 'Official Channels';
        $channels = officialChannels();

        return view($this->activeTemplate . 'user.channels', compact('pageTitle', 'channels'));
    }

    public function rewards()
    {
        $user = auth()->user();
        $plan = $user->plan;
        $rewardSettings = \App\Models\RewardPageSetting::current();
        $rewardItems = \App\Models\RewardItem::activeOrdered();
        $pageTitle = $rewardSettings->page_title ?? 'Rewards & Levels';

        $user = syncUserLevelAndLimit($user);
        $levels = persistCanonicalPlanLevels($plan);
        $referralsCount = successfulJoiningCount($user->id);

        $nextLevel = null;
        $progressPercentage = 0;
        $referralsNeeded = 0;

        foreach ($levels as $level => $data) {
            $required = (int) ($data['referrals_required'] ?? 0);
            if ($required > $referralsCount) {
                $nextLevel = (int) $level;
                $referralsNeeded = $required - $referralsCount;
                $progressPercentage = $required > 0 ? min(100, ($referralsCount / $required) * 100) : 0;
                break;
            }
        }

        return view($this->activeTemplate . 'user.rewards', compact(
            'pageTitle',
            'user',
            'plan',
            'levels',
            'referralsCount',
            'nextLevel',
            'progressPercentage',
            'referralsNeeded',
            'rewardSettings',
            'rewardItems'
        ));
    }
}
?>