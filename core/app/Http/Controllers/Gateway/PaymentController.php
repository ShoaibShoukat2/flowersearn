<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Gateway;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public $activeTemplate;

    public function __construct()
    {
        try {
            $this->activeTemplate = activeTemplate();
        } catch (\Exception $e) {
            \Log::error('PaymentController constructor error: ' . $e->getMessage());
            $this->activeTemplate = 'templates.basic.';
        }
    }

    public function deposit()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('user.login');
            }
            
            // If user plan_id is NOT 0, redirect to dashboard
            if ($user->plan_id != 0) {
                return redirect()->route('user.home');
            }
            
            $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                $gate->where('status', 1);
            })->with('method')->orderby('method_code')->get();
            
            $pageTitle = 'Deposit Methods';
            
            return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
            
        } catch (\Exception $e) {
            // Log the full error with stack trace
            \Log::error('Deposit page error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            
            // Show error message to user
            $notify[] = ['error', 'Database connection error. Please contact administrator.'];
            return redirect()->route('user.home')->withNotify($notify);
        }
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'plan_id' => 'nullable|integer|exists:plans,id',
        ]);
         
         $test = 'PKR';

        $user = auth()->user();
        $plan = null;
        if ($request->filled('plan_id')) {
            $plan = Plan::where('status', 1)->find($request->plan_id);
            if ($plan) {
                $request->merge(['amount' => $plan->price]);
            }
        }
        if (!$plan) {
            $plan = Plan::where('status', 1)->get()->first(function ($item) use ($request) {
                return abs((float) $item->price - (float) $request->amount) < 0.011;
            });
        }

        $currencies = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->get();
        $gate = $currencies->first(function ($row) use ($test) {
            return strtoupper((string) $row->currency) === $test;
        }) ?: $currencies->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        $amount = (float) $request->amount;
        if ((float) $gate->min_amount > $amount || (float) $gate->max_amount < $amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $openRequests = Deposit::where('user_id', $user->id)->whereIn('status', [0, 2])->count();
        if ($openRequests >= 3) {
            $notify[] = ['error', 'You already have a payment request in progress.'];
            return back()->withNotify($notify);
        }

        $charge = (float) $gate->fixed_charge + ($amount * (float) $gate->percent_charge / 100);
        $payable = $amount + $charge;
        $final_amo = $payable * (float) $gate->rate;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper((string) $gate->currency);
        $data->amount = $amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        if ($plan) {
            $data->plan_id = $plan->id;
        }
        try {
            $data->save();
        } catch (\Throwable $e) {
            if ($plan && str_contains($e->getMessage(), 'plan_id')) {
                unset($data->plan_id);
                try {
                    $data->save();
                } catch (\Throwable $retry) {
                    \Log::error('Deposit insert failed: ' . $retry->getMessage(), ['user_id' => $user->id]);
                    $notify[] = ['error', 'Payment could not be started. Please try again.'];
                    return back()->withNotify($notify);
                }
            } else {
                \Log::error('Deposit insert failed: ' . $e->getMessage(), ['user_id' => $user->id]);
                $notify[] = ['error', 'Payment could not be started. Please try again.'];
                return back()->withNotify($notify);
            }
        }
        session()->put('Track', $data->trx);
        session()->save();
        return to_route('user.deposit.confirm');
    }


    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            return "Sorry, invalid URL.";
        }
        $data = Deposit::where('id', $id)->where('status', 0)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status',0)->orderBy('id', 'DESC')->with('gateway')->first();
        if (!$deposit) {
            $notify[] = ['error', 'Payment session expired. Please select your package again.'];
            return to_route('user.deposit')->withNotify($notify);
        }

        [, $gateway] = $this->paymentGatewayFor($deposit);
        $alias = $gateway->alias ?? null;
        $processClass = $alias ? __NAMESPACE__ . '\\' . $alias . '\\ProcessController' : null;
        $isManual = (int) $deposit->method_code >= 1000 || !$processClass || !class_exists($processClass);

        if ($isManual) {
            return to_route('user.deposit.manual.confirm');
        }

        try {
            $data = json_decode($processClass::process($deposit));
        } catch (\Throwable $e) {
            \Log::error('Deposit confirm failed: ' . $e->getMessage(), ['trx' => $deposit->trx]);
            $notify[] = ['error', 'Unable to open the payment page. Please try again.'];
            return to_route('user.deposit')->withNotify($notify);
        }

        if (!$data || isset($data->error)) {
            $notify[] = ['error', $data->message ?? 'Unable to open the payment page. Please try again.'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if(@$data->session){
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        if (empty($data->view)) {
            return to_route('user.deposit.manual.confirm');
        }
        return view($this->activeTemplate . $data->view, compact('data', 'pageTitle', 'deposit'));
    }


    public static function userDataUpdate($deposit,$isManual = null)
    {
    


        if ($deposit->status == 0 || $deposit->status == 2) {
            $deposit->status = 1;
            $deposit->save();

            $user = User::find($deposit->user_id);
            $user->balance += $deposit->amount;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->amount = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'Deposit Via ' . $deposit->gatewayCurrency()->name;
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'deposit';
            $transaction->save();

            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'Deposit successful via '.$deposit->gatewayCurrency()->name;
            $adminNotification->click_url = urlPath('admin.deposit.successful');
            $adminNotification->save();

            levelCommission($user, $deposit->amount, 'deposit_commission', $deposit->trx);

            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Deposit successful via '.$deposit->gatewayCurrency()->name;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $deposit->gatewayCurrency()->name,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amo),
                'amount' => showAmount($deposit->amount),
                'charge' => showAmount($deposit->charge),
                'rate' => showAmount($deposit->rate),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($user->balance)
            ]);


        }
    }
    
    //fahad code for first deposit issue
    public static function userDataUpdateFirstDeposit($deposit,$isManual = null)
    {

        if ($deposit->status == 0 || $deposit->status == 2) {
            $deposit->status = 1;
            $deposit->save();

            $user = User::find($deposit->user_id);

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->amount = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'Deposit Via ' . $deposit->gatewayCurrency()->name;
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'deposit';
            $transaction->save();

            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'Deposit successful via '.$deposit->gatewayCurrency()->name;
            $adminNotification->click_url = urlPath('admin.deposit.successful');
            $adminNotification->save();

            $user->refresh();
            $paid = false;
            if ($user->plan_id) {
                $paid = levelCommission($user, $deposit->amount, 'plan_subscribe_commission', $deposit->trx);
            }
            if (!$paid) {
                levelCommission($user, $deposit->amount, 'deposit_commission', $deposit->trx);
            }

            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Deposit successful via '.$deposit->gatewayCurrency()->name;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            try {
                notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                    'method_name' => $deposit->gatewayCurrency()->name,
                    'method_currency' => $deposit->method_currency,
                    'method_amount' => showAmount($deposit->final_amo),
                    'amount' => showAmount($deposit->amount),
                    'charge' => showAmount($deposit->charge),
                    'rate' => showAmount($deposit->rate),
                    'trx' => $deposit->trx,
                    'post_balance' => showAmount($user->balance)
                ]);
            } catch (\Throwable $e) {
                \Log::error('Deposit approval notify failed: ' . $e->getMessage(), [
                    'deposit_id' => $deposit->id,
                    'user_id' => $user->id,
                ]);
            }
        }
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            $notify[] = ['error', 'Payment session expired. Please select your package again.'];
            return to_route('user.deposit')->withNotify($notify);
        }

        [$method, $gateway] = $this->paymentGatewayFor($data);
        if (!$method && !$gateway) {
            $notify[] = ['error', 'This payment method is not available right now. Please choose another method.'];
            return to_route('user.deposit')->withNotify($notify);
        }

        $pageTitle = 'Deposit Confirm';
        return view($this->activeTemplate . 'user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
    }

    protected function paymentGatewayFor(Deposit $deposit): array
    {
        $currency = null;
        if ($deposit->method_currency) {
            $currency = GatewayCurrency::where('method_code', $deposit->method_code)
                ->where('currency', $deposit->method_currency)
                ->first();
        }
        if (!$currency) {
            $currency = GatewayCurrency::where('method_code', $deposit->method_code)->first();
        }

        $gateway = $deposit->gateway;
        if (!$gateway && $currency) {
            $gateway = $currency->method()->first();
        }
        if (!$gateway) {
            $gateway = Gateway::where('code', (string) $deposit->method_code)->first();
        }
        if ($gateway) {
            $deposit->setRelation('gateway', $gateway);
        }

        return [$currency, $gateway];
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        // dd($track, $request->all());
        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        [, $gateway] = $this->paymentGatewayFor($data);
        $prepared = prepareManualDepositFields($gateway ? optional($gateway->form)->form_data : []);
        $formData = $prepared['fields'];

        $formProcessor = new FormProcessor();
        foreach ($formData as $field) {
            $field->is_required = 'required';
        }
        $validationRule = $formProcessor->valueValidation($formData);
        foreach ($formData as $field) {
            if (($field->type ?? '') === 'file') {
                $validationRule[$field->label] = ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            }
        }
        if ($prepared['needs_sender_name']) {
            $validationRule['sender_name'] = 'required|string|max:100';
        }
        if ($prepared['needs_sender_number']) {
            $validationRule['sender_number'] = 'required|string|max:30';
        }
        if ($prepared['needs_screenshot']) {
            $validationRule['payment_screenshot'] = 'required|file|mimes:jpg,jpeg,png,webp|max:5120';
        }
        $request->validate($validationRule, [
            'payment_screenshot.required' => 'Please upload the payment screenshot.',
            'payment_screenshot.mimes' => 'Payment screenshot must be a JPG, PNG, or WEBP image.',
        ]);
        $userData = $formProcessor->processFormData($request, $formData);

        if ($prepared['needs_sender_name']) {
            $userData[] = [
                'name' => 'Sender Name',
                'type' => 'text',
                'value' => trim($request->sender_name),
            ];
        }
        if ($prepared['needs_sender_number']) {
            $userData[] = [
                'name' => 'Sender Number',
                'type' => 'text',
                'value' => trim($request->sender_number),
            ];
        }
        if ($prepared['needs_screenshot'] && $request->hasFile('payment_screenshot')) {
            try {
                $directory = date('Y') . '/' . date('m') . '/' . date('d');
                $path = getFilePath('verify') . '/' . $directory;
                $userData[] = [
                    'name' => 'Payment Screenshot',
                    'type' => 'file',
                    'value' => $directory . '/' . fileUploader($request->file('payment_screenshot'), $path),
                ];
            } catch (\Throwable $e) {
                \Log::error('Payment screenshot upload failed: ' . $e->getMessage());
                $notify[] = ['error', 'Payment screenshot could not be saved. Please use a smaller JPG or PNG and try again.'];
                return back()->withNotify($notify)->withInput();
            }
        }

        $hasScreenshot = false;
        foreach ($userData as $index => $item) {
            if (($item['type'] ?? '') === 'file') {
                if (is_string($item['value'] ?? null) && trim($item['value']) !== '') {
                    $hasScreenshot = true;
                }
                continue;
            }
            $userData[$index]['value'] = trim((string) ($item['value'] ?? ''));
            if ($userData[$index]['value'] === '') {
                $notify[] = ['error', 'Please fill every required field before submitting payment.'];
                return back()->withNotify($notify)->withInput();
            }
        }
        if (!$hasScreenshot) {
            $notify[] = ['error', 'Please upload the payment screenshot before submitting payment.'];
            return back()->withNotify($notify)->withInput();
        }

        $user = auth()->user();

        $amount = $data->amount;
        $plan = null;
        if (!empty($data->plan_id)) {
            $plan = Plan::find($data->plan_id);
        }
        if (!$plan) {
            $plan = resolvePlanForDeposit($data, $user);
        }
        if (!$plan) {
            $plan = Plan::where('price', $amount)->first();
        }

        if ($plan) {
            $user->pv = $plan->id . 'r';
            if (\Illuminate\Support\Facades\Schema::hasColumn('deposits', 'plan_id')) {
                $data->plan_id = $plan->id;
            }
        }

        $user->save();

        $data->detail = $userData;
        $data->status = 2; // pending
        $data->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Deposit request from '.$data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details',$data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amo),
            'amount' => showAmount($data->amount),
            'charge' => showAmount($data->charge),
            'rate' => showAmount($data->rate),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'You have deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }


}
