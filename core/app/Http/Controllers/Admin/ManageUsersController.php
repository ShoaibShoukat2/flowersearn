<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\PtcEarn;
use App\Models\Plan;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ManageUsersController extends Controller
{

    public function allUsers()
    {
        $pageTitle = 'All Users';
        $users = $this->userData();
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function activeUsers()
    {
        $pageTitle = 'Active Users';
        $users = $this->userAprovedData();
        return view('admin.users.list', compact('pageTitle', 'users'));
    }
    
public function Today()
{
    $pageTitle = 'Active Users';

    // Get the current date in Asia/Karachi timezone
    $today = Carbon::now('Asia/Karachi')->toDateString();

    // Fetch active users (status = 1) created today
    $users = \App\Models\User::where('status', 1) // Check for status = 1
                ->whereDate('created_at', '=', $today) // Filter by today's date
                ->paginate(10); // Paginate results (adjust the number per page as needed)

    // Return the view with the page title and filtered users
    return view('admin.users.list', compact('pageTitle', 'users'));
}

    public function bannedUsers()
    {
        $pageTitle = 'Banned Users';
        $users = $this->userData('banned');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function emailUnverifiedUsers()
    {
        $pageTitle = 'Email Unverified Users';
        $users = $this->userData('emailUnverified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function kycUnverifiedUsers()
    {
        $pageTitle = 'KYC Unverified Users';
        $users = $this->userData('kycUnverified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function kycPendingUsers()
    {
        $pageTitle = 'KYC Unverified Users';
        $users = $this->userData('kycPending');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function emailVerifiedUsers()
    {
        $pageTitle = 'Email Verified Users';
        $users = $this->userData('emailVerified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }


    public function mobileUnverifiedUsers()
    {
        $pageTitle = 'Mobile Unverified Users';
        $users = $this->userData('mobileUnverified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }


    public function mobileVerifiedUsers()
    {
        $pageTitle = 'Mobile Verified Users';
        $users = $this->userData('mobileVerified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }


    public function usersWithBalance()
    {
        $pageTitle = 'Users with Balance';
        $users = $this->userData('withBalance');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }


    protected function userData($scope = null){
        if ($scope) {
            $users = User::$scope();
        }else{
            $users = User::query();
        }

        //search
        $request = request();
        if ($request->search) {
            $search = trim($request->search);
            $users  = $users->where(function ($user) use ($search) {
                            $user->where('username', 'like', "%$search%")
                                ->orWhere('email', 'like', "%$search%")
                                ->orWhereRaw('LOWER(email) = ?', [strtolower($search)])
                                ->orWhere('first_name', 'like', "%$search%")
                                ->orWhere('last_name', 'like', "%$search%");
                      });
        }
        return $users->orderBy('id','desc')->paginate(getPaginate());
    }
    
    //approved users
    protected function userAprovedData($scope = null){
        if ($scope) {
            $users = User::$scope();
        }else{
            $users = User::query();
        }

        //search
        $request = request();
        if ($request->search) {
            $search = trim($request->search);
            $users  = $users->where(function ($user) use ($search) {
                            $user->where('username', 'like', "%$search%")
                                ->orWhere('email', 'like', "%$search%")
                                ->orWhereRaw('LOWER(email) = ?', [strtolower($search)])
                                ->orWhere('first_name', 'like', "%$search%")
                                ->orWhere('last_name', 'like', "%$search%");
                      });
        }
        
        $users = $users->where('plan_id', '!=', 0);
        return $users->orderBy('id','desc')->paginate(getPaginate());
    }


    public function detail($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'User Detail - '.$user->username;

        $plans = Plan::all();
        $totalDeposit = Deposit::where('user_id',$user->id)->where('status',1)->sum('amount');
        $totalWithdrawals = Withdrawal::where('user_id',$user->id)->where('status',1)->sum('amount');
        $totalTransaction = Transaction::where('user_id',$user->id)->count();
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        
        $userCount = User::where('ref_by', $user->id)
                              ->whereHas('deposit', function ($query) {
                                  $query->where('status', 1);
                              })
                              ->count();

        $upliner = $user->ref_by ? User::find($user->ref_by) : null;
        $joiningDeposit = Deposit::where('user_id', $user->id)->where('status', 1)->orderBy('id')->first();
        if (!$joiningDeposit) {
            $joiningDeposit = Deposit::where('user_id', $user->id)->where('status', '!=', 0)->orderByDesc('id')->first();
        }

        $teamStatus = in_array(request('team_status'), ['paid', 'unpaid'], true) ? request('team_status') : 'paid';
        $teamScope = in_array(request('team_scope'), ['today', 'team'], true) ? request('team_scope') : 'team';
        $team = $this->teamMembers($user, $teamStatus, $teamScope, request('team_search'));
        $teamCounts = $this->teamCounts($user);

        return view('admin.users.detail', compact(
            'pageTitle',
            'plans',
            'user',
            'totalDeposit',
            'totalWithdrawals',
            'totalTransaction',
            'countries',
            'userCount',
            'upliner',
            'joiningDeposit',
            'team',
            'teamCounts',
            'teamStatus',
            'teamScope'
        ));
    }


    public function kycDetails($id)
    {
        $pageTitle = 'KYC Details';
        $user = User::findOrFail($id);
        return view('admin.users.kyc_detail', compact('pageTitle','user'));
    }

    public function kycApprove($id)
    {
        $user = User::findOrFail($id);
        $user->kv = 1;
        $user->save();

        notify($user,'KYC_APPROVE',[]);

        $notify[] = ['success','KYC approved successfully'];
        return to_route('admin.users.kyc.pending')->withNotify($notify);
    }

    public function kycReject($id)
    {
        $user = User::findOrFail($id);
        foreach ($user->kyc_data as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify').'/'.$kycData->value);
            }
        }
        $user->kv = 0;
        if ($user->kyc_data) {
            foreach ($user->kyc_data as $kycData) {
                if ($kycData->type == 'file') {
                    fileManager()->removeFile(getFilePath('verify').'/'.$kycData->value);
                }
            }
        }
        $user->kyc_data = null;
        $user->save();

        notify($user,'KYC_REJECT',[]);

        $notify[] = ['success','KYC rejected successfully'];
        return to_route('admin.users.kyc.pending')->withNotify($notify);
    }


   public function update(Request $request, $id)
{
    
    $user = User::findOrFail($id);
    $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));
    $countryArray   = (array)$countryData;
    $countries      = implode(',', array_keys($countryArray));

    $countryCode    = $request->country;
    $country        = $countryData->$countryCode->country;
    $dialCode       = $countryData->$countryCode->dial_code;

    $request->validate([
        // 'firstname' => 'required|string|max:40',
        // 'lastname' => 'required|string|max:40',
        'email' => 'required|email|string|max:40|unique:users,email,' . $user->id,
        'mobile' => 'required|string|max:40|unique:users,mobile,' . $user->id,
        'country' => 'required|in:'.$countries,
        'password' => 'nullable|string|min:6|max:100',
    ]);

    $user->mobile = $dialCode.$request->mobile;
    $user->country_code = $countryCode;
    $user->firstname = $request->firstname;
    $user->lastname = $request->lastname;
    $user->email = $request->email;
    $user->ref = $request->ref ?? '';
    $user->address = [
                        'address' => $request->address,
                        'city' => $request->city,
                        'state' => $request->state,
                        'zip' => $request->zip,
                        'country' => @$country,
                    ];
    $user->ev = $request->ev ? 1 : 0;
    $user->sv = $request->sv ? 1 : 0;
    $user->ts = $request->ts ? 1 : 0;
    
    if (!$request->kv) {
        $user->kv = 0;
        $user->kyc_data = null;
    } else {
        $user->kv = 1;
    }

    // Check if password is provided, if so, update it
    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
        storeSignupPassword($user, (string) $request->password);
    }
    
    // Check if password is provided, if so, update it
    if ($request->level) {
        $user->level = $request->level;
    }
    
    // Check if password is provided, if so, update it
    if ($request->plan_id) {
        $user->plan_id = $request->plan_id;
    }

    $plan = $user->plan_id ? Plan::find($user->plan_id) : null;
    if ($plan) {
        $user->daily_limit = dailyLimitForLevel($plan, (int) $user->level);
    }

    $user->save();

    $notify[] = ['success', 'User details updated successfully'];
    return back()->withNotify($notify);
}


    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|max:100',
        ]);

        $user = User::findOrFail($id);
        $user->password = bcrypt($request->password);
        storeSignupPassword($user, (string) $request->password);
        $user->save();

        $notify[] = ['success', 'Password updated successfully'];
        return back()->withNotify($notify);
    }

    public function addSubBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'act' => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $amount = $request->amount;
        $general = gs();
        $trx = getTrx();

        $transaction = new Transaction();

        if ($request->act == 'add') {
            $user->balance += $amount;

            $transaction->trx_type = '+';
            $transaction->remark = 'balance_add';

            $notifyTemplate = 'BAL_ADD';

            $notify[] = ['success', $general->cur_sym . $amount . ' added successfully'];

        } else {
            if ($amount > $user->balance) {
                $notify[] = ['error', $user->username . ' doesn\'t have sufficient balance.'];
                return back()->withNotify($notify);
            }

            $user->balance -= $amount;

            $transaction->trx_type = '-';
            $transaction->remark = 'balance_subtract';

            $notifyTemplate = 'BAL_SUB';
            $notify[] = ['success', $general->cur_sym . $amount . ' subtracted successfully'];
        }

        $user->save();

        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx =  $trx;
        $transaction->details = $request->remark;
        $transaction->save();

        notify($user, $notifyTemplate, [
            'trx' => $trx,
            'amount' => showAmount($amount),
            'remark' => $request->remark,
            'post_balance' => showAmount($user->balance)
        ]);

        return back()->withNotify($notify);
    }
    
    public function addSubBalanceFanta(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'act' => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $amount = $request->amount;
        $general = gs();
        
        $earn               = new PtcEarn();

        if ($request->act == 'add') {
            $user->fanta_balance += $amount;

            $earn->user_id      = $user->id;
            $earn->ptc_id       = 0;
            $earn->ptc_amount   = $amount;
            $earn->trx_type     = '+';
            $earn->remark       = 'ptc_earn';
            $earn->details      = $request->remark;
            
            $notify[] = ['success', $general->cur_sym . $amount . ' added successfully'];

        } else {
            if ($amount > $user->fanta_balance) {
                $notify[] = ['error', $user->username . ' doesn\'t have sufficient balance.'];
                return back()->withNotify($notify);
            }

            $user->fanta_balance -= $amount;

            $earn->user_id      = $user->id;
            $earn->ptc_id       = 0;
            $earn->ptc_amount   = $amount;
            $earn->trx_type     = '+';
            $earn->remark       = 'ptc_earn_sub';
            $earn->details      = $request->remark;
            
            $notify[] = ['success', $general->cur_sym . $amount . ' subtracted successfully'];
        }

        $user->save();
        
        $earn->save();

        return back()->withNotify($notify);
    }

    public function login($id){
        Auth::loginUsingId($id);
        return to_route('user.home');
    }

    public function status(Request $request,$id)
    {
        $user = User::findOrFail($id);
        if ($user->status == 1) {
            $request->validate([
                'reason'=>'required|string|max:255'
            ]);
            $user->status = 0;
            $user->ban_reason = $request->reason;
            $notify[] = ['success','User banned successfully'];
        }else{
            $user->status = 1;
            $user->ban_reason = null;
            $notify[] = ['success','User unbanned successfully'];
        }
        $user->save();
        return back()->withNotify($notify);

    }

    public function reApprove($id)
    {
        $user = User::findOrFail($id);

        if ($user->status == 1) {
            $notify[] = ['info', 'User is already active'];
            return back()->withNotify($notify);
        }

        $user->status = 1;
        $user->ban_reason = null;
        $user->save();

        $notify[] = ['success', 'User re-approved and activated successfully'];
        return back()->withNotify($notify);
    }


    public function showNotificationSingleForm($id)
    {
        $user = User::findOrFail($id);
        $general = gs();
        if (!$general->en && !$general->sn) {
            $notify[] = ['warning','Notification options are disabled currently'];
            return to_route('admin.users.detail',$user->id)->withNotify($notify);
        }
        $pageTitle = 'Send Notification to ' . $user->username;
        return view('admin.users.notification_single', compact('pageTitle', 'user'));
    }

    public function sendNotificationSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'subject' => 'required|string',
        ]);

        $user = User::findOrFail($id);
        notify($user,'DEFAULT',[
            'subject'=>$request->subject,
            'message'=>$request->message,
        ]);
        $notify[] = ['success', 'Notification sent successfully'];
        return back()->withNotify($notify);
    }

    public function showNotificationAllForm()
    {
        $general = gs();
        if (!$general->en && !$general->sn) {
            $notify[] = ['warning','Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }
        $users = User::where('ev',1)->where('sv',1)->where('status',1)->count();
        $pageTitle = 'Notification to Verified Users';
        return view('admin.users.notification_all', compact('pageTitle','users'));
    }

    public function sendNotificationAll(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'message' => 'required',
            'subject' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $user = User::where('status', 1)->where('ev',1)->where('sv',1)->skip($request->skip)->first();

        notify($user,'DEFAULT',[
            'subject'=>$request->subject,
            'message'=>$request->message,
        ]);

        return response()->json([
            'success'=>'message sent',
            'total_sent'=>$request->skip + 1,
        ]);
    }

    protected function teamCounts(User $user): array
    {
        $downline = User::where('ref_by', $user->id);
        $paid = (clone $downline)->where('plan_id', '!=', 0);
        $unpaid = (clone $downline)->where(function ($query) {
            $query->whereNull('plan_id')->orWhere('plan_id', 0);
        });

        return [
            'paid_team' => (clone $paid)->count(),
            'unpaid_team' => (clone $unpaid)->count(),
            'paid_today' => (clone $paid)->whereHas('deposits', function ($query) {
                $query->where('status', 1)->whereDate('updated_at', Carbon::today());
            })->count(),
            'unpaid_today' => (clone $unpaid)->whereDate('created_at', Carbon::today())->count(),
        ];
    }

    protected function teamMembers(User $user, string $status, string $scope, ?string $search)
    {
        $query = User::with(['plan', 'deposits' => function ($deposits) {
            $deposits->whereIn('status', [1, 2, 3])->orderBy('id');
        }])->where('ref_by', $user->id);

        if ($status === 'paid') {
            $query->where('plan_id', '!=', 0);
        } else {
            $query->where(function ($inner) {
                $inner->whereNull('plan_id')->orWhere('plan_id', 0);
            });
        }

        if ($scope === 'today') {
            if ($status === 'paid') {
                $query->whereHas('deposits', function ($deposits) {
                    $deposits->where('status', 1)->whereDate('updated_at', Carbon::today());
                });
            } else {
                $query->whereDate('created_at', Carbon::today());
            }
        }

        if ($search) {
            $search = trim($search);
            $query->where(function ($inner) use ($search) {
                $inner->where('email', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%")
                    ->orWhere('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        }

        return $query->orderByDesc('id')->paginate(getPaginate())->withQueryString();
    }

    public function notificationLog($id){
        $user = User::findOrFail($id);
        $pageTitle = 'Notifications Sent to '.$user->username;
        $logs = NotificationLog::where('user_id',$id)->with('user')->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle','logs','user'));
    }

}
