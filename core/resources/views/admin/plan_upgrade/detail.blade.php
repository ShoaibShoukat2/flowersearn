@extends('admin.layouts.app')

@section('panel')
    @include('admin.plan_upgrade.nav')

    @php
        $shot = $upgrade->screenshot ? asset(getFilePath('verify') . '/' . $upgrade->screenshot) : null;
        $isPending = (int) $upgrade->status === \App\Models\PlanUpgrade::PENDING;
    @endphp

    <div class="row gy-4">
        <div class="col-lg-7">
            <div class="card b-radius--10 h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Plan Upgrade Details')</h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <span class="text-muted d-block">@lang('User Name')</span>
                            <a href="{{ route('admin.users.detail', $upgrade->user_id) }}" class="fw-bold">{{ $upgrade->user->fullname ?? $upgrade->user->username }}</a>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">@lang('User Email')</span>
                            <a href="{{ route('admin.users.detail', $upgrade->user_id) }}">{{ $upgrade->user->email }}</a>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">@lang('Current / Previous Plan')</span>
                            <strong>{{ $upgrade->fromPlan->name ?? '—' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">@lang('New Plan')</span>
                            <strong>{{ $upgrade->toPlan->name ?? '—' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">@lang('Upgrade Amount')</span>
                            <strong>{{ $general->cur_sym }}{{ showAmount($upgrade->amount) }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">@lang('Request Date & Time')</span>
                            <strong>{{ showDateTime($upgrade->created_at) }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">@lang('Direct Upliner Name')</span>
                            @if($upliner)
                                <a href="{{ route('admin.users.detail', $upliner->id) }}" class="fw-bold">{{ $upliner->fullname ?: $upliner->username }}</a>
                            @else
                                <span class="text-muted">@lang('No direct upliner')</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">@lang('Direct Upliner Email')</span>
                            @if($upliner)
                                <a href="{{ route('admin.users.detail', $upliner->id) }}">{{ $upliner->email }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">@lang('Payment Method')</span>
                            <strong>{{ __($upgrade->gateway->name ?? 'Manual') }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">@lang('Sender Name')</span>
                            <strong>{{ $upgrade->sender_name ?: '—' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">@lang('Sender Number')</span>
                            <strong>{{ $upgrade->sender_number ?: '—' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">@lang('TRX')</span>
                            <strong>{{ $upgrade->trx ?: '—' }}</strong>
                        </div>
                    </div>

                    @if((int) $upgrade->status === \App\Models\PlanUpgrade::APPROVED)
                        <div class="alert alert-success mt-4 mb-0">
                            @if($upgrade->commission_given)
                                @lang('Package Upgrade Referral Commission was credited'):
                                <strong>{{ $general->cur_sym }}{{ showAmount($upgrade->commission_amount) }}</strong>
                            @else
                                @lang('This upgrade was approved without a package upgrade referral commission.')
                            @endif
                        </div>
                    @elseif((int) $upgrade->status === \App\Models\PlanUpgrade::REJECTED && $upgrade->admin_feedback)
                        <div class="alert alert-danger mt-4 mb-0">{{ $upgrade->admin_feedback }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card b-radius--10 h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Payment Screenshot')</h5>
                </div>
                <div class="card-body text-center">
                    @if($shot)
                        <img src="{{ $shot }}" alt="@lang('Payment Screenshot')" class="img-fluid rounded" style="max-height:460px;object-fit:contain;background:#f8f9fa;">
                    @else
                        <p class="text-muted mb-0">@lang('No payment screenshot was uploaded.')</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($isPending)
        <div class="card b-radius--10 mt-4 border border--primary">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Approve Upgrade')</h5>
            </div>
            <div class="card-body">
                <p class="mb-3">
                    @lang('Package upgrade referral commission is 20% of the upgrade amount'):
                    <strong>{{ $general->cur_sym }}{{ showAmount($commissionPreview) }}</strong>
                </p>
                <form action="{{ route('admin.plan.upgrade.approve', $upgrade->id) }}" method="POST">
                    @csrf
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" name="give_commission" value="1" id="giveCommission" @disabled(!$upliner)>
                        <label class="form-check-label fw-bold" for="giveCommission">
                            Kya is Plan Upgrade ka Referral Commission Upliner ko dena hai?
                        </label>
                    </div>
                    @if(!$upliner)
                        <p class="text--danger">@lang('This user has no direct upliner, so the commission cannot be credited.')</p>
                    @else
                        <p class="text-muted">@lang('Turn this on to credit the direct upliner. Leave it off to approve the new plan without commission.')</p>
                    @endif
                    <button type="submit" class="btn btn--success">
                        <i class="las la-check"></i> @lang('Approve')
                    </button>
                </form>
            </div>
        </div>

        <div class="card b-radius--10 mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Reject Upgrade')</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.plan.upgrade.reject') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $upgrade->id }}">
                    <div class="form-group">
                        <label>@lang('Reason')</label>
                        <textarea name="message" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn--danger">@lang('Reject')</button>
                </form>
            </div>
        </div>
    @endif
@endsection
