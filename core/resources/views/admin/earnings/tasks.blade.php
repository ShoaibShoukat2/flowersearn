@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <form action="" method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="fw-bold">@lang('Date')</label>
                                <input type="date" name="date" class="form-control" value="{{ $date }}">
                            </div>
                            <div class="col-md-3">
                                <label class="fw-bold">@lang('Plan / Package')</label>
                                <select name="plan_id" class="form-control">
                                    <option value="">@lang('All Packages')</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" @selected((string) request('plan_id') === (string) $plan->id)>
                                            {{ __($plan->name) }} — {{ $general->cur_sym }}{{ showAmount($plan->price) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="fw-bold">@lang('Level')</label>
                                <select name="level" class="form-control">
                                    <option value="">@lang('All Levels')</option>
                                    @for($level = 0; $level <= 8; $level++)
                                        <option value="{{ $level }}" @selected(request()->has('level') && (string) request('level') === (string) $level)>@lang('Level') {{ $level }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="fw-bold">@lang('Email')</label>
                                <input type="text" name="email" class="form-control" value="{{ request('email') }}" placeholder="@lang('Email search')">
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button class="btn btn--primary w-100" type="submit"><i class="fa fa-filter"></i> @lang('Filter')</button>
                            </div>
                            <div class="col-md-12">
                                <a href="{{ route('admin.earnings.tasks', ['date' => '']) }}" class="btn btn-sm btn--dark">@lang('All Dates')</a>
                                <a href="{{ route('admin.earnings.tasks', ['date' => now()->toDateString()]) }}" class="btn btn-sm btn--primary">@lang('Today')</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4 mb-4">
        <div class="col-md-6">
            <div class="widget-two box--shadow2 b-radius--5 bg--primary">
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($filteredAmount) }}</h3>
                    <p class="text-white">@lang('Task Earning For This Filter')</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="widget-two box--shadow2 b-radius--5 bg--success">
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $filteredTasks }}</h3>
                    <p class="text-white">@lang('Tasks Completed')</p>
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
                            <th>@lang('Plan / Package')</th>
                            <th>@lang('Level')</th>
                            <th>@lang('Tasks')</th>
                            <th>@lang('Per Task')</th>
                            <th>@lang('Total')</th>
                            <th>@lang('Date & Time')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $row)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.detail', $row->user_id) }}" class="fw-bold">
                                        {{ \App\Models\User::composeDisplayName($row->first_name, $row->last_name, $row->username) }}
                                    </a>
                                    <br>
                                    <small>{{ '@'.$row->username }}</small>
                                </td>
                                <td>{{ $row->email }}</td>
                                <td>{{ $row->plan_name ?: '—' }}</td>
                                <td>@lang('Level') {{ (int) $row->level }}</td>
                                <td>{{ $row->task_count }}</td>
                                <td>{{ $general->cur_sym }}{{ showAmount($row->per_task) }}</td>
                                <td><strong>{{ $general->cur_sym }}{{ showAmount($row->total_earning) }}</strong></td>
                                <td>{{ showDateTime($row->earned_at) }}</td>
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
