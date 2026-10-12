@extends('admin.layouts.app')

@section('panel')
    @include('admin.plan_upgrade.nav')

    <div class="card b-radius--10 mb-4">
        <div class="card-body">
            <form action="" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="fw-bold">@lang('Email / User')</label>
                        <input type="text" name="email" value="{{ request('email') }}" class="form-control" placeholder="@lang('Email, name or username')">
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold">@lang('Date')</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold">@lang('Date Applies To')</label>
                        <select name="date_on" class="form-control">
                            <option value="created" @selected(request('date_on', 'created') === 'created')>@lang('Request Date')</option>
                            <option value="reviewed" @selected(request('date_on') === 'reviewed')>@lang('Approval / Reject Date')</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn--primary" type="submit"><i class="fa fa-filter"></i> @lang('Filter')</button>
                        <a href="{{ url()->current() }}" class="btn btn--dark">@lang('Reset')</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--sm table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('User')</th>
                            <th>@lang('Previous Plan')</th>
                            <th>@lang('New Plan')</th>
                            <th>@lang('Upgrade Amount')</th>
                            <th>@lang('Sender')</th>
                            <th>@lang('Payment Screenshot')</th>
                            <th>@lang('Request Date')</th>
                            <th>@lang('Approval / Reject Date')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upgrades as $upgrade)
                            @php
                                $shot = $upgrade->screenshot ? asset(getFilePath('verify') . '/' . $upgrade->screenshot) : null;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.detail', $upgrade->user_id) }}" class="fw-bold">{{ $upgrade->user->fullname ?? $upgrade->user->username }}</a>
                                    <br>
                                    <small>{{ $upgrade->user->email ?? '—' }}</small>
                                </td>
                                <td>{{ $upgrade->fromPlan->name ?? '—' }}</td>
                                <td>{{ $upgrade->toPlan->name ?? '—' }}</td>
                                <td>
                                    <strong>{{ $general->cur_sym }}{{ showAmount($upgrade->amount) }}</strong>
                                    @if($upgrade->gateway)
                                        <br><small>{{ __($upgrade->gateway->name) }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $upgrade->sender_name }}
                                    <br>
                                    <small>{{ $upgrade->sender_number }}</small>
                                </td>
                                <td>
                                    @if($shot)
                                        <a href="{{ route('admin.plan.upgrade.details', $upgrade->id) }}">
                                            <img src="{{ $shot }}" alt="@lang('Payment Screenshot')" style="width:72px;height:72px;object-fit:cover;border-radius:8px;">
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ showDateTime($upgrade->created_at) }}</td>
                                <td>{{ $upgrade->reviewed_at ? showDateTime($upgrade->reviewed_at) : '—' }}</td>
                                <td>
                                    @if((int) $upgrade->status === \App\Models\PlanUpgrade::APPROVED)
                                        <span class="badge badge--success">@lang('Approved')</span>
                                    @elseif((int) $upgrade->status === \App\Models\PlanUpgrade::REJECTED)
                                        <span class="badge badge--danger">@lang('Rejected')</span>
                                        @if($upgrade->admin_feedback)
                                            <br><small class="text--danger">{{ $upgrade->admin_feedback }}</small>
                                        @endif
                                    @else
                                        <span class="badge badge--warning">@lang('Pending')</span>
                                    @endif
                                    @if((int) $upgrade->status === \App\Models\PlanUpgrade::APPROVED && $upgrade->commission_given)
                                        <br><small class="text--success">@lang('Package Upgrade Referral Commission') {{ $general->cur_sym }}{{ showAmount($upgrade->commission_amount) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.plan.upgrade.details', $upgrade->id) }}" class="btn btn-sm btn-outline--primary">
                                        <i class="la la-desktop"></i> @lang('View Details')
                                    </a>
                                    @if(request()->routeIs('admin.plan.upgrade.pending'))
                                        <button class="btn btn-sm btn-outline--danger ms-1 rejectUpgrade"
                                            data-id="{{ $upgrade->id }}"
                                            data-user="{{ $upgrade->user->username ?? '' }}">
                                            <i class="las la-times"></i> @lang('Reject')
                                        </button>
                                    @endif
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
        @if($upgrades->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($upgrades) }}
            </div>
        @endif
    </div>

    <div id="rejectUpgradeModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject Plan Upgrade')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i class="las la-times"></i></button>
                </div>
                <form action="{{ route('admin.plan.upgrade.reject') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p class="upgrade-user fw-bold"></p>
                        <div class="form-group">
                            <label>@lang('Reason')</label>
                            <textarea name="message" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Reject')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal></x-confirmation-modal>
@endsection

@push('script')
<script>
    (function ($) {
        'use strict';
        $('.rejectUpgrade').on('click', function () {
            var modal = $('#rejectUpgradeModal');
            modal.find('input[name=id]').val($(this).data('id'));
            modal.find('.upgrade-user').text($(this).data('user'));
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush
