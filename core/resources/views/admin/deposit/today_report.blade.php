@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4 mb-4">
        <div class="col-xxl-4 col-sm-6">
            <div class="widget-two box--shadow2 b-radius--5 bg--success">
                <div class="widget-two__icon b-radius--5 bg--success border border-white">
                    <i class="las la-money-bill-wave"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($summary['received_amount']) }}</h3>
                    <p class="text-white">@lang('Received Amount')</p>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-sm-6">
            <div class="widget-two box--shadow2 b-radius--5 bg--primary">
                <div class="widget-two__icon b-radius--5 bg--primary border border-white">
                    <i class="las la-user-check"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $summary['approved_count'] }}</h3>
                    <p class="text-white">@lang('Approved Users')</p>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-sm-6">
            <div class="widget-two box--shadow2 b-radius--5 bg--info">
                <div class="widget-two__icon b-radius--5 bg--info border border-white">
                    <i class="las la-receipt"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($summary['total_charge']) }}</h3>
                    <p class="text-white">@lang('Total Charges')</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-3 mb-4">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <form action="" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="fw-bold">@lang('Plan / Package')</label>
                            <select name="plan_id" class="form-control">
                                <option value="">@lang('All Packages')</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" @selected(optional($selectedPlan)->id == $plan->id)>
                                        {{ __($plan->name) }} — {{ $general->cur_sym }}{{ showAmount($plan->price) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">@lang('Date')</label>
                            <input name="date" type="text" data-language="en" class="datepicker-here form-control" data-position="bottom right" placeholder="@lang('Select date')" autocomplete="off" value="{{ request()->date ?? $reportDate->format('m/d/Y') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">@lang('Email')</label>
                            <input type="text" name="email" class="form-control" value="{{ request('email') }}" placeholder="@lang('Email search')">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn--primary w-100" type="submit"><i class="fa fa-filter"></i> @lang('Filter')</button>
                        </div>
                    </form>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @foreach($planSummary as $row)
                            <a href="{{ route('admin.deposit.today', ['plan_id' => $row->plan->id, 'date' => request('date', $reportDate->format('m/d/Y')), 'email' => request('email')]) }}"
                               class="btn btn-sm {{ optional($selectedPlan)->id == $row->plan->id ? 'btn--primary' : 'btn-outline--primary' }}">
                                {{ __($row->plan->name) }}: {{ $row->count }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="mb-0">
                        @lang('Approved Users') —
                        {{ showDateTime($reportDate, 'd M, Y') }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Mobile')</th>
                                    <th>@lang('Package')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Payment Method')</th>
                                    <th>@lang('Approved At')</th>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $deposit->user->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $deposit->user_id) }}">{{ $deposit->user->email }}</a>
                                            </span>
                                        </td>
                                        <td>{{ $deposit->user->mobile ?? '—' }}</td>
                                        <td>
                                            @php $approvedPlan = approvedPlanForDeposit($deposit); @endphp
                                            @if($approvedPlan)
                                                <span class="badge badge--primary">{{ __($approvedPlan->name) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $general->cur_sym }}{{ showAmount($deposit->amount) }}</strong>
                                            @if($deposit->charge > 0)
                                                <br><small class="text-muted">+ {{ showAmount($deposit->charge) }} @lang('charge')</small>
                                            @endif
                                        </td>
                                        <td>{{ __(@$deposit->gateway->name ?? 'N/A') }}</td>
                                        <td>
                                            {{ showDateTime($deposit->updated_at) }}
                                            <br>
                                            <small>{{ diffForHumans($deposit->updated_at) }}</small>
                                        </td>
                                        <td><small>{{ $deposit->trx }}</small></td>
                                        <td>
                                            <a href="{{ route('admin.deposit.details', $deposit->id) }}" class="btn btn-sm btn-outline--primary">
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
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.datepicker-here').datepicker();
        })(jQuery);
    </script>
@endpush
