@php $pageTitle = 'Withdraw Money'; @endphp

@extends($activeTemplate.'layouts.dashboard')

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
    $withdrawLimit = $withdrawLimit ?? withdrawMinimumFor(auth()->user());
@endphp

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{box-sizing:border-box;}
.wd-wrap{font-family:'Poppins',sans-serif;max-width:500px;margin:0 auto;padding:0 0 3rem;}

/* ── Floating petals ── */
.wd-petals{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.wd-petal{
    position:absolute;opacity:0;
    animation:petalFloat linear infinite;
}
@keyframes petalFloat{
    0%  {opacity:0;transform:translateY(100vh) rotate(0deg);}
    10% {opacity:0.35;}
    90% {opacity:0.15;}
    100%{opacity:0;transform:translateY(-10vh) rotate(360deg);}
}

/* ── Hero ── */
.wd-hero{
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 50%,#ad1457 100%);
    border-radius:0 0 32px 32px;
    padding:2rem 1.4rem 2.5rem;
    text-align:center;
    position:relative;overflow:hidden;
    box-shadow:0 10px 36px rgba(233,30,140,0.28);
    margin-bottom:1.5rem;
}
.wd-hero::before{
    content:'💸';position:absolute;
    font-size:90px;opacity:0.08;
    top:-10px;right:-10px;transform:rotate(15deg);
    pointer-events:none;
}
.wd-hero::after{
    content:'🌸';position:absolute;
    font-size:80px;opacity:0.08;
    bottom:-10px;left:-5px;transform:rotate(-15deg);
    pointer-events:none;
}

/* animated flower icon */
.wd-hero-icon{
    font-size:3.5rem;
    display:block;margin:0 auto 0.8rem;
    animation:heroFlower 3s ease-in-out infinite;
    filter:drop-shadow(0 4px 16px rgba(255,255,255,0.3));
    position:relative;z-index:2;
}
@keyframes heroFlower{
    0%,100%{transform:translateY(0) rotate(0deg) scale(1);}
    30%    {transform:translateY(-10px) rotate(-8deg) scale(1.08);}
    70%    {transform:translateY(-5px) rotate(6deg) scale(1.04);}
}

.wd-hero h1{
    font-size:1.7rem;font-weight:800;color:#fff;
    margin-bottom:0.3rem;position:relative;z-index:2;
    text-shadow:0 2px 10px rgba(0,0,0,0.1);
}
.wd-hero p{
    font-size:0.86rem;color:rgba(255,255,255,0.85);
    position:relative;z-index:2;margin-bottom:1.2rem;
}

/* balance pill */
.wd-balance-pill{
    display:inline-flex;align-items:center;gap:8px;
    background:rgba(255,255,255,0.18);
    border:1.5px solid rgba(255,255,255,0.3);
    border-radius:20px;padding:8px 20px;
    color:#fff;font-size:0.9rem;font-weight:700;
    backdrop-filter:blur(8px);
    position:relative;z-index:2;
    animation:pillPop 0.6s cubic-bezier(0.34,1.56,0.64,1) 0.3s both;
}
@keyframes pillPop{from{opacity:0;transform:scale(0.8);}to{opacity:1;transform:scale(1);}}
.wd-balance-pill i{font-size:14px;}

/* ── Form card ── */
.wd-card{
    background:#fff;
    border-radius:24px;
    border:1.8px solid #f0e0ec;
    padding:1.8rem 1.5rem;
    box-shadow:0 8px 32px rgba(233,30,140,0.08);
    margin:0 0.8rem 1.2rem;
    animation:cardIn 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.1s both;
}
@keyframes cardIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}

/* flower row in card */
.wd-flower-row{
    display:flex;justify-content:center;gap:8px;
    margin-bottom:1.4rem;
    font-size:1.8rem;
}
.wd-flower-row span{
    animation:flowerBob 2s ease-in-out infinite;
    display:inline-block;
}
.wd-flower-row span:nth-child(2){animation-delay:0.3s;}
.wd-flower-row span:nth-child(3){animation-delay:0.6s;}
.wd-flower-row span:nth-child(4){animation-delay:0.9s;}
.wd-flower-row span:nth-child(5){animation-delay:1.2s;}
@keyframes flowerBob{
    0%,100%{transform:translateY(0) rotate(0deg);}
    40%    {transform:translateY(-7px) rotate(-5deg);}
    70%    {transform:translateY(-3px) rotate(4deg);}
}

/* field */
.wd-field{margin-bottom:1.1rem;}
.wd-field label{
    display:flex;align-items:center;gap:7px;
    font-size:0.78rem;font-weight:700;color:#888;
    text-transform:uppercase;letter-spacing:0.06em;
    margin-bottom:0.45rem;
}
.wd-field label i{color:#e91e8c;font-size:13px;}

.wd-select,.wd-input{
    width:100%;
    padding:13px 14px;
    background:#fdf5f9;
    border:1.8px solid #f0d0e4;
    border-radius:14px;
    font-size:0.92rem;font-family:'Poppins',sans-serif;
    color:#333;outline:none;
    transition:border-color 0.25s,box-shadow 0.25s,background 0.25s;
    appearance:none;
}
.wd-select:focus,.wd-input:focus{
    border-color:#e91e8c;
    box-shadow:0 0 0 4px rgba(233,30,140,0.09);
    background:#fff;
}
.wd-select{
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23e91e8c' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 14px center;
    background-color:#fdf5f9;
    padding-right:36px;
}
.wd-input-wrap{position:relative;}
.wd-input-prefix{
    position:absolute;left:14px;top:50%;transform:translateY(-50%);
    font-size:0.85rem;font-weight:700;color:#e91e8c;
}
.wd-input.has-prefix{padding-left:38px;}
.wd-hint{font-size:0.75rem;color:#bbb;margin-top:5px;}

/* method info box */
.wd-method-info{
    background:linear-gradient(135deg,#fdf5f9,#fce4f0);
    border:1.5px solid #f0d0e4;
    border-radius:14px;padding:0.9rem 1rem;
    margin-bottom:1.1rem;display:none;
    animation:cardIn 0.3s ease both;
}
.wd-method-info.show{display:block;}
.wd-method-info p{
    font-size:0.8rem;color:#888;
    display:flex;align-items:center;gap:6px;
    margin-bottom:4px;
}
.wd-method-info p:last-child{margin-bottom:0;}
.wd-method-info i{color:#e91e8c;font-size:11px;}

/* submit button */
.wd-btn{
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
.wd-btn::before{
    content:'';position:absolute;
    top:0;left:-60%;width:40%;height:100%;
    background:rgba(255,255,255,0.15);
    transform:skewX(-20deg);
    animation:btnShine 3s ease-in-out infinite;
}
@keyframes btnShine{0%,100%{left:-60%;}50%{left:120%;}}
.wd-btn:hover{
    background-position:right center;
    transform:translateY(-2px);
    box-shadow:0 10px 30px rgba(233,30,140,0.40);
}

/* notice */
.wd-notice{
    background:rgba(255,251,235,0.95);
    border:1.5px solid #fde68a;
    border-radius:18px;
    padding:1rem 1.2rem;
    margin:0 0.8rem;
    display:flex;gap:12px;align-items:flex-start;
    animation:cardIn 0.5s ease 0.2s both;
}
.wd-notice i{color:#f59e0b;font-size:17px;flex-shrink:0;margin-top:2px;}
.wd-notice ul{font-size:0.8rem;color:#92400e;line-height:1.8;list-style:none;padding:0;}
.wd-notice ul li::before{content:'• ';color:#f59e0b;font-weight:700;}
</style>

{{-- Floating petals --}}
<div class="wd-petals" id="wdPetals"></div>

<div class="wd-wrap" style="position:relative;z-index:1;">

    {{-- ── Hero ── --}}
    <div class="wd-hero">
        <span class="wd-hero-icon">🌸</span>
        <h1>Withdraw Money</h1>
        <p>Choose your method and enter amount</p>
        <div class="wd-balance-pill">
            <i class="fas fa-wallet"></i>
            Available: Rs {{ showAmount(auth()->user()->balance) }}
        </div>
    </div>

    {{-- ── Form card ── --}}
    <div class="wd-card">

        {{-- Flower row --}}
        <div class="wd-flower-row">
            <span>🌸</span><span>🌺</span><span>🌼</span><span>🌷</span><span>🌻</span>
        </div>

        <form action="{{ route('user.withdraw.money') }}" method="post">
            @csrf
            <input type="hidden" name="method_code" id="hMethodCode">
            <input type="hidden" name="currency"    id="hCurrency">

            {{-- Method --}}
            <div class="wd-field">
                <label><i class="fas fa-credit-card"></i> Withdrawal Method</label>
                <select name="method_code" id="methodSelect" class="wd-select" required>
                    <option value="">Select Withdrawal Method</option>
                    @foreach($withdrawMethod as $data)
                        <option value="{{ $data->id }}" data-resource="{{ $data }}">
                            {{ __($data->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Amount --}}
            <div class="wd-field">
                <label><i class="fas fa-coins"></i> Withdrawal Amount</label>
                <div class="wd-input-wrap">
                    <span class="wd-input-prefix">Rs</span>
                    <input type="number" name="amount" id="amountInput"
                           class="wd-input has-prefix"
                           placeholder="Minimum Rs {{ $withdrawLimit['min'] }}"
                           step="1" min="{{ $withdrawLimit['min'] }}" required>
                </div>
                <div class="wd-hint">
                    @if($withdrawLimit['is_first'])
                        First withdrawal minimum: <strong>Rs 50</strong>. Later withdrawals: <strong>Rs 500</strong>.
                    @else
                        Minimum withdrawal: <strong>Rs 500</strong>
                    @endif
                    &nbsp;· Available: Rs {{ showAmount(auth()->user()->balance) }}
                </div>
            </div>

            <button type="submit" class="wd-btn">
                <i class="fas fa-arrow-up"></i> Submit Withdrawal Request
            </button>
        </form>
    </div>

    {{-- ── Notice ── --}}
    <div class="wd-notice">
        <i class="fas fa-exclamation-triangle"></i>
        <ul>
            <li>First withdrawal minimum: Rs 50</li>
            <li>Every withdrawal after that: Rs 500 minimum</li>
            <li>Requests processed within 24–48 hours</li>
            <li>Processing fees may be deducted</li>
            <li>Ensure your withdrawal details are correct</li>
        </ul>
    </div>

</div>

<script>
// Floating petals
(function(){
    const c = document.getElementById('wdPetals');
    const e = ['🌸','🌺','🌼','🌷','✨','💫'];
    for(let i=0;i<10;i++){
        const p=document.createElement('div');
        p.className='wd-petal';
        p.textContent=e[Math.floor(Math.random()*e.length)];
        p.style.left=Math.random()*100+'%';
        p.style.fontSize=(0.7+Math.random()*0.7)+'rem';
        p.style.animationDuration=(9+Math.random()*10)+'s';
        p.style.animationDelay=(Math.random()*10)+'s';
        c.appendChild(p);
    }
})();

// Method select
document.getElementById('methodSelect').addEventListener('change', function(){
    const opt = this.options[this.selectedIndex];
    const data = opt.dataset.resource ? JSON.parse(opt.dataset.resource) : null;

    document.getElementById('hMethodCode').value = this.value;

    if(data){
        document.getElementById('hCurrency').value = data.currency || '';
    }
});
</script>

@endsection
