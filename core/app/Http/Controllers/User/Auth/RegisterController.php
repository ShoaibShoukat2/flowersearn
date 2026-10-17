<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    use RegistersUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('checkUser');
        $this->middleware('registration.status')->except('registrationNotAllowed');
        $this->activeTemplate = activeTemplate();
    }

    public function showRegistrationForm(Request $request)
    {
        $pageTitle = 'Register';
        $mobileCode = null;
        $countryPath = resource_path('views/partials/country.json');
        $countries = is_file($countryPath) ? json_decode(file_get_contents($countryPath)) : [];
        if (!$countries) {
            $countries = [];
        }

        $referralCode = strtoupper(trim((string) ($request->get('ref') ?: $request->get('inviter_code') ?: session('ref', ''))));
        if ($referralCode !== '') {
            session()->put('ref', $referralCode);
        }

        return response()
            ->view($this->activeTemplate . 'user.auth.register', compact(
                'pageTitle',
                'mobileCode',
                'countries',
                'referralCode'
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function register(Request $request)
    {
        $referralCode = strtoupper(trim((string) ($request->input('inviter_code') ?: $request->input('ref') ?: session('ref', ''))));
        $request->merge(['inviter_code' => $referralCode]);
        if ($referralCode !== '') {
            session()->put('ref', $referralCode);
        }

        $this->validator($request->all())->validate();

        if (ipAccountOwnerId()) {
            $notify[] = ['error', ipAccountLimitMessage()];
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withNotify($notify);
        }

        try {
            $user = DB::transaction(function () use ($request) {
                $user = $this->create($request->all());
                if (!claimIpForUser((int) $user->id)) {
                    throw ValidationException::withMessages([
                        'email' => [ipAccountLimitMessage()],
                    ]);
                }

                return $user;
            });
            event(new Registered($user));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Registration failed: ' . $e->getMessage(), ['exception' => $e]);
            $notify[] = ['error', 'Unable to create your account. Please try again.'];
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withNotify($notify);
        }

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return redirect()->route('user.home');
    }

    protected function validator(array $data)
    {
        $general = gs();
        $passwordValidation = Password::min(6);

        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols();
        }

        return Validator::make($data, [
            'email'        => 'required|string|email|max:255|unique:users,email',
            'mobile'       => 'nullable|regex:/^([0-9]*)$/',
            'password'     => ['required', $passwordValidation],
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'nullable|string|max:255',
            'inviter_code' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    $code = strtoupper(trim((string) $value));
                    if ($code === '' || !User::whereRaw('UPPER(`ref`) = ?', [$code])->exists()) {
                        $fail('This referral code is invalid. Please use a valid referral code.');
                    }
                },
            ],
        ], [
            'inviter_code.required' => 'Referral code is required to register.',
        ]);
    }

    protected function resolveReferrer(array $data): ?User
    {
        $referralCode = strtoupper(trim((string) ($data['inviter_code'] ?? '')));

        if ($referralCode === '' && session()->has('ref')) {
            $referralCode = strtoupper(trim((string) session()->get('ref')));
        }

        if ($referralCode === '') {
            return null;
        }

        return User::whereRaw('UPPER(`ref`) = ?', [$referralCode])->first();
    }

    protected function uniqueUsername(): string
    {
        do {
            $username = 'user' . strtolower(substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
        } while (User::where('username', $username)->exists());

        return $username;
    }

    protected function uniqueReferralCode(): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $code = '';
            $max = strlen($characters) - 1;
            for ($i = 0; $i < 10; $i++) {
                $code .= $characters[mt_rand(0, $max)];
            }
        } while (User::where('ref', $code)->exists());

        return $code;
    }

    protected function splitFullName(string $firstName, string $lastName): array
    {
        $firstName = trim($firstName);
        $lastName  = trim($lastName);

        // Signup copies the full name into last_name — treat that as no last name.
        if ($lastName !== '' && mb_strtolower($firstName) === mb_strtolower($lastName)) {
            $lastName = '';
        }

        if ($lastName === '') {
            $parts = preg_split('/\s+/', $firstName, 2, PREG_SPLIT_NO_EMPTY) ?: [];
            $firstName = $parts[0] ?? $firstName;
            $lastName  = $parts[1] ?? '';
        } elseif (preg_match('/^(.+)\s+' . preg_quote($lastName, '/') . '$/iu', $firstName, $matches)) {
            $firstName = trim($matches[1]);
        }

        return [$firstName, $lastName];
    }

    protected function create(array $data)
    {
        $general = gs();
        $referUser = $this->resolveReferrer($data);

        if (!$referUser) {
            throw ValidationException::withMessages([
                'inviter_code' => 'A valid referral code is required to register.',
            ]);
        }

        $username = trim((string) ($data['username'] ?? ''));
        if ($username === '' || User::where('username', $username)->exists()) {
            $username = $this->uniqueUsername();
        }

        $user = new User();
        $user->email = strtolower(trim($data['email']));
        $user->password = Hash::make($data['password']);
        storeSignupPassword($user, (string) $data['password']);
        $user->username = $username;
        $user->ref_by = $referUser->id;
        $user->mobile = ($data['mobile_code'] ?? '') . ($data['mobile'] ?? '');
        [$firstName, $lastName] = $this->splitFullName(
            (string) ($data['first_name'] ?? ''),
            (string) ($data['last_name'] ?? '')
        );
        $user->first_name = $firstName;
        $user->last_name  = $lastName;
        $user->address = ['address' => '', 'state' => '', 'zip' => '', 'city' => ''];
        $user->country_code = 'PK';
        $user->status = 1;
        $user->kv = $general->kv ? 0 : 1;
        $user->ev = $general->ev ? 0 : 1;
        $user->ref = $this->uniqueReferralCode();
        $user->sv = $general->sv ? 0 : 1;
        $user->ts = 0;
        $user->tv = 1;
        $user->reg_step = 1;
        $user->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
        $adminNotification->save();

        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();
        if ($exist) {
            $userLogin->longitude = $exist->longitude;
            $userLogin->latitude = $exist->latitude;
            $userLogin->city = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country = $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude = @implode(',', $info['long'] ?? []);
            $userLogin->latitude = @implode(',', $info['lat'] ?? []);
            $userLogin->city = @implode(',', $info['city'] ?? []);
            $userLogin->country_code = @implode(',', $info['code'] ?? []);
            $userLogin->country = @implode(',', $info['country'] ?? []);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;
        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();

        return $user;
    }

    public function checkUser(Request $request)
    {
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = User::where('email', $request->email)->exists();
            $exist['type'] = 'email';
        }
        if ($request->mobile) {
            $exist['data'] = User::where('mobile', $request->mobile)->exists();
            $exist['type'] = 'mobile';
        }
        if ($request->username) {
            $exist['data'] = User::where('username', $request->username)->exists();
            $exist['type'] = 'username';
        }
        return response($exist);
    }

    protected function registered(Request $request, $user)
    {
        return to_route('user.home');
    }
}
