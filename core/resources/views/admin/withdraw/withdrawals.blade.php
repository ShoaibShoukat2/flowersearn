@extends('admin.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        @if (request()->routeIs('admin.withdraw.log') || request()->routeIs('admin.withdraw.method') || request()->routeIs('admin.users.withdrawals') || request()->routeIs('admin.users.withdrawals.method'))
            <div class="col-xl-4 col-sm-6 mb-30">
                <div class="widget-two box--shadow2 has-link b-radius--5 bg--success">
                    <a href="{{ route('admin.withdraw.approved') }}" class="item-link"></a>
                    <div class="widget-two__content">
                        <h2 class="text-white">{{ __($general->cur_sym) }}{{ showAmount($successful) }}</h2>
                        <p class="text-white">@lang('Approved Withdrawals')</p>
                    </div>
                </div><!-- widget-two end -->
            </div>
            <div class="col-xl-4 col-sm-6 mb-30">
                <div class="widget-two box--shadow2 has-link b-radius--5 bg--6">
                    <a href="{{ route('admin.withdraw.pending') }}" class="item-link"></a>
                    <div class="widget-two__content">
                        <h2 class="text-white">{{ __($general->cur_sym) }}{{ showAmount($pending) }}</h2>
                        <p class="text-white">@lang('Pending Withdrawals')</p>
                    </div>
                </div><!-- widget-two end -->
            </div>
            <div class="col-xl-4 col-sm-6 mb-30">
                <div class="widget-two box--shadow2 b-radius--5 has-link bg--pink">
                    <a href="{{ route('admin.withdraw.rejected') }}" class="item-link"></a>
                    <div class="widget-two__content">
                        <h2 class="text-white">{{ __($general->cur_sym) }}{{ showAmount($rejected) }}</h2>
                        <p class="text-white">@lang('Rejected Withdrawals')</p>
                    </div>
                </div><!-- widget-two end -->
            </div>
        @endif
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">

                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Gateway | Transaction')</th>
                                    <th>@lang('TRX ID')</th>
                                    <th>@lang('Sender Number')</th>
                                    <th>@lang('Initiated')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Conversion')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdrawals as $withdraw)
                                    @php
                                        $details = $withdraw->withdraw_information != null ? json_encode($withdraw->withdraw_information) : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-bold"><a href="{{ appendQuery('method', @$withdraw->method->id) }}"> {{ __(@$withdraw->method->name) }}</a></span>
                                        </td>
                                        <td>
                                            <div class="trx-id-container">
                                                <span class="fw-bold text-primary trx-main">{{ $withdraw->trx }}</span>
                                                <br>
                                                <button class="btn btn-sm btn-outline--info copy-trx" 
                                                        data-trx="{{ $withdraw->trx }}" 
                                                        title="Click to copy TRX ID">
                                                    <i class="las la-copy"></i> Copy
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="sender-info">
                                                @php
                                                    $senderNumber = 'N/A';
                                                    if($withdraw->withdraw_information) {
                                                        $withdrawInfo = json_decode(json_encode($withdraw->withdraw_information), true);
                                                        // Common field names for sender number
                                                        $possibleFields = ['mobile', 'phone', 'number', 'account_number', 'sender_number', 'mobile_number', 'phone_number'];
                                                        
                                                        foreach($possibleFields as $field) {
                                                            if(isset($withdrawInfo[$field]) && !empty($withdrawInfo[$field])) {
                                                                $senderNumber = $withdrawInfo[$field];
                                                                break;
                                                            }
                                                        }
                                                        
                                                        // If not found in direct fields, check in array format
                                                        if($senderNumber == 'N/A' && is_array($withdrawInfo)) {
                                                            foreach($withdrawInfo as $key => $value) {
                                                                if(is_array($value) && isset($value['value'])) {
                                                                    $fieldName = strtolower($value['name'] ?? $key);
                                                                    if(str_contains($fieldName, 'mobile') || str_contains($fieldName, 'phone') || str_contains($fieldName, 'number')) {
                                                                        $senderNumber = $value['value'];
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                
                                                @if($senderNumber != 'N/A')
                                                    <span class="fw-bold text-success sender-number">{{ $senderNumber }}</span>
                                                    <br>
                                                    <button class="btn btn-sm btn-outline--success copy-sender" 
                                                            data-sender="{{ $senderNumber }}" 
                                                            title="Click to copy Sender Number">
                                                        <i class="las la-copy"></i> Copy
                                                    </button>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="las la-exclamation-circle"></i> Not Available
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            {{ showDateTime($withdraw->created_at) }} <br> {{ diffForHumans($withdraw->created_at) }}
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ $withdraw->user->fullname }}</span>
                                            <br>
                                            <span class="small"> <a href="{{ appendQuery('search', @$withdraw->user->username) }}"><span>@</span>{{ $withdraw->user->username }}</a> </span>
                                        </td>


                                        <td>
                                            {{ __($general->cur_sym) }}{{ showAmount($withdraw->amount) }} - <span class="text-danger" title="@lang('charge')">{{ showAmount($withdraw->charge) }} </span>
                                            <br>
                                            <strong title="@lang('Amount after charge')">
                                                {{ showAmount($withdraw->amount - $withdraw->charge) }} {{ __($general->cur_text) }}
                                            </strong>

                                        </td>

                                        <td>
                                            1 {{ __($general->cur_text) }} = {{ showAmount($withdraw->rate) }} {{ __($withdraw->currency) }}
                                            <br>
                                            <strong>{{ showAmount($withdraw->final_amount) }} {{ __($withdraw->currency) }}</strong>
                                        </td>



                                        <td>
                                            @php echo $withdraw->statusBadge @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.withdraw.details', $withdraw->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-desktop"></i> @lang('Details')
                                                </a>
                                                
                                                @if($withdraw->status == 2)
                                                    <button class="btn btn-sm btn-outline--success ms-1 approveBtn" 
                                                            data-id="{{ $withdraw->id }}" 
                                                            data-amount="{{ showAmount($withdraw->amount) }} {{ __($general->cur_text) }}"
                                                            data-user="{{ $withdraw->user->username }}">
                                                        <i class="las la-check"></i> @lang('Approve')
                                                    </button>
                                                    
                                                    <button class="btn btn-sm btn-outline--danger ms-1 rejectBtn" 
                                                            data-id="{{ $withdraw->id }}" 
                                                            data-amount="{{ showAmount($withdraw->amount) }} {{ __($general->cur_text) }}"
                                                            data-user="{{ $withdraw->user->username }}">
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
                @if ($withdrawals->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($withdrawals) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">@lang('Approve Withdrawal Request')</h5>
                <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </span>
            </div>
            <form action="{{ route('admin.withdraw.approve') }}" method="POST">
                @csrf
                <input type="hidden" name="id" class="withdraw-id">
                <div class="modal-body">
                    <p>@lang('Are you sure you want to approve this withdrawal request?')</p>
                    <div class="withdraw-details">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('User'):</span>
                                <span class="fw-bold user-name"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Amount'):</span>
                                <span class="fw-bold withdraw-amount"></span>
                            </li>
                        </ul>
                    </div>
                    <div class="form-group mt-3">
                        <label>@lang('Admin Note') (@lang('Optional'))</label>
                        <textarea name="details" class="form-control" rows="3" placeholder="@lang('Add any note for the user...')"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--success">@lang('Approve')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">@lang('Reject Withdrawal Request')</h5>
                <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </span>
            </div>
            <form action="{{ route('admin.withdraw.reject') }}" method="POST">
                @csrf
                <input type="hidden" name="id" class="withdraw-id">
                <div class="modal-body">
                    <p class="text-danger">@lang('Are you sure you want to reject this withdrawal request?')</p>
                    <div class="alert alert-info">
                        <i class="las la-info-circle"></i>
                        @lang('The withdrawal amount will be refunded to user\'s account balance.')
                    </div>
                    <div class="withdraw-details">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('User'):</span>
                                <span class="fw-bold user-name"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Amount'):</span>
                                <span class="fw-bold withdraw-amount"></span>
                            </li>
                        </ul>
                    </div>
                    <div class="form-group mt-3">
                        <label>@lang('Rejection Reason') <span class="text-danger">*</span></label>
                        <textarea name="details" class="form-control" rows="3" placeholder="@lang('Please provide reason for rejection...')" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--danger">@lang('Reject')</button>
                </div>
            </form>
        </div>
    </div>
</div>




@push('breadcrumb-plugins')
    <form action="" method="GET">
        <div class="form-inline float-sm-end mb-2 ms-0 ms-xl-2 ms-lg-0">
            <div class="input-group">
                <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Username / TRX / Email')" value="{{ request()->search }}">
                <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
        <div class="form-inline float-sm-end">
            <div class="input-group">
                <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here form-control bg--white" data-position='bottom right' placeholder="@lang('Start Date - End date')" autocomplete="off" value="{{ request()->date }}">
                <input type="hidden" name="method" value="{{ @$method->id }}">
                <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
@endpush
@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            'use strict';
            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker();
            }

            // Approve button click handler
            $('.approveBtn').on('click', function() {
                var modal = $('#approveModal');
                var id = $(this).data('id');
                var amount = $(this).data('amount');
                var user = $(this).data('user');
                
                modal.find('.withdraw-id').val(id);
                modal.find('.user-name').text(user);
                modal.find('.withdraw-amount').text(amount);
                modal.find('textarea[name="details"]').val('');
                
                modal.modal('show');
            });

            // Reject button click handler
            $('.rejectBtn').on('click', function() {
                var modal = $('#rejectModal');
                var id = $(this).data('id');
                var amount = $(this).data('amount');
                var user = $(this).data('user');
                
                modal.find('.withdraw-id').val(id);
                modal.find('.user-name').text(user);
                modal.find('.withdraw-amount').text(amount);
                modal.find('textarea[name="details"]').val('');
                
                modal.modal('show');
            });

            // Form submission with AJAX
            $('#approveModal form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var btn = form.find('button[type="submit"]');
                var originalText = btn.text();
                var withdrawId = form.find('.withdraw-id').val();
                
                btn.html('<i class="las la-spinner la-spin"></i> Processing...').prop('disabled', true);
                
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        // Update status in the table
                        updateWithdrawStatus(withdrawId, 'approved');
                        
                        // Show success message
                        showNotification('success', 'Withdrawal approved successfully!');
                        
                        // Close modal
                        $('#approveModal').modal('hide');
                    },
                    error: function(xhr) {
                        var errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        showNotification('error', errorMsg);
                    },
                    complete: function() {
                        btn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Reject form submission with AJAX
            $('#rejectModal form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var btn = form.find('button[type="submit"]');
                var originalText = btn.text();
                var withdrawId = form.find('.withdraw-id').val();
                
                btn.html('<i class="las la-spinner la-spin"></i> Processing...').prop('disabled', true);
                
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        // Update status in the table
                        updateWithdrawStatus(withdrawId, 'rejected');
                        
                        // Show success message
                        showNotification('success', 'Withdrawal rejected successfully!');
                        
                        // Close modal
                        $('#rejectModal').modal('hide');
                    },
                    error: function(xhr) {
                        var errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        showNotification('error', errorMsg);
                    },
                    complete: function() {
                        btn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Function to update withdrawal status in table
            function updateWithdrawStatus(withdrawId, status) {
                var row = $('button[data-id="' + withdrawId + '"]').closest('tr');
                var statusCell = row.find('td:nth-child(8)'); // Status column (updated position)
                var actionCell = row.find('td:nth-child(9)'); // Action column (updated position)
                
                if (status === 'approved') {
                    statusCell.html('<span><span class="badge badge--success">Approved</span><br>just now</span>');
                    // Remove approve/reject buttons, keep only details
                    actionCell.find('.approveBtn, .rejectBtn').remove();
                } else if (status === 'rejected') {
                    statusCell.html('<span><span class="badge badge--danger">Rejected</span><br>just now</span>');
                    // Remove approve/reject buttons, keep only details
                    actionCell.find('.approveBtn, .rejectBtn').remove();
                }
            }

            // Copy TRX ID functionality
            $('.copy-trx').on('click', function(e) {
                e.preventDefault();
                var trxId = $(this).data('trx');
                var btn = $(this);
                var originalText = btn.html();
                
                // Copy to clipboard
                navigator.clipboard.writeText(trxId).then(function() {
                    // Success feedback
                    btn.html('<i class="las la-check"></i> Copied!').removeClass('btn-outline--info').addClass('btn-outline--success');
                    
                    // Reset after 2 seconds
                    setTimeout(function() {
                        btn.html(originalText).removeClass('btn-outline--success').addClass('btn-outline--info');
                    }, 2000);
                }).catch(function() {
                    // Fallback for older browsers
                    var textArea = document.createElement('textarea');
                    textArea.value = trxId;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    btn.html('<i class="las la-check"></i> Copied!').removeClass('btn-outline--info').addClass('btn-outline--success');
                    setTimeout(function() {
                        btn.html(originalText).removeClass('btn-outline--success').addClass('btn-outline--info');
                    }, 2000);
                });
            });

            // Copy Sender Number functionality
            $('.copy-sender').on('click', function(e) {
                e.preventDefault();
                var senderNumber = $(this).data('sender');
                var btn = $(this);
                var originalText = btn.html();
                
                // Copy to clipboard
                navigator.clipboard.writeText(senderNumber).then(function() {
                    // Success feedback
                    btn.html('<i class="las la-check"></i> Copied!').removeClass('btn-outline--success').addClass('btn-outline--primary');
                    
                    // Reset after 2 seconds
                    setTimeout(function() {
                        btn.html(originalText).removeClass('btn-outline--primary').addClass('btn-outline--success');
                    }, 2000);
                }).catch(function() {
                    // Fallback for older browsers
                    var textArea = document.createElement('textarea');
                    textArea.value = senderNumber;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    btn.html('<i class="las la-check"></i> Copied!').removeClass('btn-outline--success').addClass('btn-outline--primary');
                    setTimeout(function() {
                        btn.html(originalText).removeClass('btn-outline--primary').addClass('btn-outline--success');
                    }, 2000);
                });
            });
            function showNotification(type, message) {
                var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-triangle';
                
                var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show notification-alert" role="alert">' +
                    '<i class="' + icon + '"></i> ' + message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>');
                
                // Add to top of page
                $('.card').first().before(notification);
                
                // Auto remove after 5 seconds
                setTimeout(function() {
                    notification.fadeOut(function() {
                        $(this).remove();
                    });
                }, 5000);
            }

        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .button--group {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .button--group .btn {
            margin: 0 !important;
        }
        
        .approveBtn:hover {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: white !important;
        }
        
        .rejectBtn:hover {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
        }
        
        .withdraw-details .list-group-item {
            border: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
        }
        
        .modal-header .close {
            cursor: pointer;
            font-size: 1.5rem;
            opacity: 0.5;
        }
        
        .modal-header .close:hover {
            opacity: 0.75;
        }
        
        .notification-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .notification-alert i {
            margin-right: 8px;
        }
        
        .badge--success {
            background-color: #28a745 !important;
            color: white !important;
        }
        
        .badge--danger {
            background-color: #dc3545 !important;
            color: white !important;
        }
        
        .badge--warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        
        .trx-id-container {
            text-align: center;
        }
        
        .trx-main {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 5px;
        }
        
        .copy-trx {
            font-size: 0.75rem;
            padding: 2px 8px;
            transition: all 0.3s ease;
        }
        
        .copy-trx:hover {
            transform: translateY(-1px);
        }
        
        .table td:nth-child(2) {
            min-width: 120px;
        }
        
        .sender-info {
            text-align: center;
        }
        
        .sender-number {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 5px;
        }
        
        .copy-sender {
            font-size: 0.75rem;
            padding: 2px 8px;
            transition: all 0.3s ease;
        }
        
        .copy-sender:hover {
            transform: translateY(-1px);
        }
        
        .table td:nth-child(3) {
            min-width: 130px;
        }
    </style>
@endpush
