@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <form action="" method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="fw-bold">@lang('Date')</label>
                                <input type="date" name="date" class="form-control" value="{{ $date }}">
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">@lang('Email')</label>
                                <input type="text" name="email" class="form-control" value="{{ request('email') }}" placeholder="@lang('Email search')">
                            </div>
                            <div class="col-md-4 d-flex gap-2">
                                <button class="btn btn--primary" type="submit"><i class="fa fa-filter"></i> @lang('Filter')</button>
                                <a href="{{ route('admin.earnings.manual', ['date' => '']) }}" class="btn btn--dark">@lang('All Dates')</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="widget-two box--shadow2 b-radius--5 bg--success">
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($filteredAmount) }}</h3>
                    <p class="text-white">@lang('Manual Balance For This Filter')</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--sm table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('User')</th>
                            <th>@lang('Email')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Date & Time')</th>
                            <th>@lang('Admin / Details')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $row)
                            <tr>
                                <td>
                                    @if($row->user)
                                        <a href="{{ route('admin.users.detail', $row->user_id) }}" class="fw-bold">{{ $row->user->fullname }}</a>
                                    @else
                                        <span class="text-muted">@lang('User removed')</span>
                                    @endif
                                </td>
                                <td>{{ $row->user->email ?? '—' }}</td>
                                <td><strong>{{ $general->cur_sym }}{{ showAmount($row->amount) }}</strong></td>
                                <td>{{ showDateTime($row->created_at) }}</td>
                                <td>{{ $row->details ?: __('Admin') }}</td>
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
        @if ($records->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($records) }}
            </div>
        @endif
    </div>
@endsection
