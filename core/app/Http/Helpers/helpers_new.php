<?php

use App\Lib\GoogleAuthenticator;
use App\Models\Extension;
use App\Models\Frontend;
use Carbon\Carbon;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\CommissionLog;
use App\Models\GeneralSetting;
use App\Models\Referral;
use App\Models\Transaction;
use App\Notify\Notify;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Plan;
use App\Models\Deposit;

function systemDetails()
{
    $system['name'] = 'ptclab';
    $system['version'] = '3.4';
    $system['build_version'] = '4.2.7';
    return $system;
}

function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function activeTemplate($asset = false)
{
    $general = gs();
    $template = $general->active_template;
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $general = gs();
    $template = $general->active_template;
    return $template;
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $analytics = Extension::where('act', $key)->where('status', 1)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}


function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}


function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/templates/' . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


function getImage($image, $size = null)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}


function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true)
{
    $general = gs();
    $globalShortCodes = [
        'site_name' => $general->site_name,
        'site_currency' => $general->cur_text,
        'currency_symbol' => $general->cur_sym,
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->userColumn = $user->getForeignKey();
    $notify->send();
}

function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}


function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3) $class = 'side-menu--open';
    elseif ($type == 2) $class = 'sidebar-submenu__open';
    else $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            if (request()->route(@$param[0]) == @$param[1]) return $class;
            else return;
        }
        return $class;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}


function gatewayRedirectUrl($type = false)
{
    if ($type) {
        return 'user.deposit.history';
    } else {
        return 'user.deposit';
    }
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}


function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function levelCommission($referee, $amount, $commissionType, $trx)
{
    $general = gs();
    if (!$general->$commissionType) {
        return false;
    }

    $i = 1;
    $level = Referral::where('commission_type', $commissionType)->get();

    $tempReferee = $referee;

    while ($i <= $level->count()) {
        $referer = $tempReferee->refBy;
        if (!$referer) {
            break;
        }

        $plan = $referer->plan;
        if (!$plan) {
            $tempReferee = $referer;
            $i++;
            continue;
        }

        if ($i > $plan->ref_level) {
            $tempReferee = $referer;
            $i++;
            continue;
        }

        $commission = Referral::where('commission_type', $commissionType)->where('level', $i)->first();
        //changes by fade
        // $commission = Referral::where('commission_type', $commissionType)->first();

        if (!$commission) {
            break;
        }

        $com = ($amount * $commission->percent) / 100;
        $referer->balance += $com;
        $referer->save();


        $transactions[] = [
            'user_id' => $referer->id,
            'amount' => $com,
            'post_balance' => $referer->balance,
            'charge' => 0,
            'trx_type' => '+',
            'details' => ordinal($i) . ' level referral commission from ' . $referee->username,
            'remark' => 'referral_commission',
            'trx' => $trx,
            'created_at' => now()
        ];

        $commissionLog[] = [
            'to_id' => $referer->id,
            'from_id' => $referee->id,
            'level' => $i,
            'amount' => $com,
            'details' => ordinal($i) . ' level referral commission from ' . $referee->username,
            'type' => $commissionType,
            'trx' => $trx,
            'created_at' => now()
        ];

        notify($referer, 'REFERRAL_COMMISSION', [
            'amount' => showAmount($com),
            'post_balance' => showAmount($referer->balance),
            'trx' => $trx,
            'level' => ordinal($i),
            'type' => ucfirst(str_replace('_', ' ', $commissionType))
        ]);

        $tempReferee = $referer;
        $i++;
    }
    if (isset($transactions)) {
        Transaction::insert($transactions);
    }
    if (isset($commissionLog)) {
        CommissionLog::insert($commissionLog);
    }
}


//new code by fade

// function levelCommission($referee, $amount, $commissionType, $trx)
// {
//     $general = gs();
//     if (!$general->$commissionType) {
//         return false;
//     }

//     $i = 1;
//     $tempReferee = $referee;
//     $transactions = [];
//     $commissionLog = [];

//     while (true) {
//         $referer = $tempReferee->refBy; // Get the referrer
//         if (!$referer) {
//             break; // No more upper-level referrers
//         }

//         // Define commission rate by level
//         if ($i == 1) {
//             $percent = 25; // First referrer gets 25%
//         } elseif ($i == 2) {
//             $percent = 3; // Second referrer gets 3%
//         } else {
//             $percent = 1; // All next levels get 1%
//         }

//         // Calculate commission amount
//         $com = ($amount * $percent) / 100;

//         // Update referrer's balance
//         $referer->balance += $com;
//         $referer->save();

//         // --- Save transaction record ---
//         $transactions[] = [
//             'user_id' => $referer->id,
//             'amount' => $com,
//             'post_balance' => $referer->balance,
//             'charge' => 0,
//             'trx_type' => '+',
//             'details' => "{$percent}% commission (Level {$i}) from {$referee->username}",
//             'remark' => 'referral_commission',
//             'trx' => $trx,
//             'created_at' => now()
//         ];

//         // --- Save commission log ---
//         $commissionLog[] = [
//             'to_id' => $referer->id,
//             'from_id' => $referee->id,
//             'level' => $i,
//             'amount' => $com,
//             'details' => ordinal($i) . ' level referral commission from ' . $referee->username,
//             'type' => $commissionType,
//             'trx' => $trx,
//             'created_at' => now()
//         ];

//         // --- Optional: Notify the referrer ---
//         notify($referer, 'REFERRAL_COMMISSION', [
//             'amount' => showAmount($com),
//             'post_balance' => showAmount($referer->balance),
//             'trx' => $trx,
//             'level' => ordinal($i),
//             'type' => ucfirst(str_replace('_', ' ', $commissionType))
//         ]);

//         // Move upward in referral chain (infinite loop support)
//         $tempReferee = $referer;
//         $i++;
//     }

//     // --- Insert all at once for performance ---
//     if (!empty($transactions)) {
//         \App\Models\Transaction::insert($transactions);
//     }
//     if (!empty($commissionLog)) {
//         \App\Models\CommissionLog::insert($commissionLog);
//     }
// }





function ordinal($number)
{
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
        return $number . 'th';
    } else {
        return $number . $ends[$number % 10];
    }
}

function gs()
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    return $general;
}

if (!function_exists('updateReferrerLevelAndLimit')) {
    function updateReferrerLevelAndLimit($newUserId)
    {
        $newUser = User::find($newUserId);
        if (!$newUser || !$newUser->ref_by) return;

        $referrer = User::find($newUser->ref_by);
        if (!$referrer || !$referrer->plan_id) return;

        $plan = Plan::find($referrer->plan_id);
        if (!$plan) return;

        $levels = json_decode($plan->levels, true);

        // Count only referrals who made a deposit with status = 1
        $referralsCount = User::where('ref_by', $referrer->id)
            ->whereHas('deposits', function ($query) {
                $query->where('status', 1);
            })
            ->count();

        // Default joining thresholds (Level 0=0, 1=3, 2=12, 3=27, 4=150, 5=220, 6=370, 7=500, 8=1000)
        $defaultLevels = [
            1 => ['referrals_required' => 3],
            2 => ['referrals_required' => 12],
            3 => ['referrals_required' => 27],
            4 => ['referrals_required' => 150],
            5 => ['referrals_required' => 220],
            6 => ['referrals_required' => 370],
            7 => ['referrals_required' => 500],
            8 => ['referrals_required' => 1000],
        ];

        if (!$levels || !is_array($levels)) {
            $levels = $defaultLevels;
        }

        $newLevel = 0;
        // Task limit is fixed per PKG plan — not changed by level
        $newDailyLimit = $plan->daily_limit;

        foreach ($levels as $level => $data) {
            if (
                isset($data['referrals_required']) &&
                $referralsCount >= (int)$data['referrals_required']
            ) {
                $newLevel = (int)$level;
            }
        }

        // Cap at level 8
        if ($newLevel > 8) $newLevel = 8;

        if ($referrer->level != $newLevel || $referrer->daily_limit != $newDailyLimit) {
            $referrer->update([
                'level'       => $newLevel,
                'daily_limit' => $newDailyLimit,
            ]);
        }
    }
}

if (!function_exists('updateUsersLevelByPlan')) {
    function updateUsersLevelByPlan($planId)
    {
        $plan = Plan::find($planId);
        if (!$plan) return;

        $levels = json_decode($plan->levels, true);

        // Default joining thresholds (Level 0=0, 1=3, 2=12, 3=27, 4=150, 5=220, 6=370, 7=500, 8=1000)
        $defaultLevels = [
            1 => ['referrals_required' => 3],
            2 => ['referrals_required' => 12],
            3 => ['referrals_required' => 27],
            4 => ['referrals_required' => 150],
            5 => ['referrals_required' => 220],
            6 => ['referrals_required' => 370],
            7 => ['referrals_required' => 500],
            8 => ['referrals_required' => 1000],
        ];

        if (!$levels || !is_array($levels)) {
            $levels = $defaultLevels;
        }

        $users = User::where('plan_id', $planId)->get();

        foreach ($users as $user) {
            // Count only referrals with a deposit where status = 1
            $referralsCount = User::where('ref_by', $user->id)
                ->whereHas('deposits', function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $newLevel = 0;
            // Task limit is fixed per PKG — not changed by level
            $newDailyLimit = $plan->daily_limit;

            foreach ($levels as $level => $data) {
                if (
                    isset($data['referrals_required']) &&
                    $referralsCount >= (int)$data['referrals_required']
                ) {
                    $newLevel = (int)$level;
                }
            }

            // Cap at level 8
            if ($newLevel > 8) $newLevel = 8;

            if ($user->level != $newLevel || $user->daily_limit != $newDailyLimit) {
                $user->update([
                    'level'       => $newLevel,
                    'daily_limit' => $newDailyLimit,
                ]);
            }
        }
    }
}