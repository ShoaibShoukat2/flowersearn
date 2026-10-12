@php $pageTitle = 'My Wallet'; @endphp

@if (empty(auth()->user()->pv))
    @php redirect('/user/deposit')->send(); @endphp
@endif

@php
    use App\Models\Transaction;
    use App\Models\Withdrawal;
    $authUser   = Auth::user();
    $authUserId = Auth::id();

    $balance            = $authUser->balance;
    $flowersEarn        = (float) ($authUser->fanta_balance ?? 0);
    $totalDeposits      = \App\Models\Deposit::where('user_id',$authUserId)->where('status',1)->sum('amount');
    $referralComm       = Transaction::where('user_id',$authUserId)->where('remark','referral_commission')->sum('amount');
    $totalWithdraw      = Withdrawal::where('user_id',$authUserId)->where('status',1)->sum('amount');
    $pendingWithdraw    = Withdrawal::where('user_id',$authUserId)->where('status',2)->sum('amount');
    $rejectedWithdraw   = Withdrawal::where('user_id',$authUserId)->where('status',3)->sum('amount');
    $recentWithdraws    = Withdrawal::where('user_id',$authUserId)->where('status','!=',0)->orderBy('created_at','desc')->limit(5)->get();
@endphp

@extends($activeTemplate.'layouts.dashboard')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.wl-wrap{font-family:'Poppins',sans-serif;max-width:520px;margin:0 auto;padding:0 0 3rem;}

/* ══ HERO CARD ══ */
.wl-hero{
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 50%,#ad1457 100%);
    border-radius:0 0 36px 36px;
    padding:2rem 1.5rem 3rem;
    position:relative;overflow:hidden;
    text-align:center;
}
.wl-hero::before{
    content:'';position:absolute;inset:0;
    background:radial-gradient(ellipse at 15% 15%,rgba(255,255,255,0.18) 0%,transparent 55%),
               radial-gradient(ellipse at 85% 85%,rgba(255,255,255,0.08) 0%,transparent 45%);
    pointer-events:none;
}
/* floating circles */
.wl-circle{
    position:absolute;border-radius:50%;
    background:rgba(255,255,255,0.08);
    pointer-events:none;
}
.wl-circle-1{width:180px;height:180px;top:-60px;right:-50px;animation:circleFloat 6s ease-in-out infinite;}
.wl-circle-2{width:120px;height:120px;bottom:-40px;left:-30px;animation:circleFloat 8s ease-in-out infinite reverse;}
@keyframes circleFloat{0%,100%{transform:translate(0,0);}50%{transform:translate(12px,-12px);}}

.wl-hero-label{
    font-size:0.75rem;font-weight:600;color:rgba(255,255,255,0.75);
    text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.5rem;
    position:relative;z-index:2;
}
.wl-hero-balance{
    font-size:3rem;font-weight:800;color:#fff;
    line-height:1;margin-bottom:0.3rem;
    position:relative;z-index:2;
    animation:countUp 1s ease both;
}
@keyframes countUp{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.wl-hero-sub{
    font-size:0.82rem;color:rgba(255,255,255,0.7);
    margin-bottom:1.6rem;position:relative;z-index:2;
}

/* hero action buttons */
.wl-hero-btns{
    display:flex;gap:10px;justify-content:center;
    position:relative;z-index:2;
}
.wl-hero-btn{
    display:inline-flex;align-items:center;gap:7px;
    padding:10px 20px;border-radius:14px;
    font-size:0.86rem;font-weight:700;
    font-family:'Poppins',sans-serif;
    text-decoration:none;cursor:pointer;border:none;
    transition:all 0.25s;
}
.wl-hero-btn.solid{
    background:#fff;color:#e91e8c;
    box-shadow:0 6px 20px rgba(0,0,0,0.15);
}
.wl-hero-btn.solid:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(0,0,0,0.2);}
.wl-hero-btn.ghost{
    background:rgba(255,255,255,0.18);
    border:1.5px solid rgba(255,255,255,0.35);
    color:#fff;backdrop-filter:blur(8px);
}
.wl-hero-btn.ghost:hover{background:rgba(255,255,255,0.28);}

/* ══ FLOATING STAT CARDS ══ */
.wl-stats-row{
    display:grid;grid-template-columns:repeat(3,1fr);gap:10px;
    padding:0 1.2rem;
    margin-top:-1.4rem; /* overlap hero */
    position:relative;z-index:10;
}
.wl-stat{
    background:#fff;
    border-radius:18px;
    padding:1rem 0.7rem;
    text-align:center;
    box-shadow:0 8px 28px rgba(233,30,140,0.12);
    border:1.5px solid rgba(233,30,140,0.08);
    animation:statPop 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
}
.wl-stat:nth-child(1){animation-delay:0.1s;}
.wl-stat:nth-child(2){animation-delay:0.2s;}
.wl-stat:nth-child(3){animation-delay:0.3s;}
@keyframes statPop{from{opacity:0;transform:translateY(20px) scale(0.9);}to{opacity:1;transform:translateY(0) scale(1);}}

.wl-stat-icon{
    width:36px;height:36px;border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    font-size:15px;margin:0 auto 0.5rem;
}
.wl-stat-val{
    font-size:0.95rem;font-weight:800;color:#1a1a2e;
    line-height:1;margin-bottom:3px;
}
.wl-stat-lbl{font-size:0.65rem;font-weight:600;color:#ccc;text-transform:uppercase;letter-spacing:0.05em;}

/* ══ SECTION ══ */
.wl-section{padding:1.4rem 1.2rem 0;}
.wl-sec-head{
    display:flex;align-items:center;justify-content:space-between;
    margin-bottom:1rem;
}
.wl-sec-title{
    font-size:0.88rem;font-weight:700;color:#1a1a2e;
    display:flex;align-items:center;gap:8px;
}
.wl-sec-title::before{
    content:'';display:inline-block;
    width:4px;height:16px;
    background:linear-gradient(180deg,#ff4da6,#e91e8c);
    border-radius:2px;
}
.wl-sec-link{font-size:0.78rem;color:#e91e8c;font-weight:600;text-decoration:none;}
.wl-sec-link:hover{text-decoration:underline;}

/* ══ EARNINGS GRID ══ */
.wl-earn-grid{
    display:grid;grid-template-columns:repeat(2,1fr);gap:10px;
    padding:0 1.2rem;
}
.wl-earn-card{
    background:#fff;border-radius:18px;
    border:1.5px solid #f0e0ec;
    padding:1.1rem 1rem;
    position:relative;overflow:hidden;
    box-shadow:0 3px 14px rgba(233,30,140,0.06);
    animation:cardSlide 0.5s ease both;
}
.wl-earn-card:nth-child(1){animation-delay:0.1s;}
.wl-earn-card:nth-child(2){animation-delay:0.2s;}
@keyframes cardSlide{from{opacity:0;transform:translateX(-16px);}to{opacity:1;transform:translateX(0);}}
.wl-earn-card::after{
    content:'';position:absolute;
    width:80px;height:80px;border-radius:50%;
    background:rgba(233,30,140,0.04);
    bottom:-20px;right:-20px;
    pointer-events:none;
}
.wl-earn-icon{
    width:40px;height:40px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;
    font-size:17px;margin-bottom:0.7rem;
}
.wl-earn-val{
    font-size:1.2rem;font-weight:800;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;line-height:1;margin-bottom:3px;
}
.wl-earn-lbl{font-size:0.72rem;font-weight:600;color:#bbb;}
.wl-earn-card--flowers{cursor:pointer;user-select:none;}
.wl-earn-card--flowers:active{transform:scale(0.98);}
.wl-earn-withdraw-hint{
    display:none;
    font-size:0.65rem;color:#e91e8c;font-weight:600;
    margin-top:6px;line-height:1.3;
}
.wl-earn-card--flowers.is-open .wl-earn-withdraw-hint{display:block;}

/* ══ WITHDRAW STATS ══ */
.wl-wd-grid{
    display:grid;grid-template-columns:repeat(3,1fr);gap:10px;
    padding:0 1.2rem;
}
.wl-wd-card{
    background:#fff;border-radius:16px;
    border:1.5px solid #f0e0ec;
    padding:0.9rem 0.7rem;text-align:center;
    box-shadow:0 3px 14px rgba(233,30,140,0.05);
    animation:cardSlide 0.5s ease both;
}
.wl-wd-card:nth-child(1){animation-delay:0.15s;}
.wl-wd-card:nth-child(2){animation-delay:0.25s;}
.wl-wd-card:nth-child(3){animation-delay:0.35s;}
.wl-wd-dot{
    width:10px;height:10px;border-radius:50%;
    margin:0 auto 0.5rem;
}
.wl-wd-val{font-size:0.9rem;font-weight:800;color:#1a1a2e;line-height:1;margin-bottom:3px;}
.wl-wd-lbl{font-size:0.65rem;font-weight:600;color:#ccc;text-transform:uppercase;letter-spacing:0.04em;}

/* ══ QUICK ACTIONS ══ */
.wl-qa{
    display:grid;grid-template-columns:repeat(4,1fr);gap:10px;
    padding:0 1.2rem;
}
.wl-qa-item{
    display:flex;flex-direction:column;align-items:center;gap:7px;
    background:#fff;border:1.5px solid #f0e0ec;
    border-radius:16px;padding:0.9rem 0.4rem;
    text-decoration:none;
    transition:all 0.25s cubic-bezier(0.34,1.56,0.64,1);
    box-shadow:0 3px 12px rgba(233,30,140,0.05);
}
.wl-qa-item:hover{
    transform:translateY(-4px) scale(1.04);
    border-color:#e91e8c;
    box-shadow:0 10px 24px rgba(233,30,140,0.14);
}
.wl-qa-icon{
    width:40px;height:40px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;font-size:17px;
}
.wl-qa-lbl{font-size:0.7rem;font-weight:600;color:#666;text-align:center;}

/* ══ RECENT WITHDRAWALS ══ */
.wl-list{padding:0 1.2rem;}
.wl-item{
    display:flex;align-items:center;justify-content:space-between;
    background:#fff;border:1.5px solid #f0e0ec;
    border-radius:16px;padding:0.9rem 1.1rem;
    margin-bottom:8px;
    box-shadow:0 2px 10px rgba(233,30,140,0.04);
    animation:itemIn 0.4s ease both;
    transition:transform 0.2s;
}
.wl-item:hover{transform:translateX(4px);}
@keyframes itemIn{from{opacity:0;transform:translateX(-12px);}to{opacity:1;transform:translateX(0);}}
.wl-item:nth-child(1){animation-delay:0.05s;}
.wl-item:nth-child(2){animation-delay:0.10s;}
.wl-item:nth-child(3){animation-delay:0.15s;}
.wl-item:nth-child(4){animation-delay:0.20s;}
.wl-item:nth-child(5){animation-delay:0.25s;}

.wl-item-icon{
    width:42px;height:42px;border-radius:13px;
    display:flex;align-items:center;justify-content:center;
    font-size:16px;flex-shrink:0;
}
.wl-item-name{font-size:0.86rem;font-weight:700;color:#1a1a2e;}
.wl-item-date{font-size:0.72rem;color:#ccc;}
.wl-item-amount{font-size:0.95rem;font-weight:800;}
.wl-badge{
    display:inline-block;font-size:0.68rem;font-weight:700;
    padding:3px 10px;border-radius:20px;margin-top:3px;
}

/* empty state */
.wl-empty{
    text-align:center;padding:2.5rem 1rem;
    background:#fff;border:1.5px solid #f0e0ec;
    border-radius:18px;margin:0 1.2rem;
}
.wl-empty-emoji{font-size:3rem;margin-bottom:0.6rem;}
.wl-empty p{font-size:0.86rem;color:#ccc;margin-bottom:1rem;}
.wl-empty-btn{
    display:inline-flex;align-items:center;gap:7px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    color:#fff;border-radius:12px;padding:10px 20px;
    font-size:0.86rem;font-weight:700;text-decoration:none;
    box-shadow:0 5px 18px rgba(233,30,140,0.28);
    transition:all 0.25s;
}
.wl-empty-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(233,30,140,0.38);}
</style>

<div class="wl-wrap">

    {{-- ══ HERO ══ --}}
    <div class="wl-hero">
        <div class="wl-circle wl-circle-1"></div>
        <div class="wl-circle wl-circle-2"></div>
        <div class="wl-hero-label">Current Balance</div>
        <div class="wl-hero-balance" id="heroBalance">Rs {{ number_format($balance,2) }}</div>
        <div class="wl-hero-sub">Referral &amp; approved earnings only · Withdraw anytime</div>
        <div class="wl-hero-btns">
            <a href="{{ route('user.withdraw') }}" class="wl-hero-btn solid">
                <i class="fas fa-arrow-up"></i> Withdraw
            </a>
            <a href="{{ route('user.deposit') }}" class="wl-hero-btn ghost">
                <i class="fas fa-plus"></i> Deposit
            </a>
        </div>
    </div>

    {{-- ══ FLOATING STAT CARDS ══ --}}
    <div class="wl-stats-row">
        <div class="wl-stat">
            <div class="wl-stat-icon" style="background:#e8f5e9;">
                <i class="fas fa-check-circle" style="color:#22c55e;"></i>
            </div>
            <div class="wl-stat-val">Rs {{ number_format($totalWithdraw,0) }}</div>
            <div class="wl-stat-lbl">Withdrawn</div>
        </div>
        <div class="wl-stat">
            <div class="wl-stat-icon" style="background:#fff8e1;">
                <i class="fas fa-clock" style="color:#f59e0b;"></i>
            </div>
            <div class="wl-stat-val">Rs {{ number_format($pendingWithdraw,0) }}</div>
            <div class="wl-stat-lbl">Pending</div>
        </div>
        <div class="wl-stat">
            <div class="wl-stat-icon" style="background:#fce4ec;">
                <i class="fas fa-times-circle" style="color:#e91e8c;"></i>
            </div>
            <div class="wl-stat-val">Rs {{ number_format($rejectedWithdraw,0) }}</div>
            <div class="wl-stat-lbl">Rejected</div>
        </div>
    </div>

    {{-- ══ EARNINGS ══ --}}
    <div class="wl-section">
        <div class="wl-sec-head">
            <div class="wl-sec-title">My Earnings</div>
        </div>
    </div>
    <div class="wl-earn-grid">
        <div class="wl-earn-card">
            <div class="wl-earn-icon" style="background:#f3e5f5;">
                <i class="fas fa-users" style="color:#9c27b0;"></i>
            </div>
            <div class="wl-earn-val">Rs {{ number_format($referralComm,2) }}</div>
            <div class="wl-earn-lbl">Staff Earning</div>
        </div>
        <div class="wl-earn-card wl-earn-card--flowers" onclick="this.classList.toggle('is-open')">
            <div class="wl-earn-icon" style="background:#fce4ec;">
                <i class="fas fa-seedling" style="color:#e91e8c;"></i>
            </div>
            <div class="wl-earn-val">Rs {{ number_format($flowersEarn,2) }}</div>
            <div class="wl-earn-lbl">Flowers Earn 🌸</div>
            <div class="wl-earn-withdraw-hint">Withdraw at Level 8</div>
        </div>
    </div>

    {{-- ══ QUICK ACTIONS ══ --}}
    <div class="wl-section">
        <div class="wl-sec-head">
            <div class="wl-sec-title">Quick Actions</div>
        </div>
    </div>
    <div class="wl-qa">
        <a href="{{ route('user.deposit') }}" class="wl-qa-item">
            <div class="wl-qa-icon" style="background:#e8f5e9;"><i class="fas fa-plus-circle" style="color:#22c55e;"></i></div>
            <span class="wl-qa-lbl">Deposit</span>
        </a>
        <a href="{{ route('user.withdraw') }}" class="wl-qa-item">
            <div class="wl-qa-icon" style="background:#fce4ec;"><i class="fas fa-arrow-up" style="color:#e91e8c;"></i></div>
            <span class="wl-qa-lbl">Withdraw</span>
        </a>
        <a href="{{ route('user.transactions') }}" class="wl-qa-item">
            <div class="wl-qa-icon" style="background:#e3f2fd;"><i class="fas fa-list" style="color:#1976d2;"></i></div>
            <span class="wl-qa-lbl">History</span>
        </a>
        <a href="{{ url('/user/withdraw-history') }}" class="wl-qa-item">
            <div class="wl-qa-icon" style="background:#f3e5f5;"><i class="fas fa-clock" style="color:#9c27b0;"></i></div>
            <span class="wl-qa-lbl">W. History</span>
        </a>
    </div>

    {{-- ══ RECENT WITHDRAWALS ══ --}}
    <div class="wl-section">
        <div class="wl-sec-head">
            <div class="wl-sec-title">Recent Withdrawals</div>
            <a href="{{ url('/user/withdraw-history') }}" class="wl-sec-link">View History</a>
        </div>
    </div>

    @if($recentWithdraws->count() > 0)
    <div class="wl-list">
        @foreach($recentWithdraws as $w)
        @php
            $isSuccess  = $w->status == 1;
            $isPending  = $w->status == 2;
            $iconBg     = $isSuccess ? '#e8f5e9' : ($isPending ? '#fff8e1' : '#fce4ec');
            $iconColor  = $isSuccess ? '#22c55e' : ($isPending ? '#f59e0b' : '#e91e8c');
            $icon       = $isSuccess ? 'fa-check' : ($isPending ? 'fa-clock' : 'fa-times');
            $badgeBg    = $isSuccess ? '#e8f5e9' : ($isPending ? '#fff8e1' : '#fce4ec');
            $badgeColor = $isSuccess ? '#16a34a' : ($isPending ? '#d97706' : '#e91e8c');
            $statusText = $isSuccess ? 'Success' : ($isPending ? 'Pending' : 'Rejected');
        @endphp
        <div class="wl-item">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="wl-item-icon" style="background:{{ $iconBg }};">
                    <i class="fas {{ $icon }}" style="color:{{ $iconColor }};"></i>
                </div>
                <div>
                    <div class="wl-item-name">Rs {{ showAmount($w->amount) }}</div>
                    <div class="wl-item-date">{{ showDateTime($w->created_at,'M d, Y · h:i A') }}</div>
                </div>
            </div>
            <div style="text-align:right;">
                <div class="wl-badge" style="background:{{ $badgeBg }};color:{{ $badgeColor }};">
                    {{ $statusText }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="wl-empty">
        <div class="wl-empty-emoji">💸</div>
        <p>No withdrawals yet. Start earning and cash out!</p>
        <a href="{{ route('user.withdraw') }}" class="wl-empty-btn">
            <i class="fas fa-arrow-up"></i> Withdraw Now
        </a>
    </div>
    @endif

</div>

<script>
// Animate balance counter on load
(function() {
    const el = document.getElementById('heroBalance');
    const target = {{ $balance }};
    const duration = 1200;
    const start = performance.now();
    function step(now) {
        const p = Math.min((now - start) / duration, 1);
        const ease = 1 - Math.pow(1 - p, 3);
        el.textContent = 'Rs ' + (target * ease).toLocaleString('en-PK', {minimumFractionDigits:2, maximumFractionDigits:2});
        if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
})();
</script>

@endsection
