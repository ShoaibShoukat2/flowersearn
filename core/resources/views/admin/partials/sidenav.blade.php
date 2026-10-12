<div class="sidebar bg--black">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{route('admin.dashboard')}}" class="sidebar__main-logo"><img
                    src="https://i.postimg.cc/prr2m5TV/Whats-App-Image-2024-12-01-at-23-09-41-14034985-removebg-preview.png"
                    alt="@lang('image')"></a>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{menuActive('admin.dashboard')}}">
                    <a href="{{route('admin.dashboard')}}" class="nav-link ">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>



                <div class="sidebar-submenu {{menuActive('admin.ptc*',2)}} ">
                    <ul>





                    </ul>
                </div>
                </li>
                <li class="sidebar-menu-item {{menuActive('admin.users.active')}} ">
                    <a href="{{route('admin.users.active')}}" class="nav-link">
                        <i class="menu-icon las la-dot-circle"></i>
                        <span class="menu-title">@lang('All approved user')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{menuActive('admin.referrals.*')}}">
                    <a href="{{route('admin.referrals.index')}}" class="nav-link ">
                        <i class="menu-icon las la-link"></i>
                        <span class="menu-title">@lang('Referral Commissions')</span>
                    </a>
                </li>
                </li>
                <li class="sidebar-menu-item {{menuActive('admin.deposit.pending')}} ">
                    <a href="{{route('admin.deposit.pending')}}" class="nav-link">
                        <i class="menu-icon las la-dot-circle"></i>
                        <span class="menu-title">@lang(' New user Request')</span>
                        @if($pendingDepositsCount)
                        <span class="menu-badge pill bg--danger ms-auto">{{$pendingDepositsCount}}</span>
                        @endif
                    </a>
                </li>
                <li class="sidebar-menu-item {{menuActive('admin.withdraw.pending')}} ">
                    <a href="{{route('admin.withdraw.pending')}}" class="nav-link">
                        <i class="menu-icon las la-dot-circle"></i>
                        <span class="menu-title">@lang('Withdrawal Request')</span>

                        @if($pendingWithdrawCount)
                        <span class="menu-badge pill bg--danger ms-auto">{{$pendingWithdrawCount}}</span>
                        @endif
                    </a>
                </li>


                <li class="sidebar-menu-item {{menuActive('admin.deposit.today')}} ">
                    <a href="{{route('admin.deposit.today')}}" class="nav-link">
                        <i class="menu-icon las la-calendar-day"></i>
                        <span class="menu-title">@lang('Today Approved Users')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{menuActive('admin.deposit.approved')}} ">
                    <a href="{{route('admin.deposit.approved')}}" class="nav-link">
                        <i class="menu-icon las la-dot-circle"></i>
                        <span class="menu-title">@lang('All Approved Users')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{menuActive('admin.withdraw.approved')}} ">
                    <a href="{{route('admin.withdraw.approved')}}" class="nav-link">
                        <i class="menu-icon las la-dot-circle"></i>
                        <span class="menu-title">@lang('Today approved Withdrawal')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{menuActive('admin.deposit.rejected')}} ">
                    <a href="{{route('admin.deposit.rejected')}}" class="nav-link">
                        <i class="menu-icon las la-dot-circle"></i>
                        <span class="menu-title">@lang('Rejected User')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{menuActive('admin.plan.upgrade.*')}} ">
                    <a href="{{route('admin.plan.upgrade.dashboard')}}" class="nav-link">
                        <i class="menu-icon las la-level-up-alt"></i>
                        <span class="menu-title">@lang('Plan Upgrade')</span>
                        @if(!empty($pendingPlanUpgradesCount))
                        <span class="menu-badge pill bg--danger ms-auto">{{$pendingPlanUpgradesCount}}</span>
                        @endif
                    </a>
                </li>

                <ul>

                    <li class="sidebar-menu-item {{menuActive('admin.gateway.manual.*')}} ">
                        <a href="{{route('admin.gateway.manual.index')}}" class="nav-link">
                            <i class="menu-icon las la-dot-circle"></i>
                            <span class="menu-title">@lang(' Easypaisa & Jazz Cash')</span>
                        </a>
                    </li>

                </ul>

                <li class="sidebar__menu-header">@lang('Settings')</li>

                <li class="sidebar-menu-item {{menuActive('admin.setting.index')}}">
                    <a href="{{route('admin.setting.index')}}" class="nav-link">
                        <i class="menu-icon las la-life-ring"></i>
                        <span class="menu-title">@lang('General Setting')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{menuActive('admin.setting.contact.links')}}">
                    <a href="{{route('admin.setting.contact.links')}}" class="nav-link">
                        <i class="menu-icon lab la-whatsapp"></i>
                        <span class="menu-title">@lang('Contact Links')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{menuActive('admin.setting.contact.admins')}}">
                    <a href="{{route('admin.setting.contact.admins')}}" class="nav-link">
                        <i class="menu-icon las la-headset"></i>
                        <span class="menu-title">@lang('Contact Us Admins')</span>
                    </a>
                </li>
                
                <li class="sidebar-menu-item {{menuActive('admin.setting.levels')}}">
                    <a href="{{route('admin.setting.levels')}}" class="nav-link">
                        <i class="menu-icon las la-life-ring"></i>
                        <span class="menu-title">@lang('Levels Setting')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{menuActive('admin.rewards*')}}">
                    <a href="{{route('admin.rewards.index')}}" class="nav-link">
                        <i class="menu-icon las la-gift"></i>
                        <span class="menu-title">@lang('Rewards')</span>
                    </a>
                </li>


            </ul>
            <div class="text-center mb-3 text-uppercase">
                <span class="text--primary">{{(systemDetails()['name'])}}</span>
                <span class="text--success">@lang('V'){{systemDetails()['version']}} </span>
            </div>
        </div>
    </div>
</div>
<!-- sidebar end -->

@push('script')
<script>
    if($('li').hasClass('active')){
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            },500);
        }
</script>
@endpush