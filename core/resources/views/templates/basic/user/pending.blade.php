@php
    $pageTitle = !empty($isApproved) ? 'Account Approved' : (!empty($isRejected) ? 'Account Rejected' : 'Account Pending');
@endphp

@extends($activeTemplate.'layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
body{background:#fdf0f7 !important;}
.pnd-page,.pnd-page *{box-sizing:border-box;}
.pnd-page h1,.pnd-page h3,.pnd-page p{margin-top:0;}

/* ══ PAGE ══ */
.pnd-page{
    min-height:100vh;
    background:#fdf0f7;
    font-family:'Poppins',sans-serif;
    overflow-x:hidden;
    position:relative;
}

/* ── Floating blobs background ── */
.pnd-blob{
    position:fixed;
    border-radius:50%;
    filter:blur(60px);
    opacity:0.18;
    pointer-events:none;
    z-index:0;
    animation:blobFloat 8s ease-in-out infinite;
}
.pnd-blob-1{width:400px;height:400px;background:#e91e8c;top:-100px;left:-100px;animation-delay:0s;}
.pnd-blob-2{width:300px;height:300px;background:#ff4da6;bottom:-80px;right:-80px;animation-delay:3s;}
.pnd-blob-3{width:200px;height:200px;background:#c2185b;top:50%;left:60%;animation-delay:5s;}
@keyframes blobFloat{
    0%,100%{transform:translate(0,0) scale(1);}
    33%{transform:translate(30px,-20px) scale(1.05);}
    66%{transform:translate(-20px,30px) scale(0.95);}
}

/* ── Floating particles ── */
.pnd-particles{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.pnd-particle{
    position:absolute;
    width:6px;height:6px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    border-radius:50%;
    opacity:0;
    animation:particleRise linear infinite;
}
@keyframes particleRise{
    0%{opacity:0;transform:translateY(0) scale(0);}
    10%{opacity:0.6;}
    90%{opacity:0.2;}
    100%{opacity:0;transform:translateY(-100vh) scale(1.5);}
}

/* ── Content wrapper ── */
.pnd-wrap{
    position:relative;z-index:1;
    max-width:480px;margin:0 auto;
    padding:1.5rem 1.2rem 3rem;
    display:flex;flex-direction:column;align-items:center;gap:1.2rem;
}

/* ── Logout ── */
.pnd-logout{
    width:100%;display:flex;justify-content:flex-end;
}
.pnd-logout a{
    display:inline-flex;align-items:center;gap:7px;
    background:rgba(255,255,255,0.85);
    backdrop-filter:blur(10px);
    border:1.5px solid rgba(233,30,140,0.2);
    border-radius:14px;padding:9px 18px;
    font-size:0.86rem;font-weight:600;color:#e91e8c;
    text-decoration:none;
    box-shadow:0 4px 16px rgba(233,30,140,0.10);
    transition:all 0.3s;
}
.pnd-logout a:hover{
    background:#fff;
    border-color:#e91e8c;
    box-shadow:0 6px 20px rgba(233,30,140,0.20);
    transform:translateY(-1px);
}

/* ══ HERO CARD ══ */
.pnd-hero{
    width:100%;
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 45%,#ad1457 100%);
    border-radius:28px;
    padding:2.5rem 1.8rem 2.2rem;
    text-align:center;
    position:relative;overflow:hidden;
    box-shadow:0 20px 60px rgba(233,30,140,0.35);
    animation:heroIn 0.7s cubic-bezier(0.34,1.56,0.64,1) both;
}
@keyframes heroIn{
    from{opacity:0;transform:translateY(30px) scale(0.95);}
    to{opacity:1;transform:translateY(0) scale(1);}
}

/* animated mesh overlay */
.pnd-hero::before{
    content:'';position:absolute;inset:0;
    background:radial-gradient(ellipse at 20% 20%,rgba(255,255,255,0.15) 0%,transparent 60%),
               radial-gradient(ellipse at 80% 80%,rgba(255,255,255,0.08) 0%,transparent 50%);
    pointer-events:none;
}

/* pulse rings behind check */
.pnd-rings{
    position:relative;
    width:90px;height:90px;
    margin:0 auto 1.4rem;
}
.pnd-ring{
    position:absolute;inset:0;
    border-radius:50%;
    border:2px solid rgba(255,255,255,0.4);
    animation:ringPulse 2.5s ease-out infinite;
}
.pnd-ring:nth-child(2){animation-delay:0.6s;}
.pnd-ring:nth-child(3){animation-delay:1.2s;}
@keyframes ringPulse{
    0%{transform:scale(1);opacity:0.8;}
    100%{transform:scale(2.2);opacity:0;}
}
.pnd-check-circle{
    position:absolute;inset:0;
    background:#fff;
    border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    box-shadow:0 8px 30px rgba(0,0,0,0.15);
    animation:checkBounce 0.8s cubic-bezier(0.34,1.56,0.64,1) 0.3s both;
}
@keyframes checkBounce{
    from{transform:scale(0) rotate(-180deg);}
    to{transform:scale(1) rotate(0deg);}
}
.pnd-check-circle i{font-size:30px;color:#e91e8c;}

.pnd-hero-logo{
    height:52px;margin:0 auto 1.2rem;display:block;
    filter:drop-shadow(0 4px 12px rgba(0,0,0,0.2));
    animation:logoFade 0.6s ease 0.5s both;
}
@keyframes logoFade{from{opacity:0;transform:scale(0.8);}to{opacity:1;transform:scale(1);}}

.pnd-hero h1{
    font-size:2.2rem;font-weight:900;color:#fff;
    line-height:1.15;margin-bottom:0.6rem;
    text-shadow:0 2px 20px rgba(0,0,0,0.15);
    animation:textSlide 0.6s ease 0.4s both;
}
.pnd-hero p{
    font-size:0.9rem;color:rgba(255,255,255,0.88);
    font-weight:400;line-height:1.6;
    animation:textSlide 0.6s ease 0.5s both;
}
@keyframes textSlide{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}

/* ══ GLASS CARDS ══ */
.pnd-card{
    width:100%;
    background:rgba(255,255,255,0.88);
    backdrop-filter:blur(16px);
    border-radius:22px;
    border:1.5px solid rgba(233,30,140,0.12);
    overflow:hidden;
    box-shadow:0 8px 32px rgba(233,30,140,0.08);
    animation:cardIn 0.5s ease both;
}
.pnd-card:nth-child(3){animation-delay:0.1s;}
.pnd-card:nth-child(4){animation-delay:0.2s;}
.pnd-card:nth-child(5){animation-delay:0.3s;}
.pnd-card:nth-child(6){animation-delay:0.4s;}
@keyframes cardIn{
    from{opacity:0;transform:translateY(20px);}
    to{opacity:1;transform:translateY(0);}
}

/* ── Transaction submitted ── */
.pnd-submitted{
    display:flex;align-items:flex-start;gap:14px;
    padding:1.3rem 1.4rem;
}
.pnd-sub-icon{
    width:54px;height:54px;flex-shrink:0;
    background:linear-gradient(135deg,#22c55e,#16a34a);
    border-radius:16px;
    display:flex;align-items:center;justify-content:center;
    box-shadow:0 6px 18px rgba(34,197,94,0.30);
    animation:iconPop 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.6s both;
}
@keyframes iconPop{from{transform:scale(0);}to{transform:scale(1);}}
.pnd-sub-icon i{font-size:22px;color:#fff;}
.pnd-sub-text h3{font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:0.3rem;}
.pnd-sub-text p{font-size:0.82rem;color:#999;line-height:1.6;}

/* ── Card header ── */
.pnd-card-head{
    display:flex;align-items:center;gap:10px;
    padding:0.9rem 1.4rem;
    border-bottom:1px solid rgba(233,30,140,0.08);
    background:linear-gradient(90deg,rgba(255,77,166,0.06),transparent);
}
.pnd-card-head i{color:#e91e8c;font-size:16px;}
.pnd-card-head span{font-size:0.88rem;font-weight:700;color:#1a1a2e;}

/* ── Timer ── */
.pnd-timer-body{
    padding:1.4rem;
    display:flex;align-items:center;justify-content:center;gap:10px;
}
.pnd-time-box{
    text-align:center;
    background:linear-gradient(145deg,#fff5fb,#fce4f0);
    border:1.5px solid rgba(233,30,140,0.15);
    border-radius:16px;
    padding:0.9rem 1rem;
    min-width:70px;
    position:relative;overflow:hidden;
    transition:transform 0.1s;
}
.pnd-time-box::before{
    content:'';position:absolute;inset:0;
    background:linear-gradient(135deg,rgba(255,255,255,0.6),transparent);
    pointer-events:none;
}
.pnd-time-box .num{
    font-size:2rem;font-weight:800;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;line-height:1;margin-bottom:4px;
    display:block;
}
.pnd-time-box .lbl{
    font-size:0.65rem;font-weight:700;color:#ccc;
    text-transform:uppercase;letter-spacing:0.08em;
}
.pnd-sep{
    font-size:1.8rem;font-weight:900;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;
    margin-bottom:20px;
    animation:sepBlink 1s step-end infinite;
}
@keyframes sepBlink{0%,100%{opacity:1;}50%{opacity:0.15;}}

/* ── Steps ── */
.pnd-steps{
    display:flex;align-items:center;
    padding:1.2rem 1.4rem;
}
.pnd-step{flex:1;text-align:center;}
.pnd-step-icon{
    width:46px;height:46px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    margin:0 auto 0.45rem;font-size:17px;
    position:relative;
}
.pnd-step-icon.done{
    background:linear-gradient(135deg,#22c55e,#16a34a);
    color:#fff;box-shadow:0 4px 16px rgba(34,197,94,0.28);
}
.pnd-step-icon.active{
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    color:#fff;box-shadow:0 4px 16px rgba(233,30,140,0.30);
}
.pnd-step-icon.active::after{
    content:'';position:absolute;inset:-4px;
    border-radius:50%;
    border:2px solid rgba(233,30,140,0.4);
    animation:stepRing 1.5s ease-out infinite;
}
@keyframes stepRing{0%{transform:scale(1);opacity:0.8;}100%{transform:scale(1.6);opacity:0;}}
.pnd-step-icon.pending{background:#f0e8f0;color:#ddd;}
.pnd-step-lbl{font-size:0.68rem;font-weight:700;color:#bbb;white-space:nowrap;}
.pnd-step-lbl.done{color:#16a34a;}
.pnd-step-lbl.active{color:#e91e8c;}
.pnd-step-line{
    flex:0 0 28px;height:2px;
    background:linear-gradient(90deg,#f0d0e4,#f0d0e4);
    margin-bottom:22px;border-radius:2px;
    position:relative;overflow:hidden;
}
.pnd-step-line.done::after{
    content:'';position:absolute;inset:0;
    background:linear-gradient(90deg,#22c55e,#e91e8c);
    animation:lineGrow 0.8s ease 0.3s both;
    transform-origin:left;
}
@keyframes lineGrow{from{transform:scaleX(0);}to{transform:scaleX(1);}}

/* ── Approve button ── */
.pnd-approve-wrap{width:100%;}
.pnd-cred{padding:0.4rem 1.4rem 1.2rem;}
.pnd-cred-row{
    display:flex;flex-direction:column;gap:4px;
    padding:0.85rem 0;
    border-bottom:1px solid #fce4f0;
}
.pnd-cred-row:last-child{border-bottom:none;}
.pnd-cred-row span{
    font-size:0.72rem;font-weight:700;color:#e91e8c;
    text-transform:uppercase;letter-spacing:0.06em;
}
.pnd-cred-row strong{
    font-size:1rem;font-weight:700;color:#1a1a2e;
    word-break:break-word;
}
.pnd-approve-btn{
    width:100%;padding:15px;
    background:linear-gradient(135deg,#ff6ec7,#e91e8c,#ad1457);
    background-size:200% auto;
    border:none;border-radius:16px;
    color:#fff;font-size:1rem;font-weight:800;
    font-family:'Poppins',sans-serif;cursor:pointer;
    transition:all 0.4s;
    box-shadow:0 8px 28px rgba(233,30,140,0.35);
    display:flex;align-items:center;justify-content:center;gap:10px;
    letter-spacing:0.03em;
    position:relative;overflow:hidden;
    text-decoration:none;
}
.pnd-approve-btn::before{
    content:'';position:absolute;
    top:-50%;left:-60%;
    width:40%;height:200%;
    background:rgba(255,255,255,0.15);
    transform:skewX(-20deg);
    animation:btnShine 3s ease-in-out infinite;
}
@keyframes btnShine{0%,100%{left:-60%;}50%{left:120%;}}
.pnd-approve-btn:hover{
    background-position:right center;
    transform:translateY(-3px);
    box-shadow:0 14px 36px rgba(233,30,140,0.45);
    color:#fff;
    text-decoration:none;
}

/* ── Notice ── */
.pnd-notice{
    width:100%;
    background:rgba(255,251,235,0.9);
    backdrop-filter:blur(10px);
    border:1.5px solid #fde68a;
    border-radius:18px;
    padding:1rem 1.3rem;
    display:flex;gap:12px;align-items:flex-start;
}
.pnd-notice i{color:#f59e0b;font-size:17px;flex-shrink:0;margin-top:2px;}
.pnd-notice p{font-size:0.82rem;color:#92400e;line-height:1.7;}

/* ── Support ── */
.pnd-support{font-size:0.86rem;color:#bbb;text-align:center;}
.pnd-support a{color:#e91e8c;font-weight:600;text-decoration:none;}
.pnd-support a:hover{text-decoration:underline;}

/* ── Rejected state ── */
.pnd-hero.rejected{
    background:linear-gradient(145deg,#ff6b6b 0%,#e53935 45%,#b71c1c 100%);
    box-shadow:0 20px 60px rgba(229,57,53,0.35);
}
.pnd-hero.rejected .pnd-check-circle i{color:#e53935;}
.pnd-reject-icon{
    position:absolute;inset:0;
    background:#fff;
    border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    box-shadow:0 8px 30px rgba(0,0,0,0.15);
}
.pnd-reject-icon i{font-size:30px;color:#e53935;}
.pnd-reject-msg{
    width:100%;
    background:rgba(255,255,255,0.92);
    backdrop-filter:blur(16px);
    border-radius:22px;
    border:1.5px solid rgba(229,57,53,0.18);
    padding:1.4rem;
    text-align:center;
    box-shadow:0 8px 32px rgba(229,57,53,0.10);
}
.pnd-reject-msg h3{
    font-size:1.1rem;font-weight:800;color:#c62828;margin-bottom:0.55rem;
}
.pnd-reject-msg p{
    font-size:0.86rem;color:#666;line-height:1.65;margin:0;
}
.pnd-reject-feedback{
    margin-top:0.9rem;
    padding:0.85rem 1rem;
    background:#fff5f5;
    border:1px solid #ffcdd2;
    border-radius:14px;
    font-size:0.82rem;color:#b71c1c;line-height:1.6;
    text-align:left;
}
.pnd-resubmit-btn{
    width:100%;padding:15px;
    background:linear-gradient(135deg,#ff6b6b,#e53935,#b71c1c);
    border:none;border-radius:16px;
    color:#fff;font-size:1rem;font-weight:800;
    font-family:'Poppins',sans-serif;
    text-decoration:none;
    display:flex;align-items:center;justify-content:center;gap:10px;
    box-shadow:0 8px 28px rgba(229,57,53,0.30);
    transition:all 0.3s;
}
.pnd-resubmit-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 32px rgba(229,57,53,0.40);
    color:#fff;
}
.pnd-step-icon.rejected{
    background:linear-gradient(135deg,#ef5350,#c62828);
    color:#fff;box-shadow:0 4px 16px rgba(229,57,53,0.28);
}
.pnd-step-lbl.rejected{color:#c62828;}
.pnd-channel{
    display:flex;align-items:center;gap:12px;
    text-decoration:none;border-radius:14px;
    padding:0.85rem 0.95rem;margin-bottom:0.7rem;
    background:#f6fff8;border:1.5px solid #d9f5e5;
}
.pnd-channel.channel{background:#f3f8ff;border-color:#dbeafe;}
.pnd-channel.disabled{opacity:.6;pointer-events:none;}
.pnd-channel-icon{
    width:42px;height:42px;border-radius:12px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
    background:#25d366;color:#fff;font-size:1.15rem;
}
.pnd-channel.channel .pnd-channel-icon{background:#1976d2;}
.pnd-channel h4{margin:0 0 2px;font-size:0.9rem;font-weight:700;color:#222;}
.pnd-channel p{margin:0;font-size:0.75rem;color:#777;}
.pnd-channel-body{padding:1rem 1.15rem 1.15rem;}
.pnd-channel-copy{font-size:0.82rem;color:#666;line-height:1.55;margin:0 0 0.9rem;}
.pnd-joined-note{
    margin-top:0.4rem;padding:0.75rem 0.85rem;border-radius:12px;
    background:#e8f5e9;color:#166534;font-size:0.82rem;font-weight:600;
}

@keyframes heroFloat{
    0%,100%{transform:translateY(0) rotate(0deg) scale(1);}
    30%    {transform:translateY(-10px) rotate(-6deg) scale(1.08);}
    70%    {transform:translateY(-5px) rotate(5deg) scale(1.04);}
}
</style>

{{-- Floating blobs --}}
<div class="pnd-blob pnd-blob-1"></div>
<div class="pnd-blob pnd-blob-2"></div>
<div class="pnd-blob pnd-blob-3"></div>

{{-- Particles container --}}
<div class="pnd-particles" id="particles"></div>

<div class="pnd-page">
<div class="pnd-wrap">

    {{-- Logout --}}
    <div class="pnd-logout">
        <a href="{{ route('user.logout') }}">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    {{-- Hero --}}
    @if(!empty($isApproved))
    <div class="pnd-hero">
        <div class="pnd-rings">
            <div class="pnd-ring"></div>
            <div class="pnd-ring"></div>
            <div class="pnd-ring"></div>
            <div class="pnd-check-circle">
                <i class="fas fa-check"></i>
            </div>
        </div>
        <div style="font-size:2.5rem;margin-bottom:1rem;position:relative;z-index:2;">🌸</div>
        <h1>Account<br>Approved</h1>
        <p>Your login details are ready. Use them any time you sign in.</p>
    </div>

    <div class="pnd-card">
        <div class="pnd-card-head">
            <i class="fas fa-key"></i>
            <span>Your Login Details</span>
        </div>
        <div class="pnd-cred">
            <div class="pnd-cred-row">
                <span>Email</span>
                <strong>{{ $loginEmail }}</strong>
            </div>
            <div class="pnd-cred-row">
                <span>Username</span>
                <strong>{{ $loginUsername }}</strong>
            </div>
            <div class="pnd-cred-row">
                <span>Password</span>
                <strong>{{ $loginPassword ?: 'The password you created at signup' }}</strong>
            </div>
        </div>
    </div>

    <a href="{{ route('user.home') }}" class="pnd-approve-btn">
        <i class="fas fa-sign-in-alt"></i> Login
    </a>

    <div class="pnd-notice">
        <i class="fas fa-info-circle"></i>
        <p>Save these details before you log out. Sign in with this email, username, or phone and the same password you chose at signup.</p>
    </div>

    @elseif(!empty($isRejected))
    <div class="pnd-hero rejected">
        <div class="pnd-rings">
            <div class="pnd-ring"></div>
            <div class="pnd-ring"></div>
            <div class="pnd-ring"></div>
            <div class="pnd-reject-icon">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <div style="font-size:2.5rem;margin-bottom:1rem;position:relative;z-index:2;">❌</div>
        <h1>Account<br>Rejected</h1>
        <p>Your submission could not be approved</p>
    </div>

    <div class="pnd-reject-msg">
        <h3>Your Test ID has been rejected.</h3>
        <p>Please review your details and submit again with the correct Test ID.</p>
        @if(!empty($rejectedDeposit?->admin_feedback))
        <div class="pnd-reject-feedback">
            <strong>Rejection Reason</strong>
            <div style="margin-top:6px;">{{ $rejectedDeposit->admin_feedback }}</div>
        </div>
        @endif
    </div>

    <a href="{{ route('user.deposit') }}" class="pnd-resubmit-btn">
        <i class="fas fa-redo"></i> Resubmit Test ID
    </a>

    <div class="pnd-card">
        <div class="pnd-card-head">
            <i class="fas fa-tasks"></i>
            <span>Review Progress</span>
        </div>
        <div class="pnd-steps">
            <div class="pnd-step">
                <div class="pnd-step-icon done"><i class="fas fa-check"></i></div>
                <div class="pnd-step-lbl done">Submitted</div>
            </div>
            <div class="pnd-step-line done"></div>
            <div class="pnd-step">
                <div class="pnd-step-icon rejected"><i class="fas fa-times"></i></div>
                <div class="pnd-step-lbl rejected">Rejected</div>
            </div>
            <div class="pnd-step-line"></div>
            <div class="pnd-step">
                <div class="pnd-step-icon pending"><i class="fas fa-lock"></i></div>
                <div class="pnd-step-lbl">Approved</div>
            </div>
        </div>
    </div>

    @else
    <div class="pnd-hero">
        <div class="pnd-rings">
            <div class="pnd-ring"></div>
            <div class="pnd-ring"></div>
            <div class="pnd-ring"></div>
            <div class="pnd-check-circle">
                <i class="fas fa-check"></i>
            </div>
        </div>
        <img src="{{ asset('assets/logoEarn.png') }}" class="pnd-hero-logo" alt="Logo"
             style="display:none;">
        <div style="font-size:2.5rem;margin-bottom:1rem;position:relative;z-index:2;animation:heroFloat 3s ease-in-out infinite;">🌸</div>
        <h1>Account<br>Under Review</h1>
        <p>Your information is being verified by our team</p>
    </div>

    {{-- Transaction submitted --}}
    <div class="pnd-card">
        <div class="pnd-submitted">
            <div class="pnd-sub-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="pnd-sub-text">
                <h3>Transaction Submitted Successfully!</h3>
                <p>We are reviewing your information. If your details are correct, your account will be approved within the estimated time.</p>
            </div>
        </div>
    </div>

    {{-- Official channels, then the review timer --}}
    @php
        $communityLink = whatsappCommunityLink();
        $channelLink = whatsappChannelLink();
        $channelsJoinedAt = !empty($pendingDeposit) ? ($pendingDeposit->channels_joined_at ?? null) : null;
    @endphp
    <div class="pnd-card">
        <div class="pnd-card-head">
            <i class="fab fa-whatsapp"></i>
            <span>Join Official Channels</span>
        </div>
        <div class="pnd-channel-body">
            <p class="pnd-channel-copy">Join both official channels for important updates and training. The review timer starts only after you confirm.</p>
            <a href="{{ $communityLink ?: '#' }}" target="_blank" rel="noopener noreferrer" class="pnd-channel {{ $communityLink ? '' : 'disabled' }}">
                <div class="pnd-channel-icon"><i class="fab fa-whatsapp"></i></div>
                <div>
                    <h4>Official Community</h4>
                    <p>{{ $communityLink ? 'Tap to join the community' : 'Community link is not added yet' }}</p>
                </div>
            </a>
            <a href="{{ $channelLink ?: '#' }}" target="_blank" rel="noopener noreferrer" class="pnd-channel channel {{ $channelLink ? '' : 'disabled' }}">
                <div class="pnd-channel-icon"><i class="fas fa-broadcast-tower"></i></div>
                <div>
                    <h4>Official Channel</h4>
                    <p>{{ $channelLink ? 'Tap to join the channel' : 'Channel link is not added yet' }}</p>
                </div>
            </a>
            @if(!empty($pendingDeposit) && empty($channelsJoinedAt))
            <form action="{{ route('user.pending.channels') }}" method="POST">
                @csrf
                <button type="submit" class="pnd-approve-btn">
                    <i class="fas fa-check-circle"></i> I Have Joined the Channels
                </button>
            </form>
            @elseif(!empty($channelsJoinedAt))
            <div class="pnd-joined-note">Channels joined. Your review timer is now running.</div>
            @endif
        </div>
    </div>

    @if(!empty($channelsJoinedAt))
    <div class="pnd-card">
        <div class="pnd-card-head">
            <i class="fas fa-clock"></i>
            <span>Approval Countdown &mdash; ~5 hrs</span>
        </div>
        <div class="pnd-timer-body">
            <div class="pnd-time-box">
                <span class="num" id="hours">00</span>
                <span class="lbl">Hours</span>
            </div>
            <div class="pnd-sep">:</div>
            <div class="pnd-time-box">
                <span class="num" id="minutes">00</span>
                <span class="lbl">Mins</span>
            </div>
            <div class="pnd-sep">:</div>
            <div class="pnd-time-box">
                <span class="num" id="seconds">00</span>
                <span class="lbl">Secs</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Progress steps --}}
    <div class="pnd-card">
        <div class="pnd-card-head">
            <i class="fas fa-tasks"></i>
            <span>Review Progress</span>
        </div>
        <div class="pnd-steps">
            <div class="pnd-step">
                <div class="pnd-step-icon done"><i class="fas fa-check"></i></div>
                <div class="pnd-step-lbl done">Submitted</div>
            </div>
            <div class="pnd-step-line done"></div>
            <div class="pnd-step">
                <div class="pnd-step-icon active"><i class="fas fa-sync-alt fa-spin"></i></div>
                <div class="pnd-step-lbl active">Under Review</div>
            </div>
            <div class="pnd-step-line"></div>
            <div class="pnd-step">
                <div class="pnd-step-icon pending"><i class="fas fa-lock"></i></div>
                <div class="pnd-step-lbl">Approved</div>
            </div>
        </div>
    </div>

    {{-- Approve button --}}
    @if(in_array(auth()->user()->pv, ['1a','2a','3a','4a','5a','6a','7a','8a','9a','10a']))
    <div class="pnd-approve-wrap">
        <form action="/user/pending-approve" method="POST">
            @csrf
            <button type="submit" class="pnd-approve-btn">
                <i class="fas fa-sign-in-alt"></i> Login to Dashboard
            </button>
        </form>
    </div>
    @endif

    {{-- Notice --}}
    <div class="pnd-notice">
        <i class="fas fa-exclamation-triangle"></i>
        <p>Please stay on this page. When your account is approved, your login details and a Login button will appear here.</p>
    </div>
    @endif

    {{-- Support --}}
    <div class="pnd-support">
        Need help? <a href="{{ route('contact') }}">Contact Support</a>
    </div>

</div>
</div>

<script>
@if(empty($isApproved) && empty($isRejected) && !empty($channelsJoinedAt))
// ── Countdown timer starts after channel confirmation ──
const createdAt = "{{ \Carbon\Carbon::parse($channelsJoinedAt)->format('Y-m-d\TH:i:s') }}";
const endTime = new Date(createdAt);
endTime.setHours(endTime.getHours() + 5);

function updateTimer() {
    const diff = endTime - new Date();
    if (diff <= 0) {
        ['hours','minutes','seconds'].forEach(id => document.getElementById(id).textContent = '00');
        return;
    }
    const h = Math.floor(diff / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    document.getElementById('hours').textContent   = String(h).padStart(2,'0');
    document.getElementById('minutes').textContent = String(m).padStart(2,'0');
    document.getElementById('seconds').textContent = String(s).padStart(2,'0');

    const sBox = document.getElementById('seconds').closest('.pnd-time-box');
    sBox.style.transform = 'scale(1.06)';
    setTimeout(() => sBox.style.transform = 'scale(1)', 120);
}
updateTimer();
setInterval(updateTimer, 1000);
@endif

@if(empty($isApproved))
setTimeout(function () {
    window.location.reload();
}, 12000);
@endif

// ── Floating particles ──
(function spawnParticles() {
    const container = document.getElementById('particles');
    for (let i = 0; i < 18; i++) {
        const p = document.createElement('div');
        p.className = 'pnd-particle';
        const size = Math.random() * 6 + 3;
        p.style.cssText = `
            width:${size}px;height:${size}px;
            left:${Math.random()*100}%;
            bottom:${Math.random()*20}%;
            animation-duration:${Math.random()*8+6}s;
            animation-delay:${Math.random()*8}s;
            border-radius:${Math.random()>0.5?'50%':'4px'};
        `;
        container.appendChild(p);
    }
})();
</script>

@endsection
