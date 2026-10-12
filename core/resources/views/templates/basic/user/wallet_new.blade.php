
@if (empty(auth()->user()->pv))
        @php redirect('/user/deposit')->send(); @endphp
@endif
@auth
    @if(auth()->user()->plan_id == 0)
        @php redirect('/user/pending')->send(); @endphp
    @endif
@endauth

@php
  $authUser = Auth::user();
  $balance = number_format($authUser->balance, 0, '.', '');
  $flowersEarn = number_format((float) ($authUser->fanta_balance ?? 0), 0, '.', '');
@endphp



@php
    use App\Models\Transaction;
    $authUserId = Auth::id();
    $referralCommission = Transaction::where('user_id', $authUserId)
                                    ->where('remark', 'referral_commission')
                                    ->sum('amount');
    $formattedReferralCommission = number_format($referralCommission, 0, '.', '');
@endphp


@php
/*
    $authUserId = Auth::id();
    $ptcEarn = Transaction::where('user_id', $authUserId)
                            ->where('remark', 'ptc_earn')
                            ->where('trx_type', '+')
                            ->sum('amount');
                            
    $ptcEarnSub = Transaction::where('user_id', $authUserId)
                            ->where('remark', 'ptc_earn_subtract')
                            ->where('trx_type', '-')
                            ->sum('amount');
                            
    $formattedPtcEarn = intval($ptcEarn - $ptcEarnSub);
*/
@endphp


@php
/*
  use App\Models\Withdrawal;
  $authUserId = Auth::id();
  
  $withdrawal = Withdrawal::where('user_id', $authUserId)
                          ->where('status', 2)
                          ->first();
    
  $withdrawal = Withdrawal::where('user_id',$authUserId)->where('status',1)->sum('amount');
  $finalAmount = $withdrawal ? number_format($withdrawal->final_amount, 0, '.', '') : 0;
  */
@endphp

@php
use App\Models\Withdrawal;
$authUserId = Auth::id();

$totalAmount = Withdrawal::where('user_id', $authUserId)
                          ->where('status', 1)
                          ->sum('amount');

$totalWithraw = number_format($totalAmount, 0, '.', '');
@endphp


@php
  $authUserId = Auth::id();
  $withdrawal = Withdrawal::where('user_id', $authUserId)
                          ->where('status', 2)
                          ->first();
  $withdrawalAmount = $withdrawal ? number_format($withdrawal->final_amount, 0, '.', '') : 0;
  

@endphp

@php
  $authUserId = Auth::id();
  $withdrawal = Withdrawal::where('user_id', $authUserId)
                          ->where('status', 3)
                          ->first();
  $withdrawalAmountre = $withdrawal ? number_format($withdrawal->final_amount, 0, '.', '') : 0;
  
$pageTitle = 'wallet';
@endphp







@extends($activeTemplate.'layouts.master')

@section('content')

<style>
    
    
    .bg-111 {
  --tw-bg-opacity: 1;
  background: linear-gradient(to right, #ff6c00, #ff6c00, #ff6c00, #ff6c00, #ff6c00);
}

</style>

    
    <main>
        <section>
       <a href="home" class="h-44 flex items-center justify-center">
          <img src="{{ asset('assets/logoEarn.png') }}" style="width:300px;height:150px;" alt="SnapsTubes Logo">
        </a>
    <div class="px-4 pt-10 max-w-lg mx-auto">
        <h2 class="text-2xl font-bold text-center text-white mb-8">Wallet</h2>
        <div class="grid grid-cols-3 gap-4 w-full">
            <div class="bg-111 text-white flex flex-col items-center justify-center gap-2 p-3 rounded-lg">
                <h1 class="text-l font-bold text-center"> {{ $balance }} Rs</h1>
                <p class="text-l font-medium text-center">Current Balance</p>
            </div>
            <div class="bg-111 text-white flex flex-col items-center justify-center gap-2 p-3 rounded-lg">
                <h1 class="text-l font-bold text-center">{{ $formattedReferralCommission }} Rs</h1>
                <p class="text-l font-medium text-center">Staff Earning</p>
            </div>

            <div class="bg-111 flex flex-col items-center justify-center gap-2 p-3 rounded-lg">
                <a href="#" onclick="alert('You can withdraw at level 11')">
                    <h1 style="color: white; font-weight: bold; text-align: center; font-size: 1.25rem;">
                        {{ $flowersEarn }}
                    </h1>
                    <p style="color: white; text-align: center; font-size: 1.1rem;">
                        Flowers Earn
                    </p>
                </a>
            </div>
            
                        <div class="bg-111 text-white flex flex-col items-center justify-center gap-2 p-3 rounded-lg">
                <h1 class="text-l font-bold text-center">{{ $totalWithraw }}</h1>
                <p class="text-l font-medium text-center">Aproved Withdraw </p>
            </div>
            
           <div class="bg-111 text-white flex flex-col items-center justify-center gap-2 p-3 rounded-lg">
                <h1 class="text-l font-bold text-center">{{ $withdrawalAmount }}</h1>
                <p class="text-l font-medium text-center">Pending Withdraw</p>
            </div>
                       <div class="bg-111 text-white flex flex-col items-center justify-center gap-2 p-3 rounded-lg">
                <h1 class="text-l font-bold text-center">{{ $withdrawalAmountre }}</h1>
                <p class="text-l font-medium text-center">Rejected Withdraw</p>
            </div>
        </div>
    </div>
<?php
$withdraws = Withdrawal::where('user_id', auth()->id())->where('status', '!=', 0)->get();
?>

<section>
    <p class="text-red-600 text-center text-lg text-medium capitalize hidden" id="msg">You can only withdraw SEGA earning at level 12</p>
    <div class="w-full max-w-lg mx-auto py-12 px-4 mb-10">
        <h2 class="text-2xl font-bold text-center text-white">Withdraw <br> History</h2>
        <div class="flow-root">
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($withdraws as $withdraw)
                <li class="py-3 sm:py-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-lg font-medium text-white truncate ml-8">
                                {{ showAmount($withdraw->amount) }}
                            </p>
@php
    $statusText = '';
    $statusColor = '';

    switch ($withdraw->status) {
        case 1:
            $statusText = 'Success';
            $statusColor = 'green';
            break;
        case 2:
            $statusText = 'Pending';
            $statusColor = 'blue';
            break;
        case 3:
            $statusText = 'Rejected';
            $statusColor = 'red';
            break;
        default:
            $statusText = 'Unknown';
            break;
    }
@endphp

<!-- Inside the HTML -->
<p class="text-lg text-white w-max px-2 rounded-2xl" style="background-color: {{ $statusColor }};">
    {{ $statusText }}
</p>



                        </div>
                        <div class="inline-flex items-center text-base font-semibold text-white">
                            {{ showDateTime($withdraw->created_at) }}
                        </div>
                    </div>
                </li>
                @empty
                <li>
                    <p class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</p>
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</section>


    </main>
    
@endsection

 