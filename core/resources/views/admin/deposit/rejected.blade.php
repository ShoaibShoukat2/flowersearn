@extends('admin.layouts.app')

@section('panel')
    <style>
        .rj-plans{display:flex;flex-wrap:wrap;gap:10px;}
        .rj-plan{
            min-width:132px;
            border:1.5px solid #d9e2ec;
            background:#fff;
            border-radius:14px;
            padding:10px 12px;
            text-align:left;
            line-height:1.25;
            transition:all .2s ease;
        }
        .rj-plan:hover{border-color:#4634ff;transform:translateY(-2px);}
        .rj-plan.active{
            background:linear-gradient(135deg,#4634ff,#6d5efc);
            border-color:#4634ff;
            color:#fff;
            box-shadow:0 8px 18px rgba(70,52,255,.22);
        }
        .rj-plan .name{display:block;font-weight:700;font-size:13px;}
        .rj-plan .price{display:block;font-size:12px;opacity:.8;margin-top:2px;}
        .rj-plan .count{display:inline-block;margin-top:6px;font-size:11px;font-weight:700;padding:2px 7px;border-radius:999px;background:rgba(70,52,255,.1);}
        .rj-plan.active .count{background:rgba(255,255,255,.2);}
    </style>

    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <form action="" method="GET">
                        <input type="hidden" name="plan_id" id="rejectedPlanId" value="{{ request('plan_id') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="fw-bold">@lang('Date')</label>
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                            <div class="col-12">
                                <label class="fw-bold">@lang('Search')</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <input type="text" name="search" class="form-control" style="max-width:520px;" value="{{ $search ?? request('search') }}" placeholder="@lang('Email, username or mobile number')">
                                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i> @lang('Search')</button>
                                    <a href="{{ route('admin.deposit.rejected') }}" class="btn btn--dark">@lang('Reset')</a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="fw-bold d-block mb-2">@lang('Plan / Package')</label>
                            <div class="rj-plans">
                                <button type="submit" name="plan_id" value="" class="rj-plan {{ request()->filled('plan_id') ? '' : 'active' }}" onclick="document.getElementById('rejectedPlanId').value=''">
                                    <span class="name">@lang('All Plans')</span>
                                    <span class="price">@lang('Every package')</span>
                                </button>
                                @foreach($planSummary as $row)
                                    <button type="submit" name="plan_id" value="{{ $row->plan->id }}"
                                        class="rj-plan {{ (string) optional($selectedPlan)->id === (string) $row->plan->id ? 'active' : '' }}"
                                        onclick="document.getElementById('rejectedPlanId').value=this.value">
                                        <span class="name">{{ __($row->plan->name) }}</span>
                                        <span class="price">{{ $general->cur_sym }}{{ showAmount($row->plan->price) }}</span>
                                        <span class="count">{{ $row->count }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Package')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Rejected At')</th>
                                    <th>@lang('Rejection Reason')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $deposit->user->fullname }}</span>
                                            <br>
                                            <a href="{{ route('admin.users.detail', $deposit->user_id) }}"><span>@</span>{{ $deposit->user->username }}</a>
                                            @if($deposit->user->mobile)
                                                <br><small>{{ $deposit->user->mobile }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $deposit->user->email }}</td>
                                        <td>
                                            @php $rejectedPlan = approvedPlanForDeposit($deposit); @endphp
                                            @if($rejectedPlan)
                                                <span class="badge badge--primary">{{ __($rejectedPlan->name) }}</span>
                                            @elseif($deposit->user->plan)
                                                <span class="badge badge--primary">{{ __($deposit->user->plan->name) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ __($general->cur_sym) }}{{ showAmount($deposit->amount) }}</td>
                                        <td>
                                            {{ showDateTime($deposit->updated_at) }}
                                            <br>
                                            <small>{{ diffForHumans($deposit->updated_at) }}</small>
                                        </td>
                                        <td><span class="text--danger">{{ $deposit->admin_feedback ?: __('N/A') }}</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                data-action="{{ route('admin.deposit.reapprove.action', $deposit->id) }}"
                                                data-question="@lang('Approve this rejected user?')">
                                                <i class="las la-check-circle"></i> @lang('Approve')
                                            </button>
                                            <a href="{{ route('admin.deposit.details', $deposit->id) }}" class="btn btn-sm btn-outline--primary ms-1">
                                                <i class="la la-desktop"></i> @lang('Details')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($deposits->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($deposits) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal></x-confirmation-modal>
@endsection
