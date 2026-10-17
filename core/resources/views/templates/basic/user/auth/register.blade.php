@extends($activeTemplate . 'layouts.frontend')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;}

/* ── Page ── */
.fl-page{
    min-height:100vh;
    display:grid;
    grid-template-columns:1fr 1fr;
    font-family:'Poppins',sans-serif;
}

/* ── Left panel ── */
.fl-left{
    background:linear-gradient(145deg,#ff4da6 0%,#e91e8c 45%,#9c0060 100%);
    display:flex;flex-direction:column;
    align-items:center;justify-content:center;
    padding:3rem 2.5rem;
    position:relative;overflow:hidden;
}
.fl-left::before{content:'';position:absolute;width:380px;height:380px;background:rgba(255,255,255,0.07);border-radius:50%;top:-100px;left:-100px;}
.fl-left::after{content:'';position:absolute;width:260px;height:260px;background:rgba(255,255,255,0.05);border-radius:50%;bottom:-70px;right:-70px;}
.fl-left-inner{position:relative;z-index:2;text-align:center;color:#fff;}
.fl-brand-icon{font-size:5rem;display:block;margin-bottom:1.2rem;animation:bob 3s ease-in-out infinite;}
@keyframes bob{0%,100%{transform:translateY(0);}50%{transform:translateY(-14px);}}
.fl-brand-name{font-size:2.6rem;font-weight:700;letter-spacing:3px;margin-bottom:0.4rem;}
.fl-brand-tagline{font-size:0.9rem;font-weight:300;opacity:0.82;line-height:1.7;margin-bottom:2.5rem;}
.fl-feats{display:flex;flex-direction:column;gap:12px;width:100%;max-width:260px;}
.fl-feat{display:flex;align-items:center;gap:12px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.18);border-radius:14px;padding:0.8rem 1.1rem;}
.fl-feat i{font-size:16px;width:18px;text-align:center;}
.fl-feat span{font-size:0.84rem;font-weight:500;}

/* ── Right panel ── */
.fl-right{
    background:#fdf5f9;
    display:flex;align-items:center;justify-content:center;
    padding:2rem;overflow-y:auto;
}

/* ── Card ── */
.fl-card{
    width:100%;max-width:440px;
    background:#fff;
    border-radius:24px;
    padding:2.4rem 2.2rem 2rem;
    box-shadow:0 8px 40px rgba(233,30,140,0.10);
}

/* header */
.fl-head{margin-bottom:0.5rem;}
.fl-head h2{
    font-size:1.75rem;font-weight:700;color:#1a1a2e;
    margin-bottom:0.25rem;
}
.fl-head p{font-size:0.87rem;color:#bbb;}
.fl-head p span{color:#e91e8c;font-weight:600;}

/* underline accent */
.fl-accent{
    width:44px;height:4px;
    background:linear-gradient(90deg,#ff4da6,#e91e8c);
    border-radius:2px;margin:0.6rem 0 1.6rem;
}

/* field */
.fl-field{margin-bottom:1rem;}
.fl-field label{
    display:block;font-size:0.72rem;font-weight:700;
    color:#888;margin-bottom:0.4rem;
    text-transform:uppercase;letter-spacing:0.07em;
}

.fl-input{
    display:flex;align-items:center;
    background:#fdf5f9;
    border:1.8px solid #f0d0e4;
    border-radius:14px;overflow:hidden;
    transition:border-color 0.25s,box-shadow 0.25s,background 0.25s;
}
.fl-input:focus-within{
    border-color:#e91e8c;
    box-shadow:0 0 0 4px rgba(233,30,140,0.09);
    background:#fff;
}
.fl-input .ico{
    width:48px;display:flex;align-items:center;justify-content:center;
    color:#e91e8c;font-size:15px;flex-shrink:0;
}
.fl-input input{
    flex:1;border:none;outline:none;
    padding:14px 14px 14px 0;
    font-size:0.93rem;font-family:'Poppins',sans-serif;
    color:#333;background:transparent;
}
.fl-input input::placeholder{color:#d0b0c0;font-weight:300;}
.fl-input input[readonly]{color:#c0a0b0;cursor:default;}

/* eye toggle */
.fl-eye{
    width:44px;display:flex;align-items:center;justify-content:center;
    color:#ccc;font-size:15px;cursor:pointer;flex-shrink:0;
    transition:color 0.2s;
}
.fl-eye:hover{color:#e91e8c;}

/* error hint */
.fl-hint{font-size:0.74rem;color:#e53935;margin-top:4px;display:none;}

/* submit */
.fl-btn{
    width:100%;padding:15px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c,#c2185b);
    background-size:200% auto;
    border:none;border-radius:14px;
    color:#fff;font-size:1rem;font-weight:700;
    font-family:'Poppins',sans-serif;cursor:pointer;
    transition:all 0.35s;
    box-shadow:0 6px 24px rgba(233,30,140,0.32);
    display:flex;align-items:center;justify-content:center;gap:9px;
    margin-top:1.5rem;letter-spacing:0.02em;
}
.fl-btn:hover{background-position:right center;transform:translateY(-2px);box-shadow:0 10px 32px rgba(233,30,140,0.42);}
.fl-btn:active{transform:translateY(0);}
.fl-btn:disabled{opacity:0.6;cursor:not-allowed;transform:none;}

/* sign in link */
.fl-signin{text-align:center;font-size:0.86rem;color:#bbb;margin-top:1.3rem;}
.fl-signin a{color:#e91e8c;font-weight:600;text-decoration:none;}
.fl-signin a:hover{text-decoration:underline;}

/* ── Mobile ── */
@media(max-width:768px){
    .fl-page{
        display:block;min-height:100vh;
        background:linear-gradient(170deg,#ff4da6 0%,#e91e8c 28%,#fdf5f9 28%);
    }
    .fl-left{display:none;}
    .fl-right{
        background:transparent;
        padding:3.8rem 1.2rem 3rem;
        min-height:100vh;align-items:flex-start;
    }
    .fl-card{
        border-radius:22px;
        padding:2rem 1.5rem 1.8rem;
        box-shadow:0 14px 55px rgba(233,30,140,0.20);
    }
    .fl-mobile-logo{
        display:flex;flex-direction:column;align-items:center;
        margin-bottom:1.5rem;
    }
    .fl-mobile-logo .em{font-size:2.8rem;margin-bottom:0.2rem;}
    .fl-mobile-logo .nm{font-size:1.4rem;font-weight:700;color:#fff;letter-spacing:2px;}
}
@media(min-width:769px){.fl-mobile-logo{display:none;}}
</style>

<div class="fl-page">

    {{-- Left brand panel --}}
    <div class="fl-left">
        <div class="fl-left-inner">
            <span class="fl-brand-icon">🌸</span>
            <div class="fl-brand-name">Flower</div>
            <p class="fl-brand-tagline">Join thousands of earners.<br>Start growing your income today.</p>
            <div class="fl-feats">
                <div class="fl-feat"><i class="fas fa-shield-alt"></i><span>100% Secure &amp; Trusted</span></div>
                <div class="fl-feat"><i class="fas fa-bolt"></i><span>Instant Withdrawals</span></div>
                <div class="fl-feat"><i class="fas fa-users"></i><span>Referral Bonuses</span></div>
                <div class="fl-feat"><i class="fas fa-headset"></i><span>24/7 Support</span></div>
            </div>
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="fl-right">
        <div class="fl-card">

            {{-- Mobile logo --}}
            <div class="fl-mobile-logo">
                <span class="em">🌸</span>
                <span class="nm">Flower</span>
            </div>

            <div class="fl-head">
                <h2>Create an Account</h2>
                <p>Join <span>Flower</span> and start earning today</p>
            </div>
            <div class="fl-accent"></div>

            <form class="action-form" action="/user/register" method="post" id="regForm" autocomplete="on">
                @csrf
                <div style="position:absolute;left:-9999px;height:0;overflow:hidden;" aria-hidden="true">
                    <input type="text" name="flower_trap" tabindex="-1" autocomplete="off" value="">
                </div>
                @php $referralCode = $referralCode ?? session()->get('ref'); @endphp

                {{-- Hidden fields --}}
                <input type="hidden" id="username"      name="username"     value="{{ old('username') }}">
                <input type="hidden" id="last_name_h"   name="last_name"    value="{{ old('last_name','') }}">
                <input type="hidden" name="mobile_code">
                <input type="hidden" name="country_code">

                {{-- Hidden country select --}}
                <div style="display:none;">
                    <select name="country" class="form-select">
                        @foreach ($countries as $key => $country)
                            <option data-mobile_code="{{ $country->dial_code }}"
                                    value="{{ $country->country }}"
                                    data-code="{{ $key }}"
                                    {{ $country->country === 'Pakistan' ? 'selected' : '' }}>
                                {{ __($country->country) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Full Name --}}
                <div class="fl-field">
                    <label>Full Name</label>
                    <div class="fl-input">
                        <div class="ico"><i class="fas fa-user"></i></div>
                        <input type="text" name="first_name" id="first_name"
                               placeholder="Enter your full name"
                               value="{{ old('first_name') }}" required>
                    </div>
                    @error('first_name')<div class="fl-hint" style="display:block;">{{ $message }}</div>@enderror
                </div>

                {{-- Email --}}
                <div class="fl-field">
                    <label>Email</label>
                    <div class="fl-input">
                        <div class="ico"><i class="fas fa-envelope"></i></div>
                        <input type="email" name="email" class="checkUser"
                               placeholder="Enter your email"
                               value="{{ old('email') }}" required>
                    </div>
                    <div class="fl-hint emailExist"></div>
                    @error('email')<div class="fl-hint" style="display:block;">{{ $message }}</div>@enderror
                </div>

                {{-- Phone --}}
                <div class="fl-field">
                    <label>Phone</label>
                    <div class="fl-input">
                        <div class="ico"><i class="fas fa-phone"></i></div>
                        <input type="tel" name="mobile" class="checkUser"
                               placeholder="+92 300 1234567"
                               value="{{ old('mobile') }}">
                    </div>
                    <div class="fl-hint mobileExist"></div>
                </div>

                {{-- Password --}}
                <div class="fl-field">
                    <label>Password</label>
                    <div class="fl-input">
                        <div class="ico"><i class="fas fa-lock"></i></div>
                        <input type="password" name="password" id="passInput"
                               placeholder="Enter your password" required>
                        <div class="fl-eye" id="eyeToggle">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </div>
                    </div>
                    @error('password')<div class="fl-hint" style="display:block;">{{ $message }}</div>@enderror
                </div>
                <input type="hidden" name="password_confirmation" id="passConfirm" value="{{ old('password') }}">

                {{-- Referral Code --}}
                <div class="fl-field">
                    <label>Referral Code</label>
                    <div class="fl-input">
                        <div class="ico"><i class="fas fa-gift"></i></div>
                        <input type="text" name="inviter_code"
                               placeholder="Enter referral code"
                               value="{{ old('inviter_code', $referralCode) }}"
                               required maxlength="50" autocomplete="off">
                    </div>
                    @error('inviter_code')<div class="fl-hint" style="display:block;">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="fl-btn" id="submitBtn">
                    Create Account
                </button>
            </form>

            <div class="fl-signin">
                Already have an account? <a href="{{ route('user.login') }}">Sign In</a>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const firstNameInput = document.getElementById('first_name') || document.querySelector('[name="first_name"]');
    const lastNameH      = document.getElementById('last_name_h') || document.querySelector('[name="last_name"]');
    const passInput      = document.getElementById('passInput') || document.querySelector('[name="password"]');
    const passConfirm    = document.getElementById('passConfirm') || document.querySelector('[name="password_confirmation"]');
    const uInput         = document.getElementById('username') || document.querySelector('[name="username"]');
    const form           = document.getElementById('regForm');
    const btn            = document.getElementById('submitBtn');

    function randomUsername() {
        const c = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let u = '';
        for (let i = 0; i < 12; i++) u += c[Math.floor(Math.random() * c.length)];
        return u;
    }

    if (uInput && (!uInput.value || uInput.value.trim() === '')) {
        uInput.value = randomUsername();
    }

    function syncLastName() {
        if (!lastNameH || !firstNameInput) return;
        const parts = firstNameInput.value.trim().split(/\s+/).filter(Boolean);
        lastNameH.value = parts.length > 1 ? parts.slice(1).join(' ') : '';
    }
    if (firstNameInput) {
        firstNameInput.addEventListener('input', syncLastName);
        syncLastName();
    }

    function syncPass() {
        if (!passConfirm || !passInput) return;
        passConfirm.value = passInput.value;
    }
    if (passInput) {
        passInput.addEventListener('input', syncPass);
        passInput.addEventListener('change', syncPass);
        syncPass();
    }

    const eyeToggle = document.getElementById('eyeToggle');
    if (eyeToggle && passInput) {
        eyeToggle.addEventListener('click', function () {
            const icon = document.getElementById('eyeIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                if (icon) icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passInput.type = 'password';
                if (icon) icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            const refInput = form.querySelector('[name="inviter_code"]');
            if (!refInput || !refInput.value.trim()) {
                e.preventDefault();
                if (refInput) {
                    refInput.focus();
                    let hint = refInput.closest('.fl-field')?.querySelector('.fl-hint');
                    if (!hint) {
                        hint = document.createElement('div');
                        hint.className = 'fl-hint';
                        hint.style.display = 'block';
                        refInput.closest('.fl-field')?.appendChild(hint);
                    }
                    hint.style.display = 'block';
                    hint.textContent = 'Referral code is required to register.';
                }
                return;
            }
            if (uInput && !uInput.value.trim()) uInput.value = randomUsername();
            syncLastName();
            syncPass();
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
            }
        });
    }
});
</script>

@endsection

@push('script-lib')
<script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush

@push('script')
<script>
"use strict";
(function($) {
    @if ($mobileCode)
        $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
    @endif

    $('select[name=country]').change(function() {
        $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
        $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
    });
    $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
    $('input[name=country_code]').val($('select[name=country] :selected').data('code'));

    @if ($general->secure_password)
        $('input[name=password]').on('input', function() { secure_password($(this)); });
        $('[name=password]').focus(function() { $(this).closest('.fl-field').addClass('hover-input-popup'); });
        $('[name=password]').focusout(function() { $(this).closest('.fl-field').removeClass('hover-input-popup'); });
    @endif

    $('.checkUser').on('focusout', function() {
        var url  = '/user/check-mail';
        var type = $(this).attr('name');
        var data = { _token: '{{ csrf_token() }}' };
        if (type === 'email')  data.email  = $(this).val();
        else if (type === 'mobile') data.mobile = $(this).val();
        else return;

        $.post(url, data, function(response) {
            if (response.data != false) {
                $(`.${response.type}Exist`).text(response.type + ' already exists').show();
            } else {
                $(`.${response.type}Exist`).text('').hide();
            }
        }).fail(function() {});
    });
})(jQuery);
</script>
@endpush
