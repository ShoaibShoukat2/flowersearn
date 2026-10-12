@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card mb-4">
            <form action="{{ route('admin.deposit.update', $deposit->id) }}" method="POST">
                @csrf
                @method('PUT')
                {{-- Basic Deposit Info --}}
                <div class="card-body">
                    <div class="row mt-4">

                        <div class="col-sm-12 col-md-6 col-lg-4 mb-15">
                            <div class="form-group">
                                <label>@lang('Username')</label>
                                <input type="text" class="form-control" value="{{ @$deposit->user->username }}" readonly />
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-15">
                            <div class="form-group">
                                <label>@lang('Fullname')</label>
                                <input type="text" class="form-control" value="{{ @$deposit->user->fullname }}" readonly />
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-15">
                            <div class="form-group">
                                <label>@lang('Mobile')</label>
                                <input type="text" class="form-control" value="{{ @$deposit->user->mobile }}" readonly />
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-sm-12 col-md-6 col-lg-4 mb-15">
                            <div class="form-group">
                                <label>@lang('Gateway Name')</label>
                                <input type="text" class="form-control" value="{{ __(@$deposit->gateway->name) }}" readonly />
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-4 mb-15">
                            <div class="form-group">
                                <label>@lang('Transaction Number')</label>
                                <input type="text" class="form-control" value="{{ $deposit->trx }}" readonly />
                            </div>
                        </div>

                        
                        <div class="col-xl-4 col-md-6 mb-15">
                            <div class="form-group">
                                <label>@lang('Status')</label>
                                <input type="text" class="form-control" value="@if($deposit->status == 1) Completed @elseif($deposit->status == 2) Pending @else Rejected @endif" readonly />
                            </div>
                        </div>
                    </div>

                    {{-- Financial Info --}}
                    <div class="row mt-4">
                        <div class="col-xl-4 col-md-6 mb-15">
                            <div class="form-group">
                                <label>@lang('Amount') ({{ $general->cur_text }})</label>
                                <input type="text" value="{{ showAmount($deposit->amount) }}" class="form-control" readonly />
                            </div>
                        </div>

                    </div>

                    {{-- Editable Plan Selection --}}
                    <div class="row mt-4">
                        <div class="col-xl-4 col-md-6 mb-15">
                            <div class="form-group">
                                <label>@lang('Select Plan')</label>
                                <select name="plan_id" class="form-control" required>
                                    <option value="">@lang('Select Plan')</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ $deposit->plan_id == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                </div><!-- card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update Plan')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<x-form-generator></x-form-generator>
@endsection


@push('breadcrumb-plugins')
    <a href="{{ route('admin.deposit.details', $deposit->id) }}" class="btn btn-sm btn-outline--primary">
        <i class="la la-undo"></i> @lang('Back')
    </a>
@endpush

@push('style')
<style>
    .btn-sm {
        line-height: 5px;
    }
</style>
@endpush
