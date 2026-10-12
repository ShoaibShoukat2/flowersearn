@php $pageTitle = 'About Us'; @endphp
@extends($activeTemplate.'layouts.dashboard')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;}

.ab-page{min-height:100vh;background:#fdf5f9;font-family:'Poppins',sans-serif;position:relative;overflow-x:hidden;}

/* petals */
.ab-petals{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.ab-petal{position:absolute;opacity:0;animation:petalFloat linear infinite;}
@keyframes petalFloat{0%{opacity:0;transform:translateY(100vh) rotate(0deg);}10%{opacity:0.25;}90%{opacity:0.1;}100%{opacity:0;transform:translateY(-10vh) rotate(360deg);}}

.ab-wrap{max-width:560px;margin:0 auto;padding:0 0 3rem;position:relative;z-index:1;}

/* hero */
.ab-hero{
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 50%,#ad1457 100%);
    border-radius:0 0 40px 40px;
    padding:2.5rem 1.5rem 3rem;text-align:center;
    position:relative;overflow:hidden;
    box-shadow:0 12px 40px rgba(233,30,140,0.30);
    margin-bottom:2rem;
}
.ab-hero::before{content:'🌸';position:absolute;font-size:120px;opacity:0.07;top:-20px;left:-20px;transform:rotate(-15deg);pointer-events:none;}
.ab-hero::after{content:'🌺';position:absolute;font-size:100px;opacity:0.07;bottom:-15px;right:-10px;transform:rotate(15deg);pointer-events:none;}

.ab-hero-rings{position:relative;width:88px;height:88px;margin:0 auto 1.2rem;}
.ab-ring{position:absolute;inset:0;border-radius:50%;border:2px solid rgba(255,255,255,0.3);animation:ringPulse 2.5s ease-out infinite;}
.ab-ring:nth-child(2){animation-delay:0.7s;}
.ab-ring:nth-child(3){animation-delay:1.4s;}
@keyframes ringPulse{0%{transform:scale(1);opacity:0.7;}100%{transform:scale(2.2);opacity:0;}}

.ab-hero-icon{
    position:absolute;inset:0;background:#fff;border-radius:50%;
    display:flex;align-items:center;justify-content:center;font-size:2.4rem;
    box-shadow:0 6px 24px rgba(0,0,0,0.12);
    animation:iconBounce 0.8s cubic-bezier(0.34,1.56,0.64,1) 0.2s both;
}
@keyframes iconBounce{from{transform:scale(0) rotate(-180deg);}to{transform:scale(1) rotate(0deg);}}

.ab-hero h1{
    font-size:1.9rem;font-weight:900;color:#fff;margin-bottom:0.4rem;
    position:relative;z-index:2;text-shadow:0 2px 12px rgba(0,0,0,0.12);
    animation:titleGlow 3s ease-in-out infinite;
}
@keyframes titleGlow{0%,100%{text-shadow:0 2px 12px rgba(0,0,0,0.12);}50%{text-shadow:0 2px 20px rgba(255,255,255,0.35);}}

.ab-hero p{font-size:0.88rem;color:rgba(255,255,255,0.88);position:relative;z-index:2;line-height:1.6;}

/* intro banner */
.ab-intro{
    background:#fff;border-radius:22px;margin:0 1rem 1.2rem;
    padding:1.5rem 1.4rem;border:1.8px solid #f0e0ec;
    box-shadow:0 6px 28px rgba(233,30,140,0.08);
    position:relative;overflow:hidden;
    animation:cardIn 0.6s cubic-bezier(0.34,1.56,0.64,1) both;
}
.ab-intro::before{
    content:'';position:absolute;top:0;left:-100%;width:60%;height:100%;
    background:linear-gradient(90deg,transparent,rgba(233,30,140,0.06),transparent);
    animation:shimmer 4s ease-in-out infinite;
}
@keyframes shimmer{0%,100%{left:-100%;}50%{left:150%;}}

.ab-intro-badge{
    display:inline-flex;align-items:center;gap:6px;
    background:linear-gradient(135deg,#fff0f8,#ffe4f3);
    border:1px solid #f5c6e0;border-radius:20px;
    padding:5px 12px;font-size:0.7rem;font-weight:700;color:#e91e8c;
    margin-bottom:0.8rem;
}
.ab-intro p{font-size:0.88rem;color:#444;line-height:1.85;}

/* cards */
.ab-card{
    background:#fff;border-radius:24px;
    border:1.8px solid #f0e0ec;
    margin:0 1rem 1.2rem;overflow:hidden;
    box-shadow:0 6px 28px rgba(233,30,140,0.08);
    opacity:0;transform:translateY(24px);
    transition:opacity 0.55s ease,transform 0.55s cubic-bezier(0.34,1.56,0.64,1);
}
.ab-card.ab-visible{opacity:1;transform:translateY(0);}
.ab-card:hover{box-shadow:0 10px 36px rgba(233,30,140,0.14);transform:translateY(-2px);}

.ab-card-head{
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    padding:1.1rem 1.4rem;display:flex;align-items:center;gap:10px;
}
.ab-card-head i{color:#fff;font-size:17px;animation:iconPulse 2s ease-in-out infinite;}
@keyframes iconPulse{0%,100%{transform:scale(1);}50%{transform:scale(1.12);}}
.ab-card-head span{font-size:0.92rem;font-weight:700;color:#fff;}

.ab-card-body{padding:1.4rem;}
.ab-card-body p{font-size:0.88rem;color:#555;line-height:1.85;margin-bottom:1rem;}
.ab-card-body p:last-child{margin-bottom:0;}

/* core values */
.ab-values-title{
    text-align:center;margin:0 1rem 1rem;
    opacity:0;transform:translateY(16px);
    transition:opacity 0.5s ease,transform 0.5s ease;
}
.ab-values-title.ab-visible{opacity:1;transform:translateY(0);}
.ab-values-title h2{font-size:1.15rem;font-weight:800;color:#1a1a2e;margin-bottom:0.3rem;}
.ab-values-title p{font-size:0.78rem;color:#aaa;}

.ab-values{display:flex;flex-direction:column;gap:10px;padding:0 1rem;margin-bottom:1.2rem;}

.ab-value{
    background:#fff;border-radius:18px;border:1.8px solid #f0e0ec;
    padding:1rem 1.1rem;display:flex;align-items:center;gap:14px;
    box-shadow:0 4px 16px rgba(233,30,140,0.07);
    opacity:0;transform:translateX(-20px);
    transition:opacity 0.45s ease,transform 0.45s cubic-bezier(0.34,1.56,0.64,1),box-shadow 0.25s,border-color 0.25s;
}
.ab-value.ab-visible{opacity:1;transform:translateX(0);}
.ab-value:hover{transform:translateX(4px);box-shadow:0 8px 24px rgba(233,30,140,0.13);border-color:#e91e8c;}

.ab-value-icon{
    width:44px;height:44px;min-width:44px;border-radius:14px;
    background:linear-gradient(135deg,#ff6ec7,#e91e8c);
    display:flex;align-items:center;justify-content:center;
    font-size:1.1rem;color:#fff;
    box-shadow:0 4px 14px rgba(233,30,140,0.25);
    animation:valueIconFloat 3s ease-in-out infinite;
}
.ab-value:nth-child(2) .ab-value-icon{animation-delay:0.4s;}
.ab-value:nth-child(3) .ab-value-icon{animation-delay:0.8s;}
.ab-value:nth-child(4) .ab-value-icon{animation-delay:1.2s;}
.ab-value:nth-child(5) .ab-value-icon{animation-delay:1.6s;}
@keyframes valueIconFloat{0%,100%{transform:translateY(0);}50%{transform:translateY(-3px);}}

.ab-value-text{font-size:0.85rem;font-weight:700;color:#1a1a2e;line-height:1.4;}

/* highlight stats */
.ab-stats{
    display:grid;grid-template-columns:repeat(3,1fr);gap:10px;
    padding:0 1rem;margin-bottom:1.2rem;
}
.ab-stat{
    background:#fff;border-radius:18px;border:1.8px solid #f0e0ec;
    padding:1rem 0.6rem;text-align:center;
    box-shadow:0 4px 16px rgba(233,30,140,0.07);
    opacity:0;transform:scale(0.85);
    transition:opacity 0.5s ease,transform 0.5s cubic-bezier(0.34,1.56,0.64,1);
}
.ab-stat.ab-visible{opacity:1;transform:scale(1);}
.ab-stat-icon{font-size:1.6rem;margin-bottom:0.3rem;display:block;}
.ab-stat-label{font-size:0.68rem;font-weight:700;color:#888;line-height:1.3;}

/* closing quote */
.ab-closing{
    background:linear-gradient(135deg,#fff5fb,#fff);
    border-radius:22px;margin:0 1rem 1.2rem;
    padding:1.5rem 1.4rem;border:1.8px solid #f0e0ec;
    text-align:center;position:relative;
    box-shadow:0 6px 28px rgba(233,30,140,0.08);
    opacity:0;transform:translateY(20px);
    transition:opacity 0.55s ease,transform 0.55s ease;
}
.ab-closing.ab-visible{opacity:1;transform:translateY(0);}
.ab-closing::before{content:'"';font-size:3rem;color:#e91e8c;opacity:0.2;position:absolute;top:0;left:16px;line-height:1;}
.ab-closing p{font-size:0.86rem;color:#555;line-height:1.85;font-style:italic;position:relative;z-index:1;}

/* CTA */
.ab-cta{
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 50%,#ad1457 100%);
    border-radius:22px;margin:0 1rem 1.2rem;
    padding:1.6rem 1.4rem;text-align:center;
    box-shadow:0 10px 32px rgba(233,30,140,0.28);
    position:relative;overflow:hidden;
    opacity:0;transform:translateY(20px);
    transition:opacity 0.55s ease,transform 0.55s ease;
}
.ab-cta.ab-visible{opacity:1;transform:translateY(0);}
.ab-cta h3{font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:0.4rem;position:relative;z-index:2;}
.ab-cta p{font-size:0.82rem;color:rgba(255,255,255,0.88);margin-bottom:1.2rem;position:relative;z-index:2;}
.ab-cta-btn{
    display:inline-flex;align-items:center;gap:8px;
    background:#fff;color:#e91e8c;border-radius:14px;
    padding:11px 24px;font-size:0.88rem;font-weight:800;
    text-decoration:none;box-shadow:0 6px 20px rgba(0,0,0,0.15);
    transition:all 0.25s;position:relative;z-index:2;
}
.ab-cta-btn:hover{transform:translateY(-2px) scale(1.04);box-shadow:0 10px 28px rgba(0,0,0,0.2);color:#e91e8c;}

@keyframes cardIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
</style>

<div class="ab-petals" id="abPetals"></div>

<div class="ab-page">
<div class="ab-wrap">

    {{-- Hero --}}
    <div class="ab-hero">
        <div class="ab-hero-rings">
            <div class="ab-ring"></div>
            <div class="ab-ring"></div>
            <div class="ab-ring"></div>
            <div class="ab-hero-icon">🌸</div>
        </div>
        <h1>About FlowersEarn</h1>
        <p>Innovative E-Commerce Community Platform</p>
    </div>

    {{-- Intro --}}
    <div class="ab-intro">
        <div class="ab-intro-badge"><i class="fas fa-sparkles"></i> Who We Are</div>
        <p>FlowersEarn is an innovative e-commerce community platform that connects users with digital growth opportunities through a simple and rewarding ecosystem.</p>
    </div>

    {{-- Vision --}}
    <div class="ab-card ab-reveal">
        <div class="ab-card-head">
            <i class="fas fa-eye"></i>
            <span>Our Vision</span>
        </div>
        <div class="ab-card-body">
            <p>Our vision is to build a trusted community where users can participate in daily activities, grow their networks, and benefit from the expanding world of e-commerce. We believe that technology, teamwork, and consistency create long-term success.</p>
        </div>
    </div>

    {{-- What Members Do --}}
    <div class="ab-card ab-reveal">
        <div class="ab-card-head">
            <i class="fas fa-users"></i>
            <span>Grow With Us</span>
        </div>
        <div class="ab-card-body">
            <p>At FlowersEarn, members can complete daily tasks, build strong communities, and unlock new opportunities as the platform continues to grow. Every active user contributes to a larger digital ecosystem focused on engagement, collaboration, and sustainable growth.</p>
        </div>
    </div>

    {{-- Commitment --}}
    <div class="ab-card ab-reveal">
        <div class="ab-card-head">
            <i class="fas fa-shield-halved"></i>
            <span>Our Commitment</span>
        </div>
        <div class="ab-card-body">
            <p>Our platform is built with a strong commitment to security, transparency, and continuous innovation, ensuring a smooth experience for every member.</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="ab-stats">
        <div class="ab-stat ab-reveal">
            <span class="ab-stat-icon">🔒</span>
            <div class="ab-stat-label">Security First</div>
        </div>
        <div class="ab-stat ab-reveal">
            <span class="ab-stat-icon">🤝</span>
            <div class="ab-stat-label">Community Driven</div>
        </div>
        <div class="ab-stat ab-reveal">
            <span class="ab-stat-icon">🚀</span>
            <div class="ab-stat-label">Always Growing</div>
        </div>
    </div>

    {{-- Core Values --}}
    <div class="ab-values-title ab-reveal">
        <h2>Our Core Values</h2>
        <p>The principles that guide everything we do</p>
    </div>

    <div class="ab-values">
        <div class="ab-value ab-reveal">
            <div class="ab-value-icon"><i class="fas fa-lightbulb"></i></div>
            <div class="ab-value-text">Innovation in Digital Commerce</div>
        </div>
        <div class="ab-value ab-reveal">
            <div class="ab-value-icon"><i class="fas fa-people-group"></i></div>
            <div class="ab-value-text">Community-Driven Growth</div>
        </div>
        <div class="ab-value ab-reveal">
            <div class="ab-value-icon"><i class="fas fa-handshake"></i></div>
            <div class="ab-value-text">Trust &amp; Transparency</div>
        </div>
        <div class="ab-value ab-reveal">
            <div class="ab-value-icon"><i class="fas fa-mobile-screen"></i></div>
            <div class="ab-value-text">Simple User Experience</div>
        </div>
        <div class="ab-value ab-reveal">
            <div class="ab-value-icon"><i class="fas fa-code-branch"></i></div>
            <div class="ab-value-text">Continuous Platform Development</div>
        </div>
    </div>

    {{-- Closing --}}
    <div class="ab-closing ab-reveal">
        <p>FlowersEarn is more than a platform—it's a growing digital community where technology, e-commerce, and teamwork come together to create new opportunities for everyone.</p>
    </div>

    {{-- CTA --}}
    <div class="ab-cta ab-reveal">
        <h3>Join Our Community 🌸</h3>
        <p>Start your growth journey with FlowersEarn today</p>
        <a href="{{ route('contact') }}" class="ab-cta-btn">
            <i class="fas fa-headset"></i> Contact Us
        </a>
    </div>

</div>
</div>

<script>
(function(){
    const c=document.getElementById('abPetals');
    const e=['🌸','🌺','🌼','🌷','✨','💫','⭐'];
    for(let i=0;i<16;i++){
        const p=document.createElement('div');
        p.className='ab-petal';
        p.textContent=e[Math.floor(Math.random()*e.length)];
        p.style.left=Math.random()*100+'%';
        p.style.fontSize=(0.7+Math.random()*0.9)+'rem';
        p.style.animationDuration=(9+Math.random()*12)+'s';
        p.style.animationDelay=(Math.random()*12)+'s';
        c.appendChild(p);
    }

    const reveals=document.querySelectorAll('.ab-reveal');
    const observer=new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
            if(entry.isIntersecting){
                const siblings=Array.from(entry.target.parentElement.querySelectorAll('.ab-reveal'));
                const idx=siblings.indexOf(entry.target);
                setTimeout(function(){
                    entry.target.classList.add('ab-visible');
                },idx*80);
                observer.unobserve(entry.target);
            }
        });
    },{threshold:0.12,rootMargin:'0px 0px -30px 0px'});
    reveals.forEach(function(el){observer.observe(el);});
})();
</script>

@endsection
