@php $pageTitle = 'Withdrawal History'; @endphp

@extends($activeTemplate . 'layouts.dashboard')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
#fl-topbar{display:none !important;}
#fl-main-wrap{padding-top:0 !important;}
.fl-page-content{padding:0 !important;background:transparent !important;}

.wh-page{
    font-family:'Poppins',sans-serif;
    min-height:100vh;
    background:
        radial-gradient(ellipse 420px 280px at 100% -10%, rgba(255,182,216,0.55) 0%, transparent 60%),
        radial-gradient(ellipse 340px 220px at 0% 0%, rgba(248,187,217,0.35) 0%, transparent 55%),
        linear-gradient(180deg, #fde8f2 0%, #f7eef8 42%, #f4eaf4 100%);
    padding:0 0 3.5rem;
    position:relative;
    overflow:hidden;
}
.wh-page::before,
.wh-page::after{
    content:'';
    position:absolute;
    pointer-events:none;
    z-index:0;
}
.wh-page::before{
    width:180px;height:180px;right:-30px;top:-20px;
    background:
        radial-gradient(circle at 30% 40%, rgba(233,30,140,0.16) 0 18px, transparent 19px),
        radial-gradient(circle at 62% 28%, rgba(255,105,180,0.18) 0 14px, transparent 15px),
        radial-gradient(circle at 70% 62%, rgba(194,24,91,0.12) 0 22px, transparent 23px),
        radial-gradient(circle at 38% 72%, rgba(233,30,140,0.10) 0 16px, transparent 17px);
}
.wh-page::after{
    width:120px;height:120px;right:40px;top:28px;
    background:
        radial-gradient(circle at 50% 50%, rgba(233,30,140,0.08) 0 40px, transparent 41px);
    border-radius:50%;
}

.wh-inner{max-width:860px;margin:0 auto;padding:1.15rem 1.1rem 0;position:relative;z-index:1;}

.wh-head{
    display:flex;align-items:flex-start;gap:14px;
    margin-bottom:1.25rem;
}
.wh-back{
    width:42px;height:42px;flex-shrink:0;
    border-radius:12px;
    background:#fff;
    border:1px solid #f3d0e4;
    box-shadow:0 4px 14px rgba(233,30,140,0.10);
    display:flex;align-items:center;justify-content:center;
    color:#e91e8c;font-size:16px;text-decoration:none;
}
.wh-head-text h1{
    margin:2px 0 4px;
    font-size:1.55rem;font-weight:800;color:#1a1a2e;letter-spacing:-0.02em;line-height:1.2;
}
.wh-head-text p{
    margin:0;font-size:0.86rem;font-weight:500;color:#9aa0b4;
}

.wh-list{display:flex;flex-direction:column;gap:12px;}

.wh-card{
    background:#fff;
    border-radius:16px;
    box-shadow:0 6px 22px rgba(173,20,87,0.07);
    border:1px solid rgba(255,255,255,0.8);
    overflow:hidden;
    cursor:pointer;
}
.wh-row{
    display:flex;align-items:center;gap:14px;
    padding:1rem 1.05rem;
}
.wh-icon{
    width:46px;height:46px;border-radius:14px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;font-size:18px;
}
.wh-icon.approved{background:#e8f8ee;color:#22c55e;}
.wh-icon.pending{background:#fff4e0;color:#f59e0b;}
.wh-icon.rejected{background:#fde8ee;color:#ef4444;}

.wh-col{min-width:0;}
.wh-col.amount{flex:0 1 150px;}
.wh-col.datetime{flex:1 1 210px;}
.wh-col.status{flex:0 0 auto;}

.wh-lbl{
    display:flex;align-items:center;gap:6px;
    font-size:0.72rem;font-weight:500;color:#b0b4c3;margin-bottom:4px;
}
.wh-lbl i{color:#e91e8c;font-size:0.72rem;}
.wh-val{font-size:0.98rem;font-weight:700;color:#1a1a2e;line-height:1.25;}
.wh-val.muted{font-weight:600;color:#2b2b40;}

.wh-badge{
    display:inline-flex;align-items:center;gap:6px;
    font-size:0.78rem;font-weight:700;
    padding:5px 12px;border-radius:20px;white-space:nowrap;
}
.wh-badge::before{
    content:'';width:7px;height:7px;border-radius:50%;background:currentColor;flex-shrink:0;
}
.wh-badge.approved{background:#e9f9ef;color:#16a34a;}
.wh-badge.pending{background:#fff4e0;color:#d97706;}
.wh-badge.rejected{background:#fde8ee;color:#e11d48;}

.wh-chevron{
    margin-left:auto;flex-shrink:0;
    color:#e11d48;font-size:14px;
    transition:transform 0.2s;
}
.wh-card.is-open .wh-chevron{transform:rotate(90deg);}

.wh-details{
    display:none;
    padding:0 1.05rem 1rem 4.85rem;
    border-top:1px solid #f4e6ef;
}
.wh-card.is-open .wh-details{display:block;padding-top:0.85rem;}
.wh-drow{
    display:flex;justify-content:space-between;gap:12px;
    font-size:0.8rem;padding:4px 0;
}
.wh-drow span:first-child{color:#9aa0b4;font-weight:500;}
.wh-drow span:last-child{color:#1a1a2e;font-weight:700;text-align:right;word-break:break-word;}
.wh-note{
    margin-top:8px;padding:0.6rem 0.75rem;border-radius:10px;
    font-size:0.75rem;line-height:1.45;
}
.wh-note.approved{background:#e8f8ee;color:#166534;}
.wh-note.pending{background:#fff8e1;color:#92400e;}
.wh-note.rejected{background:#fde8ee;color:#9f1239;}

.wh-empty{
    text-align:center;padding:2.8rem 1.2rem;
    background:#fff;border-radius:16px;
    box-shadow:0 6px 22px rgba(173,20,87,0.07);
}
.wh-empty p{font-size:0.88rem;color:#9aa0b4;margin:0 0 1rem;}
.wh-empty-btn{
    display:inline-flex;align-items:center;gap:7px;
    background:linear-gradient(135deg,#ff4da6,#e91e8c);
    color:#fff;border-radius:12px;padding:10px 20px;
    font-size:0.86rem;font-weight:700;text-decoration:none;
}

@media (max-width:720px){
    .wh-row{flex-wrap:wrap;gap:10px 12px;}
    .wh-col.amount{flex:1 1 120px;}
    .wh-col.datetime{flex:1 1 160px;}
    .wh-details{padding-left:1.05rem;}
    .wh-head-text h1{font-size:1.28rem;}
}
</style>

<div class="wh-page">
    <div class="wh-inner">

        <div class="wh-head">
            <a href="{{ route('user.home') }}" class="wh-back" aria-label="Back">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="wh-head-text">
                <h1>Withdrawal History</h1>
                <p>Track all your withdrawal transactions</p>
            </div>
        </div>

        @if($withdraws->count() > 0)
        <div class="wh-list">
            @foreach($withdraws as $w)
            @php
                $isApproved = $w->status == 1;
                $isPending  = $w->status == 2;
                $statusKey  = $isApproved ? 'approved' : ($isPending ? 'pending' : 'rejected');
                $icon       = $isApproved ? 'fa-check' : ($isPending ? 'fa-clock' : 'fa-times');
                $statusText = $isApproved ? 'Approved' : ($isPending ? 'Pending' : 'Rejected');
                $methodName = $w->method->name ?? 'Withdraw';
                $infoItems  = [];
                $rawInfo    = $w->withdraw_information;
                if (is_string($rawInfo)) {
                    $rawInfo = json_decode($rawInfo);
                }
                if ($rawInfo) {
                    foreach ($rawInfo as $val) {
                        $type = is_object($val) ? ($val->type ?? '') : ($val['type'] ?? '');
                        if ($type === 'file') continue;
                        $name = is_object($val) ? ($val->name ?? '') : ($val['name'] ?? '');
                        $raw  = is_object($val) ? ($val->value ?? '') : ($val['value'] ?? '');
                        $value = is_array($raw) ? implode(', ', $raw) : $raw;
                        if ($name !== '' && $value !== '' && $value !== null) {
                            $infoItems[] = ['name' => $name, 'value' => $value];
                        }
                    }
                }
            @endphp
            <div class="wh-card" onclick="this.classList.toggle('is-open')">
                <div class="wh-row">
                    <div class="wh-icon {{ $statusKey }}">
                        <i class="fas {{ $icon }}"></i>
                    </div>
                    <div class="wh-col amount">
                        <div class="wh-lbl">Amount</div>
                        <div class="wh-val">Rs {{ showAmount($w->amount) }}</div>
                    </div>
                    <div class="wh-col datetime">
                        <div class="wh-lbl"><i class="far fa-calendar-alt"></i> Date &amp; Time</div>
                        <div class="wh-val muted">{{ showDateTime($w->created_at, 'M d, Y • h:i A') }}</div>
                    </div>
                    <div class="wh-col status">
                        <div class="wh-lbl">Status</div>
                        <span class="wh-badge {{ $statusKey }}">{{ $statusText }}</span>
                    </div>
                    <i class="fas fa-chevron-right wh-chevron"></i>
                </div>
                <div class="wh-details">
                    <div class="wh-drow"><span>Method</span><span>{{ $methodName }}</span></div>
                    <div class="wh-drow"><span>TRX</span><span>{{ $w->trx }}</span></div>
                    @if($w->charge > 0)
                    <div class="wh-drow"><span>Charge</span><span>Rs {{ showAmount($w->charge) }}</span></div>
                    @endif
                    <div class="wh-drow">
                        <span>{{ $isPending ? 'You will receive' : ($isApproved ? 'Received' : 'Net amount') }}</span>
                        <span>Rs {{ showAmount($w->after_charge) }}</span>
                    </div>
                    @foreach($infoItems as $info)
                    <div class="wh-drow"><span>{{ $info['name'] }}</span><span>{{ $info['value'] }}</span></div>
                    @endforeach
                    @if(!$isPending)
                    <div class="wh-drow">
                        <span>{{ $isApproved ? 'Processed' : 'Rejected on' }}</span>
                        <span>{{ showDateTime($w->updated_at, 'M d, Y • h:i A') }}</span>
                    </div>
                    @endif
                    @if($w->admin_feedback)
                    <div class="wh-note {{ $statusKey }}">{{ $w->admin_feedback }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="wh-empty">
            <p>You have not made any withdrawals yet.</p>
            <a href="{{ route('user.withdraw') }}" class="wh-empty-btn">
                <i class="fas fa-arrow-up"></i> Withdraw Now
            </a>
        </div>
        @endif

    </div>
</div>

@endsection
