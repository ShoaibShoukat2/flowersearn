@extends($activeTemplate.'layouts.dashboard')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">

@php
    $ads = \App\Models\Ptc::where('status', 1)
        ->where('remain', '>', 0)
        ->whereDoesntHave('views', function($q) {
            $q->where('user_id', auth()->id())->whereDate('view_date', today());
        })
        ->inRandomOrder()
        ->limit(50)
        ->get();

    $completedToday = \App\Models\PtcView::where('user_id', auth()->id())
        ->whereDate('view_date', today())->count();
    $dailyLimit     = auth()->user()->daily_limit;
    $pct            = $dailyLimit > 0 ? min(100, round(($completedToday / $dailyLimit) * 100)) : 0;
    $flowerIcons    = ['🌸','🌺','🌼','🌻','🌷','💐','🪷','🏵️'];
    $lockStatus     = $lockStatus ?? getTaskLockStatus();
@endphp

<style>
/* ═══════════════════════════════════════════
   BLOOM GARDEN — Task Room (full redesign)
═══════════════════════════════════════════ */
.bloom-page{
    --bloom-pink:#ff4d9e;
    --bloom-magenta:#e91e8c;
    --bloom-deep:#7b1fa2;
    --bloom-violet:#4a148c;
    --bloom-teal:#00e5c8;
    --bloom-gold:#ffd54f;
    --bloom-glass:rgba(255,255,255,0.08);
    --bloom-glass-border:rgba(255,255,255,0.18);
    font-family:'Outfit',sans-serif;
    max-width:520px;margin:0 auto;
    min-height:100vh;
    position:relative;
    overflow-x:hidden;
    padding-bottom:3rem;
    background:
        radial-gradient(ellipse 80% 50% at 50% -10%, rgba(255,77,158,0.35) 0%, transparent 60%),
        radial-gradient(ellipse 60% 40% at 90% 30%, rgba(123,31,162,0.4) 0%, transparent 55%),
        radial-gradient(ellipse 50% 35% at 10% 70%, rgba(0,229,200,0.15) 0%, transparent 50%),
        linear-gradient(165deg, #0d0221 0%, #1a0535 35%, #2d0a4e 65%, #0d0221 100%);
}

/* animated aurora blobs */
.bloom-aurora{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.bloom-blob{
    position:absolute;border-radius:50%;filter:blur(60px);opacity:0.45;
    animation:blobDrift 12s ease-in-out infinite alternate;
}
.bloom-blob-1{width:280px;height:280px;background:#ff4d9e;top:-80px;left:-60px;animation-duration:14s;}
.bloom-blob-2{width:220px;height:220px;background:#7b1fa2;bottom:20%;right:-50px;animation-delay:-4s;animation-duration:11s;}
.bloom-blob-3{width:180px;height:180px;background:#00e5c8;bottom:-40px;left:20%;opacity:0.2;animation-delay:-7s;animation-duration:16s;}
@keyframes blobDrift{
    0%{transform:translate(0,0) scale(1);}
    100%{transform:translate(30px,-25px) scale(1.15);}
}

/* floating particles */
.bloom-particles{position:fixed;inset:0;pointer-events:none;z-index:1;}
.bloom-particle{
    position:absolute;opacity:0;
    animation:bloomFloat linear infinite;
    text-shadow:0 0 12px rgba(255,77,158,0.6);
}
@keyframes bloomFloat{
    0%{opacity:0;transform:translateY(105vh) rotate(0deg) scale(0.5);}
    8%{opacity:0.7;}
    92%{opacity:0.3;}
    100%{opacity:0;transform:translateY(-8vh) rotate(720deg) scale(1.1);}
}

/* sparkles grid */
.bloom-stars{position:fixed;inset:0;pointer-events:none;z-index:0;}
.bloom-star{
    position:absolute;width:3px;height:3px;background:#fff;border-radius:50%;
    animation:starTwinkle 3s ease-in-out infinite;
}
@keyframes starTwinkle{0%,100%{opacity:0.15;transform:scale(1);}50%{opacity:0.9;transform:scale(1.8);}}

.bloom-inner{position:relative;z-index:2;}

/* ── HEADER ── */
.bloom-header{
    padding:1.6rem 1.25rem 0;
    text-align:center;
}
.bloom-badge{
    display:inline-flex;align-items:center;gap:6px;
    background:var(--bloom-glass);
    border:1px solid var(--bloom-glass-border);
    backdrop-filter:blur(12px);
    border-radius:30px;padding:5px 14px;
    font-size:0.68rem;font-weight:700;letter-spacing:0.12em;
    text-transform:uppercase;color:rgba(255,255,255,0.75);
    margin-bottom:0.9rem;
    animation:badgePulse 3s ease-in-out infinite;
}
.bloom-badge-dot{
    width:7px;height:7px;border-radius:50%;
    background:var(--bloom-teal);
    box-shadow:0 0 10px var(--bloom-teal);
    animation:dotBlink 1.5s ease-in-out infinite;
}
@keyframes badgePulse{0%,100%{box-shadow:0 0 0 0 rgba(255,77,158,0);}50%{box-shadow:0 0 20px rgba(255,77,158,0.25);}}
@keyframes dotBlink{0%,100%{opacity:1;}50%{opacity:0.3;}}

.bloom-title{
    font-family:'Playfair Display',serif;
    font-size:2.1rem;font-weight:700;font-style:italic;
    background:linear-gradient(135deg,#fff 0%,#ffb3d9 40%,#ff4d9e 70%,#ffd54f 100%);
    background-size:200% auto;
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;
    animation:titleShine 4s linear infinite;
    line-height:1.15;margin-bottom:0.4rem;
}
@keyframes titleShine{0%{background-position:0% center;}100%{background-position:200% center;}}
.bloom-subtitle{font-size:0.82rem;color:rgba(255,255,255,0.5);font-weight:400;}

/* ── RING PROGRESS ── */
.bloom-ring-section{
    display:flex;align-items:center;justify-content:center;
    padding:1.5rem 1.25rem 0.5rem;
    animation:fadeUp 0.8s cubic-bezier(0.34,1.56,0.64,1) both;
}
.bloom-ring-wrap{position:relative;width:150px;height:150px;}
.bloom-ring-svg{transform:rotate(-90deg);width:150px;height:150px;}
.bloom-ring-bg{fill:none;stroke:rgba(255,255,255,0.08);stroke-width:10;}
.bloom-ring-fill{
    fill:none;stroke:url(#bloomGrad);stroke-width:10;
    stroke-linecap:round;
    stroke-dasharray:408;stroke-dashoffset:408;
    transition:stroke-dashoffset 1.4s cubic-bezier(0.34,1.56,0.64,1);
    filter:drop-shadow(0 0 8px rgba(255,77,158,0.8));
}
.bloom-ring-center{
    position:absolute;inset:0;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
}
.bloom-ring-num{
    font-size:2rem;font-weight:900;color:#fff;line-height:1;
    animation:numPop 0.6s cubic-bezier(0.34,1.56,0.64,1) 0.5s both;
}
@keyframes numPop{from{opacity:0;transform:scale(0.5);}to{opacity:1;transform:scale(1);}}
.bloom-ring-of{font-size:0.65rem;color:rgba(255,255,255,0.45);font-weight:600;text-transform:uppercase;letter-spacing:0.08em;}
.bloom-ring-limit{font-size:0.9rem;font-weight:700;color:var(--bloom-pink);}

/* ── SECTION HEAD ── */
.bloom-sec-head{
    display:flex;align-items:center;justify-content:space-between;
    padding:0.5rem 1.25rem 0.9rem;
    animation:fadeUp 0.8s ease 0.25s both;
}
.bloom-sec-title{
    font-size:0.95rem;font-weight:700;color:#fff;
    display:flex;align-items:center;gap:8px;
}
.bloom-sec-title span{
    display:inline-flex;align-items:center;justify-content:center;
    width:28px;height:28px;border-radius:9px;
    background:linear-gradient(135deg,var(--bloom-pink),var(--bloom-deep));
    font-size:0.85rem;
}
.bloom-count-badge{
    background:linear-gradient(135deg,var(--bloom-pink),var(--bloom-magenta));
    color:#fff;font-size:0.72rem;font-weight:800;
    padding:4px 12px;border-radius:20px;
    box-shadow:0 4px 16px rgba(255,77,158,0.4);
    animation:badgeBounce 2s ease-in-out infinite;
}
@keyframes badgeBounce{0%,100%{transform:scale(1);}50%{transform:scale(1.06);}}

.bloom-join-note{
    margin:0 1.25rem 1rem;
    background:rgba(255,213,79,0.12);
    border:1.5px solid rgba(255,213,79,0.28);
    border-radius:16px;
    padding:0.85rem 1rem;
    color:rgba(255,255,255,0.82);
    font-size:0.8rem;line-height:1.55;font-weight:500;
}

/* ── TASK BLOOM CARDS ── */
.bloom-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:12px;
    padding:0 1.25rem;
}
@media(min-width:400px){.bloom-grid{grid-template-columns:repeat(2,1fr);gap:14px;}}

.bloom-card{
    position:relative;
    border-radius:24px;
    padding:1.4rem 1rem 1.1rem;
    text-align:center;
    cursor:pointer;
    overflow:hidden;
    background:var(--bloom-glass);
    border:1.5px solid var(--bloom-glass-border);
    backdrop-filter:blur(16px);
    animation:cardBloom 0.7s cubic-bezier(0.34,1.56,0.64,1) both;
    animation-delay:calc(0.04s + var(--n, 0) * 0.07s);
    transition:transform 0.35s cubic-bezier(0.34,1.56,0.64,1),
               box-shadow 0.35s,border-color 0.35s;
}
.bloom-card::before{
    content:'';position:absolute;
    top:-50%;left:-50%;width:200%;height:200%;
    background:conic-gradient(from 0deg,transparent,rgba(255,77,158,0.15),transparent,rgba(0,229,200,0.1),transparent);
    animation:cardSpin 6s linear infinite;
    pointer-events:none;
}
.bloom-card::after{
    content:'';position:absolute;inset:1.5px;border-radius:22px;
    background:linear-gradient(160deg,rgba(30,5,60,0.9),rgba(45,10,78,0.85));
    pointer-events:none;z-index:0;
}
.bloom-card:hover{
    transform:translateY(-8px) scale(1.04);
    border-color:rgba(255,77,158,0.6);
    box-shadow:0 20px 50px rgba(255,77,158,0.3),0 0 0 1px rgba(255,77,158,0.2);
}
@keyframes cardSpin{to{transform:rotate(360deg);}}
@keyframes cardBloom{from{opacity:0;transform:translateY(30px) scale(0.85) rotate(-3deg);}to{opacity:1;transform:translateY(0) scale(1) rotate(0);}}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}

.bloom-card-inner{position:relative;z-index:1;}

/* glowing orb behind flower */
.bloom-orb{
    position:absolute;top:50%;left:50%;
    width:80px;height:80px;border-radius:50%;
    transform:translate(-50%,-60%);
    background:radial-gradient(circle,rgba(255,77,158,0.5) 0%,transparent 70%);
    animation:orbPulse 2.5s ease-in-out infinite;
    pointer-events:none;z-index:0;
}
@keyframes orbPulse{0%,100%{transform:translate(-50%,-60%) scale(1);opacity:0.6;}50%{transform:translate(-50%,-60%) scale(1.3);opacity:1;}}

.bloom-flower{
    font-size:3.8rem;display:block;
    margin:0.2rem auto 0.6rem;
    position:relative;z-index:2;
    filter:drop-shadow(0 0 20px rgba(255,77,158,0.6));
    animation:flowerFloat 3s ease-in-out infinite;
    animation-delay:calc(var(--n, 0) * 0.25s);
    line-height:1;
    transition:transform 0.3s;
}
.bloom-card:hover .bloom-flower{animation:none;transform:scale(1.2) rotate(-8deg);}
@keyframes flowerFloat{
    0%,100%{transform:translateY(0) rotate(-3deg);}
    33%{transform:translateY(-10px) rotate(4deg);}
    66%{transform:translateY(-5px) rotate(-2deg);}
}

.bloom-card-label{
    font-size:0.72rem;font-weight:700;color:rgba(255,255,255,0.55);
    text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.7rem;
}
.bloom-start{
    display:flex;align-items:center;justify-content:center;gap:6px;
    width:100%;border:none;border-radius:14px;
    padding:10px 8px;
    font-family:'Outfit',sans-serif;
    font-size:0.78rem;font-weight:800;
    letter-spacing:0.04em;text-transform:uppercase;
    color:#fff;cursor:pointer;position:relative;z-index:2;
    background:linear-gradient(135deg,#ff4d9e,#e91e8c,#7b1fa2);
    background-size:200% auto;
    box-shadow:0 6px 20px rgba(255,77,158,0.45),inset 0 1px 0 rgba(255,255,255,0.2);
    transition:all 0.3s;overflow:hidden;
}
.bloom-start::before{
    content:'';position:absolute;inset:0;
    background:linear-gradient(90deg,transparent,rgba(255,255,255,0.25),transparent);
    transform:translateX(-100%);
    transition:transform 0.5s;
}
.bloom-start:hover::before{transform:translateX(100%);}
.bloom-start:hover{
    background-position:right center;
    transform:translateY(-2px);
    box-shadow:0 10px 28px rgba(255,77,158,0.55);
}
.bloom-start:active{transform:scale(0.96);}

/* ripple on tap */
.bloom-ripple{
    position:absolute;border-radius:50%;
    background:rgba(255,255,255,0.3);
    transform:scale(0);animation:rippleOut 0.6s ease-out forwards;
    pointer-events:none;z-index:10;
}
@keyframes rippleOut{to{transform:scale(4);opacity:0;}}

/* ── EMPTY STATE ── */
.bloom-empty{
    margin:0 1.25rem;
    text-align:center;padding:3rem 1.5rem;
    background:var(--bloom-glass);
    border:1.5px dashed rgba(255,255,255,0.15);
    border-radius:24px;backdrop-filter:blur(12px);
    animation:fadeUp 0.8s ease both;
}
.bloom-empty-flower{
    font-size:4.5rem;display:block;margin-bottom:1rem;
    animation:emptySpin 4s ease-in-out infinite;
    filter:drop-shadow(0 0 30px rgba(255,77,158,0.5));
}
@keyframes emptySpin{0%,100%{transform:rotate(-5deg) scale(1);}50%{transform:rotate(5deg) scale(1.1);}}
.bloom-empty h3{font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:0.4rem;}
.bloom-empty p{font-size:0.82rem;color:rgba(255,255,255,0.45);line-height:1.6;}

/* ══ FULLSCREEN BLOOM LAUNCHER ══ */
.bloom-launch{
    position:fixed;inset:0;z-index:9999;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    background:radial-gradient(ellipse at center,#1a0535 0%,#0d0221 100%);
    opacity:0;pointer-events:none;
    transition:opacity 0.5s ease;
}
.bloom-launch.active{opacity:1;pointer-events:all;}

.bloom-launch-aurora{
    position:absolute;inset:0;overflow:hidden;pointer-events:none;
}
.bloom-launch-aurora::before,.bloom-launch-aurora::after{
    content:'';position:absolute;border-radius:50%;filter:blur(80px);
    animation:launchAurora 3s ease-in-out infinite alternate;
}
.bloom-launch-aurora::before{width:400px;height:400px;background:#ff4d9e;top:10%;left:-10%;opacity:0.4;}
.bloom-launch-aurora::after{width:350px;height:350px;background:#7b1fa2;bottom:10%;right:-10%;opacity:0.35;animation-delay:-1.5s;}
@keyframes launchAurora{0%{transform:scale(1) rotate(0deg);}100%{transform:scale(1.2) rotate(20deg);}}

/* countdown ring */
.bloom-launch-count{
    position:absolute;top:2rem;
    display:flex;flex-direction:column;align-items:center;gap:4px;
    opacity:0;transform:translateY(-20px);
    transition:all 0.5s cubic-bezier(0.34,1.56,0.64,1);
}
.bloom-launch-count.show{opacity:1;transform:translateY(0);}
.bloom-launch-count-num{
    font-size:3.5rem;font-weight:900;
    background:linear-gradient(135deg,#fff,#ff4d9e);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    animation:countBoom 1s ease-in-out infinite;
    line-height:1;
}
@keyframes countBoom{0%,100%{transform:scale(1);}50%{transform:scale(1.25);}}
.bloom-launch-count-lbl{font-size:0.75rem;font-weight:700;color:rgba(255,255,255,0.5);letter-spacing:0.15em;text-transform:uppercase;}

/* 3D flower pot / bloom box */
.bloom-stage{position:relative;z-index:2;perspective:800px;}
.bloom-pot{
    width:200px;height:200px;position:relative;
    transform-style:preserve-3d;
    animation:potEnter 0.8s cubic-bezier(0.34,1.56,0.64,1) both;
}
@keyframes potEnter{from{transform:scale(0.3) rotateY(-30deg) translateY(100px);opacity:0;}to{transform:scale(1) rotateY(0) translateY(0);opacity:1;}}

.bloom-pot-lid{
    width:200px;height:55px;
    background:linear-gradient(180deg,#ff6ec7,#e91e8c);
    border-radius:20px 20px 8px 8px;
    position:absolute;top:-45px;left:0;
    transform-origin:bottom center;
    transition:transform 0.9s cubic-bezier(0.34,1.56,0.64,1);
    box-shadow:0 -8px 30px rgba(255,77,158,0.6);
    z-index:3;
}
.bloom-pot-lid.open{transform:rotateX(-110deg) translateY(-20px);}
.bloom-pot-lid-ribbon{
    position:absolute;width:30px;height:100%;left:50%;transform:translateX(-50%);
    background:rgba(255,255,255,0.35);border-radius:4px;
}
.bloom-pot-bow{
    position:absolute;top:-30px;left:50%;transform:translateX(-50%);
    font-size:2rem;z-index:4;
    animation:bowSwing 1.5s ease-in-out infinite;
}
@keyframes bowSwing{0%,100%{transform:translateX(-50%) rotate(-8deg);}50%{transform:translateX(-50%) rotate(8deg);}}

.bloom-pot-body{
    width:200px;height:200px;
    background:linear-gradient(145deg,#ff4d9e 0%,#c2185b 50%,#7b1fa2 100%);
    border-radius:28px;position:relative;overflow:visible;
    box-shadow:0 30px 80px rgba(255,77,158,0.5),0 0 0 1px rgba(255,255,255,0.1);
}
.bloom-pot-body::before,.bloom-pot-body::after{
    content:'';position:absolute;background:rgba(255,255,255,0.2);border-radius:4px;
}
.bloom-pot-body::before{width:30px;height:100%;left:50%;transform:translateX(-50%);}
.bloom-pot-body::after{width:100%;height:28px;top:50%;transform:translateY(-50%);}

.bloom-hero-flower{
    position:absolute;left:50%;top:50%;
    font-size:5rem;opacity:0;z-index:5;
    transform:translate(-50%,-50%) scale(0) rotate(-180deg);
    filter:drop-shadow(0 0 40px rgba(255,255,255,0.9));
    transition:all 1s cubic-bezier(0.34,1.56,0.64,1);
    pointer-events:none;
}
.bloom-hero-flower.show{
    opacity:1;
    transform:translate(-50%,-50%) translateY(-50px) scale(1.2) rotate(0deg);
}

.bloom-burst-petal{
    position:absolute;font-size:1.4rem;pointer-events:none;z-index:6;
    animation:bloomBurst 2s ease-out forwards;
}
@keyframes bloomBurst{
    0%{opacity:1;transform:translate(0,0) scale(1) rotate(0deg);}
    100%{opacity:0;transform:translate(var(--bx),var(--by)) scale(2.5) rotate(360deg);}
}

.bloom-launch-close{
    position:absolute;top:-20px;right:-20px;
    width:40px;height:40px;border-radius:50%;border:none;
    background:rgba(255,255,255,0.15);backdrop-filter:blur(8px);
    color:#fff;font-size:16px;cursor:pointer;z-index:10;
    display:flex;align-items:center;justify-content:center;
    transition:all 0.2s;border:1px solid rgba(255,255,255,0.2);
}
.bloom-launch-close:hover{background:rgba(255,77,158,0.5);transform:scale(1.1);}

.bloom-launch-text{
    margin-top:2rem;font-size:0.9rem;font-weight:600;
    color:rgba(255,255,255,0.7);letter-spacing:0.06em;
    opacity:0;transform:translateY(10px);
    transition:all 0.5s ease 0.3s;
    text-align:center;z-index:2;
}
.bloom-launch-text.show{opacity:1;transform:translateY(0);}

/* progress shimmer on ring */
.bloom-shimmer{
    position:absolute;inset:0;border-radius:50%;
    background:conic-gradient(from 0deg,transparent,rgba(255,255,255,0.05),transparent);
    animation:shimmerSpin 4s linear infinite;
    pointer-events:none;
}
@keyframes shimmerSpin{to{transform:rotate(360deg);}}
</style>

{{-- Aurora + particles --}}
<div class="bloom-aurora">
    <div class="bloom-blob bloom-blob-1"></div>
    <div class="bloom-blob bloom-blob-2"></div>
    <div class="bloom-blob bloom-blob-3"></div>
</div>
<div class="bloom-stars" id="bloomStars"></div>
<div class="bloom-particles" id="bloomParticles"></div>

<div class="bloom-page">
<div class="bloom-inner">

    {{-- Header --}}
    <header class="bloom-header">
        <div class="bloom-badge">
            <span class="bloom-badge-dot"></span>
            Live Garden
        </div>
        <h1 class="bloom-title">Bloom Tasks</h1>
        <p class="bloom-subtitle">Pick a flower, watch it bloom, earn rewards</p>
    </header>

    @if(($lockStatus['phase'] ?? '') === 'joining_window' && !empty($lockStatus['deadline']))
    <div class="bloom-join-note">
        Complete 1 joining by {{ $lockStatus['deadline']->format('d M Y') }} to keep tasks unlocked.
    </div>
    @endif

    {{-- Ring progress --}}
    <section class="bloom-ring-section">
        <div class="bloom-ring-wrap">
            <div class="bloom-shimmer"></div>
            <svg class="bloom-ring-svg" viewBox="0 0 150 150">
                <defs>
                    <linearGradient id="bloomGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#ff4d9e"/>
                        <stop offset="50%" stop-color="#e91e8c"/>
                        <stop offset="100%" stop-color="#00e5c8"/>
                    </linearGradient>
                </defs>
                <circle class="bloom-ring-bg" cx="75" cy="75" r="65"/>
                <circle class="bloom-ring-fill" id="bloomRing" cx="75" cy="75" r="65"
                    style="stroke-dashoffset:{{ 408 - (408 * $pct / 100) }}"/>
            </svg>
            <div class="bloom-ring-center">
                <div class="bloom-ring-num" id="ringNum">{{ $completedToday }}</div>
                <div class="bloom-ring-of">of</div>
                <div class="bloom-ring-limit">{{ $dailyLimit }}</div>
            </div>
        </div>
    </section>

    {{-- Tasks --}}
    <div class="bloom-sec-head">
        <div class="bloom-sec-title"><span>🌺</span> Garden Blooms</div>
        <div class="bloom-count-badge">{{ $ads->count() }} ready</div>
    </div>

    @if($ads->count() > 0)
    <div class="bloom-grid">
        @foreach($ads as $i => $ad)
        @php $icon = $flowerIcons[$i % count($flowerIcons)]; @endphp
        <div class="bloom-card" style="--n:{{ $i }}" data-task-url="{{ route('user.ptc.show', encrypt($ad->id.'|'.auth()->id())) }}">
            <div class="bloom-orb"></div>
            <div class="bloom-card-inner">
                <div class="bloom-card-label">Flower #{{ $i + 1 }}</div>
                <span class="bloom-flower">{{ $icon }}</span>
                <button type="button" class="bloom-start" onclick="launchBloom(this, event)">
                    <span>✨</span> Bloom Now
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bloom-empty">
        <span class="bloom-empty-flower">🌙</span>
        <h3>Garden is resting</h3>
        <p>All blooms picked for today.<br>Come back tomorrow for fresh flowers!</p>
    </div>
    @endif

</div>
</div>

{{-- Fullscreen bloom launcher --}}
<div class="bloom-launch" id="bloomLaunch">
    <div class="bloom-launch-aurora"></div>

    <div class="bloom-launch-count" id="launchCount">
        <div class="bloom-launch-count-num" id="launchCountNum">3</div>
        <div class="bloom-launch-count-lbl">Blooming...</div>
    </div>

    <div class="bloom-stage">
        <div class="bloom-pot" id="bloomPot">
            <div class="bloom-pot-bow">🎀</div>
            <div class="bloom-pot-lid" id="bloomLid">
                <div class="bloom-pot-lid-ribbon"></div>
            </div>
            <div class="bloom-pot-body">
                <div class="bloom-hero-flower" id="bloomHeroFlower">🌸</div>
            </div>
            <button class="bloom-launch-close" onclick="closeBloomLaunch()" type="button">✕</button>
        </div>
    </div>

    <div class="bloom-launch-text" id="launchText">Your flower is blooming...</div>
</div>

<script>
(function(){
    /* ── Stars ── */
    const starsEl = document.getElementById('bloomStars');
    for(let i = 0; i < 40; i++){
        const s = document.createElement('div');
        s.className = 'bloom-star';
        s.style.left = Math.random() * 100 + '%';
        s.style.top  = Math.random() * 100 + '%';
        s.style.animationDelay = (Math.random() * 3) + 's';
        s.style.animationDuration = (2 + Math.random() * 3) + 's';
        starsEl.appendChild(s);
    }

    /* ── Floating particles ── */
    const partEl = document.getElementById('bloomParticles');
    const emojis = ['🌸','🌺','✨','💫','🌷','⭐','🪷'];
    for(let i = 0; i < 18; i++){
        const p = document.createElement('div');
        p.className = 'bloom-particle';
        p.textContent = emojis[Math.floor(Math.random() * emojis.length)];
        p.style.left = Math.random() * 100 + '%';
        p.style.fontSize = (0.7 + Math.random() * 1) + 'rem';
        p.style.animationDuration = (10 + Math.random() * 14) + 's';
        p.style.animationDelay = (Math.random() * 12) + 's';
        partEl.appendChild(p);
    }

    /* ── Animate ring on load ── */
    const ring = document.getElementById('bloomRing');
    if(ring){
        const target = ring.style.strokeDashoffset;
        ring.style.strokeDashoffset = '408';
        setTimeout(() => { ring.style.strokeDashoffset = target; }, 400);
    }

    /* ── Card ripple ── */
    document.querySelectorAll('.bloom-card').forEach(card => {
        card.addEventListener('click', function(e){
            if(e.target.closest('.bloom-start')) return;
            const btn = this.querySelector('.bloom-start');
            if(btn) launchBloom(btn, e);
        });
    });
})();

let bloomTaskUrl = null;
let bloomTimers = [];

function clearBloomTimers(){
    bloomTimers.forEach(t => clearTimeout(t));
    bloomTimers = [];
}

function launchBloom(btn, e){
    if(e){ e.stopPropagation(); }

    /* ripple */
    const card = btn.closest('.bloom-card');
    if(card && e){
        const rect = card.getBoundingClientRect();
        const rip = document.createElement('span');
        rip.className = 'bloom-ripple';
        const size = Math.max(rect.width, rect.height);
        rip.style.width = rip.style.height = size + 'px';
        rip.style.left = (e.clientX - rect.left - size/2) + 'px';
        rip.style.top  = (e.clientY - rect.top - size/2) + 'px';
        card.appendChild(rip);
        setTimeout(() => rip.remove(), 600);
    }

    bloomTaskUrl = card ? card.dataset.taskUrl : null;
    if(!bloomTaskUrl) return;

    const launch   = document.getElementById('bloomLaunch');
    const lid      = document.getElementById('bloomLid');
    const hero     = document.getElementById('bloomHeroFlower');
    const countEl  = document.getElementById('launchCount');
    const countNum = document.getElementById('launchCountNum');
    const textEl   = document.getElementById('launchText');
    const pot      = document.getElementById('bloomPot');
    const flower   = card ? card.querySelector('.bloom-flower')?.textContent || '🌸' : '🌸';

    clearBloomTimers();
    lid.classList.remove('open');
    hero.classList.remove('show');
    countEl.classList.remove('show');
    textEl.classList.remove('show');
    hero.textContent = flower;
    pot.style.animation = 'none';
    void pot.offsetWidth;
    pot.style.animation = '';

    launch.classList.add('active');
    document.body.style.overflow = 'hidden';

    bloomTimers.push(setTimeout(() => {
        lid.classList.add('open');
        burstPetals(pot);

        bloomTimers.push(setTimeout(() => {
            hero.classList.add('show');
            textEl.classList.add('show');
            textEl.textContent = 'Your ' + flower + ' is blooming...';
        }, 500));

        bloomTimers.push(setTimeout(() => {
            countEl.classList.add('show');
            let c = 3;
            countNum.textContent = c;
            const iv = setInterval(() => {
                c--;
                countNum.textContent = c > 0 ? c : '✨';
                if(c <= 0) clearInterval(iv);
            }, 1000);

            bloomTimers.push(setTimeout(() => {
                if(bloomTaskUrl) window.location.href = bloomTaskUrl;
            }, 3000));
        }, 900));
    }, 500));
}

function burstPetals(container){
    const icons = ['🌸','🌺','🌼','🌻','🌷','✨','💫','💐'];
    for(let i = 0; i < 40; i++){
        const p = document.createElement('div');
        p.className = 'bloom-burst-petal';
        p.textContent = icons[Math.floor(Math.random() * icons.length)];
        const angle = (Math.PI * 2 / 40) * i + Math.random() * 0.5;
        const dist  = 120 + Math.random() * 200;
        p.style.setProperty('--bx', Math.cos(angle) * dist + 'px');
        p.style.setProperty('--by', Math.sin(angle) * dist - 80 + 'px');
        p.style.left = '50%';
        p.style.top  = '40%';
        p.style.animationDelay = (Math.random() * 0.4) + 's';
        container.appendChild(p);
        setTimeout(() => p.remove(), 2500);
    }
}

function closeBloomLaunch(){
    clearBloomTimers();
    const launch = document.getElementById('bloomLaunch');
    const lid    = document.getElementById('bloomLid');
    const hero   = document.getElementById('bloomHeroFlower');
    const countEl= document.getElementById('launchCount');
    const textEl = document.getElementById('launchText');

    hero.classList.remove('show');
    countEl.classList.remove('show');
    textEl.classList.remove('show');

    bloomTimers.push(setTimeout(() => {
        lid.classList.remove('open');
        bloomTimers.push(setTimeout(() => {
            launch.classList.remove('active');
            document.body.style.overflow = '';
            bloomTaskUrl = null;
        }, 600));
    }, 200));
}

document.addEventListener('keydown', e => {
    if(e.key === 'Escape' && document.getElementById('bloomLaunch').classList.contains('active')){
        closeBloomLaunch();
    }
});
</script>

@endsection
