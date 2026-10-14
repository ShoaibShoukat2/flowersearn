@extends('admin.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        @if (request()->routeIs('admin.deposit.list') || request()->routeIs('admin.deposit.method') || request()->routeIs('admin.users.deposits') || request()->routeIs('admin.users.deposits.method'))
            <div class="col-xxl-3 col-sm-6 mb-30">
                <div class="widget-two box--shadow2 b-radius--5 bg--success has-link">
                    <a href="{{ route('admin.deposit.successful') }}" class="item-link"></a>
                    <div class="widget-two__content">
                        <h2 class="text-white">{{ __($general->cur_sym) }}{{ showAmount($successful) }}</h2>
                        <p class="text-white">@lang('Successful Deposit')</p>
                    </div>
                </div><!-- widget-two end -->
            </div>
            <div class="col-xxl-3 col-sm-6 mb-30">
                <div class="widget-two box--shadow2 b-radius--5 bg--6 has-link">
                    <a href="{{ route('admin.deposit.pending') }}" class="item-link"></a>
                    <div class="widget-two__content">
                        <h2 class="text-white">{{ __($general->cur_sym) }}{{ showAmount($pending) }}</h2>
                        <p class="text-white">@lang('Pending Deposit')</p>
                    </div>
                </div><!-- widget-two end -->
            </div>
            <div class="col-xxl-3 col-sm-6 mb-30">
                <div class="widget-two box--shadow2 has-link b-radius--5 bg--pink">
                    <a href="{{ route('admin.deposit.rejected') }}" class="item-link"></a>
                    <div class="widget-two__content">
                        <h2 class="text-white">{{ __($general->cur_sym) }}{{ showAmount($rejected) }}</h2>
                        <p class="text-white">@lang('Rejected Deposit')</p>
                    </div>
                </div><!-- widget-two end -->
            </div>
            <div class="col-xxl-3 col-sm-6 mb-30">
                <div class="widget-two box--shadow2 has-link b-radius--5 bg--dark">
                    <a href="{{ route('admin.deposit.initiated') }}" class="item-link"></a>
                    <div class="widget-two__content">
                        <h2 class="text-white">{{ __($general->cur_sym) }}{{ showAmount($initiated) }}</h2>
                        <p class="text-white">@lang('Initiated Deposit')</p>
                    </div>
                </div><!-- widget-two end -->
            </div>
        @endif

        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Gateway | Transaction')</th>
                                    <th>@lang('Initiated')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Conversion')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    @php
                                        $details = $deposit->detail ? json_encode($deposit->detail) : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-bold"> <a href="{{ appendQuery('method', @$deposit->gateway->alias) }}">{{ __(@$deposit->gateway->name) }}</a> </span>
                                            <br>
                                            <small> {{ $deposit->trx }} </small>
                                        </td>

                                        <td>
                                            {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $deposit->user->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ appendQuery('search', @$deposit->user->username) }}"><span>@</span>{{ $deposit->user->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            {{ __($general->cur_sym) }}{{ showAmount($deposit->amount) }} + <span class="text-danger" title="@lang('charge')">{{ showAmount($deposit->charge) }} </span>
                                            <br>
                                            <strong title="@lang('Amount with charge')">
                                                {{ showAmount($deposit->amount + $deposit->charge) }} {{ __($general->cur_text) }}
                                            </strong>
                                        </td>
                                        <td>
                                            1 {{ __($general->cur_text) }} = {{ showAmount($deposit->rate) }} {{ __($deposit->method_currency) }}
                                            <br>
                                            <strong>{{ showAmount($deposit->final_amo) }} {{ __($deposit->method_currency) }}</strong>
                                        </td>
                                        <td>
                                            @php echo $deposit->statusBadge @endphp
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="{{ route('admin.deposit.details', $deposit->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-desktop"></i> @lang('Details')
                                                </a>
                                                @if($deposit->status == 2)
                                                    <button type="button" class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.deposit.approve', $deposit->id) }}"
                                                        data-question="@lang('Are you sure to approve this account?')">
                                                        <i class="las la-check"></i> @lang('Approve')
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline--danger rejectBtn"
                                                        data-id="{{ $deposit->id }}"
                                                        data-amount="{{ showAmount($deposit->amount) }} {{ __($general->cur_text) }}"
                                                        data-username="{{ @$deposit->user->username }}">
                                                        <i class="las la-times"></i> @lang('Reject')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($deposits->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($deposits) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
{{-- REJECT MODAL — same reasons and the same reject route used on the details page --}}
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject Deposit Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.deposit.reject') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>@lang('Are you sure to') <span class="fw-bold">@lang('reject')</span> <span class="fw-bold withdraw-amount text-success"></span> @lang('deposit of') <span class="fw-bold withdraw-user"></span>?</p>
                        <div class="form-group">
                            <label class="fw-bold">@lang('Quick Rejection Reasons')</label>
                            <div class="d-flex flex-column gap-2 mb-3">
                                @foreach([
                                    'Kindly correct payment screenshot attach karke account dobara submit karein.',
                                    'Fake payment detected. Kindly submit a valid payment.',
                                    'Your account is already approved. Kindly do not submit the account again. Please login to your existing account.',
                                    'Kindly submit the correct details along with the payment screenshot.',
                                ] as $reason)
                                    <button type="button" class="btn btn-outline--danger text-start quick-reject-reason">{{ $reason }}</button>
                                @endforeach
                            </div>
                            <label class="fw-bold mt-2">@lang('Reason for Rejection')</label>
                            <textarea name="message" maxlength="255" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                            <small class="text-muted">@lang('Select a quick reason or type a custom reason. The user will see this reason.')</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal></x-confirmation-modal>
@endsection


@push('breadcrumb-plugins')
    @if (!request()->routeIs('admin.users.deposits') && !request()->routeIs('admin.users.deposits.method'))
        <form action="" method="GET">
            <div class="form-inline float-sm-end mb-2 ms-0 ms-xl-2 ms-lg-0">
                <div class="input-group">
                    <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Username / TRX / Email')" value="{{ request()->search ?? '' }}">
                    <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
            <div class="form-inline float-sm-end">
                <div class="input-group">
                    <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here form-control bg--white" data-position='bottom right' placeholder="@lang('Start date - End date')" autocomplete="off" value="{{ request()->date }}">
                    <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
    @endif
@endpush


@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            if ($.fn.datepicker) {
                $('.datepicker-here').datepicker();
            }

            $('.rejectBtn').on('click', function () {
                var modal = $('#rejectModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.find('.withdraw-user').text($(this).data('username'));
                modal.find('textarea[name=message]').val('');
                modal.find('.quick-reject-reason').removeClass('btn--danger').addClass('btn-outline--danger');
                modal.modal('show');
            });

            $('.quick-reject-reason').on('click', function () {
                var modal = $('#rejectModal');
                modal.find('textarea[name=message]').val($(this).text().trim());
                modal.find('.quick-reject-reason').removeClass('btn--danger').addClass('btn-outline--danger');
                $(this).removeClass('btn-outline--danger').addClass('btn--danger');
            });
        })(jQuery)
    </script>
@endpush
