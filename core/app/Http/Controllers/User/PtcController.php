<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Ptc;
use App\Models\PtcView;
use App\Models\PtcEarn;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\TaskLock;
use App\Models\Transaction;
use Carbon\Carbon;

class PtcController extends Controller
{
    // public function index()
    // {
    //     // dd('this working ptc ads');
    //     $pageTitle = "PTC Ads";

    //     $ads = Ptc::where('status', 1)->where('user_id', '!=', auth()->id())->where('remain', '>', 0)->inRandomOrder()->orderBy('remain', 'desc')->limit(50)->get();

    //     // dd($ads);
    //     $ads = Ptc::where('status', 1)
    //         ->where('remain', '>', 0)
    //         ->whereDoesntHave('views', function ($q) {
    //             $q->where('user_id', auth()->user()->id)->whereDate('view_date', Date('Y-m-d'));
    //         })
    //         ->inRandomOrder()
    //         ->orderBy('remain', 'desc')
    //         ->limit(50)
    //         ->get();
            
    //     // dd($ads);
    //     return view(activeTemplate() . 'user.ptc.index', compact('ads', 'pageTitle'));
    // }
    
    // public function index()
    // {
    //     $pageTitle    = "SnapsTubes Work Room";
    //     $user         = auth()->user();
    //     $ads          = collect();
    //     $lockMessage  = null;
    
    //     $referralsCount = User::where('ref_by', $user->id)->count();
    //     $daysSinceRegister = Carbon::now()->diffInDays($user->created_at);
    
    //     // Get levels JSON from plans table
    //     $plan = Plan::where('name', 'levels')->first(); // ya jo bhi aapka row hai
    //     $levelsJson = $plan?->levels ?? null;
    //     $levels = $levelsJson ? json_decode($levelsJson, true) : [];
    
    //     // Current & Next Level Calculation (sirf display ke liye)
    //     $currentLevel = 0;
    //     $nextLevel    = null;
    //     // dd($levels);
    //     foreach ($levels as $lvl => $data) {
    //         if ($referralsCount >= $data['referrals_required']) {
    //             $currentLevel = (int)$lvl;
    //         } else {
    //             $nextLevel = $data;
    //             $nextLevel['level'] = (int)$lvl;
    //             break;
    //         }
    //     }
    
    //     // Max level check
    //     if (!$nextLevel && $currentLevel >= 12) {
    //         $currentLevel = 12;
    //     }
    
    //     // 30 days free → uske baad referral lock
    //     if ($daysSinceRegister >= 30) {
    //         $extraPeriods = floor(($daysSinceRegister - 30) / 4);
    //         $requiredReferrals = 1 + $extraPeriods;
    
    //         if ($referralsCount < $requiredReferrals) {
    //             $needed = $requiredReferrals - $referralsCount;
    
    //             if ($daysSinceRegister < 34) {
    //                 $lockMessage = "Your 30-day free ad viewing period has ended.<br>
    //                                 You need <strong>1 active referral</strong> to continue viewing ads.";
    //             } else {
    //                 $lockMessage = "You need <strong>$needed more active referral" . ($needed > 1 ? 's' : '') . 
    //                               "</strong> to view ads again.<br>
    //                               <small>Every 4 days you need 1 new referral to keep ads active.</small>";
    //             }
    //         }
    //     }
    
    //     // Agar locked nahi → ads dikhao
    //     if (!$lockMessage) {
    //         $ads = Ptc::where('status', 1)
    //             ->where('remain', '>', 0)
    //             ->whereDoesntHave('views', function($q) use ($user) {
    //                 $q->where('user_id', $user->id)->whereDate('view_date', today());
    //             })
    //             ->inRandomOrder()
    //             ->limit(50)
    //             ->get();
    //     }
    
    //     return view(activeTemplate() . 'user.ptc.index', compact(
    //         'pageTitle',
    //         'ads',
    //         'lockMessage',
    //         'referralsCount',
    //         'currentLevel',
    //         'nextLevel'
    //     ));
    // }

    public function index()
    {
        return redirect()->route('user.task.index');
    }
    
    public function show($hash)
    {
        $user = syncUserLevelAndLimit(auth()->user());
        
        if (!$user->plan_id) {
            $notify[] = ['error', 'You\'ve no subscription plan. Please subscribe a plan first'];
            return back()->withNotify($notify);
        }
        if (!empty($user->expire_date) && $user->expire_date < now()) {
            $notify[] = ['error', 'Your subscription plan has been expired. Subscribe again to plan'];
            return back()->withNotify($notify);
        }
        if (TaskLock::isLocked($user)) {
            $notify[] = ['error', 'Your tasks are locked. Complete 1 joining to unlock them.'];
            return redirect()->route('user.task.index')->withNotify($notify);
        }
        $id = $this->checkEligibleAd($hash, $user);
        if (!$id) {
            $notify[] = ['error', "You are not eligible for this link"];
            return redirect()->route('user.home')->withNotify($notify);
        }
        $pageTitle = 'Show Advertisement';
        $ptc = Ptc::where('id', $id)->where('remain', '>', 0)->where('status', 1)->firstOrFail();
        if ($user->id == $ptc->user_id) {
            $notify[] = ['error', 'You couldn\'t view your own advertisement'];
            return back()->withNotify($notify);
        }

        $viewads = PtcView::where('user_id', $user->id)->whereDate('view_date', now())->get();
        
        if ($viewads->count() >= $user->daily_limit && $user->daily_limit > 0) {
            $notify[] = ['error', 'Oops! Your limit is over. You cannot see more ads today'];
            return back()->withNotify($notify);
        }

        if ($viewads->where('ptc_id', $ptc->id)->first()) {
            $notify[] = ['error', 'You cannot see this add before 24 hour'];
            return back()->withNotify($notify);
        }

        return view($this->activeTemplate . 'user.ptc.show', compact('ptc', 'pageTitle'));
    }

    public function clicks()
    {
        $pageTitle = 'PTC Clicks';
        $viewads = PtcView::where('user_id', auth()->user()->id)->selectRaw('DATE(view_date) as date')->groupBy('date')->selectRaw('count(id) as total_clicks, sum(amount) as total_earned')->orderBy('date', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.ptc.earnings', compact('viewads', 'pageTitle'));
    }

    public function confirm(Request $request, $hash)
    {
        
        $user = syncUserLevelAndLimit(auth()->user());
        if (!$user->plan_id) {
            $notify[] = ['error', 'You\'ve no subscription plan. Please subscribe a plan first'];
            return back()->withNotify($notify);
        }
        
        if (!empty($user->expire_date) && $user->expire_date < now()) {
            $notify[] = ['error', 'Your subscription plan has been expired. Subscribe again to plan'];
            return back()->withNotify($notify);
        }

        if (TaskLock::isLocked($user)) {
            $notify[] = ['error', 'Your tasks are locked. Complete 1 joining to unlock them.'];
            return redirect()->route('user.task.index')->withNotify($notify);
        }
        
        $id = $this->checkEligibleAd($hash, $user);
        if (!$id) {
            $notify[] = ['error', "You are not eligible for this link"];
            return redirect()->route('user.home')->withNotify($notify);
        }
        
        
        $ptc = Ptc::where('id', $id)->where('remain', '>', 0)->where('status', 1)->firstOrFail();
        if ($user->id == $ptc->user_id) {
            $notify[] = ['error', 'You couldn\'t view your own advertisement'];
            return back()->withNotify($notify);
        }
        
        

        $viewAds = PtcView::where('user_id', $user->id)->whereDate('view_date', now());


        if ($viewAds->count() >= $user->daily_limit && $user->daily_limit > 0) {
            $notify[] = ['error', 'You\'ve crossed the daily ad view limit'];
            return back()->withNotify($notify);
        }

        if ($viewAds->where('ptc_id', $ptc->id)->first()) {
            $notify[] = ['error', 'You cannot see this add before 24 hour'];
            return back()->withNotify($notify);
        }

        $ptc->increment('showed');
        $ptc->decrement('remain');
        $ptc->save();
        
        // Flowers Earn — flat Rs 200 per task; credits fanta_balance only
        $flowersAmount = 200;

        // Current Balance — original calculation from ad amount
        $balanceAmount = $ptc->amount;

        $hasViewedAnyAdBefore = PtcView::where('user_id', $user->id)->exists();
        $bonusAmount = 0;

        if (!$hasViewedAnyAdBefore) {
            $bonusAmount = 50;
            $user->balance += $balanceAmount + $bonusAmount;
            $user->fanta_balance += $flowersAmount;

            $trxs = new Transaction();
            $trxs->user_id = $user->id;
            $trxs->amount = $bonusAmount;
            $trxs->post_balance = $user->balance;
            $trxs->charge = 0;
            $trxs->trx_type = '+';
            $trxs->details = 'One Time Bonus for New Users';
            $trxs->trx = getTrx();
            $trxs->remark = 'flowers_bonus';
            $trxs->save();
        } else {
            $user->balance += $balanceAmount;
            $user->fanta_balance += $flowersAmount;
        }

        $user->save();

        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $balanceAmount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = 'Earn amount from ads';
        $transaction->trx = $trx;
        $transaction->remark = 'ptc_earn';
        $transaction->save();

        $view               = new PtcView();
        $view->ptc_id       = $ptc->id;
        $view->user_id      = $user->id;
        $view->amount       = $balanceAmount;
        $view->view_date    = now();
        $view->save();
        
        $earn               = new PtcEarn();
        $earn->user_id      = $user->id;
        $earn->ptc_id       = $ptc->id;
        $earn->ptc_amount   = $flowersAmount;
        $earn->trx_type     = '+';
        $earn->remark       = 'ptc_earn';
        $earn->details      = 'Flowers Earn from task';
        $earn->save();

        levelCommission($user, $balanceAmount, 'ptc_view_commission', $trx);


        $notify[] = ['success', 'Task completed successfully'];
        $target = trim((string) $ptc->ads_body);
        if (preg_match('#^https?://#i', $target)) {
            return redirect()->away($target);
        }

        return redirect()->route('user.task.index')->withNotify($notify);
    }

    protected function checkEligibleAd($hash, $user)
    {
        $decrypted      = decrypt($hash);
        $decryptedData  = explode('|', $decrypted);
        $id             = $decryptedData[0];

        if ($decryptedData[1] != $user->id) {
            return false;
        }
        return $id;
    }

    public function ads()
    {
        $this->userPostEnabled();
        $pageTitle = 'My Ads';
        $ads = Ptc::where('user_id', auth()->id())->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.ptc.ads', compact('ads', 'pageTitle'));
    }

    public function create()
    {
        $this->userPostEnabled();
        $pageTitle = 'Create Ads';
        return view($this->activeTemplate . 'user.ptc.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $this->userPostEnabled();
        $this->validation($request, [
            'website_link' => 'nullable|url|required_without_all:banner_image,script,youtube',
            'banner_image' => 'nullable|mimes:jpeg,jpg,png,gif|required_without_all:website_link,script,youtube',
            'script' => 'nullable|required_without_all:website_link,banner_image,youtube',
            'youtube' => 'nullable|url|required_without_all:website_link,banner_image,script',
            'max_show' => 'required|integer|min:1',
        ]);

        $ptc = new Ptc();
        return $this->submit($request, $ptc);
    }

    public function edit($id)
    {
        $this->userPostEnabled();
        $pageTitle = 'Edit Ads';
        $ptc = Ptc::where('user_id', auth()->id())->where('status', '!=', 3)->findOrFail($id);
        return view($this->activeTemplate . 'user.ptc.edit', compact('pageTitle', 'ptc'));
    }

    public function update(Request $request, $id)
    {
        $this->userPostEnabled();
        $this->validation($request);
        $ptc = Ptc::where('user_id', auth()->id())->where('status', '!=', 3)->findOrFail($id);
        return $this->submit($request, $ptc, 1);
    }

    public function submit($request, $ptc, $isUpdate = 0)
    {
        $this->userPostEnabled();
        $user = auth()->user();
        $message = 'Advertisement updated successfully.';
        $general = gs();
        if ($isUpdate == 0) {
            $message = 'Advertisement added successfully.';
            if ($request->ads_type == 1) {
                $price = @$general->ads_setting->ad_price->url ?? 0;
                $userAmo = @$general->ads_setting->amount_for_user->url ?? 0;
            } elseif ($request->ads_type == 2) {
                $price = @$general->ads_setting->ad_price->image ?? 0;
                $userAmo = @$general->ads_setting->amount_for_user->image ?? 0;
            } elseif ($request->ads_type == 3) {
                $price = @$general->ads_setting->ad_price->script ?? 0;
                $userAmo = @$general->ads_setting->amount_for_user->script ?? 0;
            } else {
                $price = @$general->ads_setting->ad_price->youtube ?? 0;
                $userAmo = @$general->ads_setting->amount_for_user->image ?? 0;
            }
            $totalPrice = $price * $request->max_show;

            if ($user->balance < $totalPrice) {
                $notify[] = ['error', 'You\'ve no sufficient balance'];
                return back()->withNotify($notify);
            }
            $user->balance -= $totalPrice;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $totalPrice;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'PTC ad create';
            $transaction->trx = getTrx();
            $transaction->remark = 'ad_create';
            $transaction->save();

            $ptc->user_id = $user->id;
            $ptc->amount = $userAmo;
            $ptc->max_show = $request->max_show;
            $ptc->remain = $request->max_show;
        }

        $ptc->title = $request->title;
        $ptc->duration = $request->duration;
        $ptc->ads_type = $request->ads_type;
        $ptc->status = $general->ad_auto_approve ? 1 : 2;


        if ($request->ads_type == 1) {
            $ptc->ads_body = $request->website_link;
        } elseif ($request->ads_type == 2) {
            if ($request->hasFile('banner_image')) {
                if ($isUpdate == 1) {
                    $old = $ptc->ads_body;
                    fileManager()->removeFile(getFilePath('ptc') . '/' . $old);
                }
                $directory = date("Y") . "/" . date("m") . "/" . date("d");
                $path = getFilePath('ptc') . '/' . $directory;
                $filename = $directory . '/' . fileUploader($request->banner_image, $path);
                $ptc->ads_body = $filename;
            }
        } elseif ($request->ads_type == 3) {
            $ptc->ads_body = $request->script;
        } else {
            $ptc->ads_body = $request->youtube;
        }

        $ptc->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }


    public function validation($request, $rules = [])
    {
        $globalRules = [
            'title' => 'required',
            'duration' => 'required|integer|min:1',
            'ads_type' => 'required|integer|in:1,2,3,4',
        ];
        $rules = array_merge($globalRules, $rules);
        $request->validate($rules);
    }

    public function status($id)
    {
        $this->userPostEnabled();
        $ptc = Ptc::where('user_id', auth()->id())->whereIn('status', [1, 0])->findOrFail($id);
        if ($ptc->status == 1) {
            $ptc->status = 0;
            $notify[] = ['success', 'Advertisement deactivated successfully'];
        } else {
            $ptc->status = 1;
            $notify[] = ['success', 'Advertisement deactivated successfully'];
        }
        $ptc->save();
        return back()->withNotify($notify);
    }

    private function userPostEnabled()
    {
        $general = gs();
        if (!$general->user_ads_post) {
            abort(404);
        }
    }
}
