@extends($activeTemplate.'layouts.dashboard')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{box-sizing:border-box;}
.wp-wrap{font-family:'Poppins',sans-serif;max-width:500px;margin:0 auto;padding:0 0 3rem;position:relative;z-index:1;}

/* ── Floating petals ── */
.wp-petals{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.wp-petal{position:absolute;opacity:0;animation:petalFloat linear infinite;}
@keyframes petalFloat{
    0%  {opacity:0;transform:translateY(100vh) rotate(0deg);}
    10% {opacity:0.3;}90%{opacity:0.12;}
    100%{opacity:0;transform:translateY(-10vh) rotate(360deg);}
}

/* ── Hero ── */
.wp-hero{
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 50%,#ad1457 100%);
    border-radius:0 0 36px 36px;
    padding:2rem 1.4rem 2.8rem;
    text-align:center;position:relative;overflow:hidden;
    box-shadow:0 10px 36px rgba(233,30,140,0.28);
    margin-bottom:1.5rem;
}
.wp-hero::before{content:'💸';position:absolute;font-size:90px;opacity:0.08;top:-10px;right:-10px;transform:rotate(15deg);pointer-events:none;}
.wp-hero::after{content:'🌸';position:absolute;font-size:80px;opacity:0.08;bottom:-10px;left:-5px;transform:rotate(-15deg);pointer-events:none;}

.wp-hero-emoji{
    font-size:3.8rem;display:block;margin:0 auto 0.8rem;
    animation:heroFloat 3s ease-in-out infinite;
    filter:drop-shadow(0 4px 16px rgba(255,255,255,0.3));
    position:relative;z-index:2;
}
@keyframes heroFloat{
    0%,100%{transform:translateY(0) rotate(0deg) scale(1);}
    30%    {transform:translateY(-12px) rotate(-8deg) scale(1.08);}
    70%    {transform:translateY(-6px) rotate(6deg) scale(1.04);}
}
.wp-hero h1{font-size:1.7rem;font-weight:800;color:#fff;margin-bottom:0.3rem;position:relative;z-index:2;text-shadow:0 2px 10px rgba(0,0,0,0.1);}
.wp-hero p{font-size:0.86rem;color:rgba(255,255,255,0.85);position:relative;z-index:2;}

/* ── Cards ── */
.wp-card{
    background:#fff;border-radius:22px;
    border:1.8px solid #f0e0ec;
    padding:1.4rem 1.3rem;
    margin:0 0.8rem 1.2rem;
    box-shadow:0 6px 24px rgba(233,30,140,0.07);
    animation:cardIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both;
}
.wp-card:nth-child(2){animation-delay:0.1s;}
.wp-card:nth-child(3){animation-delay:0.2s;}
.wp-card:nth-child(4){animation-delay:0.3s;}
@keyframes cardIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}

.wp-card-title{
    display:flex;align-items:center;gap:8px;
    font-size:0.82rem;font-weight:700;color:#888;
    text-transform:uppercase;letter-spacing:0.07em;
    margin-bottom:1rem;
    padding-bottom:0.7rem;
    border-bottom:1.5px solid #fce4f0;
}
.wp-card-title i{color:#e91e8c;font-size:14px;}

/* info rows */
.wp-info-row{
    display:flex;align-items:center;justify-content:space-between;
    padding:0.65rem 0;
    border-bottom:1px solid #fdf0f7;
}
.wp-info-row:last-child{border-bottom:none;}
.wp-info-lbl{font-size:0.82rem;color:#aaa;font-weight:500;}
.wp-info-val{font-size:0.9rem;font-weight:700;color:#1a1a2e;}
.wp-info-val.pink{
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;
}

/* notice */
.wp-notice{
    background:linear-gradient(135deg,#fff8e1,#fffbeb);
    border:1.5px solid #fde68a;
    border-radius:18px;
    padding:1rem 1.2rem;
    margin:0 0.8rem 1.2rem;
    display:flex;gap:12px;align-items:flex-start;
    animation:cardIn 0.5s ease 0.15s both;
}
.wp-notice i{color:#f59e0b;font-size:18px;flex-shrink:0;margin-top:2px;}
.wp-notice p{font-size:0.8rem;color:#92400e;line-height:1.7;}

/* form fields override */
.wp-card .form-group{margin-bottom:1rem;}
.wp-card label{
    display:block;font-size:0.74rem;font-weight:700;
    color:#888;margin-bottom:0.4rem;
    text-transform:uppercase;letter-spacing:0.06em;
}
.wp-card input,
.wp-card textarea,
.wp-card select{
    width:100%;padding:12px 14px;
    background:#fdf5f9 !important;
    border:1.8px solid #f0d0e4 !important;
    border-radius:12px !important;
    color:#333 !important;font-size:0.9rem !important;
    font-family:'Poppins',sans-serif !important;
    outline:none !important;
    transition:border-color 0.25s,box-shadow 0.25s !important;
    -webkit-text-fill-color:#333 !important;
}
.wp-card input:focus,
.wp-card textarea:focus,
.wp-card select:focus{
    border-color:#e91e8c !important;
    box-shadow:0 0 0 4px rgba(233,30,140,0.09) !important;
    background:#fff !important;
}
.wp-card input[type=file]{
    padding:10px 14px !important;
    border-style:dashed !important;
}

/* submit button */
.wp-btn{
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
.wp-btn::before{
    content:'';position:absolute;
    top:0;left:-60%;width:40%;height:100%;
    background:rgba(255,255,255,0.15);
    transform:skewX(-20deg);
    animation:btnShine 3s ease-in-out infinite;
}
@keyframes btnShine{0%,100%{left:-60%;}50%{left:120%;}}
.wp-btn:hover{
    background-position:right center;
    transform:translateY(-2px);
    box-shadow:0 10px 30px rgba(233,30,140,0.40);
}

/* support link */
.wp-support{text-align:center;font-size:0.84rem;color:#bbb;margin:0 0.8rem;}
.wp-support a{color:#e91e8c;font-weight:600;text-decoration:none;}
.wp-support a:hover{text-decoration:underline;}
</style>

{{-- Floating petals --}}
<div class="wp-petals" id="wpPetals"></div>

<div class="wp-wrap">

    {{-- ── Hero ── --}}
    <div class="wp-hero">
        <span class="wp-hero-emoji">💸</span>
        <h1>Withdrawal Confirmation</h1>
        <p>Review your details before submitting</p>
    </div>

    {{-- ── Notice ── --}}
    <div class="wp-notice">
        <i class="fas fa-info-circle"></i>
        <p>Withdrawal is processed within <strong>2–24 hours</strong>. No service fee. By submitting, you agree to our terms and conditions. In case of fraud, your account may be frozen.</p>
    </div>

    {{-- ── Withdrawal details summary ── --}}
    <div class="wp-card">
        <div class="wp-card-title">
            <i class="fas fa-receipt"></i> Withdrawal Summary
        </div>
        <div class="wp-info-row">
            <span class="wp-info-lbl">Method</span>
            <span class="wp-info-val">{{ $withdraw->method->name ?? '—' }}</span>
        </div>
        <div class="wp-info-row">
            <span class="wp-info-lbl">Amount</span>
            <span class="wp-info-val pink">Rs {{ showAmount($withdraw->amount) }}</span>
        </div>
        @if(isset($withdraw->charge) && $withdraw->charge > 0)
        <div class="wp-info-row">
            <span class="wp-info-lbl">Charge</span>
            <span class="wp-info-val">Rs {{ showAmount($withdraw->charge) }}</span>
        </div>
        <div class="wp-info-row">
            <span class="wp-info-lbl">You Receive</span>
            <span class="wp-info-val pink">Rs {{ showAmount($withdraw->after_charge) }}</span>
        </div>
        @endif
        <div class="wp-info-row">
            <span class="wp-info-lbl">Status</span>
            <span class="wp-info-val" style="color:#f59e0b;">⏳ Pending</span>
        </div>
    </div>

    {{-- ── Form ── --}}
    <div class="wp-card">
        <div class="wp-card-title">
            <i class="fas fa-file-alt"></i> Complete Details
        </div>
        <form action="{{ route('user.withdraw.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <x-viser-form identifier="id" identifierValue="{{ $withdraw->method->form_id }}"></x-viser-form>
            <button type="submit" class="wp-btn">
                <i class="fas fa-check-circle"></i> Confirm Withdrawal
            </button>
        </form>
    </div>

    {{-- ── Support ── --}}
    <div class="wp-support">
        Need help? <a href="{{ route('ticket') }}">Contact Support</a>
    </div>

</div>

<script>
(function(){
    const c=document.getElementById('wpPetals');
    const e=['🌸','🌺','🌼','🌷','✨','💫'];
    for(let i=0;i<10;i++){
        const p=document.createElement('div');
        p.className='wp-petal';
        p.textContent=e[Math.floor(Math.random()*e.length)];
        p.style.left=Math.random()*100+'%';
        p.style.fontSize=(0.7+Math.random()*0.7)+'rem';
        p.style.animationDuration=(9+Math.random()*10)+'s';
        p.style.animationDelay=(Math.random()*10)+'s';
        c.appendChild(p);
    }
})();
</script>

@push('script')
<script>
(function($){
    "use strict";
    $('select[name=method_code]').change(function(){
        if(!$(this).val()){ $('.preview-details').addClass('d-none'); return; }
        var r=$('select[name=method_code] option:selected').data('resource');
        var fixed=parseFloat(r.fixed_charge),pct=parseFloat(r.percent_charge),rate=parseFloat(r.rate);
        $('.min').text(parseFloat(r.min_limit).toFixed(2));
        $('.max').text(parseFloat(r.max_limit).toFixed(2));
        var amt=parseFloat($('input[name=amount]').val())||0;
        if(amt<=0){$('.preview-details').addClass('d-none');return;}
        $('.preview-details').removeClass('d-none');
        var charge=parseFloat(fixed+(amt*pct/100)).toFixed(2);
        $('.charge').text(charge);
        if(r.currency!='{{ $general->cur_text }}'){
            $('.rate-element').html(`<span>@lang('Conversion Rate')</span><span class="fw-bold">1 {{__($general->cur_text)}} = <span class="rate">${rate}</span> <span class="base-currency">${r.currency}</span></span>`);
            $('.rate-element,.in-site-cur').removeClass('d-none').addClass('d-flex');
        }else{
            $('.rate-element').html('');
            $('.rate-element,.in-site-cur').addClass('d-none').removeClass('d-flex');
        }
        var recv=parseFloat(amt-charge).toFixed(2);
        $('.receivable').text(recv);
        $('.final_amo').text(parseFloat(recv*rate).toFixed(2));
        $('.base-currency').text(r.currency);
        $('.method_currency').text(r.currency);
    });
    $('input[name=amount]').on('input',function(){
        $('select[name=method_code]').change();
        $('.amount').text(parseFloat($(this).val()).toFixed(2));
    });
})(jQuery);
</script>
@endpush

@endsection
