@extends($activeTemplate.'layouts.frontend')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{box-sizing:border-box;}
body{font-family:'Poppins',sans-serif;}

.ct-page{
    min-height:100vh;
    background:#fdf5f9;
    font-family:'Poppins',sans-serif;
    position:relative;overflow-x:hidden;
}


/* ── Floating petals ── */
.ct-petals{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.ct-petal{position:absolute;opacity:0;animation:petalFloat linear infinite;}
@keyframes petalFloat{
    0%  {opacity:0;transform:translateY(100vh) rotate(0deg);}
    10% {opacity:0.25;}90%{opacity:0.1;}
    100%{opacity:0;transform:translateY(-10vh) rotate(360deg);}
}

.ct-wrap{max-width:560px;margin:0 auto;padding:0 0 3rem;position:relative;z-index:1;}

/* ── Hero ── */
.ct-hero{
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 50%,#ad1457 100%);
    border-radius:0 0 40px 40px;
    padding:2.5rem 1.5rem 3rem;
    text-align:center;position:relative;overflow:hidden;
    box-shadow:0 12px 40px rgba(233,30,140,0.30);
    margin-bottom:2rem;
}
.ct-hero::before{content:'💬';position:absolute;font-size:110px;opacity:0.07;top:-15px;right:-10px;transform:rotate(15deg);pointer-events:none;}
.ct-hero::after{content:'🌸';position:absolute;font-size:90px;opacity:0.07;bottom:-10px;left:-10px;transform:rotate(-15deg);pointer-events:none;}

.ct-hero-rings{position:relative;width:88px;height:88px;margin:0 auto 1.2rem;}
.ct-ring{position:absolute;inset:0;border-radius:50%;border:2px solid rgba(255,255,255,0.3);animation:ringPulse 2.5s ease-out infinite;}
.ct-ring:nth-child(2){animation-delay:0.7s;}
.ct-ring:nth-child(3){animation-delay:1.4s;}
@keyframes ringPulse{0%{transform:scale(1);opacity:0.7;}100%{transform:scale(2.2);opacity:0;}}
.ct-hero-icon{
    position:absolute;inset:0;background:#fff;border-radius:50%;
    display:flex;align-items:center;justify-content:center;font-size:2.4rem;
    box-shadow:0 6px 24px rgba(0,0,0,0.12);
    animation:iconBounce 0.8s cubic-bezier(0.34,1.56,0.64,1) 0.2s both;
}
@keyframes iconBounce{from{transform:scale(0) rotate(-180deg);}to{transform:scale(1) rotate(0deg);}}

.ct-hero h1{font-size:1.8rem;font-weight:900;color:#fff;margin-bottom:0.4rem;position:relative;z-index:2;text-shadow:0 2px 12px rgba(0,0,0,0.12);}
.ct-hero p{font-size:0.87rem;color:rgba(255,255,255,0.88);position:relative;z-index:2;line-height:1.6;}

/* ── Cards ── */
.ct-card{
    background:#fff;border-radius:24px;
    border:1.8px solid #f0e0ec;
    margin:0 1rem 1.2rem;
    overflow:hidden;
    box-shadow:0 6px 28px rgba(233,30,140,0.08);
    animation:cardIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
}
.ct-card:nth-child(2){animation-delay:0.1s;}
.ct-card:nth-child(3){animation-delay:0.2s;}
.ct-card:nth-child(4){animation-delay:0.3s;}
@keyframes cardIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}

.ct-card-head{
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    padding:1.1rem 1.4rem;
    display:flex;align-items:center;gap:10px;
}
.ct-card-head i{color:#fff;font-size:17px;}
.ct-card-head span{font-size:0.92rem;font-weight:700;color:#fff;}

.ct-card-body{padding:1.4rem;}

/* ── Form fields ── */
.ct-field{margin-bottom:1rem;}
.ct-field label{
    display:block;font-size:0.74rem;font-weight:700;
    color:#888;margin-bottom:0.4rem;
    text-transform:uppercase;letter-spacing:0.06em;
}
.ct-input,.ct-textarea{
    width:100%;padding:12px 14px;
    background:#fdf5f9;
    border:1.8px solid #f0d0e4;
    border-radius:12px;
    color:#333;font-size:0.9rem;
    font-family:'Poppins',sans-serif;
    outline:none;
    transition:border-color 0.25s,box-shadow 0.25s,background 0.25s;
}
.ct-input:focus,.ct-textarea:focus{
    border-color:#e91e8c;
    box-shadow:0 0 0 4px rgba(233,30,140,0.09);
    background:#fff;
}
.ct-input::placeholder,.ct-textarea::placeholder{color:#d0b0c0;font-weight:300;}
.ct-textarea{min-height:110px;resize:vertical;}

.ct-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
@media(max-width:400px){.ct-grid-2{grid-template-columns:1fr;}}

/* submit */
.ct-btn{
    width:100%;padding:14px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c,#c2185b);
    background-size:200% auto;
    border:none;border-radius:14px;
    color:#fff;font-size:0.96rem;font-weight:700;
    font-family:'Poppins',sans-serif;cursor:pointer;
    transition:all 0.35s;
    box-shadow:0 6px 22px rgba(233,30,140,0.30);
    display:flex;align-items:center;justify-content:center;gap:9px;
    position:relative;overflow:hidden;
    margin-top:0.4rem;
}
.ct-btn::before{
    content:'';position:absolute;top:0;left:-60%;width:40%;height:100%;
    background:rgba(255,255,255,0.15);transform:skewX(-20deg);
    animation:btnShine 3s ease-in-out infinite;
}
@keyframes btnShine{0%,100%{left:-60%;}50%{left:120%;}}
.ct-btn:hover{background-position:right center;transform:translateY(-2px);box-shadow:0 10px 30px rgba(233,30,140,0.40);}

/* ── WhatsApp buttons ── */
.ct-wa-btn{
    display:flex;align-items:center;gap:12px;
    background:linear-gradient(135deg,#22c55e,#16a34a);
    border-radius:14px;padding:1rem 1.2rem;
    text-decoration:none;margin-bottom:10px;
    transition:all 0.25s;
    box-shadow:0 4px 16px rgba(34,197,94,0.22);
}
.ct-wa-btn:last-child{margin-bottom:0;}
.ct-wa-btn:hover{transform:translateY(-3px) scale(1.02);box-shadow:0 8px 24px rgba(34,197,94,0.32);}
.ct-wa-btn.channel{background:linear-gradient(135deg,#3b82f6,#2563eb);box-shadow:0 4px 16px rgba(59,130,246,0.22);}
.ct-wa-btn.channel:hover{box-shadow:0 8px 24px rgba(59,130,246,0.32);}
.ct-wa-icon{width:46px;height:46px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;overflow:hidden;}
.ct-wa-photo{width:46px;height:46px;border-radius:50%;object-fit:cover;flex-shrink:0;background:#fff;}
.ct-wa-text h4{font-size:0.9rem;font-weight:700;color:#fff;margin-bottom:2px;}
.ct-wa-text p{font-size:0.74rem;color:rgba(255,255,255,0.82);}

/* ── Info rows ── */
.ct-info-row{
    display:flex;align-items:center;gap:12px;
    padding:0.8rem 0;border-bottom:1px solid #fce4f0;
}
.ct-info-row:last-child{border-bottom:none;}
.ct-info-icon{
    width:40px;height:40px;border-radius:12px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;font-size:16px;
}
.ct-info-title{font-size:0.84rem;font-weight:700;color:#1a1a2e;}
.ct-info-sub{font-size:0.74rem;color:#bbb;}

/* ── Alert ── */
.ct-alert{
    margin:0 1rem 1rem;padding:1rem 1.2rem;
    border-radius:14px;font-size:0.86rem;font-weight:600;
    display:flex;align-items:center;gap:10px;
}
.ct-alert.success{background:#f0fdf4;border:1.5px solid #bbf7d0;color:#16a34a;}
.ct-alert.error{background:#fff1f2;border:1.5px solid #fecdd3;color:#e11d48;}
</style>

{{-- Floating petals --}}
<div class="ct-petals" id="ctPetals"></div>

<div class="ct-page">
<div class="ct-wrap">

    {{-- ── Hero ── --}}
    <div class="ct-hero">
        <div class="ct-hero-rings">
            <div class="ct-ring"></div>
            <div class="ct-ring"></div>
            <div class="ct-ring"></div>
            <div class="ct-hero-icon">💬</div>
        </div>
        <h1>Contact Flower</h1>
        <p>Get in touch for support, questions<br>or business inquiries — we're here for you!</p>
    </div>

    {{-- Alerts --}}
    @if(session('notify'))
        @foreach(session('notify') as $msg)
        <div class="ct-alert {{ $msg[0] == 'success' ? 'success' : 'error' }}">
            <i class="fas fa-{{ $msg[0] == 'success' ? 'check-circle' : 'exclamation-circle' }}"></i>
            {{ __($msg[1]) }}
        </div>
        @endforeach
    @endif

    {{-- ── WhatsApp ── --}}
    <div class="ct-card">
        <div class="ct-card-head">
            <i class="fab fa-whatsapp"></i>
            <span>Join Our Community</span>
        </div>
        <div class="ct-card-body">
            @php $contactChannels = officialChannels(); @endphp
            @forelse($contactChannels as $contactChannel)
            <a href="{{ $contactChannel->link }}" target="_blank" rel="noopener noreferrer" class="ct-wa-btn">
                @if($contactPhoto = officialChannelImageUrl($contactChannel))
                    <img src="{{ $contactPhoto }}" alt="{{ $contactChannel->title }}" class="ct-wa-photo">
                @else
                    <div class="ct-wa-icon"><i class="{{ str_contains((string) $contactChannel->icon, 'fa-') ? $contactChannel->icon : 'fab fa-whatsapp' }}" style="color:#fff;"></i></div>
                @endif
                <div class="ct-wa-text">
                    <h4>{{ $contactChannel->title }}</h4>
                    <p>{{ $contactChannel->purpose ?: ($contactChannel->description ?: 'Open this official channel') }}</p>
                </div>
            </a>
            @empty
            @php
                $communityLink = whatsappCommunityLink();
                $channelLink = whatsappChannelLink();
            @endphp
            <a href="{{ $communityLink ?: '#' }}" target="_blank" rel="noopener noreferrer" class="ct-wa-btn {{ $communityLink ? '' : 'disabled' }}" @unless($communityLink) style="pointer-events:none;opacity:.6" @endunless>
                <div class="ct-wa-icon"><i class="fab fa-whatsapp" style="color:#fff;"></i></div>
                <div class="ct-wa-text">
                    <h4>Official Community</h4>
                    <p>Join our official WhatsApp community group</p>
                </div>
            </a>
            <a href="{{ $channelLink ?: '#' }}" target="_blank" rel="noopener noreferrer" class="ct-wa-btn channel {{ $channelLink ? '' : 'disabled' }}" @unless($channelLink) style="pointer-events:none;opacity:.6" @endunless>
                <div class="ct-wa-icon"><i class="fas fa-broadcast-tower" style="color:#fff;"></i></div>
                <div class="ct-wa-text">
                    <h4>Official Channel</h4>
                    <p>Official WhatsApp channel for updates and announcements</p>
                </div>
            </a>
            @endforelse
        </div>
    </div>

    {{-- ── Info ── --}}
    <div class="ct-card">
        <div class="ct-card-head">
            <i class="fas fa-info-circle"></i>
            <span>Support Info</span>
        </div>
        <div class="ct-card-body">
            <div class="ct-info-row">
                <div class="ct-info-icon" style="background:#fce4f0;"><i class="fas fa-clock" style="color:#e91e8c;"></i></div>
                <div>
                    <div class="ct-info-title">Response Time</div>
                    <div class="ct-info-sub">Usually within 24 hours</div>
                </div>
            </div>
            <div class="ct-info-row">
                <div class="ct-info-icon" style="background:#e8f5e9;"><i class="fas fa-headset" style="color:#22c55e;"></i></div>
                <div>
                    <div class="ct-info-title">Support Hours</div>
                    <div class="ct-info-sub">24/7 Community Support</div>
                </div>
            </div>
            <div class="ct-info-row">
                <div class="ct-info-icon" style="background:#e3f2fd;"><i class="fas fa-language" style="color:#1976d2;"></i></div>
                <div>
                    <div class="ct-info-title">Languages</div>
                    <div class="ct-info-sub">English, Urdu, Hindi</div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<script>
(function(){
    const c=document.getElementById('ctPetals');
    const e=['🌸','🌺','🌼','🌷','✨','💫','⭐'];
    for(let i=0;i<14;i++){
        const p=document.createElement('div');
        p.className='ct-petal';
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
