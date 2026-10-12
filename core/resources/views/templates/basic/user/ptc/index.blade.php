@php $pageTitle = 'Work Room'; @endphp
@extends($activeTemplate.'layouts.dashboard')

@section('content')
@if (empty(auth()->user()->pv))
    <script>window.location="/user/deposit";</script>
@elseif (in_array(auth()->user()->pv, ['rt']))
    <script>window.location="/user/pending";</script>
@endif

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{box-sizing:border-box;}
.wr-wrap{font-family:'Poppins',sans-serif;max-width:600px;margin:0 auto;padding:0 0 2rem;position:relative;z-index:1;}

/* ── Floating petals bg ── */
.wr-petals{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.wr-petal{position:absolute;opacity:0;animation:petalDrift linear infinite;}
@keyframes petalDrift{
    0%  {opacity:0;transform:translateY(100vh) rotate(0deg);}
    10% {opacity:0.3;}90%{opacity:0.12;}
    100%{opacity:0;transform:translateY(-10vh) rotate(360deg);}
}

/* ── Hero ── */
.wr-hero{
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 50%,#ad1457 100%);
    border-radius:0 0 36px 36px;
    padding:1.8rem 1.4rem 2.5rem;
    text-align:center;position:relative;overflow:hidden;
    box-shadow:0 10px 36px rgba(233,30,140,0.28);
    margin-bottom:1.5rem;
}
.wr-hero::before{content:'🌸';position:absolute;font-size:100px;opacity:0.07;top:-15px;left:-15px;transform:rotate(-15deg);pointer-events:none;}
.wr-hero::after{content:'🌺';position:absolute;font-size:80px;opacity:0.07;bottom:-10px;right:-5px;transform:rotate(15deg);pointer-events:none;}

.wr-hero-emoji{
    font-size:3.5rem;display:block;margin:0 auto 0.7rem;
    animation:heroFloat 3s ease-in-out infinite;
    filter:drop-shadow(0 4px 16px rgba(255,255,255,0.3));
    position:relative;z-index:2;
}
@keyframes heroFloat{
    0%,100%{transform:translateY(0) rotate(0deg) scale(1);}
    30%    {transform:translateY(-12px) rotate(-8deg) scale(1.08);}
    70%    {transform:translateY(-6px) rotate(6deg) scale(1.04);}
}
.wr-hero h1{font-size:1.7rem;font-weight:800;color:#fff;margin-bottom:0.3rem;position:relative;z-index:2;text-shadow:0 2px 10px rgba(0,0,0,0.1);}
.wr-hero p{font-size:0.86rem;color:rgba(255,255,255,0.85);position:relative;z-index:2;margin-bottom:1.2rem;}

/* progress */
.wr-prog-wrap{position:relative;z-index:2;background:rgba(255,255,255,0.2);border-radius:20px;height:10px;margin:0 auto;max-width:260px;overflow:hidden;}
.wr-prog-bar{height:100%;border-radius:20px;background:linear-gradient(90deg,#fff,rgba(255,255,255,0.7));transition:width 1.2s cubic-bezier(0.34,1.56,0.64,1);box-shadow:0 0 10px rgba(255,255,255,0.5);}
.wr-prog-lbl{font-size:0.76rem;color:rgba(255,255,255,0.9);font-weight:600;margin-top:6px;position:relative;z-index:2;}

/* ── Stat cards ── */
.wr-stats{
    display:grid;grid-template-columns:repeat(3,1fr);gap:10px;
    padding:0 1rem;margin-top:-1.3rem;position:relative;z-index:10;margin-bottom:1.4rem;
}
.wr-stat{
    background:#fff;border-radius:18px;border:1.5px solid rgba(233,30,140,0.1);
    padding:0.9rem 0.5rem;text-align:center;
    box-shadow:0 6px 20px rgba(233,30,140,0.10);
    animation:statPop 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
}
.wr-stat:nth-child(1){animation-delay:0.1s;}
.wr-stat:nth-child(2){animation-delay:0.2s;}
.wr-stat:nth-child(3){animation-delay:0.3s;}
@keyframes statPop{from{opacity:0;transform:translateY(16px) scale(0.9);}to{opacity:1;transform:translateY(0) scale(1);}}
.wr-stat-val{font-size:1.2rem;font-weight:800;background:linear-gradient(135deg,#ff4da6,#e91e8c);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1;margin-bottom:3px;}
.wr-stat-lbl{font-size:0.65rem;font-weight:600;color:#ccc;text-transform:uppercase;letter-spacing:0.05em;}

/* ── Section label ── */
.wr-sec{padding:0 1rem;margin-bottom:0.8rem;}
.wr-sec-title{font-size:0.78rem;font-weight:700;color:#bbb;text-transform:uppercase;letter-spacing:0.08em;}

/* ══ TASK CARDS ══ */
.wr-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;padding:0 1rem;}
@media(min-width:480px){.wr-grid{grid-template-columns:repeat(3,1fr);}}

.wr-card{
    background:#fff;border:1.8px solid #f0e0ec;border-radius:22px;
    padding:1.3rem 0.8rem 1rem;text-align:center;
    position:relative;overflow:hidden;
    box-shadow:0 4px 18px rgba(233,30,140,0.07);
    animation:cardIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
    transition:transform 0.25s,box-shadow 0.25s,border-color 0.25s;
}
.wr-card:hover{transform:translateY(-5px) scale(1.02);box-shadow:0 14px 36px rgba(233,30,140,0.15);border-color:#e91e8c;}
@keyframes cardIn{from{opacity:0;transform:translateY(20px) scale(0.92);}to{opacity:1;transform:translateY(0) scale(1);}}
.wr-card:nth-child(1){animation-delay:0.05s;}.wr-card:nth-child(2){animation-delay:0.10s;}
.wr-card:nth-child(3){animation-delay:0.15s;}.wr-card:nth-child(4){animation-delay:0.20s;}
.wr-card:nth-child(5){animation-delay:0.25s;}.wr-card:nth-child(6){animation-delay:0.30s;}

/* shimmer */
.wr-card::before{
    content:'';position:absolute;top:0;left:-60%;width:40%;height:100%;
    background:linear-gradient(90deg,transparent,rgba(255,255,255,0.45),transparent);
    transform:skewX(-20deg);transition:left 0.5s;pointer-events:none;
}
.wr-card:hover::before{left:120%;}

/* gift emoji center */
.wr-gift{
    font-size:3.8rem;display:block;margin:0 auto 0.7rem;
    cursor:pointer;user-select:none;
    animation:giftBob 2.5s ease-in-out infinite;
    filter:drop-shadow(0 4px 14px rgba(233,30,140,0.22));
    line-height:1;transition:transform 0.2s;
}
.wr-gift:hover{transform:scale(1.15) rotate(-8deg);}
.wr-card:nth-child(2) .wr-gift{animation-delay:0.4s;}
.wr-card:nth-child(3) .wr-gift{animation-delay:0.8s;}
.wr-card:nth-child(4) .wr-gift{animation-delay:1.2s;}
.wr-card:nth-child(5) .wr-gift{animation-delay:0.6s;}
.wr-card:nth-child(6) .wr-gift{animation-delay:1.0s;}
@keyframes giftBob{
    0%,100%{transform:translateY(0) rotate(0deg) scale(1);}
    25%    {transform:translateY(-10px) rotate(-6deg) scale(1.06);}
    75%    {transform:translateY(-5px) rotate(5deg) scale(1.03);}
}

.wr-card-title{font-size:0.82rem;font-weight:700;color:#1a1a2e;margin-bottom:0.5rem;}
.wr-earn{
    font-size:1rem;font-weight:800;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;margin-bottom:0.3rem;
}
.wr-dur{font-size:0.72rem;color:#ccc;margin-bottom:0.8rem;}

.wr-start-btn{
    display:block;width:100%;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    background-size:200% auto;
    color:#fff;border:none;border-radius:12px;
    padding:9px 8px;font-size:0.8rem;font-weight:700;
    font-family:'Poppins',sans-serif;text-decoration:none;
    box-shadow:0 4px 14px rgba(233,30,140,0.28);
    transition:all 0.3s;position:relative;overflow:hidden;
}
.wr-start-btn:hover{background-position:right center;transform:translateY(-2px);box-shadow:0 7px 20px rgba(233,30,140,0.38);color:#fff;}

/* ── Flower overlay ── */
.flower-overlay{
    position:absolute;inset:0;border-radius:22px;
    display:flex;align-items:center;justify-content:center;
    z-index:20;pointer-events:none;opacity:0;
    background:rgba(255,240,250,0);backdrop-filter:blur(0px);
    transition:opacity 0.4s ease,background 0.4s ease,backdrop-filter 0.4s ease;
}
.flower-overlay.open{opacity:1;pointer-events:all;background:rgba(255,240,250,0.93);backdrop-filter:blur(8px);}
.flower-inner{
    display:flex;flex-direction:column;align-items:center;gap:6px;
    transform:translateY(70px) scale(0.3);opacity:0;
    transition:transform 0.55s cubic-bezier(0.34,1.56,0.64,1),opacity 0.4s ease;
}
.flower-overlay.open .flower-inner{transform:translateY(0) scale(1);opacity:1;}
.flower-big{font-size:3.5rem;filter:drop-shadow(0 6px 18px rgba(233,30,140,0.4));}
.flower-overlay.open .flower-big{animation:flowerSpin 0.7s cubic-bezier(0.34,1.56,0.64,1) both;}
@keyframes flowerSpin{0%{transform:rotate(-180deg) scale(0);}65%{transform:rotate(12deg) scale(1.18);}100%{transform:rotate(0deg) scale(1);}}
.flower-petals{display:flex;gap:4px;font-size:1.3rem;}
.flower-petals span{opacity:0;transform:translateY(20px) scale(0);}
.flower-overlay.open .flower-petals span{animation:petalPop 0.5s cubic-bezier(0.34,1.56,0.64,1) both;}
.flower-overlay.open .flower-petals span:nth-child(1){animation-delay:0.15s;}
.flower-overlay.open .flower-petals span:nth-child(2){animation-delay:0.22s;}
.flower-overlay.open .flower-petals span:nth-child(3){animation-delay:0.29s;}
.flower-overlay.open .flower-petals span:nth-child(4){animation-delay:0.36s;}
.flower-overlay.open .flower-petals span:nth-child(5){animation-delay:0.43s;}
@keyframes petalPop{0%{opacity:0;transform:translateY(20px) scale(0) rotate(-30deg);}65%{opacity:1;transform:translateY(-8px) scale(1.25) rotate(6deg);}100%{opacity:1;transform:translateY(0) scale(1) rotate(0deg);}}
.flower-close-btn{
    margin-top:4px;background:linear-gradient(135deg,#ff4da6,#e91e8c);
    border:none;border-radius:20px;color:#fff;font-size:0.75rem;font-weight:700;
    padding:5px 16px;cursor:pointer;font-family:'Poppins',sans-serif;
    box-shadow:0 4px 14px rgba(233,30,140,0.30);transition:transform 0.2s;
}
.flower-close-btn:hover{transform:scale(1.07);}
.flower-overlay.closing .flower-inner{transform:translateY(-60px) scale(0.2);opacity:0;transition:transform 0.4s cubic-bezier(0.55,0,1,0.45),opacity 0.35s ease;}
.flower-overlay.closing{opacity:0;background:rgba(255,240,250,0);backdrop-filter:blur(0px);transition:opacity 0.4s ease 0.1s,background 0.4s ease,backdrop-filter 0.4s ease;}
.flower-particle{position:absolute;font-size:1rem;pointer-events:none;animation:particleFly 1s ease-out both;}
@keyframes particleFly{0%{opacity:1;transform:translate(0,0) scale(1);}100%{opacity:0;transform:translate(var(--tx),var(--ty)) scale(0.2);}}

/* ── Locked ── */
.wr-locked{
    background:#fff;border:1.8px solid #fce4f0;border-radius:22px;
    padding:2.5rem 1.5rem;text-align:center;margin:0 1rem;
    box-shadow:0 6px 24px rgba(233,30,140,0.08);
}
.wr-locked-emoji{font-size:3rem;margin-bottom:0.8rem;display:block;}
.wr-locked h3{font-size:1.2rem;font-weight:800;color:#1a1a2e;margin-bottom:0.5rem;}
.wr-locked p{font-size:0.84rem;color:#aaa;line-height:1.6;}

/* ── Level card ── */
.wr-level-card{
    background:#fff;border:1.8px solid #f0e0ec;border-radius:22px;
    padding:1.4rem 1.3rem;margin:1.2rem 1rem 0;
    box-shadow:0 6px 24px rgba(233,30,140,0.07);
    animation:cardIn 0.5s ease 0.3s both;
}
.wr-level-head{
    display:flex;align-items:center;gap:10px;
    margin-bottom:1rem;
}
.wr-level-head i{color:#e91e8c;font-size:16px;}
.wr-level-head span{font-size:0.88rem;font-weight:700;color:#1a1a2e;}
.wr-level-badge{
    display:inline-flex;align-items:center;gap:6px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    color:#fff;border-radius:20px;padding:5px 14px;
    font-size:0.82rem;font-weight:700;margin-bottom:1rem;
}
.wr-next-prog{background:#fdf5f9;border:1.5px solid #f0d0e4;border-radius:14px;padding:0.9rem 1rem;margin-bottom:0.8rem;}
.wr-next-prog p{font-size:0.8rem;color:#888;margin-bottom:0.5rem;}
.wr-prog-track{background:#f0d0e4;border-radius:20px;height:8px;overflow:hidden;}
.wr-prog-fill{height:100%;border-radius:20px;background:linear-gradient(90deg,#ff4da6,#e91e8c);transition:width 1.2s cubic-bezier(0.34,1.56,0.64,1);}
.wr-prog-note{font-size:0.75rem;color:#e91e8c;font-weight:600;margin-top:5px;}
.wr-ref-code{
    background:#fdf5f9;border:1.5px solid #f0d0e4;border-radius:12px;
    padding:0.8rem 1rem;text-align:center;
}
.wr-ref-code-lbl{font-size:0.72rem;font-weight:600;color:#bbb;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;}
.wr-ref-code-val{font-size:1rem;font-weight:800;color:#e91e8c;letter-spacing:2px;word-break:break-all;}
</style>

{{-- Floating petals --}}
<div class="wr-petals" id="wrPetals"></div>

<div class="wr-wrap">

    {{-- ── Hero ── --}}
    <div class="wr-hero">
        <span class="wr-hero-emoji">🌸</span>
        <h1>Work Room</h1>
        <p>Tap the gift to reveal your flower reward!</p>
        @php
            $todayViews = \App\Models\PtcView::where('user_id',auth()->id())->whereDate('view_date',today())->count();
            $limit = auth()->user()->daily_limit;
            $pct = $limit > 0 ? min(100, round(($todayViews/$limit)*100)) : 0;
        @endphp
        <div class="wr-prog-wrap">
            <div class="wr-prog-bar" id="heroBar" style="width:{{ $pct }}%;"></div>
        </div>
        <div class="wr-prog-lbl">{{ $todayViews }} / {{ $limit }} tasks today</div>
    </div>

    {{-- ── Stats ── --}}
    <div class="wr-stats">
        <div class="wr-stat">
            <div class="wr-stat-val">{{ $todayViews }}</div>
            <div class="wr-stat-lbl">Today</div>
        </div>
        <div class="wr-stat">
            <div class="wr-stat-val">{{ $limit }}</div>
            <div class="wr-stat-lbl">Limit</div>
        </div>
        <div class="wr-stat">
            <div class="wr-stat-val">{{ $referralsCount }}</div>
            <div class="wr-stat-lbl">Referrals</div>
        </div>
    </div>

    {{-- ── Locked message ── --}}
    @if(isset($lockMessage))
    <div class="wr-locked">
        <span class="wr-locked-emoji">🔒</span>
        <h3>Tasks Locked</h3>
        <p>{!! $lockMessage !!}</p>
    </div>

    {{-- ── Tasks ── --}}
    @elseif($ads->count() > 0)
    <div class="wr-sec">
        <div class="wr-sec-title">Available Tasks ({{ $ads->count() }})</div>
    </div>
    <div class="wr-grid">
        @foreach($ads as $ad)
        <div class="wr-card">

            {{-- Flower overlay --}}
            <div class="flower-overlay" id="flower-{{ $ad->id }}">
                <div class="flower-inner">
                    <div class="flower-big">🌸</div>
                    <div class="flower-petals">
                        <span>🌺</span><span>🌼</span><span>🌸</span>
                        <span>🌻</span><span>🌷</span>
                    </div>
                    <button class="flower-close-btn" data-id="{{ $ad->id }}">✕ Close</button>
                </div>
            </div>

            {{-- Gift emoji — tap to open flower --}}
            <span class="wr-gift tap-gift" data-id="{{ $ad->id }}">🎁</span>

            <div class="wr-card-title">{{ __($ad->title) }}</div>
            <div class="wr-earn">Rs {{ showAmount($ad->amount) }}</div>
            <div class="wr-dur">⏱ {{ $ad->duration }}s</div>

            <a href="{{ route('user.ptc.show', encrypt($ad->id.'|'.auth()->user()->id)) }}"
               class="wr-start-btn wr-start-task"
               data-ad-id="{{ $ad->id }}">
                <i class="fas fa-play-circle"></i> Start Task
            </a>
        </div>
        @endforeach
    </div>

    @else
    <div class="wr-locked">
        <span class="wr-locked-emoji">🌱</span>
        <h3>No Tasks Available</h3>
        <p>Check back later for new tasks!</p>
    </div>
    @endif

    {{-- ── Level card ── --}}
    <div class="wr-level-card">
        <div class="wr-level-head">
            <i class="fas fa-trophy"></i>
            <span>Your Progress</span>
        </div>

        <div class="wr-level-badge">
            <i class="fas fa-layer-group"></i> Level {{ auth()->user()->level }} / 8
        </div>

        @if($nextLevel)
        <div class="wr-next-prog">
            <p>Next Level {{ $nextLevel['level'] }} — {{ $nextLevel['referrals_required'] }} referrals needed</p>
            <div class="wr-prog-track">
                <div class="wr-prog-fill" style="width:{{ min(100, ($referralsCount / $nextLevel['referrals_required']) * 100) }}%;"></div>
            </div>
            <div class="wr-prog-note">{{ max(0, $nextLevel['referrals_required'] - $referralsCount) }} more to go!</div>
        </div>
        @else
        <div class="wr-next-prog" style="text-align:center;">
            <p style="color:#e91e8c;font-weight:700;">🏆 MAX LEVEL REACHED!</p>
        </div>
        @endif

        <div class="wr-ref-code">
            <div class="wr-ref-code-lbl">Your Referral Code</div>
            <div class="wr-ref-code-val">{{ auth()->user()->ref }}</div>
        </div>
    </div>

</div>



<script>
// Floating petals
(function(){
    const c=document.getElementById('wrPetals');
    const e=['🌸','🌺','🌼','🌷','✨','💫'];
    for(let i=0;i<12;i++){
        const p=document.createElement('div');
        p.className='wr-petal';
        p.textContent=e[Math.floor(Math.random()*e.length)];
        p.style.left=Math.random()*100+'%';
        p.style.fontSize=(0.7+Math.random()*0.8)+'rem';
        p.style.animationDuration=(9+Math.random()*10)+'s';
        p.style.animationDelay=(Math.random()*10)+'s';
        c.appendChild(p);
    }
})();

// Flower animation
const PARTICLES=['🌸','🌺','🌼','✨','💫','🌷'];
function spawnParticles(ov){
    for(let i=0;i<8;i++){
        const p=document.createElement('span');
        p.className='flower-particle';
        p.textContent=PARTICLES[Math.floor(Math.random()*PARTICLES.length)];
        const angle=(Math.PI*2/8)*i,dist=50+Math.random()*30;
        p.style.setProperty('--tx',Math.cos(angle)*dist+'px');
        p.style.setProperty('--ty',Math.sin(angle)*dist-15+'px');
        p.style.left='50%';p.style.top='35%';
        p.style.animationDelay=(Math.random()*0.15)+'s';
        ov.appendChild(p);
        setTimeout(()=>p.remove(),1200);
    }
}
/** Pending redirect after "Start Task" (cleared if user closes overlay first). */
let pendingTaskNav=null;

function openFlower(id){
    const ov=document.getElementById('flower-'+id);
    if(!ov)return;
    if(pendingTaskNav&&String(pendingTaskNav.id)!==String(id)){
        clearTimeout(pendingTaskNav.timeoutId);
        pendingTaskNav=null;
    }
    ov.classList.remove('closing','open');
    void ov.offsetWidth;
    requestAnimationFrame(function(){
        requestAnimationFrame(function(){
            ov.classList.add('open');
            spawnParticles(ov);
        });
    });
}
function closeFlower(id){
    const ov=document.getElementById('flower-'+id);
    if(!ov)return;
    if(pendingTaskNav&&String(pendingTaskNav.id)===String(id)){
        clearTimeout(pendingTaskNav.timeoutId);
        pendingTaskNav=null;
    }
    ov.classList.add('closing');
    ov.classList.remove('open');
    setTimeout(()=>ov.classList.remove('closing'),500);
}
function startWorkRoomTask(id,url){
    openFlower(id);
    if(pendingTaskNav){
        clearTimeout(pendingTaskNav.timeoutId);
        pendingTaskNav=null;
    }
    pendingTaskNav={
        id:String(id),
        timeoutId:setTimeout(function(){
            pendingTaskNav=null;
            window.location.href=url;
        },1050)
    };
}
document.addEventListener('click',function(e){
    const start=e.target.closest('.wr-start-task');
    if(start){
        e.preventDefault();
        e.stopPropagation();
        startWorkRoomTask(start.getAttribute('data-ad-id'),start.getAttribute('href'));
        return;
    }
    const gift=e.target.closest('.tap-gift');
    if(gift){e.preventDefault();e.stopPropagation();openFlower(gift.dataset.id);return;}
    const btn=e.target.closest('.flower-close-btn');
    if(btn){e.preventDefault();e.stopPropagation();closeFlower(btn.dataset.id);return;}
    const ov=e.target.closest('.flower-overlay');
    if(ov&&!e.target.closest('.flower-inner'))closeFlower(ov.id.replace('flower-',''));
});

// Animate progress bar
window.addEventListener('load',function(){
    const b=document.getElementById('heroBar');
    if(b){const w=b.style.width;b.style.width='0';setTimeout(()=>b.style.width=w,400);}
});
</script>

@endsection
