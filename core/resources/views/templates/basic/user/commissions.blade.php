@php
    $pageTitle = 'Referral Commissions';
@endphp

@extends($activeTemplate.'layouts.dashboard')

@section('content')
@if (empty(auth()->user()->pv))
        @php redirect('/user/deposit')->send(); @endphp
@endif
@auth
    @if(auth()->user()->plan_id == 0)
        @php redirect('/user/pending')->send(); @endphp
    @endif
@endauth

<div class="fade-in">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="gradient-bg rounded-2xl p-6 text-white relative overflow-hidden shadow-lg">
            <div class="relative z-10">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="bi bi-people text-white text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-center mb-2">Referral Commissions</h2>
                <p class="text-white/90 text-center">Track your earnings from referrals</p>
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        </div>
    </div>

    <!-- Commission Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @php
            $totalCommissions = $commissions->sum('amount');
            $depositCommissions = $commissions->where('type', 'deposit_commission')->sum('amount');
            $planCommissions = $commissions->where('type', 'plan_subscribe_commission')->sum('amount');
            $ptcCommissions = $commissions->where('type', 'ptc_view_commission')->sum('amount');
        @endphp
        
        <div class="bg-light border border-border-light rounded-xl p-6 hover-scale shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-cash-stack text-green-600 text-xl"></i>
                </div>
                <span class="text-xs text-gray-500 bg-green-50 px-2 py-1 rounded-full">Total</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">Rs {{ number_format($totalCommissions, 2) }}</h3>
            <p class="text-sm text-gray-500">Total Commissions</p>
        </div>

        <div class="bg-light border border-border-light rounded-xl p-6 hover-scale shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-plus-circle text-blue-600 text-xl"></i>
                </div>
                <span class="text-xs text-gray-500 bg-blue-50 px-2 py-1 rounded-full">Deposit</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">Rs {{ number_format($depositCommissions, 2) }}</h3>
            <p class="text-sm text-gray-500">Deposit Commissions</p>
        </div>

        <div class="bg-light border border-border-light rounded-xl p-6 hover-scale shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-eye text-purple-600 text-xl"></i>
                </div>
                <span class="text-xs text-gray-500 bg-purple-50 px-2 py-1 rounded-full">Ads</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">Rs {{ number_format($ptcCommissions, 2) }}</h3>
            <p class="text-sm text-gray-500">Ad View Commissions</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-light border border-border-light rounded-xl p-6 mb-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Filter Commissions</h3>
            <button type="button" class="lg:hidden gradient-bg px-4 py-2 rounded-lg text-white text-sm" onclick="toggleFilter()">
                <i class="bi bi-funnel mr-2"></i>Filter
            </button>
        </div>
        
        <div id="filter-form" class="lg:block">
            <form action="" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">TRX/Username</label>
                        <input type="text" name="search" value="{{ request()->search }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Search transaction or username">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Commission Type</label>
                        <select name="remark" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">All Types</option>
                            <option value="deposit_commission" @selected(request()->remark == 'deposit_commission')>Deposit Commission</option>
                            <option value="plan_subscribe_commission" @selected(request()->remark == 'plan_subscribe_commission')>Plan Subscribe Commission</option>
                            <option value="ptc_view_commission" @selected(request()->remark == 'ptc_view_commission')>Advertisement View Commission</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                        <select name="level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">All Levels</option>
                            @for($i = 1; $i <= $levels; $i++)
                                <option value="{{ $i }}" @selected(request()->level == $i)>{{ ordinal($i) }} Level</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full gradient-bg px-6 py-2 rounded-lg text-white font-medium hover:opacity-90 transition-opacity">
                            <i class="bi bi-search mr-2"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Commissions List -->
    <div class="bg-light border border-border-light rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border-light">
            <h3 class="text-lg font-semibold text-gray-800">Commission History</h3>
        </div>
        
        @if($commissions->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-light-gray">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From User</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-light">
                        @foreach($commissions as $log)
                        <tr class="hover:bg-light-gray transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="bi bi-arrow-down-left text-green-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $log->trx }}</p>
                                        <p class="text-xs text-gray-500">Commission</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="bi bi-person text-blue-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm text-gray-800">{{ $log->userFrom->username }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                    {{ ordinal($log->level) }} Level
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->type == 'deposit_commission')
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Deposit</span>
                                @elseif($log->type == 'plan_subscribe_commission')
                                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Plan Subscribe</span>
                                @else
                                    <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">Ads View</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-green-600">+Rs {{ showAmount($log->amount) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4 p-4">
                @foreach($commissions as $log)
                <div class="bg-light-gray rounded-lg p-4 border">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="bi bi-arrow-down-left text-green-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $log->trx }}</p>
                                <p class="text-xs text-gray-500">From: {{ $log->userFrom->username }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-green-600">+Rs {{ showAmount($log->amount) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                            {{ ordinal($log->level) }} Level
                        </span>
                        @if($log->type == 'deposit_commission')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Deposit</span>
                        @elseif($log->type == 'plan_subscribe_commission')
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Plan Subscribe</span>
                        @else
                            <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">Ads View</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="bi bi-people text-gray-400 text-5xl mb-4"></i>
                <h4 class="text-gray-600 font-semibold mb-2">No Commissions Yet</h4>
                <p class="text-gray-500 mb-4">You haven't earned any referral commissions yet.</p>
                <a href="{{ route('user.referred') }}" class="gradient-bg px-6 py-3 rounded-lg text-white font-medium hover:opacity-90 transition-opacity shadow-sm">
                    View My Referrals
                </a>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($commissions->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Showing {{ $commissions->firstItem() }} to {{ $commissions->lastItem() }} of {{ $commissions->total() }} results
        </div>
        <div class="pagination-container">
            {{ paginateLinks($commissions) }}
        </div>
    </div>
    @endif
</div>

<script>
function toggleFilter() {
    const filterForm = document.getElementById('filter-form');
    filterForm.classList.toggle('hidden');
}

// Set selected values
document.addEventListener('DOMContentLoaded', function() {
    const remarkSelect = document.querySelector('[name=remark]');
    const levelSelect = document.querySelector('[name=level]');
    
    if (remarkSelect) remarkSelect.value = '{{ request()->remark }}';
    if (levelSelect) levelSelect.value = '{{ request()->level }}';
});
</script>
@endsection
