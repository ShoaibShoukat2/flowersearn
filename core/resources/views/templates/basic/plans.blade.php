@php
    $pageTitle = 'Subscription Plans';
@endphp

@extends($activeTemplate . 'layouts.dashboard')

@push('style')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="fade-in max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header with Animated Background -->
    <div class="mb-12 relative">
        <div class="gradient-bg rounded-3xl p-8 md:p-12 text-white relative overflow-hidden shadow-2xl">
            <!-- Animated Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 w-32 h-32 bg-white/5 rounded-full blur-xl animate-pulse" style="animation-delay: 2s;"></div>
            </div>
            
            <div class="relative z-10 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 mb-6 rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Choose Your Plan</h1>
                <p class="text-xl text-white/90 max-w-2xl mx-auto">Select the perfect subscription plan to maximize your earning potential</p>
            </div>
        </div>
    </div>

    <!-- Current Plan Status -->
    @auth
        @if(auth()->user()->runningPlan)
        <div class="mb-10">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6 flex items-center space-x-6 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-green-900 font-bold text-lg mb-1">🎉 Active Subscription</h4>
                    <p class="text-green-700 font-medium">{{ auth()->user()->plan->name ?? 'Current Plan' }}</p>
                    <p class="text-green-600 text-sm mt-1">You're enjoying premium benefits</p>
                </div>
            </div>
        </div>
        @endif
    @endauth

    <!-- Plans Grid -->
    @if($plans->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach ($plans as $index => $plan)
                @php
                    $isCurrentPlan = auth()->check() && auth()->user()->runningPlan && auth()->user()->plan_id == $plan->id;
                    $colors = [
                        ['from' => 'from-blue-500', 'to' => 'to-indigo-600', 'icon' => 'bg-blue-100', 'iconText' => 'text-blue-600', 'badge' => 'bg-blue-500'],
                        ['from' => 'from-purple-500', 'to' => 'to-pink-600', 'icon' => 'bg-purple-100', 'iconText' => 'text-purple-600', 'badge' => 'bg-purple-500'],
                        ['from' => 'from-orange-500', 'to' => 'to-red-600', 'icon' => 'bg-orange-100', 'iconText' => 'text-orange-600', 'badge' => 'bg-orange-500'],
                    ];
                    $color = $colors[$index % 3];
                @endphp
                
                <div class="group relative">
                    <!-- Popular Badge -->
                    @if($index === 1)
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 z-10">
                        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-6 py-2 rounded-full text-sm font-bold shadow-lg">
                            ⭐ POPULAR
                        </div>
                    </div>
                    @endif
                    
                    <div class="bg-white border-2 {{ $isCurrentPlan ? 'border-green-400 shadow-2xl' : 'border-gray-200' }} rounded-3xl overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 h-full">
                        
                        <!-- Plan Header with Gradient -->
                        <div class="relative bg-gradient-to-br {{ $color['from'] }} {{ $color['to'] }} p-8 text-white overflow-hidden">
                            <!-- Background Pattern -->
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full -translate-y-16 translate-x-16"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white rounded-full translate-y-12 -translate-x-12"></div>
                            </div>
                            
                            <div class="relative z-10 text-center">
                                <!-- Icon -->
                                <div class="inline-flex items-center justify-center w-20 h-20 mb-4 rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                    </svg>
                                </div>
                                
                                <h3 class="text-2xl font-bold mb-3">{{ __($plan->name) }}</h3>
                                
                                <!-- Price -->
                                <div class="mb-2">
                                    <span class="text-5xl font-extrabold">Rs {{ showAmount($plan->price) }}</span>
                                </div>
                                <p class="text-white/90 text-sm font-medium">{{ __($general->cur_text) }}</p>
                                
                                @if($isCurrentPlan)
                                <div class="mt-4">
                                    <span class="inline-block bg-green-400 text-green-900 px-4 py-1 rounded-full text-sm font-bold">
                                        ✓ Your Current Plan
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Plan Features -->
                        <div class="p-8">
                            <div class="space-y-5 mb-8">
                                <!-- Daily Limit -->
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-md">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 mb-1">Daily PTC Limit</h4>
                                        <p class="text-gray-600 text-sm">{{ $plan->daily_limit }} ads per day</p>
                                    </div>
                                </div>

                                <!-- Validity -->
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center shadow-md">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 mb-1">Plan Validity</h4>
                                        <p class="text-gray-600 text-sm">{{ $plan->validity }} days</p>
                                    </div>
                                </div>

                                <!-- Referral Levels -->
                                @if($plan->ref_level)
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center shadow-md">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 mb-1">Referral Levels</h4>
                                        <p class="text-gray-600 text-sm">{{ $plan->ref_level }} levels</p>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Subscribe Button -->
                            @if ($isCurrentPlan)
                                <button class="w-full bg-gray-100 text-gray-500 py-4 px-6 rounded-2xl font-bold cursor-not-allowed" disabled>
                                    ✓ Current Plan
                                </button>
                            @else
                                <button class="buyBtn w-full bg-gradient-to-r {{ $color['from'] }} {{ $color['to'] }} text-white py-4 px-6 rounded-2xl font-bold hover:shadow-xl transition-all duration-300 transform hover:scale-105" data-id="{{ $plan->id }}">
                                    Subscribe Now →
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-20">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-3xl mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
            </div>
            <h4 class="text-gray-700 font-bold text-xl mb-2">No Plans Available</h4>
            <p class="text-gray-500">Check back later for exciting subscription plans.</p>
        </div>
    @endif

    <!-- Benefits Section with Modern Cards -->
    <div class="mt-16 mb-12">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Why Choose Our Plans?</h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">Unlock premium features and maximize your earning potential with our subscription plans</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Benefit 1 -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-3xl p-8 hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Higher Earnings</h3>
                <p class="text-gray-600 leading-relaxed">Increase your daily PTC limit significantly and unlock more earning opportunities every day</p>
            </div>
            
            <!-- Benefit 2 -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-3xl p-8 hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Referral Bonuses</h3>
                <p class="text-gray-600 leading-relaxed">Earn passive income from multiple referral levels and build your earning network</p>
            </div>
            
            <!-- Benefit 3 -->
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-3xl p-8 hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-pink-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Premium Features</h3>
                <p class="text-gray-600 leading-relaxed">Access exclusive features and priority support for the best experience</p>
            </div>
        </div>
    </div>

    <!-- Trust Indicators -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-3xl p-8 md:p-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-indigo-600 mb-2">1000+</div>
                <div class="text-gray-600 font-medium">Active Users</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-purple-600 mb-2">24/7</div>
                <div class="text-gray-600 font-medium">Support</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-pink-600 mb-2">100%</div>
                <div class="text-gray-600 font-medium">Secure</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-red-600 mb-2">Fast</div>
                <div class="text-gray-600 font-medium">Payouts</div>
            </div>
        </div>
    </div>
</div>

<!-- Subscription Confirmation Modal -->
<div id="BuyModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl transform transition-all">
        <form method="post" action="{{ route('user.buyPlan') }}">
            @csrf
            <input type="hidden" name="id">
            
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900">Confirm Subscription</h3>
                    <button type="button" class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors" onclick="closeModal()">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-8">
                @auth
                    <div class="text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-gray-900 font-bold text-lg mb-3">Ready to Subscribe?</h4>
                        <p class="text-gray-600 mb-6">Confirm your subscription to unlock premium features and start earning more</p>
                        
                        @if(auth()->user()->runningPlan)
                            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-2xl p-4">
                                <div class="flex items-start space-x-3">
                                    <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div class="text-left">
                                        <p class="text-yellow-900 font-semibold text-sm mb-1">Important Notice</p>
                                        <p class="text-yellow-800 text-sm">Your current plan will be replaced with this new subscription. All limitations will be reset according to the new package.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h4 class="text-gray-900 font-bold text-lg mb-3">Login Required</h4>
                        <p class="text-gray-600 mb-6">Please login to your account to subscribe to this plan</p>
                    </div>
                @endauth
            </div>
            
            <!-- Modal Footer -->
            <div class="p-6 border-t border-gray-200">
                @auth
                    <div class="flex space-x-3">
                        <button type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-3 px-6 rounded-xl font-semibold transition-colors" onclick="closeModal()">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-red-600 text-white py-3 px-6 rounded-xl font-semibold hover:shadow-lg transition-all">
                            Confirm & Subscribe
                        </button>
                    </div>
                @else
                    <a href="{{ route('user.login') }}" class="w-full block text-center bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-6 rounded-xl font-semibold hover:shadow-lg transition-all">
                        Login to Continue
                    </a>
                @endauth
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
(function($) {
    "use strict";
    
    $('.buyBtn').click(function() {
        var modal = $('#BuyModal');
        modal.find('input[name=id]').val($(this).data('id'));
        modal.removeClass('hidden');
    });
    
    function closeModal() {
        $('#BuyModal').addClass('hidden');
    }
    
    // Make closeModal globally available
    window.closeModal = closeModal;
    
})(jQuery);
</script>
@endpush
