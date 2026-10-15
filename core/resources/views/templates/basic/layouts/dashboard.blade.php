<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $general->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/dashboard-mobile.css') }}">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#e91e8c',
                        'primary-dark': '#c2185b',
                        'primary-light': '#f8d7e8',
                        secondary: '#f8f4f6',
                        light: '#ffffff',
                        'light-gray': '#fdf6f9',
                        'border-light': '#f0d0e0'
                    },
                    fontFamily: { poppins: ['Poppins','sans-serif'] }
                }
            }
        }
    </script>

    @stack('style-lib')
    @stack('style')

    <style>
        *{box-sizing:border-box;}
        body{font-family:'Poppins',sans-serif;background:#f8f4f6;}

        /* ─── Sidebar ─── */
        #fl-sidebar{
            width:240px;
            background:#fff;
            border-right:1.5px solid #f0d0e0;
            height:100vh;
            position:fixed;
            top:0;left:0;
            z-index:50;
            display:flex;
            flex-direction:column;
            transition:transform 0.3s ease;
        }

        .fl-sb-logo{
            padding:1.4rem 1.2rem 1rem;
            border-bottom:1px solid #f0d0e0;
            display:flex;
            align-items:center;
            gap:10px;
        }

        .fl-sb-logo-icon{
            width:38px;height:38px;
            background:linear-gradient(135deg,#e91e8c,#c2185b);
            border-radius:10px;
            display:flex;align-items:center;justify-content:center;
            font-size:18px;flex-shrink:0;
        }

        .fl-sb-logo-text{
            font-family:'Playfair Display',serif;
            font-size:1.35rem;font-weight:700;
            background:linear-gradient(135deg,#e91e8c,#c2185b);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;
            background-clip:text;
        }

        /* User pill */
        .fl-sb-user{
            margin:1rem 0.8rem 0.5rem;
            padding:0.75rem 0.9rem;
            background:#fdf6f9;
            border:1.5px solid #f0d0e0;
            border-radius:14px;
            display:flex;align-items:center;gap:10px;
        }

        .fl-sb-avatar{
            width:36px;height:36px;
            background:linear-gradient(135deg,#e91e8c,#f06292);
            border-radius:10px;
            display:flex;align-items:center;justify-content:center;
            color:#fff;font-size:14px;flex-shrink:0;
        }

        .fl-sb-uname{font-size:0.82rem;font-weight:600;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .fl-sb-plan{font-size:0.72rem;color:#e91e8c;font-weight:500;}

        /* Nav */
        .fl-sb-nav{flex:1;overflow-y:auto;padding:0.5rem 0.6rem 1rem;}
        .fl-sb-nav::-webkit-scrollbar{width:3px;}
        .fl-sb-nav::-webkit-scrollbar-thumb{background:#f0d0e0;border-radius:2px;}

        .fl-sb-section{
            font-size:0.68rem;font-weight:700;color:#ccc;
            text-transform:uppercase;letter-spacing:0.08em;
            padding:0.8rem 0.6rem 0.3rem;
        }

        .fl-nav-link{
            display:flex;align-items:center;gap:10px;
            padding:0.6rem 0.8rem;
            border-radius:10px;
            color:#777;font-size:0.85rem;font-weight:500;
            text-decoration:none;
            transition:all 0.2s;
            margin-bottom:2px;
        }

        .fl-nav-link i{width:16px;text-align:center;font-size:14px;color:#e91e8c;flex-shrink:0;}
        .fl-nav-photo{width:22px;height:22px;border-radius:50%;object-fit:cover;flex-shrink:0;}
        .fl-nav-link:hover{background:#fdf6f9;color:#e91e8c;}
        .fl-nav-link.active{background:linear-gradient(135deg,#e91e8c,#f06292);color:#fff;box-shadow:0 3px 12px rgba(233,30,140,0.22);}
        .fl-nav-link.active i{color:#fff;}

        /* Dropdown */
        .fl-dd-btn{
            display:flex;align-items:center;justify-content:space-between;
            padding:0.6rem 0.8rem;border-radius:10px;
            color:#777;font-size:0.85rem;font-weight:500;
            cursor:pointer;background:none;border:none;
            width:100%;font-family:'Poppins',sans-serif;
            transition:all 0.2s;margin-bottom:2px;
        }
        .fl-dd-btn:hover{background:#fdf6f9;color:#e91e8c;}
        .fl-dd-btn .l{display:flex;align-items:center;gap:10px;}
        .fl-dd-btn .l i{width:16px;text-align:center;font-size:14px;color:#e91e8c;}
        .fl-dd-btn .arr{font-size:10px;color:#ccc;transition:transform 0.25s;}

        .fl-dd-content{display:none;padding:2px 0 4px 2.2rem;}
        .fl-dd-content a{
            display:block;padding:0.4rem 0.6rem;
            font-size:0.8rem;color:#999;text-decoration:none;
            border-radius:8px;transition:all 0.2s;
        }
        .fl-dd-content a:hover{color:#e91e8c;background:#fdf6f9;}

        .fl-logout-link{
            display:flex;align-items:center;gap:10px;
            padding:0.6rem 0.8rem;border-radius:10px;
            color:#e53935;font-size:0.85rem;font-weight:500;
            text-decoration:none;transition:all 0.2s;
            margin-top:0.5rem;
        }
        .fl-logout-link:hover{background:#fff5f5;}
        .fl-logout-link i{width:16px;text-align:center;font-size:14px;}

        /* ─── Topbar ─── */
        #fl-topbar{
            position:fixed;
            top:0;left:240px;right:0;
            height:60px;
            background:#fff;
            border-bottom:1.5px solid #f0d0e0;
            display:flex;align-items:center;justify-content:space-between;
            padding:0 1.5rem;
            z-index:40;
            transition:left 0.3s;
        }

        .fl-topbar-left{display:flex;align-items:center;gap:12px;}

        .fl-hamburger{
            display:none;
            width:36px;height:36px;
            border:1.5px solid #f0d0e0;
            border-radius:9px;background:#fff;
            align-items:center;justify-content:center;
            cursor:pointer;color:#888;font-size:16px;
        }

        @media(max-width:1023px){
            .fl-hamburger{display:flex;}
            #fl-sidebar{transform:translateX(-100%);}
            #fl-topbar{left:0;}
            #fl-main-wrap{margin-left:0 !important;}
        }

        .fl-topbar-title{font-size:1rem;font-weight:600;color:#333;}

        /* Breadcrumb */
        .fl-breadcrumb{
            display:flex;align-items:center;gap:6px;
            font-size:0.75rem;color:#bbb;
        }
        .fl-breadcrumb span{color:#e91e8c;}

        .fl-topbar-right{display:flex;align-items:center;gap:8px;}

        .fl-tb-icon{
            width:36px;height:36px;
            border:1.5px solid #f0d0e0;border-radius:9px;
            background:#fff;display:flex;align-items:center;justify-content:center;
            cursor:pointer;color:#888;font-size:14px;
            transition:all 0.2s;position:relative;
        }
        .fl-tb-icon:hover{border-color:#e91e8c;color:#e91e8c;background:#fdf6f9;}

        .fl-notif-badge{
            position:absolute;top:5px;right:5px;
            width:7px;height:7px;
            background:#e91e8c;border-radius:50%;
            border:1.5px solid #fff;
        }

        /* User dropdown trigger */
        .fl-user-trigger{
            display:flex;align-items:center;gap:8px;
            padding:5px 10px 5px 5px;
            border:1.5px solid #f0d0e0;border-radius:10px;
            background:#fff;cursor:pointer;
            transition:all 0.2s;font-family:'Poppins',sans-serif;
        }
        .fl-user-trigger:hover{border-color:#e91e8c;background:#fdf6f9;}

        .fl-user-trigger-av{
            width:28px;height:28px;
            background:linear-gradient(135deg,#e91e8c,#f06292);
            border-radius:7px;display:flex;align-items:center;justify-content:center;
            color:#fff;font-size:12px;
        }

        .fl-user-trigger-name{font-size:0.8rem;font-weight:600;color:#444;}

        /* Dropdown */
        .fl-user-dd{
            position:absolute;right:0;top:calc(100% + 8px);
            width:190px;background:#fff;
            border:1.5px solid #f0d0e0;border-radius:14px;
            box-shadow:0 10px 35px rgba(233,30,140,0.1);
            z-index:100;display:none;overflow:hidden;
        }

        .fl-user-dd a{
            display:flex;align-items:center;gap:9px;
            padding:0.6rem 1rem;font-size:0.83rem;color:#555;
            text-decoration:none;transition:all 0.2s;
        }
        .fl-user-dd a:hover{background:#fdf6f9;color:#e91e8c;}
        .fl-user-dd a i{width:14px;text-align:center;color:#e91e8c;font-size:13px;}
        .fl-user-dd hr{border:none;border-top:1px solid #f0d0e0;margin:3px 0;}
        .fl-user-dd .dd-logout{color:#e53935;}
        .fl-user-dd .dd-logout i{color:#e53935;}
        .fl-user-dd .dd-logout:hover{background:#fff5f5;}

        /* Fix text orientation */
        #fl-user-dd,#fl-user-dd *{
            writing-mode:horizontal-tb !important;
            direction:ltr !important;
            transform:none !important;
        }

        /* ─── Main content ─── */
        #fl-main-wrap{
            margin-left:240px;
            padding-top:60px;
            min-height:100vh;
            transition:margin-left 0.3s;
        }

        .fl-page-content{padding:1.5rem;}

        /* Animations */
        .fade-in{animation:fadeIn 0.45s ease both;}
        @keyframes fadeIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:translateY(0);}}

        .hover-scale{transition:transform 0.2s ease,box-shadow 0.2s ease;}
        .hover-scale:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(233,30,140,0.12) !important;}

        .gradient-bg{background:linear-gradient(135deg,#e91e8c,#f06292);}
    </style>
</head>

<body>
    @stack('fbComment')

    <!-- Mobile overlay -->
    <div id="fl-overlay" onclick="flCloseSidebar()"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:45;"></div>

    <!-- ─── Sidebar ─── -->
    <aside id="fl-sidebar">
        <!-- Logo -->
        <div class="fl-sb-logo">
            <div class="fl-sb-logo-icon">🌸</div>
            <div class="fl-sb-logo-text">Flower</div>
        </div>

        <!-- User pill -->
        <div class="fl-sb-user">
            <div class="fl-sb-avatar"><i class="fas fa-user"></i></div>
            <div style="overflow:hidden;min-width:0;">
                @php
                    $u = auth()->user();
                    $dn = $u ? ($u->fullname ?: ($u->email ?: 'User')) : 'Guest';
                @endphp
                <div class="fl-sb-uname">{{ $dn }}</div>
                <div class="fl-sb-plan">@if($u && $u->plan){{ $u->plan->name }}@else Free Plan @endif</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="fl-sb-nav">
            <div class="fl-sb-section">Main</div>

            <a href="{{ route('user.home') }}" class="fl-nav-link {{ request()->routeIs('user.home') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>

            <a href="{{ route('user.wallet') }}" class="fl-nav-link {{ request()->routeIs('user.wallet') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i> Wallet
            </a>

            <div class="fl-sb-section">Finance</div>

            <button class="fl-dd-btn" onclick="flDD('wd','wdArr')">
                <div class="l"><i class="fas fa-minus-circle"></i> Withdraw</div>
                <i class="fas fa-chevron-down arr" id="wdArr"></i>
            </button>
            <div class="fl-dd-content" id="wd">
                <a href="{{ route('user.withdraw') }}">Withdraw Now</a>
                <a href="{{ url('/user/withdraw-history') }}">History</a>
            </div>

            @if($u && $u->plan_id)
            <a href="{{ route('user.plan.upgrade.index') }}" class="fl-nav-link {{ request()->routeIs('user.plan.upgrade.*') ? 'active' : '' }}">
                <i class="fas fa-level-up-alt"></i> Plan Upgrade
            </a>
            @endif

            <div class="fl-sb-section">Earn</div>

            <a href="{{ route('user.task.index') }}" class="fl-nav-link {{ request()->routeIs('user.task.*') ? 'active' : '' }}">
                <i class="fas fa-tasks"></i> Tasks
            </a>

            <a href="{{ route('user.rewards') }}" class="fl-nav-link {{ request()->routeIs('user.rewards') ? 'active' : '' }}">
                <i class="fas fa-gift"></i> Rewards
            </a>

            <div class="fl-sb-section">Help</div>

            <a href="{{ route('user.contact') }}" class="fl-nav-link {{ request()->routeIs('user.contact') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i> Contact Us
            </a>

            @php $sidebarChannels = officialChannels(); @endphp
            @forelse($sidebarChannels as $sidebarChannel)
            <a href="{{ $sidebarChannel->link }}" target="_blank" rel="noopener noreferrer" class="fl-nav-link">
                @if($sidebarPhoto = officialChannelImageUrl($sidebarChannel))
                    <img src="{{ $sidebarPhoto }}" alt="" class="fl-nav-photo">
                @else
                    <i class="{{ str_contains((string) $sidebarChannel->icon, 'fa-') ? $sidebarChannel->icon : 'fas fa-bullhorn' }}"></i>
                @endif
                {{ $sidebarChannel->title }}
            </a>
            @empty
            @if(whatsappChannelLink())
            <a href="{{ whatsappChannelLink() }}" target="_blank" rel="noopener noreferrer" class="fl-nav-link">
                <i class="fab fa-whatsapp"></i> Channel
            </a>
            @endif
            @if(whatsappCommunityLink())
            <a href="{{ whatsappCommunityLink() }}" target="_blank" rel="noopener noreferrer" class="fl-nav-link">
                <i class="fas fa-users"></i> Community
            </a>
            @endif
            @endforelse

            <div class="fl-sb-section">Account</div>

            <button class="fl-dd-btn" onclick="flDD('acc','accArr')">
                <div class="l"><i class="fas fa-user-cog"></i> Account</div>
                <i class="fas fa-chevron-down arr" id="accArr"></i>
            </button>
            <div class="fl-dd-content" id="acc">
                <a href="{{ route('user.profile.setting') }}">Profile</a>
                <a href="{{ route('user.change.password') }}">Change Password</a>
                <a href="{{ route('user.twofactor') }}">2FA Security</a>
                @if($u && $u->kv == 0)
                <a href="{{ route('user.kyc.form') }}">KYC Verification</a>
                @endif
            </div>

            <a href="{{ route('user.logout') }}" class="fl-logout-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </aside>

    <!-- ─── Main wrapper ─── -->
    <div id="fl-main-wrap">

        <!-- Topbar -->
        <nav id="fl-topbar">
            <div class="fl-topbar-left">
                <button class="fl-hamburger" onclick="flOpenSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="fl-topbar-title">{{ $pageTitle ?? 'Dashboard' }}</div>
                    <div class="fl-breadcrumb">
                        <i class="fas fa-home" style="font-size:10px;"></i>
                        <span>/</span>
                        <span>{{ $pageTitle ?? 'Dashboard' }}</span>
                    </div>
                </div>
            </div>

            <div class="fl-topbar-right">
                <div class="fl-tb-icon">
                    <i class="fas fa-bell"></i>
                    <span class="fl-notif-badge"></span>
                </div>

                <div class="relative">
                    <button class="fl-user-trigger" onclick="flUserDD()" type="button">
                        <div class="fl-user-trigger-av"><i class="fas fa-user"></i></div>
                        <span class="fl-user-trigger-name hidden sm:block">{{ $dn ?? 'User' }}</span>
                        <i class="fas fa-chevron-down" style="font-size:9px;color:#ccc;"></i>
                    </button>

                    <div id="fl-user-dd" class="fl-user-dd">
                        <a href="{{ route('user.profile.setting') }}"><i class="fas fa-user"></i> Profile</a>
                        <a href="{{ route('user.change.password') }}"><i class="fas fa-cog"></i> Settings</a>
                        <a href="{{ route('user.twofactor') }}"><i class="fas fa-shield-alt"></i> 2FA</a>
                        <hr>
                        <a href="{{ route('user.logout') }}" class="dd-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page content -->
        <main class="fl-page-content">
            @yield('content')
        </main>
    </div>

    <script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    @include('partials.notify')

    <script>
        function flOpenSidebar() {
            document.getElementById('fl-sidebar').style.transform = 'translateX(0)';
            document.getElementById('fl-overlay').style.display = 'block';
        }
        function flCloseSidebar() {
            document.getElementById('fl-sidebar').style.transform = 'translateX(-100%)';
            document.getElementById('fl-overlay').style.display = 'none';
        }

        function flDD(id, arrId) {
            const dd = document.getElementById(id);
            const arr = document.getElementById(arrId);
            const open = dd.style.display === 'block';
            dd.style.display = open ? 'none' : 'block';
            if (arr) arr.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        function flUserDD() {
            const m = document.getElementById('fl-user-dd');
            m.style.display = m.style.display === 'block' ? 'none' : 'block';
        }

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.relative')) {
                const m = document.getElementById('fl-user-dd');
                if (m) m.style.display = 'none';
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const main = document.querySelector('.fl-page-content');
            if (main) main.classList.add('fade-in');
        });
    </script>

    @stack('script-lib')
    @stack('script')
</body>
</html>
