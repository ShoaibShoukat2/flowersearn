@extends($activeTemplate.'layouts.dashboard')
@section('page-title', 'My Team')

@section('content')
@if (empty(auth()->user()->pv))
    @php redirect('/user/deposit')->send(); @endphp
@endif
@auth
    @if(auth()->user()->plan_id == 0)
        @php redirect('/user/pending')->send(); @endphp
    @endif
@endauth

@php
    use App\Models\User;
    $authUser  = Auth::user();
    $userCount = User::where('ref_by', $authUser->id)
        ->whereHas('deposit', function($q){ $q->where('status',1); })
        ->count();
    $totalRefs = User::where('ref_by', $authUser->id)->count();
@endphp

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{box-sizing:border-box;}
.tm-wrap{font-family:'Poppins',sans-serif;max-width:520px;margin:0 auto;padding:0 0 3rem;position:relative;z-index:1;}

/* ── Floating petals ── */
.tm-petals{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.tm-petal{position:absolute;opacity:0;animation:petalFloat linear infinite;}
@keyframes petalFloat{
    0%  {opacity:0;transform:translateY(100vh) rotate(0deg);}
    10% {opacity:0.3;}90%{opacity:0.15;}
    100%{opacity:0;transform:translateY(-10vh) rotate(360deg);}
}

/* ── Hero ── */
.tm-hero{
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 50%,#ad1457 100%);
    border-radius:0 0 36px 36px;
    padding:2rem 1.4rem 2.8rem;
    text-align:center;
    position:relative;overflow:hidden;
    box-shadow:0 10px 36px rgba(233,30,140,0.28);
    margin-bottom:1.5rem;
}
.tm-hero::before{content:'🌸';position:absolute;font-size:100px;opacity:0.07;top:-15px;left:-15px;transform:rotate(-15deg);pointer-events:none;}
.tm-hero::after{content:'🌺';position:absolute;font-size:80px;opacity:0.07;bottom:-10px;right:-5px;transform:rotate(15deg);pointer-events:none;}

.tm-hero-emoji{
    font-size:4rem;display:block;margin:0 auto 0.8rem;
    animation:heroFloat 3s ease-in-out infinite;
    filter:drop-shadow(0 4px 16px rgba(255,255,255,0.3));
    position:relative;z-index:2;
}
@keyframes heroFloat{
    0%,100%{transform:translateY(0) rotate(0deg) scale(1);}
    30%    {transform:translateY(-12px) rotate(-8deg) scale(1.08);}
    70%    {transform:translateY(-6px) rotate(6deg) scale(1.04);}
}
.tm-hero h1{font-size:1.8rem;font-weight:800;color:#fff;margin-bottom:0.3rem;position:relative;z-index:2;text-shadow:0 2px 10px rgba(0,0,0,0.1);}
.tm-hero p{font-size:0.86rem;color:rgba(255,255,255,0.85);position:relative;z-index:2;margin-bottom:1.4rem;}

/* ── Stat pills ── */
.tm-pills{
    display:flex;justify-content:center;gap:10px;flex-wrap:wrap;
    position:relative;z-index:2;
}
.tm-pill{
    display:inline-flex;align-items:center;gap:7px;
    background:rgba(255,255,255,0.18);
    border:1.5px solid rgba(255,255,255,0.3);
    border-radius:20px;padding:7px 16px;
    color:#fff;font-size:0.82rem;font-weight:700;
    backdrop-filter:blur(8px);
    animation:pillPop 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
}
.tm-pill:nth-child(2){animation-delay:0.1s;}
.tm-pill:nth-child(3){animation-delay:0.2s;}
@keyframes pillPop{from{opacity:0;transform:scale(0.8);}to{opacity:1;transform:scale(1);}}

/* ── Stat cards (floating) ── */
.tm-stats{
    display:grid;grid-template-columns:repeat(3,1fr);gap:10px;
    padding:0 1rem;
    margin-top:-1.4rem;
    position:relative;z-index:10;
    margin-bottom:1.4rem;
}
.tm-stat{
    background:#fff;border-radius:18px;
    border:1.5px solid rgba(233,30,140,0.1);
    padding:1rem 0.5rem;text-align:center;
    box-shadow:0 6px 20px rgba(233,30,140,0.10);
    animation:statPop 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
}
.tm-stat:nth-child(1){animation-delay:0.1s;}
.tm-stat:nth-child(2){animation-delay:0.2s;}
.tm-stat:nth-child(3){animation-delay:0.3s;}
@keyframes statPop{from{opacity:0;transform:translateY(16px) scale(0.9);}to{opacity:1;transform:translateY(0) scale(1);}}
.tm-stat-val{
    font-size:1.3rem;font-weight:800;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;line-height:1;margin-bottom:3px;
}
.tm-stat-lbl{font-size:0.65rem;font-weight:600;color:#ccc;text-transform:uppercase;letter-spacing:0.05em;}

/* ── Referral link card ── */
.tm-card{
    background:#fff;border-radius:22px;
    border:1.8px solid #f0e0ec;
    padding:1.4rem 1.3rem;
    margin:0 0.8rem 1.2rem;
    box-shadow:0 6px 24px rgba(233,30,140,0.07);
    animation:cardIn 0.5s ease both;
}
.tm-card:nth-child(3){animation-delay:0.1s;}
.tm-card:nth-child(4){animation-delay:0.2s;}
@keyframes cardIn{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}

.tm-card-title{
    font-size:0.82rem;font-weight:700;color:#888;
    text-transform:uppercase;letter-spacing:0.07em;
    display:flex;align-items:center;gap:7px;
    margin-bottom:0.9rem;
}
.tm-card-title i{color:#e91e8c;font-size:13px;}

/* ref link row */
.tm-ref-row{
    display:flex;align-items:center;gap:8px;
    background:#fdf5f9;border:1.8px solid #f0d0e4;
    border-radius:12px;padding:0.6rem 0.9rem;
}
.tm-ref-row input{
    flex:1;border:none;outline:none;background:transparent;
    font-size:0.78rem;color:#888;font-family:'Poppins',sans-serif;min-width:0;
}
.tm-copy-btn{
    flex-shrink:0;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    border:none;border-radius:9px;
    padding:7px 13px;color:#fff;
    font-size:0.75rem;font-weight:700;
    cursor:pointer;font-family:'Poppins',sans-serif;
    transition:all 0.2s;white-space:nowrap;
    display:flex;align-items:center;gap:5px;
}
.tm-copy-btn:hover{transform:scale(1.04);box-shadow:0 4px 14px rgba(233,30,140,0.3);}
.tm-copy-btn.copied{background:linear-gradient(135deg,#22c55e,#16a34a);}

/* ── User list ── */
.tm-user-item{
    display:flex;align-items:center;gap:12px;
    padding:0.85rem 0;
    border-bottom:1px solid #fce4f0;
    animation:itemIn 0.4s ease both;
    transition:transform 0.2s;
}
.tm-user-item:hover{transform:translateX(4px);}
.tm-user-item:last-child{border-bottom:none;}
@keyframes itemIn{from{opacity:0;transform:translateX(-12px);}to{opacity:1;transform:translateX(0);}}

.tm-user-avatar{
    width:44px;height:44px;border-radius:50%;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    display:flex;align-items:center;justify-content:center;
    font-size:1.2rem;flex-shrink:0;
    box-shadow:0 4px 12px rgba(233,30,140,0.22);
}
.tm-user-name{font-size:0.88rem;font-weight:700;color:#1a1a2e;}
.tm-user-email{font-size:0.72rem;color:#ccc;}
.tm-user-level{
    margin-left:auto;flex-shrink:0;
    background:#fce4f0;color:#e91e8c;
    font-size:0.72rem;font-weight:700;
    padding:4px 10px;border-radius:20px;
}

/* empty */
.tm-empty{text-align:center;padding:2.5rem 1rem;}
.tm-empty-emoji{font-size:3rem;margin-bottom:0.6rem;animation:heroFloat 2s ease-in-out infinite;display:block;}
.tm-empty p{font-size:0.86rem;color:#ccc;}
</style>

{{-- Floating petals --}}
<div class="tm-petals" id="tmPetals"></div>

<div class="tm-wrap">

    {{-- ── Hero ── --}}
    <div class="tm-hero">
        <span class="tm-hero-emoji">🌸</span>
        <h1>My Team</h1>
        <p>Grow your team and earn more rewards</p>
        <div class="tm-pills">
            <div class="tm-pill"><i class="fas fa-users"></i> {{ $totalRefs }} Total</div>
            <div class="tm-pill"><i class="fas fa-check-circle"></i> {{ $userCount }} Active</div>
            <div class="tm-pill"><i class="fas fa-layer-group"></i> Level {{ $user->level }}</div>
        </div>
    </div>

    {{-- ── Floating stat cards ── --}}
    <div class="tm-stats">
        <div class="tm-stat">
            <div class="tm-stat-val">{{ $totalRefs }}</div>
            <div class="tm-stat-lbl">Referred</div>
        </div>
        <div class="tm-stat">
            <div class="tm-stat-val">{{ $userCount }}</div>
            <div class="tm-stat-lbl">Active</div>
        </div>
        <div class="tm-stat">
            <div class="tm-stat-val">{{ $user->daily_limit }}</div>
            <div class="tm-stat-lbl">Task Limit</div>
        </div>
    </div>

    {{-- ── Referral link ── --}}
    <div class="tm-card">
        <div class="tm-card-title"><i class="fas fa-link"></i> Your Referral Link</div>
        <div class="tm-ref-row">
            <input type="text" id="refLink"
                   value="{{ route('home') }}/ref/{{ $user->ref }}" readonly>
            <button class="tm-copy-btn" id="copyBtn" onclick="copyRef()">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>

    {{-- ── Referred users list ── --}}
    <div class="tm-card">
        <div class="tm-card-title"><i class="fas fa-users"></i> Referred Members</div>

        @forelse($refUsers as $i => $ru)
        <div class="tm-user-item" style="animation-delay:{{ $i * 0.05 }}s;">
            <div class="tm-user-avatar">🌸</div>
            <div>
                <div class="tm-user-name">{{ $ru->fullname ?: $ru->username }}</div>
                <div class="tm-user-email">{{ $ru->email ?: 'No email' }}</div>
            </div>
            <div class="tm-user-level">Lv {{ $ru->level }}</div>
        </div>
        @empty
        <div class="tm-empty">
            <span class="tm-empty-emoji">🌱</span>
            <p>No referred users yet.<br>Share your link and grow your team!</p>
        </div>
        @endforelse

        @if($refUsers->hasPages())
        <div style="margin-top:1rem;">{{ $refUsers->links() }}</div>
        @endif
    </div>

</div>

<script>
// Floating petals
(function(){
    const c=document.getElementById('tmPetals');
    const e=['🌸','🌺','🌼','🌷','✨','💫'];
    for(let i=0;i<12;i++){
        const p=document.createElement('div');
        p.className='tm-petal';
        p.textContent=e[Math.floor(Math.random()*e.length)];
        p.style.left=Math.random()*100+'%';
        p.style.fontSize=(0.7+Math.random()*0.8)+'rem';
        p.style.animationDuration=(9+Math.random()*10)+'s';
        p.style.animationDelay=(Math.random()*10)+'s';
        c.appendChild(p);
    }
})();

// Copy referral link
function copyRef(){
    const val = document.getElementById('refLink').value;
    const btn = document.getElementById('copyBtn');
    navigator.clipboard.writeText(val).then(function(){
        btn.classList.add('copied');
        btn.innerHTML='<i class="fas fa-check"></i> Copied!';
        setTimeout(()=>{
            btn.classList.remove('copied');
            btn.innerHTML='<i class="fas fa-copy"></i> Copy';
        },2000);
    }).catch(function(){
        document.getElementById('refLink').select();
        document.execCommand('copy');
    });
}
</script>

@endsection
