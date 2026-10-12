@php $pageTitle = 'Transaction History'; @endphp

@extends($activeTemplate . 'layouts.dashboard')

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
    $trxLabels = [
        'ptc_earn'              => 'Ad Earning',
        'ptc_earn_subtract'     => 'Ad Adjustment',
        'ptc_earn_sub'          => 'Ad Adjustment',
        'balance_add'           => 'Bonus (Admin)',
        'balance_subtract'      => 'Deduction (Admin)',
        'balance_received'      => 'Receiving',
        'balance_transfer'      => 'Transfer',
        'referral_commission'   => 'Referral Commission',
        'deposit'               => 'Deposit',
        'withdraw'              => 'Withdrawal',
        'withdraw_reject'       => 'Withdraw Rejected',
        'flowers_bonus'         => 'Flowers Bonus',
        'subscribe_plan'        => 'Plan Purchase',
        'registration_bonus'    => 'Registration Bonus',
        'ad_create'             => 'Ad Create',
        'ad_reject'             => 'Ad Rejected',
    ];
@endphp

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.th-wrap{font-family:'Poppins',sans-serif;max-width:520px;margin:0 auto;padding:0 0 3rem;}

.th-back{
    display:inline-flex;align-items:center;gap:6px;
    margin:0 1.2rem 0.8rem;font-size:0.82rem;font-weight:600;
    color:#e91e8c;text-decoration:none;
}

.th-hero{
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 50%,#ad1457 100%);
    border-radius:0 0 28px 28px;
    padding:1.6rem 1.4rem 1.8rem;
    text-align:center;
    box-shadow:0 10px 36px rgba(233,30,140,0.28);
    margin-bottom:1.2rem;
}
.th-hero h1{
    font-size:1.4rem;font-weight:800;color:#fff;margin:0 0 0.3rem;
}
.th-hero p{
    font-size:0.82rem;color:rgba(255,255,255,0.85);margin:0;
}

.th-list{padding:0 1.2rem;display:flex;flex-direction:column;gap:10px;}
.th-item{
    background:#fff;border:1.5px solid #f0e0ec;border-radius:16px;
    padding:1rem 1.15rem;
    display:flex;align-items:center;justify-content:space-between;gap:12px;
    box-shadow:0 3px 14px rgba(233,30,140,0.06);
}
.th-left{min-width:0;flex:1;}
.th-label{
    font-size:0.95rem;font-weight:700;color:#1a1a2e;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.th-date{font-size:0.75rem;font-weight:500;color:#999;margin-top:3px;}
.th-amount{
    font-size:0.98rem;font-weight:800;white-space:nowrap;flex-shrink:0;
}
.th-amount.credit{color:#16a34a;}
.th-amount.debit{color:#e91e8c;}

.th-empty{
    text-align:center;padding:2.5rem 1.2rem;margin:0 1.2rem;
    background:#fff;border:1.5px solid #f0e0ec;border-radius:18px;
}
.th-empty p{font-size:0.86rem;color:#ccc;margin:0;}

.th-pagination{padding:1.2rem 1.2rem 0;display:flex;justify-content:center;}
</style>

<div class="th-wrap">

    <a href="{{ route('user.wallet') }}" class="th-back"><i class="fas fa-arrow-left"></i> Back to Wallet</a>

    <div class="th-hero">
        <h1>Transaction History</h1>
        <p>All balance activity in one place</p>
    </div>

    @if($transactions->count() > 0)
    <div class="th-list">
        @foreach($transactions as $trx)
        @php
            $label = $trxLabels[$trx->remark] ?? keyToTitle($trx->remark);
            $isCredit = $trx->trx_type == '+';
        @endphp
        <div class="th-item">
            <div class="th-left">
                <div class="th-label">{{ $label }}</div>
                <div class="th-date">{{ showDateTime($trx->created_at, 'M d, Y · h:i A') }}</div>
            </div>
            <div class="th-amount {{ $isCredit ? 'credit' : 'debit' }}">
                {{ $isCredit ? '+' : '-' }}Rs {{ showAmount($trx->amount) }}
            </div>
        </div>
        @endforeach
    </div>

    @if($transactions->hasPages())
    <div class="th-pagination">
        {{ paginateLinks($transactions) }}
    </div>
    @endif
    @else
    <div class="th-empty">
        <p>No transactions found yet.</p>
    </div>
    @endif

</div>

@endsection
