@extends($activeTemplate.'layouts.dashboard')

@section('content')

@php
    $user        = auth()->user();
    $plans       = \App\Models\Plan::where('status', 1)->get();
    $runningPlanId = $user ? $user->plan_id : 0;

    $planEmojis  = ['🌱','🌿','🌸','💐','🌺'];
    $planColors  = [
        ['#ff4da6','#e91e8c'],
        ['#a855f7','#7c3aed'],
        ['#f59e0b','#d97706'],
        ['#10b981','#059669'],
        ['#3b82f6','#2563eb'],
    ];
@endphp

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
.dep-wrap{font-family:'Poppins',sans-serif;max-width:680px;margin:0 auto;padding:0 0 2rem;}

/* ── Page title ── */
.dep-title{
    text-align:center;margin-bottom:2rem;
}
.dep-title h2{
    font-size:1.6rem;font-weight:800;color:#1a1a2e;
    margin-bottom:0.3rem;
}
.dep-title p{font-size:0.88rem;color:#aaa;}

/* ── Alert banners ── */
.dep-alert{
    display:flex;align-items:flex-start;gap:12px;
    border-radius:14px;padding:1rem 1.2rem;
    margin-bottom:1rem;font-size:0.86rem;
}
.dep-alert.warn{background:#fffbeb;border:1.5px solid #fde68a;color:#92400e;}
.dep-alert.danger{background:#fff1f2;border:1.5px solid #fecdd3;color:#9f1239;}
.dep-alert i{font-size:18px;margin-top:1px;flex-shrink:0;}

/* ── Section label ── */
.dep-label{
    font-size:0.72rem;font-weight:700;color:#aaa;
    text-transform:uppercase;letter-spacing:0.08em;
    margin-bottom:1rem;
}

/* ══════════════════════════════
   PLAN CARDS  — horizontal style
══════════════════════════════ */
.pkg-list{display:flex;flex-direction:column;gap:14px;margin-bottom:2rem;}

.pkg-card{
    display:flex;align-items:stretch;
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    border:2px solid #f0e0ec;
    cursor:pointer;
    transition:border-color 0.25s, box-shadow 0.25s, transform 0.2s;
    position:relative;
}
.pkg-card:hover{
    border-color:#e91e8c;
    box-shadow:0 6px 28px rgba(233,30,140,0.12);
    transform:translateY(-2px);
}
.pkg-card.selected{
    border-color:#e91e8c;
    box-shadow:0 8px 32px rgba(233,30,140,0.18);
    transform:translateY(-2px);
}
.pkg-card.running{
    cursor:default;opacity:0.72;
    border-color:#e5e7eb;
}
.pkg-card.running:hover{transform:none;box-shadow:none;border-color:#e5e7eb;}

/* left accent bar */
.pkg-accent{
    width:6px;flex-shrink:0;
}

/* emoji badge */
.pkg-badge{
    width:64px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
    font-size:1.8rem;
    background:#fdf5f9;
}

/* main content */
.pkg-body{
    flex:1;padding:1.1rem 1rem 1.1rem 0.6rem;
    display:flex;flex-direction:column;justify-content:center;
    gap:6px;
}
.pkg-name{
    font-size:1rem;font-weight:700;color:#1a1a2e;
    line-height:1.2;
}
.pkg-price{
    font-size:1.5rem;font-weight:800;
    line-height:1;
}
.pkg-meta{
    display:flex;align-items:center;gap:8px;flex-wrap:wrap;
    margin-top:2px;
}
.pkg-tag{
    display:inline-flex;align-items:center;gap:5px;
    font-size:0.75rem;font-weight:500;color:#666;
    background:#f5f5f5;border-radius:20px;
    padding:3px 10px;
}
.pkg-tag i{font-size:11px;color:#22c55e;}

/* running badge */
.pkg-running-badge{
    display:inline-flex;align-items:center;gap:5px;
    font-size:0.75rem;font-weight:600;
    background:#f0fdf4;color:#16a34a;
    border:1px solid #bbf7d0;
    border-radius:20px;padding:3px 10px;
}

/* right arrow */
.pkg-arrow{
    width:44px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
    color:#ddd;font-size:16px;
    transition:color 0.2s;
}
.pkg-card:hover .pkg-arrow,
.pkg-card.selected .pkg-arrow{color:#e91e8c;}

/* selected tick */
.pkg-tick{
    position:absolute;top:10px;right:10px;
    width:22px;height:22px;
    background:#e91e8c;border-radius:50%;
    display:none;align-items:center;justify-content:center;
}
.pkg-tick i{font-size:11px;color:#fff;}
.pkg-card.selected .pkg-tick{display:flex;}

/* ── Payment method ── */
.dep-select-wrap{margin-bottom:1.8rem;}
.dep-select{
    width:100%;
    padding:13px 16px;
    border:1.8px solid #f0d0e4;
    border-radius:14px;
    font-size:0.93rem;font-family:'Poppins',sans-serif;
    color:#333;background:#fdf5f9;
    outline:none;
    transition:border-color 0.25s,box-shadow 0.25s;
    appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23e91e8c' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 16px center;
    padding-right:40px;
}
.dep-select:focus{
    border-color:#e91e8c;
    box-shadow:0 0 0 4px rgba(233,30,140,0.09);
    background-color:#fff;
}

/* ── Submit button ── */
.dep-btn{
    width:100%;padding:15px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c,#c2185b);
    background-size:200% auto;
    border:none;border-radius:14px;
    color:#fff;font-size:1rem;font-weight:700;
    font-family:'Poppins',sans-serif;cursor:pointer;
    transition:all 0.35s;
    box-shadow:0 6px 24px rgba(233,30,140,0.30);
    display:flex;align-items:center;justify-content:center;gap:10px;
    letter-spacing:0.02em;
}
.dep-btn:hover:not(:disabled){
    background-position:right center;
    transform:translateY(-2px);
    box-shadow:0 10px 32px rgba(233,30,140,0.42);
}
.dep-btn:disabled{
    opacity:0.45;cursor:not-allowed;
    background:linear-gradient(135deg,#f0a0c8,#d06090);
    box-shadow:none;
}

@media(max-width:480px){
    .pkg-badge{width:52px;font-size:1.5rem;}
    .pkg-price{font-size:1.3rem;}
}
</style>

<div class="dep-wrap">

    {{-- Alerts --}}
    @auth
        @if(auth()->user()->pv === 're')
        @php
            $rejectionReason = \App\Models\Deposit::where('user_id', auth()->id())->where('status', 3)->latest('id')->value('admin_feedback');
        @endphp
        <div class="dep-alert danger">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Account Rejected</strong><br>
                @if($rejectionReason)
                    {{ $rejectionReason }}
                @else
                    Your payment could not be verified. Please submit the correct sender name, sender number, and payment screenshot again.
                @endif
            </div>
        </div>
        @endif

        @if(auth()->user()->plan_id != '0')
        <div class="dep-alert warn">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Session Expired</strong><br>
                Your login session has expired. Please logout and login again.
            </div>
        </div>
        @endif
    @endauth

    {{-- Title --}}
    <div class="dep-title">
        <h2>Choose Your Package</h2>
        <p>Select a plan that fits your goals and start earning</p>
    </div>

    <form action="{{ route('user.deposit.insert') }}" method="post">
        @csrf
        <input type="hidden" name="amount"  id="selected_amount" value="">
        <input type="hidden" name="plan_id" id="selected_plan_id" value="">
        <input type="hidden" name="currency">

        {{-- Plan cards --}}
        <div class="dep-label">Available Packages</div>
        <div class="pkg-list">
            @foreach($plans as $i => $plan)
                @php
                    $isCurrent = $user && $user->plan_id == $plan->id;
                    $ci        = $i % count($planColors);
                    $c1        = $planColors[$ci][0];
                    $c2        = $planColors[$ci][1];
                    $emoji     = $planEmojis[$ci];
                @endphp

                <div class="pkg-card {{ $isCurrent ? 'running' : '' }}"
                     data-plan-id="{{ $plan->id }}"
                     data-price="{{ (float) $plan->price }}"
                     @if(!$isCurrent) onclick="selectPlan(this)" @endif>

                    {{-- accent bar --}}
                    <div class="pkg-accent" style="background:linear-gradient(180deg,{{ $c1 }},{{ $c2 }});"></div>

                    {{-- emoji --}}
                    <div class="pkg-badge">{{ $emoji }}</div>

                    {{-- body --}}
                    <div class="pkg-body">
                        <div class="pkg-name">{{ $plan->name }}</div>
                        <div class="pkg-price" style="color:{{ $c1 }};">
                            Rs {{ showAmount($plan->price) }}
                        </div>
                        <div class="pkg-meta">
                            @if($isCurrent)
                                <span class="pkg-running-badge">
                                    <i class="fas fa-check-circle"></i> Running Plan
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- arrow --}}
                    <div class="pkg-arrow"><i class="fas fa-chevron-right"></i></div>

                    {{-- selected tick --}}
                    <div class="pkg-tick"><i class="fas fa-check"></i></div>
                </div>
            @endforeach
        </div>

        {{-- Payment method --}}
        <div class="dep-label">Payment Method</div>
        <div class="dep-select-wrap">
            <select name="method_code" id="method_code_select" class="dep-select" required>
                <option value="">Select payment method</option>
                @foreach($gatewayCurrency as $data)
                    <option value="{{ $data->method_code }}" data-gateway="{{ json_encode($data) }}">
                        {{ $data->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Submit --}}
        <button type="submit" id="submit_btn" class="dep-btn" disabled>
            <i class="fas fa-arrow-right"></i> Proceed to Payment
        </button>
    </form>

</div>

@endsection

@push('script')
<script>
function selectPlan(el) {
    document.querySelectorAll('.pkg-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('selected_amount').value = el.dataset.price;
    document.getElementById('selected_plan_id').value = el.dataset.planId;
    checkReady();
}

document.getElementById('method_code_select').addEventListener('change', function () {
    checkReady();
});

function checkReady() {
    const ok = document.getElementById('selected_amount').value &&
               document.getElementById('selected_plan_id').value &&
               document.getElementById('method_code_select').value;
    document.getElementById('submit_btn').disabled = !ok;
}
</script>
@endpush
