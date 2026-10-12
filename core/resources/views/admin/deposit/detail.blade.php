@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30 justify-content-center">
        <div class="col-xl-4 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Deposit Via') {{ __(@$deposit->gateway->name) }}</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Date')
                            <span class="fw-bold">{{ showDateTime($deposit->created_at) }}</span>
                        </li>
                        @php
                            $upliner = $deposit->user->refBy ?? null;
                        @endphp
                        <li class="list-group-item">
                            <strong>@lang('Direct Upliner')</strong>
                            @if($upliner)
                                <div class="mt-2">
                                    <div>
                                        <span class="text-muted">@lang('Username'):</span>
                                        <a href="{{ route('admin.users.detail', $upliner->id) }}" class="fw-bold">{{ $upliner->username }}</a>
                                    </div>
                                    <div>
                                        <span class="text-muted">@lang('Email'):</span>
                                        <a href="{{ route('admin.users.detail', $upliner->id) }}">{{ $upliner->email }}</a>
                                    </div>
                                </div>
                            @else
                                <span class="d-block text-muted mt-1">@lang('This account has no direct upliner.')</span>
                            @endif
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Transaction Number')
                            <span class="fw-bold">{{ $deposit->trx }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Username')
                            <span class="fw-bold">
                                <a href="{{ route('admin.users.detail', $deposit->user_id) }}">{{ @$deposit->user->username }}</a>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Name')
                            <span class="fw-bold">
                                <a href="{{ route('admin.users.detail', $deposit->user_id) }}">{{ @$deposit->user->fullname ?? '' }} </a>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Email')
                            <span class="fw-bold">
                                {{ @$deposit->user->email }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Mobile')
                            <span class="fw-bold">
                                +{{ @$deposit->user->mobile }}
                            </span>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Method')
                            <span class="fw-bold">{{ __(@$deposit->gateway->name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Amount')
                            <span class="fw-bold">{{ showAmount($deposit->amount ) }} {{ __($general->cur_text) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Charge')
                            <span class="fw-bold">{{ showAmount($deposit->charge ) }} {{ __($general->cur_text) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('After Charge')
                            <span class="fw-bold">{{ showAmount($deposit->amount+$deposit->charge ) }} {{ __($general->cur_text) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Rate')
                            <span class="fw-bold">1 {{__($general->cur_text)}}
                                = {{ showAmount($deposit->rate) }} {{__($deposit->baseCurrency())}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Payable')
                            <span class="fw-bold">{{ showAmount($deposit->final_amo ) }} {{__($deposit->method_currency)}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @php echo $deposit->statusBadge @endphp
                        </li>
                        @if($deposit->admin_feedback)
                            <li class="list-group-item">
                                <strong>@lang('Admin Response')</strong>
                                <br>
                                <p>{{__($deposit->admin_feedback)}}</p>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        @if($details || $deposit->status == 2 || $deposit->status == 3)
        <div class="col-xl-8 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="card-title mb-50 border-bottom pb-2">@lang('User Deposit Information')</h5>
                    @if($details != null)
                        @foreach(json_decode($details) as $val)
                            @if($deposit->method_code >= 1000)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6>{{__($val->name)}}</h6>
                                    @if($val->type == 'checkbox')
                                        {{ implode(',',$val->value) }}
                                    @elseif($val->type == 'file')
                                        @if($val->value)
                                            @php
                                                $filePath = getFilePath('verify').'/'.$val->value;
                                                $fileExtension = strtolower(pathinfo($val->value, PATHINFO_EXTENSION));
                                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                            @endphp
                                            
                                            @if(in_array($fileExtension, $imageExtensions))
                                                <!-- Display image inline -->
                                                <div class="mb-3">
                                                    <img src="{{ asset($filePath) }}" alt="Transaction Screenshot" class="img-fluid rounded shadow" style="max-width: 100%; max-height: 500px; border: 2px solid #ddd;">
                                                </div>
                                                <a href="{{ route('admin.download.attachment',encrypt($filePath)) }}" class="btn btn-sm btn-primary" download>
                                                    <i class="fa fa-download"></i> @lang('Download Image')
                                                </a>
                                            @else
                                                <!-- For non-image files, show download link -->
                                                <a href="{{ route('admin.download.attachment',encrypt($filePath)) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-file"></i> @lang('Download Attachment')
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-muted">@lang('No File')</span>
                                        @endif
                                    @else
                                    <p>{{__($val->value)}}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                        @if($deposit->method_code < 1000)
                            @include('admin.deposit.gateway_data',['details'=>json_decode($details)])
                        @endif
                    @endif
                    @if($deposit->status == 2)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button class="btn btn--success ms-1 confirmationBtn"
                                data-action="{{ route('admin.deposit.approve', $deposit->id) }}"
                                data-question="@lang('Are you sure to approve this transaction?')"
                                ><i class="fas fa-check"></i>
                                    @lang('Approve')
                                </button>

                                <button class="btn btn--danger ms-1 rejectBtn"
                                        data-id="{{ $deposit->id }}"
                                        data-info="{{$details}}"
                                        data-amount="{{ showAmount($deposit->amount)}} {{ __($general->cur_text) }}"
                                        data-username="{{ @$deposit->user->username }}"><i class="fas fa-ban"></i> @lang('Reject')
                                </button>
                                
                                <a class="btn btn--success ms-1"
                                href="{{ route('admin.deposit.edit', $deposit->id) }}"
                                ><i class="fas fa-eye"></i>
                                    @lang('Edit')
                                </a>
                            </div>
                        </div>
                    @endif
                    @if($deposit->status == 3)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button class="btn btn--success ms-1 confirmationBtn"
                                    data-action="{{ route('admin.deposit.reapprove.action', $deposit->id) }}"
                                    data-question="@lang('Are you sure to re-approve this rejected user?')">
                                    <i class="las la-check-circle"></i> @lang('Re-Approve')
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- REJECT MODAL --}}
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject Deposit Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.deposit.reject')}}" method="POST">
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

@push('script')
    <script>
        (function ($) {
            "use strict";

            $('.rejectBtn').on('click', function () {
                var modal = $('#rejectModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.find('.withdraw-user').text($(this).data('username'));
                modal.find('.quick-reject-reason').removeClass('btn--danger').addClass('btn-outline--danger');
                modal.modal('show');
            });

            $('.quick-reject-reason').on('click', function () {
                var modal = $('#rejectModal');
                modal.find('textarea[name=message]').val($(this).text().trim());
                modal.find('.quick-reject-reason').removeClass('btn--danger').addClass('btn-outline--danger');
                $(this).removeClass('btn-outline--danger').addClass('btn--danger');
            });
        })(jQuery);
    </script>
@endpush
