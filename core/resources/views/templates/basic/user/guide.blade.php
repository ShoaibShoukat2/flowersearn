@php $pageTitle = 'Guide'; @endphp

@if (empty(auth()->user()->pv))
    @php redirect('/user/deposit')->send(); @endphp
@elseif (in_array(auth()->user()->pv, ['rt']))
    @php redirect('/user/pending')->send(); @endphp
@endif

@extends($activeTemplate.'layouts.master')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;}

.gd-page{
    min-height:100vh;
    background:#fdf5f9;
    font-family:'Poppins',sans-serif;
    position:relative;overflow-x:hidden;
}

/* ── Floating petals ── */
.gd-petals{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.gd-petal{position:absolute;opacity:0;animation:petalFloat linear infinite;}
@keyframes petalFloat{
    0%  {opacity:0;transform:translateY(100vh) rotate(0deg);}
    10% {opacity:0.25;}90%{opacity:0.1;}
    100%{opacity:0;transform:translateY(-10vh) rotate(360deg);}
}

.gd-wrap{max-width:520px;margin:0 auto;padding:0 0 3rem;position:relative;z-index:1;}

/* ── Hero ── */
.gd-hero{
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 50%,#ad1457 100%);
    border-radius:0 0 40px 40px;
    padding:2.5rem 1.5rem 3rem;
    text-align:center;position:relative;overflow:hidden;
    box-shadow:0 12px 40px rgba(233,30,140,0.30);
    margin-bottom:2rem;
}
.gd-hero::before{content:'🌸';position:absolute;font-size:120px;opacity:0.07;top:-20px;left:-20px;transform:rotate(-15deg);pointer-events:none;}
.gd-hero::after{content:'🌺';position:absolute;font-size:100px;opacity:0.07;bottom:-15px;right:-10px;transform:rotate(15deg);pointer-events:none;}

/* animated rings */
.gd-rings{position:relative;width:90px;height:90px;margin:0 auto 1.2rem;}
.gd-ring{position:absolute;inset:0;border-radius:50%;border:2px solid rgba(255,255,255,0.3);animation:ringPulse 2.5s ease-out infinite;}
.gd-ring:nth-child(2){animation-delay:0.7s;}
.gd-ring:nth-child(3){animation-delay:1.4s;}
@keyframes ringPulse{0%{transform:scale(1);opacity:0.7;}100%{transform:scale(2.2);opacity:0;}}
.gd-hero-icon{
    position:absolute;inset:0;
    background:#fff;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-size:2.5rem;
    box-shadow:0 6px 24px rgba(0,0,0,0.12);
    animation:iconBounce 0.8s cubic-bezier(0.34,1.56,0.64,1) 0.2s both;
}
@keyframes iconBounce{from{transform:scale(0) rotate(-180deg);}to{transform:scale(1) rotate(0deg);}}

.gd-hero h1{font-size:1.8rem;font-weight:900;color:#fff;margin-bottom:0.4rem;position:relative;z-index:2;text-shadow:0 2px 12px rgba(0,0,0,0.12);}
.gd-hero p{font-size:0.88rem;color:rgba(255,255,255,0.88);position:relative;z-index:2;line-height:1.6;}

/* ── Main description card ── */
.gd-main-card{
    background:#fff;border-radius:24px;
    border:1.8px solid #f0e0ec;
    margin:0 1rem 1.2rem;
    overflow:hidden;
    box-shadow:0 8px 32px rgba(233,30,140,0.09);
    animation:cardIn 0.6s cubic-bezier(0.34,1.56,0.64,1) 0.1s both;
}
@keyframes cardIn{from{opacity:0;transform:translateY(24px);}to{opacity:1;transform:translateY(0);}}

.gd-main-card-head{
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    padding:1.2rem 1.4rem;
    display:flex;align-items:center;gap:10px;
}
.gd-main-card-head i{color:#fff;font-size:18px;}
.gd-main-card-head span{font-size:0.95rem;font-weight:700;color:#fff;}

.gd-main-card-body{padding:1.4rem;}
.gd-main-card-body p{
    font-size:0.88rem;color:#555;line-height:1.85;
    position:relative;
}
.gd-main-card-body p::first-letter{
    font-size:1.4rem;font-weight:800;color:#e91e8c;
    float:left;margin-right:4px;line-height:1;
}

/* ── Feature cards ── */
.gd-features{
    display:grid;grid-template-columns:1fr 1fr;gap:10px;
    padding:0 1rem;margin-bottom:1.2rem;
}
.gd-feat{
    background:#fff;border-radius:18px;
    border:1.8px solid #f0e0ec;
    padding:1.1rem 0.9rem;
    box-shadow:0 4px 16px rgba(233,30,140,0.07);
    animation:cardIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
    transition:transform 0.25s,box-shadow 0.25s,border-color 0.25s;
    position:relative;overflow:hidden;
}
.gd-feat:hover{transform:translateY(-4px) scale(1.02);box-shadow:0 10px 28px rgba(233,30,140,0.14);border-color:#e91e8c;}
.gd-feat:nth-child(1){animation-delay:0.15s;}
.gd-feat:nth-child(2){animation-delay:0.22s;}
.gd-feat:nth-child(3){animation-delay:0.29s;}
.gd-feat:nth-child(4){animation-delay:0.36s;}

/* shimmer */
.gd-feat::before{content:'';position:absolute;top:0;left:-60%;width:40%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.5),transparent);transform:skewX(-20deg);transition:left 0.5s;pointer-events:none;}
.gd-feat:hover::before{left:120%;}

.gd-feat-icon{
    width:44px;height:44px;border-radius:14px;
    display:flex;align-items:center;justify-content:center;
    font-size:1.4rem;margin-bottom:0.7rem;
}
.gd-feat-title{font-size:0.82rem;font-weight:700;color:#1a1a2e;margin-bottom:0.3rem;}
.gd-feat-desc{font-size:0.72rem;color:#bbb;line-height:1.5;}

/* ── Steps card ── */
.gd-steps-card{
    background:#fff;border-radius:24px;
    border:1.8px solid #f0e0ec;
    margin:0 1rem 1.2rem;
    padding:1.4rem;
    box-shadow:0 8px 32px rgba(233,30,140,0.09);
    animation:cardIn 0.5s ease 0.3s both;
}
.gd-steps-title{
    font-size:0.82rem;font-weight:700;color:#888;
    text-transform:uppercase;letter-spacing:0.07em;
    display:flex;align-items:center;gap:7px;
    margin-bottom:1.2rem;
}
.gd-steps-title i{color:#e91e8c;}

.gd-step{
    display:flex;align-items:flex-start;gap:12px;
    margin-bottom:1rem;
    animation:stepIn 0.4s ease both;
}
.gd-step:last-child{margin-bottom:0;}
.gd-step:nth-child(1){animation-delay:0.35s;}
.gd-step:nth-child(2){animation-delay:0.45s;}
.gd-step:nth-child(3){animation-delay:0.55s;}
.gd-step:nth-child(4){animation-delay:0.65s;}
@keyframes stepIn{from{opacity:0;transform:translateX(-12px);}to{opacity:1;transform:translateX(0);}}

.gd-step-num{
    width:32px;height:32px;flex-shrink:0;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-size:0.78rem;font-weight:800;color:#fff;
    box-shadow:0 4px 12px rgba(233,30,140,0.28);
}
.gd-step-text{
    font-size:0.84rem;color:#555;line-height:1.6;
    padding-top:4px;
}
.gd-step-text strong{color:#1a1a2e;font-weight:700;}

/* ── CTA card ── */
.gd-cta{
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 50%,#ad1457 100%);
    border-radius:22px;
    margin:0 1rem 1.2rem;
    padding:1.6rem 1.4rem;
    text-align:center;
    box-shadow:0 10px 32px rgba(233,30,140,0.28);
    position:relative;overflow:hidden;
    animation:cardIn 0.5s ease 0.4s both;
}
.gd-cta::before{content:'💬';position:absolute;font-size:80px;opacity:0.08;bottom:-15px;right:-10px;pointer-events:none;}
.gd-cta h3{font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:0.4rem;position:relative;z-index:2;}
.gd-cta p{font-size:0.82rem;color:rgba(255,255,255,0.88);margin-bottom:1.2rem;position:relative;z-index:2;line-height:1.6;}
.gd-cta-btn{
    display:inline-flex;align-items:center;gap:8px;
    background:#fff;color:#e91e8c;
    border-radius:14px;padding:11px 24px;
    font-size:0.88rem;font-weight:800;
    text-decoration:none;
    box-shadow:0 6px 20px rgba(0,0,0,0.15);
    transition:all 0.25s;
    position:relative;z-index:2;
}
.gd-cta-btn:hover{transform:translateY(-2px) scale(1.04);box-shadow:0 10px 28px rgba(0,0,0,0.2);color:#e91e8c;}
</style>

{{-- Floating petals --}}
<div class="gd-petals" id="gdPetals"></div>

<div class="gd-page">
<div class="gd-wrap">

    {{-- ── Hero ── --}}
    <div class="gd-hero">
        <div class="gd-rings">
            <div class="gd-ring"></div>
            <div class="gd-ring"></div>
            <div class="gd-ring"></div>
            <div class="gd-hero-icon">📖</div>
        </div>
        <h1>Flower Guide</h1>
        <p>Everything you need to know to grow<br>your team and maximize your earnings</p>
    </div>

    {{-- ── Main description ── --}}
    <div class="gd-main-card">
        <div class="gd-main-card-head">
            <i class="fas fa-seedling"></i>
            <span>How It Works</span>
        </div>
        <div class="gd-main-card-body">
            <p>Build your team and grow your network to unlock more opportunities. Start working on social media by creating and sharing videos to attract new members. The stronger your team, the better your results. Focus on referrals and stay active — every referral increases your progress. We also offer free bonuses to support your growth. For extra rewards and updates, make sure to join our affiliate channel and stay connected.</p>
        </div>
    </div>

    {{-- ── Feature cards ── --}}
    <div class="gd-features">
        <div class="gd-feat">
            <div class="gd-feat-icon" style="background:#fce4f0;">🤝</div>
            <div class="gd-feat-title">Build Your Team</div>
            <div class="gd-feat-desc">Grow your network and unlock more earning opportunities</div>
        </div>
        <div class="gd-feat">
            <div class="gd-feat-icon" style="background:#e8f5e9;">📱</div>
            <div class="gd-feat-title">Social Media</div>
            <div class="gd-feat-desc">Share videos to attract new members and grow faster</div>
        </div>
        <div class="gd-feat">
            <div class="gd-feat-icon" style="background:#fff8e1;">🎁</div>
            <div class="gd-feat-title">Free Bonuses</div>
            <div class="gd-feat-desc">We offer free bonuses to support your growth journey</div>
        </div>
        <div class="gd-feat">
            <div class="gd-feat-icon" style="background:#e3f2fd;">📢</div>
            <div class="gd-feat-title">Affiliate Channel</div>
            <div class="gd-feat-desc">Join our channel for extra rewards and latest updates</div>
        </div>
    </div>

    {{-- ── Steps ── --}}
    <div class="gd-steps-card">
        <div class="gd-steps-title">
            <i class="fas fa-list-ol"></i> Steps to Grow
        </div>
        <div class="gd-step">
            <div class="gd-step-num">1</div>
            <div class="gd-step-text"><strong>Invite friends</strong> — Share your referral link on social media and WhatsApp groups</div>
        </div>
        <div class="gd-step">
            <div class="gd-step-num">2</div>
            <div class="gd-step-text"><strong>Create content</strong> — Make short videos about Flower Earn to attract new members</div>
        </div>
        <div class="gd-step">
            <div class="gd-step-num">3</div>
            <div class="gd-step-text"><strong>Stay active</strong> — Complete daily tasks and keep your team engaged</div>
        </div>
        <div class="gd-step">
            <div class="gd-step-num">4</div>
            <div class="gd-step-text"><strong>Join our channel</strong> — Get free bonuses, updates and extra rewards</div>
        </div>
    </div>

    {{-- ── CTA ── --}}
    <div class="gd-cta">
        <h3>Need More Help? 🌸</h3>
        <p>Contact our support team for any questions or join our official channel for the latest updates and bonuses.</p>
        <a href="{{ route('contact') }}" class="gd-cta-btn">
            <i class="fas fa-headset"></i> Contact Support
        </a>
    </div>

</div>
</div>

<script>
(function(){
    const c=document.getElementById('gdPetals');
    const e=['🌸','🌺','🌼','🌷','✨','💫','⭐'];
    for(let i=0;i<14;i++){
        const p=document.createElement('div');
        p.className='gd-petal';
        p.textContent=e[Math.floor(Math.random()*e.length)];
        p.style.left=Math.random()*100+'%';
        p.style.fontSize=(0.7+Math.random()*0.9)+'rem';
        p.style.animationDuration=(9+Math.random()*12)+'s';
        p.style.animationDelay=(Math.random()*12)+'s';
        c.appendChild(p);
    }
})();
</script>

@endsection
