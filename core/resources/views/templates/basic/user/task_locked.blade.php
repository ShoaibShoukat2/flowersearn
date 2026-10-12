@extends($activeTemplate.'layouts.dashboard')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">

@php
    $lockStatus = $lockStatus ?? getTaskLockStatus();
    $user = $user ?? auth()->user();
    $refLink = $lockStatus['referral_link'] ?? (url('/ref/' . ($user->ref ?? '')));
@endphp

<style>
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
.bloom-inner{position:relative;z-index:2;padding:2rem 1.25rem 0;}
.tl-lock-wrap{text-align:center;padding-top:1.2rem;}
.tl-lock-icon{
    width:92px;height:92px;margin:0 auto 1.2rem;
    border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    background:rgba(255,255,255,0.08);
    border:1.5px solid rgba(255,255,255,0.18);
    box-shadow:0 0 40px rgba(255,77,158,0.25);
    font-size:2.6rem;
    animation:lockPulse 2.4s ease-in-out infinite;
}
@keyframes lockPulse{
    0%,100%{transform:scale(1);box-shadow:0 0 40px rgba(255,77,158,0.25);}
    50%{transform:scale(1.06);box-shadow:0 0 55px rgba(255,77,158,0.4);}
}
.tl-title{
    font-family:'Playfair Display',serif;
    font-size:2rem;font-weight:700;font-style:italic;
    background:linear-gradient(135deg,#fff 0%,#ffb3d9 40%,#ff4d9e 70%,#ffd54f 100%);
    background-size:200% auto;
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;
    margin-bottom:0.55rem;
}
.tl-sub{
    font-size:0.88rem;color:rgba(255,255,255,0.62);line-height:1.65;
    max-width:360px;margin:0 auto 1.6rem;
}
.tl-card{
    background:var(--bloom-glass);
    border:1.5px solid var(--bloom-glass-border);
    backdrop-filter:blur(16px);
    border-radius:22px;
    padding:1.25rem 1.15rem;
    text-align:left;
    margin-bottom:1rem;
}
.tl-card-label{
    font-size:0.72rem;font-weight:700;letter-spacing:0.08em;
    text-transform:uppercase;color:rgba(255,255,255,0.45);
    margin-bottom:0.7rem;
}
.tl-rule{
    display:flex;gap:10px;align-items:flex-start;
    font-size:0.82rem;color:rgba(255,255,255,0.78);line-height:1.5;
    margin-bottom:0.55rem;
}
.tl-rule:last-child{margin-bottom:0;}
.tl-dot{
    width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-top:6px;
    background:var(--bloom-teal);box-shadow:0 0 10px var(--bloom-teal);
}
.tl-ref-row{
    display:flex;align-items:center;gap:8px;
    background:rgba(255,255,255,0.06);
    border:1.5px solid rgba(255,255,255,0.14);
    border-radius:12px;padding:0.55rem 0.7rem;
}
.tl-ref-row input{
    flex:1;border:none;outline:none;background:transparent;
    font-size:0.76rem;color:rgba(255,255,255,0.75);
    font-family:'Outfit',sans-serif;min-width:0;
}
.tl-copy{
    flex-shrink:0;
    background:linear-gradient(135deg,#ff4d9e,#e91e8c);
    border:none;border-radius:9px;
    padding:7px 12px;color:#fff;
    font-size:0.75rem;font-weight:700;
    cursor:pointer;font-family:'Outfit',sans-serif;
    white-space:nowrap;
}
.tl-copy.copied{background:linear-gradient(135deg,#22c55e,#16a34a);}
.tl-cta{
    display:block;text-align:center;text-decoration:none;
    background:linear-gradient(135deg,#ff4d9e,#e91e8c);
    color:#fff;border-radius:14px;
    padding:0.95rem 1rem;font-size:0.92rem;font-weight:800;
    box-shadow:0 8px 24px rgba(233,30,140,0.35);
    margin-top:0.4rem;
}
.tl-cta:hover{color:#fff;}
.tl-note{
    margin-top:1.1rem;
    font-size:0.78rem;color:rgba(255,255,255,0.45);line-height:1.55;
}
</style>

<div class="bloom-aurora">
    <div class="bloom-blob bloom-blob-1"></div>
    <div class="bloom-blob bloom-blob-2"></div>
    <div class="bloom-blob bloom-blob-3"></div>
</div>

<div class="bloom-page">
<div class="bloom-inner">
    <div class="tl-lock-wrap">
        <div class="tl-lock-icon">🔒</div>
        <h1 class="tl-title">Tasks Locked</h1>
        <p class="tl-sub">Complete 1 joining to reopen your garden. Tasks unlock automatically as soon as the joining is approved.</p>
    </div>

    <div class="tl-card">
        <div class="tl-card-label">How to unlock</div>
        <div class="tl-rule"><span class="tl-dot"></span> Share your referral link and complete 1 joining.</div>
        <div class="tl-rule"><span class="tl-dot"></span> After that, keep tasks open with 1 joining every 7 days.</div>
    </div>

    <div class="tl-card">
        <div class="tl-card-label">Your referral link</div>
        <div class="tl-ref-row">
            <input type="text" id="taskLockRefLink" value="{{ $refLink }}" readonly>
            <button type="button" class="tl-copy" id="taskLockCopyBtn" onclick="copyTaskLockRef()">Copy</button>
        </div>
    </div>

    <a href="{{ route('user.referred') }}" class="tl-cta">Go to My Team</a>
    <p class="tl-note">Once your required joining is completed, this page closes and tasks reopen on their own.</p>
</div>
</div>

<script>
function copyTaskLockRef(){
    const input = document.getElementById('taskLockRefLink');
    const btn = document.getElementById('taskLockCopyBtn');
    const val = input.value;
    navigator.clipboard.writeText(val).then(function(){
        btn.classList.add('copied');
        btn.textContent = 'Copied!';
        setTimeout(function(){
            btn.classList.remove('copied');
            btn.textContent = 'Copy';
        }, 2000);
    }).catch(function(){
        input.select();
        document.execCommand('copy');
    });
}
</script>

@endsection
