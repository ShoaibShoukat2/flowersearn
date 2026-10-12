@extends($activeTemplate.'layouts.frontend')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;}

.fl-page{
    min-height:100vh;
    display:flex;align-items:center;justify-content:center;
    background:linear-gradient(145deg,#ff4da6 0%,#e91e8c 40%,#fdf5f9 40%);
    padding:2rem 1.2rem;
    font-family:'Poppins',sans-serif;
}

.fl-card{
    width:100%;max-width:440px;
    background:#fff;
    border-radius:24px;
    padding:2.5rem 2.2rem;
    box-shadow:0 16px 60px rgba(233,30,140,0.18);
}

.fl-icon-wrap{
    width:72px;height:72px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    margin:0 auto 1.4rem;
    box-shadow:0 8px 24px rgba(233,30,140,0.30);
}
.fl-icon-wrap i{font-size:28px;color:#fff;}

.fl-card-head{text-align:center;margin-bottom:1.8rem;}
.fl-card-head h2{font-size:1.55rem;font-weight:700;color:#1a1a2e;margin-bottom:0.4rem;}
.fl-card-head p{font-size:0.87rem;color:#aaa;line-height:1.6;}
.fl-card-head .email-badge{
    display:inline-block;
    background:#fce4f0;color:#e91e8c;
    font-size:0.82rem;font-weight:600;
    padding:4px 14px;border-radius:20px;
    margin-top:0.5rem;
}

/* OTP inputs — reuse existing verification_code partial styles + override */
.verification-code-wrapper{width:100%;}
.verification-area{width:100%;}

/* button */
.fl-btn{
    width:100%;padding:14px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c,#c2185b);
    background-size:200% auto;
    border:none;border-radius:12px;
    color:#fff;font-size:0.96rem;font-weight:600;
    font-family:'Poppins',sans-serif;cursor:pointer;
    transition:all 0.35s;
    box-shadow:0 6px 22px rgba(233,30,140,0.30);
    display:flex;align-items:center;justify-content:center;gap:9px;
    margin-top:1rem;
}
.fl-btn:hover{
    background-position:right center;
    transform:translateY(-2px);
    box-shadow:0 10px 30px rgba(233,30,140,0.40);
}

.fl-resend{
    text-align:center;font-size:0.84rem;color:#aaa;
    margin-top:1.2rem;line-height:1.7;
}
.fl-resend a{color:#e91e8c;font-weight:600;text-decoration:none;}
.fl-resend a:hover{text-decoration:underline;}

.fl-back{
    display:flex;align-items:center;justify-content:center;
    gap:6px;margin-top:1rem;
    font-size:0.86rem;color:#aaa;text-decoration:none;
    transition:color 0.2s;
}
.fl-back:hover{color:#e91e8c;}
</style>

<div class="fl-page">
    <div class="fl-card">

        <div class="fl-icon-wrap">
            <i class="fas fa-envelope-open-text"></i>
        </div>

        <div class="fl-card-head">
            <h2>Check Your Email 📬</h2>
            <p>We sent a 6-digit verification code to</p>
            <span class="email-badge">{{ showEmailAddress($email) }}</span>
        </div>

        <form action="{{ route('user.password.verify.code') }}" method="POST" class="submit-form">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            @include($activeTemplate.'partials.verification_code')

            <button type="submit" class="fl-btn">
                <i class="fas fa-check-circle"></i> Verify Code
            </button>
        </form>

        <div class="fl-resend">
            Check your Junk/Spam folder too. Not received?<br>
            <a href="{{ route('user.password.request') }}">Resend the code</a>
        </div>

        <a href="{{ route('user.login') }}" class="fl-back">
            <i class="fas fa-arrow-left"></i> Back to Sign In
        </a>

    </div>
</div>

@endsection
