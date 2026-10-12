@php
    $pageTitle = $rewardSettings->page_title ?? 'Rewards & Levels';
@endphp

@extends($activeTemplate.'layouts.dashboard')

@section('content')
<div class="fade-in">
    <div class="mb-8">
        <div class="gradient-bg rounded-2xl p-6 text-white relative overflow-hidden shadow-lg">
            <div class="relative z-10 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="bi bi-trophy text-white text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold mb-2">{{ $rewardSettings->page_title }}</h2>
                @if($rewardSettings->page_subtitle)
                <p class="text-white/90">{{ $rewardSettings->page_subtitle }}</p>
                @endif
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-light border border-border-light rounded-xl p-6 hover-scale shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-award text-blue-600 text-xl"></i>
                </div>
                <span class="text-xs text-gray-500 bg-blue-50 px-2 py-1 rounded-full">Current</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">Level {{ $user->level }}</h3>
            <p class="text-sm text-gray-500">Your Current Level</p>
        </div>

        <div class="bg-light border border-border-light rounded-xl p-6 hover-scale shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-people text-green-600 text-xl"></i>
                </div>
                <span class="text-xs text-gray-500 bg-green-50 px-2 py-1 rounded-full">Total</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $referralsCount }}</h3>
            <p class="text-sm text-gray-500">Successful Joinings</p>
        </div>

        <div class="bg-light border border-border-light rounded-xl p-6 hover-scale shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-lightning text-purple-600 text-xl"></i>
                </div>
                <span class="text-xs text-gray-500 bg-purple-50 px-2 py-1 rounded-full">Limit</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $user->daily_limit }}</h3>
            <p class="text-sm text-gray-500">Daily Task Limit</p>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8 shadow-sm">
        <div class="flex items-start space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="bi bi-lightbulb text-blue-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-blue-800 font-semibold text-lg mb-2">{{ $rewardSettings->tips_title ?: 'How to Level Up' }}</h3>
                <p class="text-blue-700 text-sm leading-relaxed">
                    {{ $rewardSettings->tips_content ?: 'Level up by completing successful joinings through your referral link. Share your link with family, friends, or other people on social media. When someone joins through your link and completes the required process, it will count toward your level progress.' }}
                </p>
            </div>
        </div>
    </div>

    @if($rewardItems->count() > 0)
    <div class="bg-light border border-border-light rounded-xl p-6 mb-8 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Available Rewards</h3>
            <span class="text-sm text-gray-500">{{ $rewardItems->count() }} offers</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($rewardItems as $item)
            <div class="p-4 rounded-xl border border-pink-100 bg-gradient-to-br from-pink-50 to-white">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white border border-pink-100 flex items-center justify-center text-2xl flex-shrink-0">
                        @if($item->icon && str_contains($item->icon, 'fa-'))
                            <i class="{{ $item->icon }} text-pink-500"></i>
                        @else
                            {{ $item->icon ?: '🎁' }}
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-3 mb-1">
                            <h4 class="font-semibold text-gray-800">{{ $item->title }}</h4>
                            @if($item->badge)
                            <span class="text-xs font-bold px-2 py-1 rounded-full bg-pink-100 text-pink-700">{{ $item->badge }}</span>
                            @endif
                        </div>
                        @if($item->description)
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $item->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($rewardSettings->show_levels && $nextLevel)
    <div class="bg-light border border-border-light rounded-xl p-6 mb-8 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Progress to Level {{ $nextLevel }}</h3>
            <span class="text-sm text-gray-500">{{ $referralsNeeded }} more joinings needed</span>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
            <div class="gradient-bg h-3 rounded-full transition-all duration-500" style="width: {{ $progressPercentage }}%"></div>
        </div>

        <div class="flex justify-between text-sm text-gray-600">
            <span>{{ $referralsCount }} joinings</span>
            <span>{{ number_format($progressPercentage, 1) }}% complete</span>
        </div>
    </div>
    @elseif($rewardSettings->show_levels && (int) $user->level >= 8)
    <div class="bg-pink-50 border border-pink-200 rounded-xl p-6 mb-8 shadow-sm">
        <h3 class="text-lg font-semibold text-pink-800 mb-1">Level 8 Complete</h3>
        <p class="text-sm text-pink-700">🌷 Flowers Earning unlock ho chuki hai. ❤️✨</p>
    </div>
    @endif

    @if($rewardSettings->show_levels && $levels && count($levels) > 0)
    <div class="bg-light border border-border-light rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Level Requirements</h3>
            <span class="text-sm text-gray-500">{{ $plan ? $plan->name : 'No Plan' }} Plan</span>
        </div>

        <div class="space-y-4">
            @foreach($levels as $level => $data)
            @php $levelNum = (int) $level; @endphp
            @if($levelNum < 1)
                @continue
            @endif
            <div class="flex items-center justify-between p-4 rounded-lg border {{ $user->level >= $levelNum ? 'bg-green-50 border-green-200' : ($levelNum === 8 ? 'bg-pink-50 border-pink-200' : 'bg-gray-50 border-gray-200') }}">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $user->level >= $levelNum ? 'bg-green-100' : ($levelNum === 8 ? 'bg-pink-100' : 'bg-gray-100') }}">
                        @if($user->level >= $levelNum)
                            <i class="bi bi-check-circle text-green-600 text-xl"></i>
                        @else
                            <i class="bi bi-lock text-gray-400 text-xl"></i>
                        @endif
                    </div>
                    <div>
                        <h4 class="font-semibold {{ $user->level >= $levelNum ? 'text-green-800' : 'text-gray-700' }}">
                            Level {{ $levelNum }} ➜ {{ isset($data['referrals_required']) ? $data['referrals_required'] : 0 }} Joinings
                        </h4>
                        @if(!empty($data['daily_limit']))
                        <p class="text-sm {{ $user->level >= $levelNum ? 'text-green-600' : 'text-gray-500' }}">
                            Daily tasks: {{ (int) $data['daily_limit'] }}
                        </p>
                        @endif
                        @if($levelNum === 8)
                        <p class="text-sm {{ $user->level >= 8 ? 'text-green-600' : 'text-pink-600' }}">
                            Flowers Earning unlock
                        </p>
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    @if($user->level >= $levelNum)
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                        Unlocked
                    </span>
                    @else
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                        Locked
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 p-4 rounded-xl bg-pink-50 border border-pink-200 text-center">
            <p class="text-sm font-medium text-pink-800 leading-relaxed">
                🌷 Level 8 complete karne par Flowers Earning Unlock ho jayegi. ❤️✨
            </p>
        </div>
    </div>
    @elseif($rewardSettings->show_levels)
    <div class="bg-light border border-border-light rounded-xl p-6 shadow-sm">
        <div class="text-center py-12">
            <i class="bi bi-info-circle text-gray-400 text-5xl mb-4"></i>
            <h4 class="text-gray-600 font-semibold mb-2">No Levels Available</h4>
            <p class="text-gray-500 mb-4">No level system is configured for your current plan.</p>
            @if(!$plan)
            <a href="{{ route('user.deposit') }}" class="gradient-bg px-6 py-3 rounded-lg text-white font-medium hover:opacity-90 transition-opacity shadow-sm">
                Choose a Plan
            </a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
