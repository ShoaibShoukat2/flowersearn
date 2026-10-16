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
use App\Models\Reffer;
use App\Models\Transaction;
use App\Notify\Notify;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\UserIpLock;
use App\Models\Plan;
use App\Models\Deposit;
use App\Models\OfficialChannel;

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
    try {
        $general = gs();
        $template = $general->active_template;
        if ($asset) return 'assets/templates/' . $template . '/';
        return 'templates.' . $template . '.';
    } catch (\Exception $e) {
        \Log::error('activeTemplate() function error: ' . $e->getMessage());
        // Return default template
        if ($asset) return 'assets/templates/basic/';
        return 'templates.basic.';
    }
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

function parseAdminDate(?string $value): ?Carbon
{
    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    foreach (['m/d/Y', 'n/j/Y', 'Y-m-d', 'd/m/Y'] as $format) {
        try {
            $date = Carbon::createFromFormat($format, $value);
            if ($date instanceof Carbon) {
                return $date;
            }
        } catch (\Throwable $e) {
        }
    }

    return null;
}

function parseAdminDateRange(?string $value): ?array
{
    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    $parts = preg_split('/\s+-\s+/', $value);
    $start = parseAdminDate(trim((string) ($parts[0] ?? '')));
    if (!$start) {
        return null;
    }

    $end = parseAdminDate(trim((string) ($parts[1] ?? ''))) ?: $start->copy();

    return [$start->copy()->startOfDay(), $end->copy()->endOfDay()];
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

function referralPercentForLevel($commissionType, int $level, $planId = null): ?float
{
    if ($planId && Schema::hasTable('reffers')) {
        $reffer = Reffer::where('plan_id', $planId)->where('type', $commissionType)->first();
        if ($reffer) {
            $commisions = $reffer->commision;
            if (is_string($commisions)) {
                $commisions = json_decode($commisions, true);
            }
            if (is_array($commisions) && array_key_exists($level - 1, $commisions) && $commisions[$level - 1] !== '' && $commisions[$level - 1] !== null) {
                return (float) $commisions[$level - 1];
            }
        }
    }

    $commission = Referral::where('commission_type', $commissionType)->where('level', $level)->first();
    return $commission ? (float) $commission->percent : null;
}

function referralLevelCount($commissionType, $planId = null): int
{
    if ($planId && Schema::hasTable('reffers')) {
        $reffer = Reffer::where('plan_id', $planId)->where('type', $commissionType)->first();
        if ($reffer) {
            $commisions = $reffer->commision;
            if (is_string($commisions)) {
                $commisions = json_decode($commisions, true);
            }
            if (is_array($commisions) && count($commisions) > 0) {
                return count($commisions);
            }
        }
    }

    return (int) Referral::where('commission_type', $commissionType)->count();
}

function levelCommission($referee, $amount, $commissionType, $trx)
{
    $planId = $referee->plan_id ?? null;
    $hasPlanRates = $planId && Schema::hasTable('reffers') && Reffer::where('plan_id', $planId)->where('type', $commissionType)->exists();
    $general = gs();

    if (!$hasPlanRates && empty($general->$commissionType)) {
        return false;
    }

    if ($trx && CommissionLog::where('trx', $trx)->where('from_id', $referee->id)->exists()) {
        return false;
    }

    $maxLevel = referralLevelCount($commissionType, $planId);
    if ($maxLevel < 1) {
        return false;
    }

    $i = 1;
    $tempReferee = $referee;
    $transactions = [];
    $commissionLog = [];
    $isJoiningCommission = $commissionType === 'plan_subscribe_commission';

    while ($i <= $maxLevel) {
        $referer = $tempReferee->refBy;
        if (!$referer) {
            break;
        }

        $refererPlan = $referer->plan;
        if (!$isJoiningCommission) {
            if (!$refererPlan) {
                $tempReferee = $referer;
                $i++;
                continue;
            }

            if ($refererPlan->ref_level && $i > (int) $refererPlan->ref_level) {
                $tempReferee = $referer;
                $i++;
                continue;
            }
        }

        $percent = referralPercentForLevel($commissionType, $i, $planId);
        if ($percent === null) {
            break;
        }

        $com = ($amount * $percent) / 100;
        if ($com <= 0) {
            $tempReferee = $referer;
            $i++;
            continue;
        }

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

        try {
            notify($referer, 'REFERRAL_COMMISSION', [
                'amount' => showAmount($com),
                'post_balance' => showAmount($referer->balance),
                'trx' => $trx,
                'level' => ordinal($i),
                'type' => ucfirst(str_replace('_', ' ', $commissionType))
            ]);
        } catch (\Throwable $e) {
            Log::error('Referral commission notify failed: ' . $e->getMessage(), [
                'referer_id' => $referer->id,
                'from_id' => $referee->id,
                'trx' => $trx,
            ]);
        }

        $tempReferee = $referer;
        $i++;
    }

    if ($transactions) {
        Transaction::insert($transactions);
    }
    if ($commissionLog) {
        CommissionLog::insert($commissionLog);
    }

    return !empty($commissionLog);
}

function resolvePlanForDeposit($deposit, $user = null, $explicitPlanId = null): ?Plan
{
    $ids = [];
    if ($explicitPlanId) {
        $ids[] = (int) $explicitPlanId;
    }
    if (!empty($deposit->plan_id)) {
        $ids[] = (int) $deposit->plan_id;
    }

    $user = $user ?: User::find($deposit->user_id);
    if ($user && preg_match('/^(\d+)[rpa]$/i', trim((string) $user->pv), $match)) {
        $ids[] = (int) $match[1];
    }

    foreach (array_unique(array_filter($ids)) as $id) {
        $plan = Plan::find($id);
        if ($plan) {
            return $plan;
        }
    }

    $amount = (float) $deposit->amount;
    $plan = Plan::where('price', $deposit->amount)->first();
    if ($plan) {
        return $plan;
    }

    return Plan::all()->first(function ($item) use ($amount) {
        return abs((float) $item->price - $amount) < 0.011;
    });
}

function assignPlanToUser($user, Plan $plan): void
{
    $user->plan_id = $plan->id;
    if (!empty($plan->validity)) {
        $user->expire_date = now()->addDays((int) $plan->validity);
    }
    $user->pv = $plan->id . 'a';
    repairSignupPassword($user);
    $user->save();
    syncUserLevelAndLimit($user);
}

if (!function_exists('ensureSignupPasswordColumn')) {
    function ensureSignupPasswordColumn(): bool
    {
        static $ready = null;
        if ($ready !== null) {
            return $ready;
        }

        try {
            if (Schema::hasTable('users') && !Schema::hasColumn('users', 'signup_password')) {
                Schema::table('users', function ($table) {
                    $table->text('signup_password')->nullable();
                });
            }
            $ready = Schema::hasColumn('users', 'signup_password');
        } catch (\Throwable $e) {
            Log::error('Unable to store signup password column: ' . $e->getMessage());
            $ready = false;
        }

        return $ready;
    }
}

if (!function_exists('storeSignupPassword')) {
    function storeSignupPassword($user, string $plain): void
    {
        if ($plain === '' || !$user || !ensureSignupPasswordColumn()) {
            return;
        }

        $user->signup_password = encrypt($plain);
    }
}

if (!function_exists('signupPasswordFor')) {
    function signupPasswordFor($user): ?string
    {
        if (!$user || empty($user->signup_password) || !Schema::hasColumn('users', 'signup_password')) {
            return null;
        }

        try {
            $plain = decrypt($user->signup_password);
        } catch (\Throwable $e) {
            return null;
        }

        return is_string($plain) && $plain !== '' ? $plain : null;
    }
}

if (!function_exists('repairSignupPassword')) {
    function repairSignupPassword($user): void
    {
        if (!$user) {
            return;
        }

        $plain = signupPasswordFor($user);
        if ($plain === null) {
            return;
        }

        if (!Hash::check($plain, (string) $user->password)) {
            $user->password = Hash::make($plain);
            if ($user->isDirty('password')) {
                $user->save();
            }
        }
    }
}

if (!function_exists('findUserByLogin')) {
    function findUserByLogin(?string $login): ?User
    {
        $login = trim((string) $login);
        if ($login === '') {
            return null;
        }

        $normalized = mb_strtolower($login);
        $user = User::whereRaw('LOWER(email) = ?', [$normalized])->first();
        if ($user) {
            return $user;
        }

        $user = User::whereRaw('LOWER(username) = ?', [$normalized])->first();
        if ($user) {
            return $user;
        }

        $digits = preg_replace('/\D+/', '', $login);
        if (strlen($digits) < 8) {
            return null;
        }

        return User::where('mobile', $login)
            ->orWhere('mobile', $digits)
            ->orWhere('mobile', 'like', '%' . $digits)
            ->orderByDesc('id')
            ->first();
    }
}

if (!function_exists('passwordMatchesAccount')) {
    function passwordMatchesAccount($user, string $password): bool
    {
        if (!$user || $password === '') {
            return false;
        }

        if (Hash::check($password, (string) $user->password)) {
            return true;
        }

        $stored = signupPasswordFor($user);
        if ($stored !== null && hash_equals($stored, $password)) {
            $user->password = Hash::make($password);
            $user->save();
            return true;
        }

        return false;
    }
}

function completeJoiningApproval($deposit, $plan = null, $isManual = true): ?Plan
{
    $deposit = Deposit::find($deposit->id);
    if (!$deposit) {
        return null;
    }

    $user = User::find($deposit->user_id);
    if (!$user) {
        return null;
    }

    $plan = $plan ?: resolvePlanForDeposit($deposit, $user);
    if ($plan) {
        if (Schema::hasColumn('deposits', 'plan_id') && (int) $deposit->plan_id !== (int) $plan->id) {
            $deposit->plan_id = $plan->id;
            $deposit->save();
        }
        assignPlanToUser($user, $plan);
    }

    \App\Http\Controllers\Gateway\PaymentController::userDataUpdateFirstDeposit($deposit, $isManual);
    $user->refresh();

    if ($plan) {
        updateReferrerLevelAndLimit($user->id);
    }

    return $plan;
}


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
    try {
        $general = Cache::get('GeneralSetting');
        if (!$general) {
            $general = GeneralSetting::first();
            Cache::put('GeneralSetting', $general);
        }
        return $general;
    } catch (\Exception $e) {
        \Log::error('gs() function error: ' . $e->getMessage());
        return (object)[
            'site_name' => 'SnapsTubes',
            'cur_text' => 'USD',
            'cur_sym' => '$',
            'active_template' => 'basic',
            'force_ssl' => false,
            'whatsapp_community_link' => null,
            'whatsapp_channel_link' => null,
        ];
    }
}

if (!function_exists('isAccountRejected')) {
    function isAccountRejected($user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return false;
        }
        if ((int) $user->plan_id !== 0) {
            return false;
        }
        if (preg_match('/^\d+[pa]$/i', trim((string) $user->pv))) {
            return false;
        }
        if (trim((string) $user->pv) !== 're') {
            return false;
        }

        $latestDeposit = Deposit::where('user_id', $user->id)
            ->whereIn('status', [1, 2, 3])
            ->latest('id')
            ->first();

        if ($latestDeposit && in_array((int) $latestDeposit->status, [1, 2], true)) {
            return false;
        }

        return true;
    }
}

if (!function_exists('whatsappCommunityLink')) {
    function whatsappCommunityLink(): ?string
    {
        $link = trim((string) (gs()->whatsapp_community_link ?? ''));
        return $link !== '' ? $link : null;
    }
}

if (!function_exists('whatsappChannelLink')) {
    function whatsappChannelLink(): ?string
    {
        $link = trim((string) (gs()->whatsapp_channel_link ?? ''));
        return $link !== '' ? $link : null;
    }
}

if (!function_exists('ensureOfficialChannelsTable')) {
    function ensureOfficialChannelsTable(): void
    {
        static $done = false;
        if ($done) {
            return;
        }

        try {
            if (!Schema::hasTable('official_channels')) {
                Schema::create('official_channels', function ($table) {
                    $table->id();
                    $table->string('title');
                    $table->string('purpose')->nullable();
                    $table->text('description')->nullable();
                    $table->string('link', 500);
                    $table->string('icon')->nullable();
                    $table->string('image')->nullable();
                    $table->boolean('status')->default(true);
                    $table->unsignedInteger('sort_order')->default(0);
                    $table->timestamps();
                });
                seedOfficialChannelsFromLegacyLinks();
            } elseif (!Schema::hasColumn('official_channels', 'image')) {
                Schema::table('official_channels', function ($table) {
                    $table->string('image')->nullable();
                });
            }

            $done = true;
        } catch (\Throwable $e) {
            Log::error('Unable to prepare official channels table: ' . $e->getMessage());
        }
    }
}

if (!function_exists('seedOfficialChannelsFromLegacyLinks')) {
    function seedOfficialChannelsFromLegacyLinks(): void
    {
        if (OfficialChannel::query()->exists()) {
            return;
        }

        $general = gs();
        $now = now();
        $rows = [];
        $community = trim((string) ($general->whatsapp_community_link ?? ''));
        $channel = trim((string) ($general->whatsapp_channel_link ?? ''));

        if ($community !== '') {
            $rows[] = [
                'title' => 'Flowers Official Community',
                'purpose' => 'Community | Guidelines | Support',
                'description' => 'Official community group for members.',
                'link' => $community,
                'icon' => 'fab fa-whatsapp',
                'status' => 1,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($channel !== '') {
            $rows[] = [
                'title' => 'Flowers Official Channel',
                'purpose' => 'Trainings | Guidelines | Earning Tips',
                'description' => 'Trainings, working guidelines, important instructions and earning tips.',
                'link' => $channel,
                'icon' => 'fas fa-broadcast-tower',
                'status' => 1,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows) {
            OfficialChannel::insert($rows);
        }
    }
}

if (!function_exists('memberDisplayName')) {
    function memberDisplayName($user): string
    {
        if (!$user) {
            return 'Member';
        }

        $name = trim(User::composeDisplayName(
            $user->first_name ?? $user->firstname ?? '',
            $user->last_name ?? $user->lastname ?? '',
            ''
        ));

        if ($name === '' || strcasecmp($name, (string) ($user->username ?? '')) === 0) {
            $emailName = strstr((string) ($user->email ?? ''), '@', true);
            return $emailName !== false && $emailName !== '' ? $emailName : 'Member';
        }

        return $name;
    }
}

if (!function_exists('officialChannels')) {
    function officialChannels(bool $enabledOnly = true)
    {
        ensureOfficialChannelsTable();

        try {
            $channels = OfficialChannel::orderBy('sort_order')->orderBy('id')->get();
        } catch (\Throwable $e) {
            Log::error('Unable to load official channels: ' . $e->getMessage());
            return collect();
        }

        if (!$enabledOnly) {
            return $channels;
        }

        return $channels->where('status', 1)->filter(function ($channel) {
            return trim((string) $channel->link) !== '';
        })->values();
    }
}

if (!function_exists('officialChannelImageUrl')) {
    function officialChannelImageUrl($channel): ?string
    {
        $file = trim((string) ($channel->image ?? ''));
        if ($file === '') {
            return null;
        }

        return asset(getFilePath('channel') . '/' . $file);
    }
}

if (!function_exists('whatsappChatLink')) {
    function whatsappChatLink(?string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $number);
        if ($digits === '') {
            return null;
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = '92' . substr($digits, 1);
        }

        return 'https://wa.me/' . $digits;
    }
}

if (!function_exists('botimOpenLink')) {
    function botimOpenLink(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        $digits = preg_replace('/\D+/', '', $value);
        if ($digits === '') {
            return null;
        }

        return 'https://botim.me/' . $digits;
    }
}

if (!function_exists('defaultJoiningLevels')) {
    function defaultJoiningLevels(): array
    {
        return [
            1 => ['referrals_required' => 3],
            2 => ['referrals_required' => 12],
            3 => ['referrals_required' => 27],
            4 => ['referrals_required' => 150],
            5 => ['referrals_required' => 220],
            6 => ['referrals_required' => 370],
            7 => ['referrals_required' => 500],
            8 => ['referrals_required' => 1000],
        ];
    }
}

if (!function_exists('normalizePlanLevels')) {
    function normalizePlanLevels($levels): array
    {
        if (is_string($levels)) {
            $levels = json_decode($levels, true);
        }

        if (!is_array($levels)) {
            $levels = [];
        }

        $canonical = defaultJoiningLevels();
        $normalized = [];

        foreach ($canonical as $level => $data) {
            $stored = $levels[$level] ?? $levels[(string) $level] ?? [];
            if (!is_array($stored)) {
                $stored = [];
            }

            $row = [
                'referrals_required' => (int) $data['referrals_required'],
            ];

            if (isset($stored['daily_limit']) && $stored['daily_limit'] !== '') {
                $row['daily_limit'] = $stored['daily_limit'];
            }

            $normalized[$level] = $row;
        }

        return $normalized;
    }
}

if (!function_exists('successfulJoiningCount')) {
    function successfulJoiningCount($userId): int
    {
        return User::where('ref_by', $userId)
            ->whereHas('deposits', function ($query) {
                $query->where('status', 1);
            })
            ->count();
    }
}

if (!function_exists('levelFromJoinings')) {
    function levelFromJoinings(int $joinings, $levels = null): int
    {
        $levels = normalizePlanLevels($levels);
        $level = 0;
        foreach ($levels as $lvl => $data) {
            if (isset($data['referrals_required']) && $joinings >= (int) $data['referrals_required']) {
                $level = (int) $lvl;
            }
        }
        return min($level, 8);
    }
}

if (!function_exists('dailyLimitForLevel')) {
    function dailyLimitForLevel($plan, int $level): int
    {
        $fallback = max(0, (int) ($plan->daily_limit ?? 0));
        if (!$plan || $level < 1) {
            return $fallback;
        }

        $levels = normalizePlanLevels($plan->levels ?? null);
        $row = $levels[min($level, 8)] ?? null;

        if (is_array($row) && isset($row['daily_limit']) && $row['daily_limit'] !== '' && $row['daily_limit'] !== null) {
            $limit = (int) round((float) $row['daily_limit']);
            if ($limit > $fallback) {
                return $limit;
            }
        }

        return $fallback;
    }
}

if (!function_exists('syncUserLevelAndLimit')) {
    function syncUserLevelAndLimit($user)
    {
        if (!$user || empty($user->plan_id)) {
            return $user;
        }

        $plan = Plan::find($user->plan_id);
        if (!$plan) {
            return $user;
        }

        $levels = normalizePlanLevels($plan->levels);
        $newLevel = levelFromJoinings(successfulJoiningCount($user->id), $levels);
        $newDailyLimit = dailyLimitForLevel($plan, $newLevel);

        if ((int) $user->level !== $newLevel || (int) $user->daily_limit !== $newDailyLimit) {
            $user->level = $newLevel;
            $user->daily_limit = $newDailyLimit;
            $user->save();
        }

        return $user;
    }
}

if (!function_exists('persistCanonicalPlanLevels')) {
    function persistCanonicalPlanLevels($plan): array
    {
        $levels = normalizePlanLevels($plan ? $plan->levels : null);
        if (!$plan) {
            return $levels;
        }

        $stored = $plan->levels;
        if (is_string($stored)) {
            $stored = json_decode($stored, true);
        }

        $joiningMismatch = false;
        foreach ($levels as $lvl => $data) {
            $existing = is_array($stored) ? ($stored[$lvl] ?? $stored[(string) $lvl] ?? null) : null;
            if (!is_array($existing) || (int) ($existing['referrals_required'] ?? 0) !== (int) $data['referrals_required']) {
                $joiningMismatch = true;
                break;
            }
        }

        if ($joiningMismatch) {
            $plan->levels = $levels;
            $plan->save();
        }

        return $levels;
    }
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

        syncUserLevelAndLimit($referrer);
    }
}

if (!function_exists('updateUsersLevelByPlan')) {
    function updateUsersLevelByPlan($planId)
    {
        $plan = Plan::find($planId);
        if (!$plan) return;

        $users = User::where('plan_id', $planId)->get();

        foreach ($users as $user) {
            syncUserLevelAndLimit($user);
        }
    }
}

function applyAdminTrxUserSearch($query, $search, $trxColumn = 'trx')
{
    if (!$search) {
        return $query;
    }

    return $query->where(function ($q) use ($search, $trxColumn) {
        $q->where($trxColumn, 'like', "%$search%")
            ->orWhereHas('user', function ($user) use ($search) {
                $user->where('username', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
    });
}

if (!function_exists('areTasksLocked')) {
    function areTasksLocked($user = null): bool
    {
        $user = $user ?? auth()->user();
        return $user ? \App\Lib\TaskLock::isLocked($user) : false;
    }
}

if (!function_exists('getTaskLockStatus')) {
    function getTaskLockStatus($user = null): array
    {
        $user = $user ?? auth()->user();
        return $user
            ? \App\Lib\TaskLock::status($user)
            : ['locked' => false, 'phase' => 'unlocked'];
    }
}

if (!function_exists('withdrawMinimumFor')) {
    function withdrawMinimumFor($user = null, $exceptId = null): array
    {
        $firstMin = 50;
        $nextMin  = 500;
        $user = $user ?? auth()->user();

        if (!$user) {
            return ['is_first' => true, 'min' => $firstMin];
        }

        $query = \App\Models\Withdrawal::where('user_id', $user->id)
            ->whereIn('status', [1, 2, 3]);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        $isFirst = !$query->exists();

        return [
            'is_first' => $isFirst,
            'min'      => $isFirst ? $firstMin : $nextMin,
        ];
    }
}

if (!function_exists('isTransactionIdField')) {
    function isTransactionIdField($field): bool
    {
        $name = strtolower(trim((($field->name ?? '') . ' ' . ($field->label ?? ''))));
        return (bool) preg_match('/transaction|trx|trxn|\btid\b/', $name);
    }
}

if (!function_exists('manualFieldKind')) {
    function manualFieldKind($field): string
    {
        if (($field->type ?? '') === 'file') {
            return 'file';
        }

        $name = strtolower(trim((($field->name ?? '') . ' ' . ($field->label ?? ''))));
        if (preg_match('/number|mobile|phone|whatsapp|sender.?no/', $name)) {
            return 'number';
        }
        if (preg_match('/name/', $name)) {
            return 'name';
        }

        return 'text';
    }
}

if (!function_exists('manualFieldPlaceholder')) {
    function manualFieldPlaceholder($field): string
    {
        $kind = manualFieldKind($field);
        if ($kind === 'name') {
            return 'یہاں رقم بھیجنے والے کا نام لکھیں';
        }
        if ($kind === 'number') {
            return 'یہاں وہ نمبر لکھیں جس سے رقم بھیجی گئی ہے';
        }

        return (string) ($field->name ?? '');
    }
}

if (!function_exists('manualFieldOptions')) {
    function manualFieldOptions($field): array
    {
        $options = is_object($field) ? ($field->options ?? []) : [];
        if (is_string($options)) {
            $options = preg_split('/\s*,\s*/', $options, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        } elseif (is_object($options)) {
            $options = array_values((array) $options);
        } elseif (!is_array($options)) {
            $options = [];
        }

        return array_values($options);
    }
}

if (!function_exists('manualFieldExtensions')) {
    function manualFieldExtensions($field): string
    {
        $extensions = is_object($field) ? ($field->extensions ?? '') : '';
        if (is_array($extensions) || is_object($extensions)) {
            $extensions = implode(',', (array) $extensions);
        }
        $extensions = trim((string) $extensions);
        if ($extensions === '' || strtolower($extensions) === 'null') {
            return 'jpg,jpeg,png,webp';
        }

        return $extensions;
    }
}

if (!function_exists('prepareManualDepositFields')) {
    function prepareManualDepositFields($formData): array
    {
        if (is_string($formData)) {
            $decoded = json_decode($formData);
            $formData = (is_array($decoded) || is_object($decoded)) ? $decoded : [];
        }
        if ($formData instanceof \Illuminate\Support\Collection) {
            $formData = $formData->all();
        }
        if (!is_array($formData) && !is_object($formData)) {
            $formData = [];
        }

        $fields = [];
        foreach ($formData as $field) {
            if (is_array($field)) {
                $field = (object) $field;
            }
            if (!is_object($field) || isTransactionIdField($field)) {
                continue;
            }
            $fields[] = $field;
        }

        $kinds = array_map('manualFieldKind', $fields);

        return [
            'fields' => $fields,
            'needs_sender_name' => !in_array('name', $kinds, true),
            'needs_sender_number' => !in_array('number', $kinds, true),
            'needs_screenshot' => !in_array('file', $kinds, true),
        ];
    }
}

if (!function_exists('depositDetailItems')) {
    function depositDetailItems($deposit): array
    {
        $details = $deposit->detail ?? null;
        if (is_string($details)) {
            $details = json_decode($details);
        }
        if (!$details) {
            return [];
        }

        $items = [];
        foreach ($details as $val) {
            if (!is_object($val)) {
                continue;
            }
            $items[] = $val;
        }

        return $items;
    }
}

if (!function_exists('depositProofFiles')) {
    function depositProofFiles($deposit): array
    {
        $files = [];
        foreach (depositDetailItems($deposit) as $val) {
            if (($val->type ?? '') !== 'file' || empty($val->value)) {
                continue;
            }
            $relative = getFilePath('verify') . '/' . $val->value;
            $files[] = [
                'name' => $val->name ?? 'Payment Screenshot',
                'url' => asset($relative),
                'download' => route('admin.download.attachment', encrypt($relative)),
            ];
        }

        return $files;
    }
}

if (!function_exists('depositMatchesPlan')) {
    function depositMatchesPlan($deposit, $plan): bool
    {
        if (!$plan) {
            return false;
        }

        if (Schema::hasColumn('deposits', 'plan_id') && !empty($deposit->plan_id)) {
            return (int) $deposit->plan_id === (int) $plan->id;
        }

        return abs((float) $deposit->amount - (float) $plan->price) < 0.011;
    }
}

if (!function_exists('constrainDepositToPlan')) {
    function constrainDepositToPlan($query, $plan)
    {
        if (!$plan) {
            return $query;
        }

        $price = $plan->price;
        $id = $plan->id;

        if (!Schema::hasColumn('deposits', 'plan_id')) {
            return $query->where('amount', $price);
        }

        return $query->where(function ($outer) use ($id, $price) {
            $outer->where('plan_id', $id)
                ->orWhere(function ($inner) use ($price) {
                    $inner->where(function ($missing) {
                        $missing->whereNull('plan_id')->orWhere('plan_id', 0);
                    })->where('amount', $price);
                });
        });
    }
}

if (!function_exists('approvedPlanForDeposit')) {
    function approvedPlanForDeposit($deposit): ?Plan
    {
        static $plans = null;
        if ($plans === null) {
            $plans = Plan::orderBy('price')->get();
        }

        if (Schema::hasColumn('deposits', 'plan_id') && !empty($deposit->plan_id)) {
            $matched = $plans->firstWhere('id', (int) $deposit->plan_id);
            if ($matched) {
                return $matched;
            }
        }

        $amount = (float) $deposit->amount;
        return $plans->first(function ($plan) use ($amount) {
            return abs((float) $plan->price - $amount) < 0.011;
        });
    }
}

if (!function_exists('ensurePlanUpgradesTable')) {
    function ensurePlanUpgradesTable(): bool
    {
        static $ready = null;
        if ($ready !== null) {
            return $ready;
        }

        try {
            if (!Schema::hasTable('plan_upgrades')) {
                Schema::create('plan_upgrades', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('from_plan_id')->nullable();
                    $table->unsignedBigInteger('to_plan_id');
                    $table->decimal('amount', 28, 8)->default(0);
                    $table->string('sender_name')->nullable();
                    $table->string('sender_number')->nullable();
                    $table->string('screenshot')->nullable();
                    $table->unsignedInteger('method_code')->nullable();
                    $table->string('trx', 40)->nullable();
                    $table->tinyInteger('status')->default(2);
                $table->text('admin_feedback')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->boolean('commission_given')->default(false);
                $table->decimal('commission_amount', 28, 8)->default(0);
                $table->unsignedBigInteger('upliner_id')->nullable();
                $table->timestamps();
                $table->index(['status', 'created_at']);
                $table->index('user_id');
            });
            }

            if (Schema::hasTable('plan_upgrades')) {
                $upgradeColumns = [
                    'commission_given' => function ($table) {
                        $table->boolean('commission_given')->default(false);
                    },
                    'commission_amount' => function ($table) {
                        $table->decimal('commission_amount', 28, 8)->default(0);
                    },
                    'upliner_id' => function ($table) {
                        $table->unsignedBigInteger('upliner_id')->nullable();
                    },
                ];
                foreach ($upgradeColumns as $column => $callback) {
                    if (!Schema::hasColumn('plan_upgrades', $column)) {
                        Schema::table('plan_upgrades', $callback);
                    }
                }
            }

            $ready = Schema::hasTable('plan_upgrades');
        } catch (\Throwable $e) {
            Log::error('Unable to prepare plan upgrades table: ' . $e->getMessage());
            $ready = false;
        }

        return $ready;
    }
}

if (!function_exists('ensureDepositChannelJoinColumn')) {
    function ensureDepositChannelJoinColumn(): void
    {
        static $done = false;
        if ($done) {
            return;
        }

        try {
            if (Schema::hasTable('deposits') && !Schema::hasColumn('deposits', 'channels_joined_at')) {
                Schema::table('deposits', function ($table) {
                    $table->timestamp('channels_joined_at')->nullable();
                });
            }
            $done = true;
        } catch (\Throwable $e) {
            Log::error('Unable to add channels_joined_at: ' . $e->getMessage());
        }
    }
}

if (!function_exists('ensureContactAdminsTable')) {
    function ensureContactAdminsTable(): void
    {
        static $done = false;
        if ($done) {
            return;
        }

        try {
            if (!Schema::hasTable('contact_admins')) {
                Schema::create('contact_admins', function ($table) {
                    $table->id();
                    $table->string('name');
                    $table->string('category');
                    $table->text('instructions')->nullable();
                    $table->string('contact_number')->nullable();
                    $table->string('whatsapp_number')->nullable();
                    $table->string('bot_link', 500)->nullable();
                    $table->string('contact_type', 20)->default('whatsapp');
                    $table->timestamps();
                });
            } elseif (!Schema::hasColumn('contact_admins', 'bot_link')) {
                Schema::table('contact_admins', function ($table) {
                    $table->string('bot_link', 500)->nullable();
                });
            }
            $done = true;
        } catch (\Throwable $e) {
            Log::error('Unable to prepare contact admins table: ' . $e->getMessage());
        }
    }
}

if (!function_exists('ipAccountLimitMessage')) {
    function ipAccountLimitMessage(): string
    {
        return 'This IP address already has an account. Only one account can register or sign in from the same IP.';
    }
}

if (!function_exists('accountLockIp')) {
    function accountLockIp(): ?string
    {
        $ip = trim((string) getRealIP());
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return null;
        }

        return $ip;
    }
}

if (!function_exists('ensureUserIpLocks')) {
    function ensureUserIpLocks(): void
    {
        static $done = false;
        if ($done) {
            return;
        }

        try {
            $created = false;
            if (!Schema::hasTable('user_ip_locks')) {
                Schema::create('user_ip_locks', function ($table) {
                    $table->id();
                    $table->string('ip', 45)->unique();
                    $table->unsignedBigInteger('user_id')->index();
                    $table->timestamps();
                });
                $created = true;
            }

            if ($created) {
                backfillUserIpLocks();
            }
            $done = true;
        } catch (\Throwable $e) {
            Log::error('Unable to prepare user IP locks: ' . $e->getMessage());
        }
    }
}

if (!function_exists('backfillUserIpLocks')) {
    function backfillUserIpLocks(): void
    {
        if (!Schema::hasTable('user_ip_locks') || !Schema::hasTable('user_logins')) {
            return;
        }

        $ids = DB::table('user_logins')
            ->selectRaw('MIN(id) as id')
            ->whereNotNull('user_ip')
            ->where('user_ip', '!=', '')
            ->groupBy('user_ip')
            ->pluck('id');

        foreach ($ids->chunk(200) as $chunk) {
            $logins = DB::table('user_logins')->whereIn('id', $chunk->all())->get();
            $now = now();
            $inserts = [];

            foreach ($logins as $login) {
                $ip = trim((string) $login->user_ip);
                if (!filter_var($ip, FILTER_VALIDATE_IP) || $ip === '127.0.0.1' || $ip === '::1') {
                    continue;
                }
                if (empty($login->user_id)) {
                    continue;
                }

                $inserts[] = [
                    'ip' => $ip,
                    'user_id' => $login->user_id,
                    'created_at' => $login->created_at ?? $now,
                    'updated_at' => $now,
                ];
            }

            if ($inserts) {
                DB::table('user_ip_locks')->insertOrIgnore($inserts);
            }
        }
    }
}

if (!function_exists('ipAccountOwnerId')) {
    function ipAccountOwnerId(?string $ip = null): ?int
    {
        $ip = $ip ?? accountLockIp();
        if (!$ip) {
            return null;
        }

        ensureUserIpLocks();
        if (!Schema::hasTable('user_ip_locks')) {
            return null;
        }

        $owner = UserIpLock::where('ip', $ip)->value('user_id');

        return $owner ? (int) $owner : null;
    }
}

if (!function_exists('claimIpForUser')) {
    function claimIpForUser(int $userId, ?string $ip = null): bool
    {
        $ip = $ip ?? accountLockIp();
        if (!$ip) {
            return true;
        }

        ensureUserIpLocks();
        if (!Schema::hasTable('user_ip_locks')) {
            return true;
        }

        $owner = ipAccountOwnerId($ip);
        if ($owner === $userId) {
            return true;
        }
        if ($owner) {
            return false;
        }

        try {
            UserIpLock::create([
                'ip' => $ip,
                'user_id' => $userId,
            ]);

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            $owner = UserIpLock::where('ip', $ip)->value('user_id');

            return (int) $owner === $userId;
        }
    }
}