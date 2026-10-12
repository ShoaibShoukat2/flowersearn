@php $pageTitle = 'Dashboard'; @endphp
@extends($activeTemplate . 'layouts.dashboard')

@if (empty(auth()->user()->pv))
    @php redirect('/user/deposit')->send(); @endphp
@endif
@auth
    @if(auth()->user()->plan_id == 0 && !preg_match('/^\d+a$/i', trim((string) auth()->user()->pv)))
        @php redirect('/user/pending')->send(); @endphp
    @endif
@endauth

@php
    use App\Models\Withdrawal;
    use App\Models\RewardItem;
    use App\Models\RewardPageSetting;
    $featureCaption = getContent('features.content', true);
    $authUserId     = Auth::id();
    $user           = auth()->user();
    $completedToday = \App\Models\PtcView::where('user_id',$authUserId)->whereDate('view_date', today())->count();
    $displayName    = $user->fullname ?: ($user->username ?? 'User');
    $refLink        = route('home').'/ref/'.($user->ref ?? '');
    $rewardSettings = RewardPageSetting::current();
    $dashboardRewards = $rewardSettings->show_on_dashboard
        ? RewardItem::activeOrdered(3)
        : collect();
    $recentWithdraws = Withdrawal::where('user_id', $authUserId)
        ->where('status', '!=', 0)
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
@endphp

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ══════════════════════════════════════
   FLOWER DASHBOARD
══════════════════════════════════════ */
.fd-wrap{
    font-family:'Poppins',sans-serif;
    max-width:520px;
    margin:0 auto;
    padding:0 0 2rem;
    background:#fdf5f9;
    min-height:100vh;
}

/* ── Hero header ── */
.fd-hero{
    background:linear-gradient(160deg,#ff4da6 0%,#e91e8c 55%,#c2185b 100%);
    border-radius:0 0 32px 32px;
    padding:2rem 1.4rem 2.5rem;
    position:relative;overflow:hidden;
    text-align:center;
}
.fd-hero::before{
    content:'🌸';position:absolute;
    font-size:120px;opacity:0.07;
    top:-20px;left:-20px;transform:rotate(-20deg);
    pointer-events:none;
}
.fd-hero::after{
    content:'🌺';position:absolute;
    font-size:100px;opacity:0.07;
    bottom:-20px;right:-10px;transform:rotate(15deg);
    pointer-events:none;
}

/* avatar */
.fd-avatar-wrap{
    position:relative;
    width:80px;height:80px;
    margin:0 auto 0.8rem;
}
.fd-avatar{
    width:80px;height:80px;border-radius:50%;
    border:3px solid rgba(255,255,255,0.6);
    object-fit:cover;
    background:#fff;
    display:flex;align-items:center;justify-content:center;
    font-size:2rem;
    box-shadow:0 6px 20px rgba(0,0,0,0.15);
}
.fd-avatar-ring{
    position:absolute;inset:-5px;
    border-radius:50%;
    border:2px solid rgba(255,255,255,0.35);
    animation:avatarRing 2s ease-in-out infinite;
}
@keyframes avatarRing{0%,100%{transform:scale(1);opacity:0.6;}50%{transform:scale(1.08);opacity:1;}}

.fd-name{
    font-family:'Playfair Display',serif;
    font-size:1.5rem;font-weight:700;color:#fff;
    margin-bottom:0.6rem;
    text-shadow:0 2px 10px rgba(0,0,0,0.1);
}

/* wallet / refer row */
.fd-wallet-row{
    display:flex;align-items:center;justify-content:center;gap:10px;
    flex-wrap:wrap;
}
.fd-ref-btn{
    display:inline-flex;align-items:center;gap:7px;
    background:rgba(255,255,255,0.12);
    border:1.5px solid rgba(255,255,255,0.25);
    border-radius:20px;padding:7px 16px;
    color:rgba(255,255,255,0.9);font-size:0.85rem;font-weight:500;
    cursor:pointer;backdrop-filter:blur(8px);
    transition:all 0.2s;
    font-family:'Poppins',sans-serif;
}
.fd-ref-btn:hover{background:rgba(255,255,255,0.22);}

/* ── Notice bar ── */
.fd-notice{
    margin:1.2rem 1.2rem 0;
    background:#fff;
    border:1.5px solid #f0d0e4;
    border-radius:14px;
    padding:0.75rem 1rem;
    display:flex;align-items:center;gap:10px;
    box-shadow:0 2px 12px rgba(233,30,140,0.06);
}
.fd-notice-icon{
    width:34px;height:34px;flex-shrink:0;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-size:13px;
}
.fd-notice marquee{font-size:0.84rem;color:#666;font-weight:500;}

/* ── Section label ── */
.fd-section{padding:1.2rem 1.2rem 0;}
.fd-sec-label{
    font-size:0.72rem;font-weight:700;color:#ccc;
    text-transform:uppercase;letter-spacing:0.08em;
    margin-bottom:0.9rem;
}

/* ══ MENU GRID ══ */
.fd-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:10px;
    padding:0 1.2rem;
}

.fd-menu-item{
    display:flex;flex-direction:column;align-items:center;
    gap:8px;
    background:#fff;
    border:1.5px solid #f0e0ec;
    border-radius:18px;
    padding:1.1rem 0.5rem 0.9rem;
    text-decoration:none;
    position:relative;overflow:hidden;
    transition:all 0.25s cubic-bezier(0.34,1.56,0.64,1);
    box-shadow:0 2px 12px rgba(233,30,140,0.05);
}
.fd-menu-item::before{
    content:'';position:absolute;
    inset:0;border-radius:18px;
    background:linear-gradient(135deg,rgba(255,77,166,0.04),transparent);
    pointer-events:none;
}
.fd-menu-item:hover{
    transform:translateY(-4px) scale(1.03);
    border-color:#e91e8c;
    box-shadow:0 10px 28px rgba(233,30,140,0.15);
}
.fd-menu-item:active{transform:scale(0.97);}

.fd-menu-icon{
    width:52px;height:52px;
    border-radius:16px;
    display:flex;align-items:center;justify-content:center;
    font-size:22px;
    position:relative;
    transition:transform 0.3s;
}
.fd-menu-item:hover .fd-menu-icon{transform:scale(1.1) rotate(-5deg);}

/* flower petal decoration on icon */
.fd-menu-icon::after{
    content:'';position:absolute;
    inset:-3px;border-radius:18px;
    border:1.5px solid transparent;
    background:linear-gradient(135deg,rgba(255,77,166,0.2),rgba(233,30,140,0.1)) border-box;
    -webkit-mask:linear-gradient(#fff 0 0) padding-box,linear-gradient(#fff 0 0);
    -webkit-mask-composite:destination-out;
    mask-composite:exclude;
    opacity:0;transition:opacity 0.25s;
}
.fd-menu-item:hover .fd-menu-icon::after{opacity:1;}

.fd-menu-label{
    font-size:0.76rem;font-weight:600;color:#555;
    text-align:center;line-height:1.3;
}
.fd-menu-item{min-height:124px;}
.fd-contact-btn{
    display:flex;align-items:center;justify-content:center;gap:10px;
    margin:14px 1.2rem 0;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    color:#fff;border-radius:16px;padding:15px 18px;
    font-size:0.95rem;font-weight:700;text-decoration:none;
    box-shadow:0 8px 22px rgba(233,30,140,0.28);
}
.fd-contact-btn:hover{color:#fff;transform:translateY(-2px);}

/* ── Stats row ── */
.fd-stats{
    display:grid;
    grid-template-columns:1fr;
    gap:10px;
    padding:0 1.2rem;
}
.fd-stat{
    background:#fff;
    border:1.5px solid #f0e0ec;
    border-radius:16px;
    padding:1rem 0.8rem;
    text-align:center;
    box-shadow:0 2px 12px rgba(233,30,140,0.05);
}
.fd-stat-val{
    font-size:1.1rem;font-weight:800;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;
    line-height:1;margin-bottom:4px;
}
.fd-stat-lbl{font-size:0.68rem;font-weight:600;color:#ccc;text-transform:uppercase;letter-spacing:0.05em;}

/* ── Referral card ── */
.fd-ref-card{
    margin:0 1.2rem;
    background:#fff;
    border:1.5px solid #f0e0ec;
    border-radius:18px;
    padding:1.2rem 1.3rem;
    box-shadow:0 2px 12px rgba(233,30,140,0.05);
}
.fd-ref-card-title{
    font-size:0.85rem;font-weight:700;color:#333;
    margin-bottom:0.8rem;
    display:flex;align-items:center;gap:7px;
}
.fd-ref-card-title i{color:#e91e8c;}
.fd-ref-input-row{
    display:flex;align-items:center;gap:8px;
    background:#fdf5f9;
    border:1.5px solid #f0d0e4;
    border-radius:12px;
    padding:0.6rem 0.8rem;
}
.fd-ref-input-row input{
    flex:1;border:none;outline:none;background:transparent;
    font-size:0.8rem;color:#888;font-family:'Poppins',sans-serif;
    min-width:0;
}
.fd-ref-copy{
    flex-shrink:0;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    border:none;border-radius:9px;
    padding:7px 13px;color:#fff;
    font-size:0.78rem;font-weight:700;
    cursor:pointer;font-family:'Poppins',sans-serif;
    transition:all 0.2s;white-space:nowrap;
    display:flex;align-items:center;gap:5px;
}
.fd-ref-copy:hover{transform:scale(1.04);box-shadow:0 4px 14px rgba(233,30,140,0.3);}
.fd-ref-copy.copied{background:linear-gradient(135deg,#22c55e,#16a34a);}

.fd-rewards-grid{display:grid;grid-template-columns:1fr;gap:10px;margin-bottom:1.2rem;}
.fd-reward-card{
    display:flex;align-items:flex-start;gap:12px;
    background:#fff;border:1.5px solid rgba(233,30,140,0.1);
    border-radius:16px;padding:0.95rem 1rem;
    box-shadow:0 4px 16px rgba(233,30,140,0.08);
}
.fd-reward-icon{
    width:42px;height:42px;border-radius:12px;
    background:#fce4ec;display:flex;align-items:center;justify-content:center;
    font-size:1.3rem;flex-shrink:0;
}
.fd-reward-title{font-size:0.92rem;font-weight:700;color:#333;margin-bottom:2px;}
.fd-reward-desc{font-size:0.78rem;color:#888;line-height:1.45;}
.fd-reward-badge{
    display:inline-block;margin-top:6px;
    font-size:0.68rem;font-weight:700;color:#e91e8c;
    background:#fce4ec;padding:3px 8px;border-radius:999px;
}
.fd-reward-more{
    display:block;text-align:center;margin-top:0.4rem;
    font-size:0.82rem;font-weight:600;color:#e91e8c;text-decoration:none;
}

/* ── Withdraw history ── */
.fd-wh-head{
    display:flex;align-items:center;justify-content:space-between;
    padding:0 1.2rem;margin-bottom:0.75rem;
}
.fd-wh-link{
    font-size:0.78rem;font-weight:600;color:#e91e8c;text-decoration:none;
}
.fd-wh-link:hover{text-decoration:underline;}
.fd-wh-list{
    display:flex;flex-direction:column;gap:8px;
    padding:0 1.2rem;margin-bottom:1.2rem;
}
.fd-wh-item{
    display:flex;align-items:center;justify-content:space-between;gap:10px;
    background:#fff;border:1.5px solid #f0e0ec;border-radius:14px;
    padding:0.85rem 1rem;box-shadow:0 2px 10px rgba(233,30,140,0.05);
}
.fd-wh-amt{font-size:0.88rem;font-weight:700;color:#333;}
.fd-wh-date{font-size:0.72rem;color:#bbb;margin-top:2px;}
.fd-wh-badge{
    font-size:0.65rem;font-weight:700;padding:3px 9px;border-radius:20px;white-space:nowrap;
}
.fd-wh-empty{
    margin:0 1.2rem 1.2rem;padding:1.2rem;text-align:center;
    background:#fff;border:1.5px dashed #f0d0e4;border-radius:14px;
    font-size:0.82rem;color:#bbb;
}
</style>

<div class="fd-wrap">

    {{-- ── Hero ── --}}
    <div class="fd-hero">
        <div class="fd-avatar-wrap">
            <div class="fd-avatar-ring"></div>
            @if($user->image)
                <img src="{{ asset('assets/images/user/profile/'.$user->image) }}"
                     class="fd-avatar" alt="{{ $displayName }}">
            @else
                <div class="fd-avatar" style="background:rgba(255,255,255,0.25);">
                    🌸
                </div>
            @endif
        </div>

        <div class="fd-name">{{ $displayName }}</div>

        <div class="fd-wallet-row">
            <button class="fd-ref-btn" onclick="copyRefLink()">
                <i class="fas fa-copy"></i>
                <span id="refBtnText">Copy Refer Link</span>
            </button>
        </div>
        <input type="hidden" id="refLinkVal" value="{{ $refLink }}">
    </div>

    {{-- ── Notice ── --}}
    @if($featureCaption)
    <div class="fd-notice">
        <div class="fd-notice-icon"><i class="fas fa-bullhorn"></i></div>
        <marquee behavior="scroll" direction="left" scrollamount="3">
            {{ __($featureCaption->data_values->heading ?? 'Welcome to Flower Earn!') }}
        </marquee>
    </div>
    @endif

    {{-- ── Menu grid ── --}}
    <div class="fd-section">
        <div class="fd-sec-label">Quick Menu</div>
    </div>

    @php
    $menus = [
        ['href'=>route('user.task.index'),  'icon'=>'fas fa-play-circle', 'bg'=>'#e8f5e9','color'=>'#388e3c', 'label'=>'Work'],
        ['href'=>route('user.wallet'),      'icon'=>'fas fa-wallet',      'bg'=>'#f3e5f5','color'=>'#7b1fa2', 'label'=>'Wallet'],
        ['href'=>route('user.referred'),    'icon'=>'fas fa-users',       'bg'=>'#fce4ec','color'=>'#e91e8c', 'label'=>'Team'],
        ['href'=>route('user.rewards'),     'icon'=>'fas fa-gift',        'bg'=>'#fff3e0','color'=>'#ef6c00', 'label'=>'Levels & Rewards'],
        ['href'=>route('user.channels'),    'icon'=>'fas fa-bullhorn',    'bg'=>'#e8f5e9','color'=>'#128C7E', 'label'=>'Official Channels'],
        ['href'=>route('user.withdraw'),    'icon'=>'fas fa-arrow-up',    'bg'=>'#fce4ec','color'=>'#c2185b', 'label'=>'Withdraw'],
    ];
    @endphp

    <div class="fd-grid">
        @foreach($menus as $m)
        <a href="{{ $m['href'] }}"
           class="fd-menu-item"
           @if(!empty($m['external'])) target="_blank" rel="noopener noreferrer" @endif>
            <div class="fd-menu-icon" style="background:{{ $m['bg'] }};">
                <i class="{{ $m['icon'] }}" style="color:{{ $m['color'] }};"></i>
            </div>
            <span class="fd-menu-label">{{ $m['label'] }}</span>
        </a>
        @endforeach
    </div>

    <a href="{{ route('user.contact') }}" class="fd-contact-btn">
        <i class="fas fa-headset"></i> Contact Us
    </a>

    {{-- ── Stats ── --}}
    <div class="fd-section">
        <div class="fd-sec-label">Today's Overview</div>
    </div>
    <div class="fd-stats" style="margin-bottom:1.2rem;">
        <div class="fd-stat">
            <div class="fd-stat-val">{{ $completedToday }} / {{ $user->daily_limit }}</div>
            <div class="fd-stat-lbl">Tasks Today</div>
        </div>
    </div>

    @if($dashboardRewards->count() > 0)
    <div class="fd-section">
        <div class="fd-sec-label">Rewards</div>
    </div>
    <div class="fd-rewards-grid">
        @foreach($dashboardRewards as $reward)
        <div class="fd-reward-card">
            <div class="fd-reward-icon">
                @if($reward->icon && str_contains($reward->icon, 'fa-'))
                    <i class="{{ $reward->icon }}" style="color:#e91e8c;"></i>
                @else
                    {{ $reward->icon ?: '🎁' }}
                @endif
            </div>
            <div>
                <div class="fd-reward-title">{{ $reward->title }}</div>
                @if($reward->description)
                <div class="fd-reward-desc">{{ $reward->description }}</div>
                @endif
                @if($reward->badge)
                <span class="fd-reward-badge">{{ $reward->badge }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <a href="{{ route('user.rewards') }}" class="fd-reward-more">View All Rewards →</a>
    @endif

    {{-- ── Withdraw History ── --}}
    <div class="fd-section">
        <div class="fd-sec-label">Withdraw History</div>
    </div>
    <div class="fd-wh-head">
        <span style="font-size:0.78rem;color:#aaa;">Recent withdrawals</span>
        <a href="{{ url('/user/withdraw-history') }}" class="fd-wh-link">View History</a>
    </div>
    @if($recentWithdraws->count() > 0)
    <div class="fd-wh-list">
        @foreach($recentWithdraws as $w)
        @php
            $isApproved = $w->status == 1;
            $isPending  = $w->status == 2;
            $badgeBg    = $isApproved ? '#e8f5e9' : ($isPending ? '#fff8e1' : '#fce4ec');
            $badgeColor = $isApproved ? '#16a34a' : ($isPending ? '#d97706' : '#e91e8c');
            $statusText = $isApproved ? 'Approved' : ($isPending ? 'Pending' : 'Rejected');
        @endphp
        <div class="fd-wh-item">
            <div>
                <div class="fd-wh-amt">Rs {{ showAmount($w->amount) }}</div>
                <div class="fd-wh-date">{{ showDateTime($w->created_at, 'M d, Y · h:i A') }}</div>
            </div>
            <span class="fd-wh-badge" style="background:{{ $badgeBg }};color:{{ $badgeColor }};">{{ $statusText }}</span>
        </div>
        @endforeach
    </div>
    @else
    <div class="fd-wh-empty">No withdrawals yet.</div>
    @endif

    {{-- ── Referral card ── --}}
    <div class="fd-section">
        <div class="fd-sec-label">Referral</div>
    </div>
    <div class="fd-ref-card">
        <div class="fd-ref-card-title">
            <i class="fas fa-link"></i> Your Referral Link
        </div>
        <div class="fd-ref-input-row">
            <input type="text" value="{{ $refLink }}" readonly id="refLinkInput">
            <button class="fd-ref-copy" id="copyBtn" onclick="copyRefFull()">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>

</div>

<script>
// Copy refer link (hero button)
function copyRefLink() {
    const val = document.getElementById('refLinkVal').value;
    navigator.clipboard.writeText(val).then(function() {
        const btn = document.getElementById('refBtnText');
        btn.textContent = 'Copied! ✓';
        setTimeout(() => btn.textContent = 'Copy Refer Link', 2000);
    }).catch(function() {
        const el = document.getElementById('refLinkVal');
        el.select(); document.execCommand('copy');
    });
}

// Copy refer link (card button)
function copyRefFull() {
    const val = document.getElementById('refLinkInput').value;
    const btn = document.getElementById('copyBtn');
    navigator.clipboard.writeText(val).then(function() {
        btn.classList.add('copied');
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        setTimeout(() => {
            btn.classList.remove('copied');
            btn.innerHTML = '<i class="fas fa-copy"></i> Copy';
        }, 2000);
    }).catch(function() {
        document.getElementById('refLinkInput').select();
        document.execCommand('copy');
    });
}
</script>

@endsection
