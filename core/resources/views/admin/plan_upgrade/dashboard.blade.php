@extends('admin.layouts.app')

@section('panel')
    @include('admin.plan_upgrade.nav')

    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <div class="widget-two box--shadow2 b-radius--5 bg--primary has-link">
                <a href="{{ route('admin.plan.upgrade.approved', ['date' => $today->toDateString(), 'date_on' => 'reviewed']) }}" class="item-link"></a>
                <div class="widget-two__icon b-radius--5 bg--primary"><i class="las la-user-check"></i></div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $stats['today_users'] }}</h3>
                    <p class="text-white">@lang('Today Upgrade Users')</p>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="widget-two box--shadow2 b-radius--5 bg--success has-link">
                <a href="{{ route('admin.plan.upgrade.approved', ['date' => $today->toDateString(), 'date_on' => 'reviewed']) }}" class="item-link"></a>
                <div class="widget-two__icon b-radius--5 bg--success"><i class="las la-wallet"></i></div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($stats['today_received']) }}</h3>
                    <p class="text-white">@lang('Today Upgrade Received')</p>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="widget-two box--shadow2 b-radius--5 bg--warning has-link">
                <a href="{{ route('admin.plan.upgrade.pending') }}" class="item-link"></a>
                <div class="widget-two__icon b-radius--5 bg--warning"><i class="las la-clock"></i></div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $stats['pending'] }}</h3>
                    <p class="text-white">@lang('Pending Upgrades')</p>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="widget-two box--shadow2 b-radius--5 bg--danger has-link">
                <a href="{{ route('admin.plan.upgrade.rejected') }}" class="item-link"></a>
                <div class="widget-two__icon b-radius--5 bg--danger"><i class="las la-times-circle"></i></div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $stats['rejected'] }}</h3>
                    <p class="text-white">@lang('Rejected Upgrades')</p>
                </div>
            </div>
        </div>
    </div>

    <p class="text-muted mt-4 mb-0">@lang('Upgrade receipts stay in this section. They are separate from Today Received Balance on the main dashboard.')</p>
@endsection
