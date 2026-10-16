@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="row gy-4">

                <div class="col-xxl-3 col-sm-6">
                    <div class="widget-two style--two box--shadow2 b-radius--5 bg--19">
                        <div class="widget-two__icon b-radius--5 bg--primary">
                            <i class="las la-money-bill-wave-alt"></i>
                        </div>
                        <div class="widget-two__content">
                            <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($user->balance) }}</h3>
                            <p class="text-white">@lang('Balance')</p>
                        </div>
                        <a href="{{ route('admin.report.transaction') }}?search={{ $user->username }}" class="widget-two__btn">@lang('View All')</a>
                    </div>
                </div>
                <!-- dashboard-w1 end -->

                <div class="col-xxl-3 col-sm-6">
                    <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary">
                        <div class="widget-two__icon b-radius--5 bg--primary">
                            <i class="las la-wallet"></i>
                        </div>
                        <div class="widget-two__content">
                            <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($totalDeposit) }}</h3>
                            <p class="text-white">@lang('Deposits')</p>
                        </div>
                        <a href="{{ route('admin.deposit.list') }}?search={{ $user->username }}" class="widget-two__btn">@lang('View All')</a>
                    </div>
                </div>
                <!-- dashboard-w1 end -->

                <div class="col-xxl-3 col-sm-6">
                    <div class="widget-two style--two box--shadow2 b-radius--5 bg--1">
                        <div class="widget-two__icon b-radius--5 bg--primary">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="widget-two__content">
                            <h3 class="text-white">{{ $general->cur_sym }}{{ showAmount($totalWithdrawals) }}</h3>
                            <p class="text-white">@lang('Withdrawals')</p>
                        </div>
                        <a href="{{ route('admin.withdraw.log') }}?search={{ $user->username }}" class="widget-two__btn">@lang('View All')</a>
                    </div>
                </div>
                <!-- dashboard-w1 end -->

                <div class="col-xxl-3 col-sm-6">
                    <div class="widget-two style--two box--shadow2 b-radius--5 bg--17">
                        <div class="widget-two__icon b-radius--5 bg--primary">
                            <i class="las la-exchange-alt"></i>
                        </div>
                        <div class="widget-two__content">
                            <h3 class="text-white">{{ $totalTransaction }}</h3>
                            <p class="text-white">@lang('Transactions')</p>
                        </div>
                        <a href="{{ route('admin.report.transaction') }}?search={{ $user->username }}" class="widget-two__btn">@lang('View All')</a>
                    </div>
                </div>
                <!-- dashboard-w1 end -->

            </div>

            <div class="d-flex flex-wrap gap-3 mt-4">
                <div class="flex-fill">
                    <button data-bs-toggle="modal" data-bs-target="#addSubModal" class="btn btn--success btn--shadow w-100 btn-lg bal-btn" data-act="add">
                        <i class="las la-plus-circle"></i> @lang('Balance')
                    </button>
                </div>

                <div class="flex-fill">
                    <button data-bs-toggle="modal" data-bs-target="#addSubModal" class="btn btn--danger btn--shadow w-100 btn-lg bal-btn" data-act="sub">
                        <i class="las la-minus-circle"></i> @lang('Balance')
                    </button>
                </div>

                <div class="flex-fill">
                    <a href="{{route('admin.report.login.history')}}?search={{ $user->username }}" class="btn btn--primary btn--shadow w-100 btn-lg">
                        <i class="las la-list-alt"></i>@lang('Logins')
                    </a>
                </div>

                <div class="flex-fill">
                    <a href="{{ route('admin.users.notification.log',$user->id) }}" class="btn btn--secondary btn--shadow w-100 btn-lg">
                        <i class="las la-bell"></i>@lang('Notifications')
                    </a>
                </div>

                <div class="flex-fill">
                    <a href="{{route('admin.users.login',$user->id)}}" target="_blank" class="btn btn--primary btn--gradi btn--shadow w-100 btn-lg">
                        <i class="las la-sign-in-alt"></i>@lang('Login as User')
                    </a>
                </div>

                @if($user->kyc_data)
                <div class="flex-fill">
                    <a href="{{ route('admin.users.kyc.details', $user->id) }}" target="_blank" class="btn btn--dark btn--shadow w-100 btn-lg">
                        <i class="las la-user-check"></i>@lang('KYC Data')
                    </a>
                </div>
                @endif

                <div class="flex-fill">
                    @if($user->status == 1)
                    <button type="button" class="btn btn--warning btn--gradi btn--shadow w-100 btn-lg userStatus" data-bs-toggle="modal" data-bs-target="#userStatusModal">
                        <i class="las la-ban"></i>@lang('Ban User')
                    </button>
                    @else
                    <form action="{{ route('admin.users.reapprove', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn--success btn--shadow w-100 btn-lg">
                            <i class="las la-check-circle"></i>@lang('Re-Approve')
                        </button>
                    </form>
                    @endif
                </div>
                
                
                <div class="flex-fill">
                    <button data-bs-toggle="modal" data-bs-target="#snapstubesAddSubModal" class="btn btn--success btn--shadow w-100 btn-lg bal-btn" data-act="add">
                        <i class="las la-plus-circle"></i> @lang('Flowers Earn')
                    </button>
                </div>

                <div class="flex-fill">
                    <button data-bs-toggle="modal" data-bs-target="#snapstubesAddSubModal" class="btn btn--danger btn--shadow w-100 btn-lg bal-btn" data-act="sub">
                        <i class="las la-minus-circle"></i> @lang('Flowers Earn')
                    </button>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('IP Lock')</h5>
                    @if($ipLocks->count())
                        <form action="{{ route('admin.users.ip.release', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn--warning btn-sm">
                                <i class="las la-unlock"></i> @lang('Release IP')
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">@lang('Only this account can register or sign in from a locked IP. The first account that used the IP keeps it.')</p>
                    @if($ipLocks->count())
                        <ul class="mb-0">
                            @foreach($ipLocks as $lock)
                                <li><code>{{ $lock->ip }}</code></li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-0">@lang('No IP is locked to this account yet.')</p>
                    @endif
                </div>
            </div>


            <div class="row gy-4 mt-1">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">@lang('Direct Upliner')</h5>
                        </div>
                        <div class="card-body">
                            @if($upliner)
                                <p class="mb-2">
                                    <span class="text-muted d-block">@lang('Username')</span>
                                    <a href="{{ route('admin.users.detail', $upliner->id) }}" class="fw-bold">{{ $upliner->username }}</a>
                                </p>
                                <p class="mb-0">
                                    <span class="text-muted d-block">@lang('Email')</span>
                                    <a href="{{ route('admin.users.detail', $upliner->id) }}">{{ $upliner->email }}</a>
                                </p>
                            @else
                                <p class="text-muted mb-0">@lang('This account has no direct upliner.')</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">@lang('Payment Screenshot')</h5>
                        </div>
                        <div class="card-body">
                            @if($joiningDeposit)
                                <p class="mb-2">
                                    <strong>{{ $general->cur_sym }}{{ showAmount($joiningDeposit->amount) }}</strong>
                                    <span class="text-muted">— {{ showDateTime($joiningDeposit->updated_at) }}</span>
                                </p>
                                @foreach(depositDetailItems($joiningDeposit) as $info)
                                    @if(($info->type ?? '') !== 'file')
                                        <p class="mb-1"><span class="text-muted">{{ __($info->name ?? '') }}:</span> {{ is_array($info->value ?? null) ? implode(', ', $info->value) : ($info->value ?? '') }}</p>
                                    @endif
                                @endforeach
                                @forelse(depositProofFiles($joiningDeposit) as $proof)
                                    <a href="{{ $proof['url'] }}" target="_blank">
                                        <img src="{{ $proof['url'] }}" alt="{{ $proof['name'] }}" class="img-fluid rounded mt-2" style="max-height:280px;">
                                    </a>
                                @empty
                                    <p class="text-muted mb-0">@lang('No payment screenshot was saved with this approval.')</p>
                                @endforelse
                            @else
                                <p class="text-muted mb-0">@lang('No payment record is saved for this user.')</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-30">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Information of') {{$user->fullname}} </h5>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.users.update',[$user->id])}}" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>@lang('First Name')</label>
                                    <input class="form-control" type="text" name="first_name" required value="{{$user->first_name}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">@lang('Last Name')</label>
                                    <input class="form-control" type="text" name="last_name" required value="{{$user->last_name}}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email') </label>
                                    <input class="form-control" type="email" name="email" value="{{$user->email}}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Mobile Number') </label>
                                    <div class="input-group ">
                                        <span class="input-group-text mobile-code"></span>
                                        <input type="number" name="mobile" value="{{ old('mobile') }}" id="mobile" class="form-control checkUser" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Level') </label>
                                    <input class="form-control" type="number" name="level" value="{{$user->level}}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Total Team') </label>
                                    <div class="input-group ">
                                        
                                        <input type="number" value="{{ $userCount }}" id="mobile" class="form-control checkUser" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Referrence Code') </label>
                                    <div class="input-group ">
                                        
                                        <input type="text" name="ref" value="{{ $user->ref }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Select Plan')</label>
                                    <select name="plan_id" class="form-control" required>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ $user->plan_id == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label>@lang('Address')</label>
                                    <input class="form-control" type="text" name="address" value="{{@$user->address->address}}">
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input class="form-control" type="text" name="city" value="{{@$user->address->city}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('State')</label>
                                    <input class="form-control" type="text" name="state" value="{{@$user->address->state}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Zip/Postal')</label>
                                    <input class="form-control" type="text" name="zip" value="{{@$user->address->zip}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Country')</label>
                                    <select name="country" class="form-control">
                                        @foreach($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $key }}">{{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="form-group  col-xl-3 col-md-6 col-12">
                                <label>@lang('Email Verification')</label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                       data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="ev"
                                       @if($user->ev) checked @endif>

                            </div>

                            <div class="form-group  col-xl-3 col-md-6 col-12">
                                <label>@lang('Mobile Verification')</label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                       data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="sv"
                                       @if($user->sv) checked @endif>

                            </div>
                            <div class="form-group col-xl-3 col-md- col-12">
                                <label>@lang('2FA Verification') </label>
                                <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="ts" @if($user->ts) checked @endif>
                            </div>
                            <div class="form-group col-xl-3 col-md- col-12">
                                <label>@lang('KYC') </label>
                                <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="kv" @if($user->kv == 1) checked @endif>
                            </div>
                        </div>


                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>



    <div class="card mt-30">
        <div class="card-header">
            <h5 class="card-title mb-0">@lang('Change Password')</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.password', $user->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('New Password')</label>
                            <input class="form-control" type="text" name="password" required minlength="6" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update Password')</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-30" id="user-team">
        <div class="card-header">
            <h5 class="card-title mb-0">@lang('Team')</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="{{ route('admin.users.detail', $user->id) }}?team_status=paid&team_scope=today#user-team" class="btn btn-sm {{ $teamStatus === 'paid' && $teamScope === 'today' ? 'btn--success' : 'btn-outline--success' }}">
                    @lang('Paid Users Today') ({{ $teamCounts['paid_today'] }})
                </a>
                <a href="{{ route('admin.users.detail', $user->id) }}?team_status=paid&team_scope=team#user-team" class="btn btn-sm {{ $teamStatus === 'paid' && $teamScope === 'team' ? 'btn--success' : 'btn-outline--success' }}">
                    @lang('Paid Users Team') ({{ $teamCounts['paid_team'] }})
                </a>
                <a href="{{ route('admin.users.detail', $user->id) }}?team_status=unpaid&team_scope=today#user-team" class="btn btn-sm {{ $teamStatus === 'unpaid' && $teamScope === 'today' ? 'btn--warning' : 'btn-outline--warning' }}">
                    @lang('Unpaid Users Today') ({{ $teamCounts['unpaid_today'] }})
                </a>
                <a href="{{ route('admin.users.detail', $user->id) }}?team_status=unpaid&team_scope=team#user-team" class="btn btn-sm {{ $teamStatus === 'unpaid' && $teamScope === 'team' ? 'btn--warning' : 'btn-outline--warning' }}">
                    @lang('Unpaid Users Team') ({{ $teamCounts['unpaid_team'] }})
                </a>
            </div>
            <form method="GET" action="{{ route('admin.users.detail', $user->id) }}#user-team" class="row g-2 mb-3">
                <input type="hidden" name="team_status" value="{{ $teamStatus }}">
                <input type="hidden" name="team_scope" value="{{ $teamScope }}">
                <div class="col-md-6">
                    <input type="text" name="team_search" value="{{ request('team_search') }}" class="form-control" placeholder="@lang('Search name, email or mobile')">
                </div>
                <div class="col-md-3">
                    <button class="btn btn--primary w-100" type="submit">@lang('Search')</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Details')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($team as $member)
                            @php
                                $approvedDeposit = $member->deposits->firstWhere('status', 1);
                                $proofDeposit = $approvedDeposit ?: $member->deposits->sortByDesc('id')->first();
                                $memberDate = $approvedDeposit ? $approvedDeposit->updated_at : $member->created_at;
                                $isPaid = (int) $member->plan_id !== 0;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.detail', $member->id) }}" class="fw-bold d-block">{{ $member->fullname }}</a>
                                    <a href="{{ route('admin.users.detail', $member->id) }}" class="small">{{ $member->email }}</a>
                                    @if($member->plan)
                                        <br><small class="text-muted">{{ $member->plan->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ showDateTime($memberDate) }}
                                    <br>
                                    <small class="text-muted">{{ $isPaid ? __('Approved') : __('Joined') }}</small>
                                </td>
                                <td>
                                    @if($isPaid)
                                        <span class="badge badge--success">@lang('Paid')</span>
                                    @else
                                        <span class="badge badge--warning">@lang('Unpaid')</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $shotUrls = $proofDeposit ? collect(depositProofFiles($proofDeposit))->pluck('url')->values() : collect();
                                    @endphp
                                    @if($shotUrls->isNotEmpty())
                                        <button type="button" class="btn btn-sm btn-outline--primary team-shot-btn" data-images='@json($shotUrls)'>
                                            @lang('Details')
                                        </button>
                                    @else
                                        <span class="text-muted">@lang('No screenshot')</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center text-muted">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($team->hasPages())
                <div class="pt-3">
                    {{ paginateLinks($team) }}
                </div>
            @endif
        </div>
    </div>

    {{-- Add Sub Balance MODAL --}}
    <div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="type"></span> <span>@lang('Balance')</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.users.add.sub.balance',$user->id)}}" method="POST">
                    @csrf
                    <input type="hidden" name="act">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="amount" class="form-control" placeholder="@lang('Please provide positive amount')" required>
                                <div class="input-group-text">{{ __($general->cur_text) }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Remark')</label>
                            <textarea class="form-control" placeholder="@lang('Remark')" name="remark" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    
    {{-- SnapsTubes Add Sub Balance MODAL --}}
    <div id="snapstubesAddSubModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="type"></span> <span>@lang('Balance')</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.users.add.sub.balance.fanta',$user->id)}}" method="POST">
                    @csrf
                    <input type="hidden" name="act" id="snapstubesearn">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="amount" class="form-control" placeholder="@lang('Please provide positive amount')" required>
                                <div class="input-group-text">{{ __($general->cur_text) }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Remark')</label>
                            <textarea class="form-control" placeholder="@lang('Remark')" name="remark" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="teamShotModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width:min(720px, 92vw);max-width:50vw;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Payment Screenshot')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body text-center" id="teamShotBody" style="background:#f7f7f7;"></div>
            </div>
        </div>
    </div>
    <style>
        @media (max-width: 768px) {
            #teamShotModal .modal-dialog { max-width: 92vw !important; }
        }
        #teamShotBody img {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
            border-radius: 8px;
            background: #fff;
        }
    </style>

    <div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span>@lang('Ban User')</span>
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.users.status',$user->id)}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <h6 class="mb-2">@lang('If you ban this user he/she won\'t able to access his/her dashboard.')</h6>
                        <div class="form-group">
                            <label>@lang('Reason')</label>
                            <textarea class="form-control" name="reason" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')
<script>
    (function($){
    "use strict"
        $('.bal-btn').click(function(){
            var act = $(this).data('act');
            $('#addSubModal').find('input[name=act]').val(act);
            if (act == 'add') {
                $('.type').text('Add');
            }else{
                $('.type').text('Subtract');
            }
        });
        let mobileElement = $('.mobile-code');
        $('select[name=country]').change(function(){
            mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
        });

        $('select[name=country]').val('{{@$user->country_code}}');
        let dialCode        = $('select[name=country] :selected').data('mobile_code');
        let mobileNumber    = `{{ $user->mobile }}`;
        mobileNumber        = mobileNumber.replace(dialCode,'');
        $('input[name=mobile]').val(mobileNumber);
        mobileElement.text(`+${dialCode}`);

        $(document).on('click', '.team-shot-btn', function (e) {
            e.preventDefault();
            var images = [];
            try {
                images = JSON.parse($(this).attr('data-images') || '[]');
            } catch (err) {
                images = [];
            }
            var body = $('#teamShotBody').empty();
            if (!images.length) {
                body.append('<p class="text-muted mb-0">No payment screenshot was saved.</p>');
            } else {
                images.forEach(function (src) {
                    body.append($('<img>', {
                        src: src,
                        alt: 'Payment screenshot',
                        class: 'img-fluid'
                    }));
                });
            }
            $('#teamShotModal').modal('show');
        });

    })(jQuery);
</script>

<script>
$(document).ready(function() {
    // When any button with data-act is clicked
    $('[data-bs-target="#snapstubesAddSubModal"]').on('click', function() {
        var actionType = $(this).data('act'); // get "add" or "sub"
        
        // set hidden input value inside the modal
        $('#snapstubesearn').val(actionType);
        
        // also change modal title dynamically (optional)
        let modalTitle = (actionType === 'add') ? 'Add Balance' : 'Subtract Balance';
        $('#snapstubesAddSubModal .modal-title .type').text(modalTitle);
    });
});
</script>

@endpush
