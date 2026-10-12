@extends($activeTemplate.'layouts.frontend')
@section('content')
@php $loginCaption = getContent('login.content',true); @endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;margin:0;padding:0;}

body{font-family:'Poppins',sans-serif;}

/* ── Full page wrapper ── */
.fl-page{
    min-height:100vh;
    display:grid;
    grid-template-columns:1fr 1fr;
    font-family:'Poppins',sans-serif;
}

/* ══════════════════════════════
   LEFT PANEL
══════════════════════════════ */
.fl-left{
    background:linear-gradient(145deg,#ff4da6 0%,#e91e8c 45%,#9c0060 100%);
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:3rem 2.5rem;
    position:relative;
    overflow:hidden;
}

/* decorative circles */
.fl-left::before{
    content:'';position:absolute;
    width:380px;height:380px;
    background:rgba(255,255,255,0.07);
    border-radius:50%;top:-100px;left:-100px;
}
.fl-left::after{
    content:'';position:absolute;
    width:260px;height:260px;
    background:rgba(255,255,255,0.05);
    border-radius:50%;bottom:-70px;right:-70px;
}

.fl-left-inner{position:relative;z-index:2;text-align:center;color:#fff;}

.fl-brand-icon{
    font-size:5.5rem;
    display:block;
    margin-bottom:1.2rem;
    animation:bob 3s ease-in-out infinite;
}
@keyframes bob{0%,100%{transform:translateY(0);}50%{transform:translateY(-14px);}}

.fl-brand-name{
    font-size:2.6rem;
    font-weight:700;
    letter-spacing:3px;
    margin-bottom:0.4rem;
}

.fl-brand-tagline{
    font-size:0.9rem;
    font-weight:300;
    opacity:0.82;
    line-height:1.7;
    margin-bottom:2.5rem;
}

.fl-features{
    display:flex;flex-direction:column;gap:12px;
    width:100%;max-width:270px;
}

.fl-feat{
    display:flex;align-items:center;gap:12px;
    background:rgba(255,255,255,0.12);
    border:1px solid rgba(255,255,255,0.18);
    border-radius:14px;
    padding:0.8rem 1.1rem;
    backdrop-filter:blur(8px);
    transition:background 0.25s;
}
.fl-feat:hover{background:rgba(255,255,255,0.2);}
.fl-feat i{font-size:17px;opacity:0.95;width:20px;text-align:center;}
.fl-feat span{font-size:0.84rem;font-weight:500;}

/* ══════════════════════════════
   RIGHT PANEL
══════════════════════════════ */
.fl-right{
    background:#fdf5f9;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:2.5rem 2rem;
}

.fl-card{
    width:100%;
    max-width:420px;
    background:#fff;
    border-radius:24px;
    padding:2.5rem 2.2rem;
    box-shadow:0 8px 40px rgba(233,30,140,0.10);
}

/* header */
.fl-card-head{margin-bottom:2rem;}

.fl-card-head h2{
    font-size:1.7rem;
    font-weight:700;
    color:#1a1a2e;
    margin-bottom:0.35rem;
}

.fl-card-head p{
    font-size:0.87rem;
    color:#aaa;
}
.fl-card-head p span{color:#e91e8c;font-weight:600;}

/* fields */
.fl-field{margin-bottom:1.15rem;}

.fl-field label{
    display:block;
    font-size:0.75rem;
    font-weight:600;
    color:#666;
    margin-bottom:0.45rem;
    text-transform:uppercase;
    letter-spacing:0.06em;
}

.fl-input{
    display:flex;
    align-items:center;
    background:#fdf5f9;
    border:1.8px solid #f0d0e4;
    border-radius:12px;
    overflow:hidden;
    transition:border-color 0.25s,box-shadow 0.25s;
}
.fl-input:focus-within{
    border-color:#e91e8c;
    box-shadow:0 0 0 4px rgba(233,30,140,0.09);
    background:#fff;
}

.fl-input .ico{
    width:46px;
    display:flex;align-items:center;justify-content:center;
    color:#e91e8c;font-size:14px;flex-shrink:0;
}

.fl-input input{
    flex:1;border:none;outline:none;
    padding:13px 14px 13px 0;
    font-size:0.92rem;
    font-family:'Poppins',sans-serif;
    color:#333;background:transparent;
}
.fl-input input::placeholder{color:#ccc;font-weight:300;}

/* remember / forgot row */
.fl-meta{
    display:flex;align-items:center;
    justify-content:space-between;
    margin-bottom:1.6rem;flex-wrap:wrap;gap:8px;
}

.fl-remember{
    display:flex;align-items:center;gap:7px;
    font-size:0.82rem;color:#888;cursor:pointer;
}
.fl-remember input{width:15px;height:15px;accent-color:#e91e8c;}

.fl-forgot{
    font-size:0.82rem;color:#e91e8c;
    font-weight:600;text-decoration:none;
}
.fl-forgot:hover{text-decoration:underline;}

/* submit button */
.fl-btn{
    width:100%;padding:14px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c,#c2185b);
    background-size:200% auto;
    border:none;border-radius:12px;
    color:#fff;font-size:0.96rem;font-weight:600;
    font-family:'Poppins',sans-serif;
    cursor:pointer;
    transition:all 0.35s;
    box-shadow:0 6px 22px rgba(233,30,140,0.30);
    display:flex;align-items:center;justify-content:center;gap:9px;
    letter-spacing:0.03em;
}
.fl-btn:hover{
    background-position:right center;
    transform:translateY(-2px);
    box-shadow:0 10px 30px rgba(233,30,140,0.40);
}
.fl-btn:active{transform:translateY(0);}

/* divider */
.fl-divider{
    display:flex;align-items:center;gap:12px;
    margin:1.4rem 0;color:#ddd;font-size:0.78rem;
}
.fl-divider::before,.fl-divider::after{
    content:'';flex:1;height:1px;background:#f0d0e4;
}

/* register link */
.fl-signup{
    text-align:center;font-size:0.86rem;color:#aaa;
}
.fl-signup a{
    color:#e91e8c;font-weight:600;text-decoration:none;
}
.fl-signup a:hover{text-decoration:underline;}

/* ══════════════════════════════
   MOBILE  (≤ 768px)
══════════════════════════════ */
@media(max-width:768px){
    .fl-page{
        display:block;
        min-height:100vh;
        background:linear-gradient(160deg,#ff4da6 0%,#e91e8c 35%,#fdf5f9 35%);
    }

    .fl-left{display:none;}

    .fl-right{
        background:transparent;
        padding:2.5rem 1.2rem 3rem;
        min-height:100vh;
        align-items:flex-start;
        padding-top:3.5rem;
    }

    .fl-card{
        border-radius:20px;
        padding:2rem 1.5rem;
        box-shadow:0 12px 50px rgba(233,30,140,0.18);
    }

    /* small logo on mobile */
    .fl-mobile-logo{
        display:flex;
        flex-direction:column;
        align-items:center;
        margin-bottom:1.8rem;
    }
    .fl-mobile-logo .emoji{font-size:3rem;margin-bottom:0.3rem;}
    .fl-mobile-logo .name{
        font-size:1.5rem;font-weight:700;
        color:#fff;letter-spacing:2px;
    }
}

@media(min-width:769px){
    .fl-mobile-logo{display:none;}
}
</style>

<div class="fl-page">

    <!-- ── Left brand panel (desktop) ── -->
    <div class="fl-left">
        <div class="fl-left-inner">
            <span class="fl-brand-icon">🌸</span>
            <div class="fl-brand-name">Flower</div>
            <p class="fl-brand-tagline">Your trusted earning platform.<br>Grow your income every day.</p>

            <div class="fl-features">
                <div class="fl-feat">
                    <i class="fas fa-shield-alt"></i>
                    <span>100% Secure &amp; Trusted</span>
                </div>
                <div class="fl-feat">
                    <i class="fas fa-bolt"></i>
                    <span>Instant Withdrawals</span>
                </div>
                <div class="fl-feat">
                    <i class="fas fa-users"></i>
                    <span>Referral Bonuses</span>
                </div>
                <div class="fl-feat">
                    <i class="fas fa-headset"></i>
                    <span>24/7 Support</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Right form panel ── -->
    <div class="fl-right">
        <div class="fl-card">

            <!-- Mobile logo (only visible on small screens) -->
            <div class="fl-mobile-logo">
                <span class="emoji">🌸</span>
                <span class="name">Flower</span>
            </div>

            <div class="fl-card-head">
                <h2>Welcome back 👋</h2>
                <p>Sign in to your <span>Flower</span> account</p>
            </div>

            <form class="action-form loginForm" action="/user/login" method="post">
                @csrf

                <div class="fl-field">
                    <label>Email, Username, or Phone</label>
                    <div class="fl-input">
                        <div class="ico"><i class="fas fa-user"></i></div>
                        <input type="text" name="username" placeholder="Enter your email, username, or phone" required autofocus>
                    </div>
                </div>

                <div class="fl-field">
                    <label>Password</label>
                    <div class="fl-input">
                        <div class="ico"><i class="fas fa-lock"></i></div>
                        <input type="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="fl-meta">
                    <label class="fl-remember">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="{{ route('user.password.request') }}" class="fl-forgot">Forgot Password?</a>
                </div>

                <button type="submit" class="fl-btn">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="fl-divider">or</div>

            <div class="fl-signup">
                New here? <a href="{{ route('user.register') }}">Create an account</a>
            </div>

        </div>
    </div>

</div>

@endsection
