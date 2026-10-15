@extends('admin.layouts.app')
@section('panel')
    <div class="row g-3 mb-4">
        @foreach($levelSummary as $summaryLevel => $summaryCount)
            @php
                $summaryQuery = request()->except(['page', 'level']);
                $isActiveLevel = request()->filled('level') && (string) request('level') === (string) $summaryLevel;
                if (!$isActiveLevel) {
                    $summaryQuery['level'] = $summaryLevel;
                }
            @endphp
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('admin.leaderboard.index', $summaryQuery) }}" class="card b-radius--10 h-100 {{ $isActiveLevel ? 'border border--primary' : '' }}" style="text-decoration:none;">
                    <div class="card-body text-center py-3">
                        <div class="fw-bold text--primary">@lang('Level') {{ $summaryLevel }}</div>
                        <div class="fs-5 fw-bold">{{ $summaryCount }} @lang('Users')</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card responsive-filter-card mb-4 filter-open">
                <div class="card-body">
                    <form action="{{ route('admin.leaderboard.index') }}" method="GET">
                        <div class="d-flex flex-wrap gap-3">
                            <div class="flex-grow-1">
                                <label>@lang('Username')</label>
                                <input type="text" name="username" value="{{ $username }}" class="form-control" placeholder="@lang('Search display name')">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Email')</label>
                                <input type="text" name="email" value="{{ $email }}" class="form-control" placeholder="@lang('Search email')">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Plan / Package')</label>
                                <select name="plan_id" class="form-control">
                                    <option value="">@lang('All Packages')</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" @selected((string) request('plan_id') === (string) $plan->id)>{{ __($plan->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Level')</label>
                                <select name="level" class="form-control">
                                    <option value="">@lang('All Levels')</option>
                                    @foreach($levelOptions as $level)
                                        <option value="{{ $level }}" @selected(request()->filled('level') && (string) request('level') === (string) $level)>@lang('Level') {{ $level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Sort By')</label>
                                <select name="sort" class="form-control">
                                    <option value="" @selected($sort === '')>@lang('Default ranking')</option>
                                    <option value="level" @selected($sort === 'level')>@lang('Level – Highest to Lowest')</option>
                                    <option value="earnings" @selected($sort === 'earnings')>@lang('Total Earnings – Highest to Lowest')</option>
                                    <option value="team" @selected($sort === 'team')>@lang('Total Team – Highest to Lowest')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date Applies To')</label>
                                <select name="date_basis" class="form-control">
                                    <option value="approval" @selected(request('date_basis', 'approval') === 'approval')>@lang('Account Approval Date')</option>
                                    <option value="registration" @selected(request('date_basis') === 'registration')>@lang('Registration Date')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('From')</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('To')</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                            </div>
                            <div class="flex-grow-1 align-self-end d-flex gap-2">
                                <button class="btn btn--primary w-100 h-45" type="submit"><i class="fas fa-filter"></i> @lang('Filter')</button>
                                <a href="{{ route('admin.leaderboard.index') }}" class="btn btn-outline--dark h-45">@lang('Reset')</a>
                            </div>
                        </div>
                    </form>
                    <small class="text-muted d-block mt-3">
                        @lang('Default ranking keeps a higher level above a lower level, even when the lower level has more earnings. Members on the same level are ordered by approved team, then total earnings. Click a level card to show only that level. Filters work together.')
                    </small>
                </div>
            </div>

            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('#')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Total Team')</th>
                                    <th>@lang('Level')</th>
                                    <th>@lang('Total Earnings')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $loop->index }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.detail', $user->id) }}" class="fw-bold">{{ memberDisplayName($user) }}</a>
                                            <br>
                                            <span class="small text-muted">{{ __(@$user->plan->name ?? 'No package') }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.detail', $user->id) }}">{{ $user->email }}</a>
                                        </td>
                                        <td>{{ (int) $user->board_team }}</td>
                                        <td><span class="badge badge--primary">@lang('Level') {{ (int) $user->board_level }}</span></td>
                                        <td>{{ $general->cur_sym }}{{ showAmount($user->board_earnings) }}</td>
                                        <td>
                                            <a href="{{ route('admin.leaderboard.show', $user->id) }}" class="btn btn-sm btn-outline--primary">
                                                <i class="las la-desktop"></i> @lang('View Details')
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
                @if($users->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($users) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
