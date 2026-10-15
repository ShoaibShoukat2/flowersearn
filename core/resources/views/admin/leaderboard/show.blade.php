@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-3">
        <div class="col-lg-12">
            <a href="{{ route('admin.leaderboard.index') }}" class="btn btn-sm btn-outline--primary">
                <i class="las la-arrow-left"></i> @lang('Back to Leaderboard')
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-30">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Member')</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Username')</span>
                            <a href="{{ route('admin.users.detail', $user->id) }}" class="fw-bold">{{ $displayName }}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Email')</span>
                            <a href="{{ route('admin.users.detail', $user->id) }}">{{ $user->email }}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Current Plan / Package')</span>
                            <span class="fw-bold">{{ __(@$user->plan->name ?? 'No package') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Current Level')</span>
                            <span class="fw-bold">@lang('Level') {{ (int) $displayLevel }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Total Team')</span>
                            <span class="fw-bold">{{ (int) $totalTeam }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Approved Direct Referrals')</span>
                            <span class="fw-bold">{{ (int) $activeTeam }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Total Earnings')</span>
                            <span class="fw-bold">{{ $general->cur_sym }}{{ showAmount($earnings) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Registration Date')</span>
                            <span class="fw-bold">{{ showDateTime($user->created_at) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Account Approval Date')</span>
                            <span class="fw-bold">
                                @if($approvalDeposit)
                                    {{ showDateTime($approvalDeposit->updated_at) }}
                                @else
                                    @lang('Not approved yet')
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-30">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Direct Upline')</h5>
                </div>
                <div class="card-body">
                    @if($upliner)
                        <p class="mb-2">
                            <span class="text-muted d-block">@lang('Username')</span>
                            <a href="{{ route('admin.users.detail', $upliner->id) }}" class="fw-bold">{{ $uplinerName }}</a>
                        </p>
                        <p class="mb-0">
                            <span class="text-muted d-block">@lang('Email')</span>
                            <a href="{{ route('admin.users.detail', $upliner->id) }}">{{ $upliner->email }}</a>
                        </p>
                    @else
                        <p class="text-muted mb-0">@lang('This account has no direct upliner.')</p>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Payment Details')</h5>
                </div>
                <div class="card-body">
                    @if($paymentDeposit)
                        <p class="mb-1"><span class="text-muted">@lang('Amount'):</span> <strong>{{ $general->cur_sym }}{{ showAmount($paymentDeposit->amount) }}</strong></p>
                        <p class="mb-1"><span class="text-muted">@lang('TRX'):</span> {{ $paymentDeposit->trx }}</p>
                        <p class="mb-2"><span class="text-muted">@lang('Method'):</span> {{ __(@$paymentDeposit->gateway->name ?? 'Manual') }}</p>
                        @foreach(depositDetailItems($paymentDeposit) as $info)
                            @if(($info->type ?? '') !== 'file')
                                <p class="mb-1"><span class="text-muted">{{ __($info->name ?? 'Detail') }}:</span> {{ is_array($info->value ?? null) ? implode(', ', $info->value) : ($info->value ?? '') }}</p>
                            @endif
                        @endforeach
                        <div class="mt-3">
                            <h6 class="mb-2">@lang('Original Payment Screenshot')</h6>
                            @forelse(depositProofFiles($paymentDeposit) as $proof)
                                <a href="{{ $proof['url'] }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ $proof['url'] }}" alt="{{ $proof['name'] }}" class="img-fluid rounded border" style="max-height:360px; cursor:zoom-in;">
                                </a>
                                <div class="mt-2">
                                    <a href="{{ $proof['url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline--primary">
                                        <i class="las la-expand"></i> @lang('Open Screenshot')
                                    </a>
                                </div>
                            @empty
                                <p class="text-muted mb-0">@lang('No payment screenshot was saved with this account.')</p>
                            @endforelse
                        </div>
                    @else
                        <p class="text-muted mb-0">@lang('No payment record is saved for this user.')</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
