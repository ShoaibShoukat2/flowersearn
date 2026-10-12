@php
    $pageTitle = 'Click Earnings';
@endphp

@extends($activeTemplate.'layouts.dashboard')

@section('content')
@if (empty(auth()->user()->pv))
        @php redirect('/user/deposit')->send(); @endphp
@elseif (in_array(auth()->user()->pv, ['rt']))
        @php redirect('/user/pending')->send(); @endphp
@endif

<div class="fade-in">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="gradient-bg rounded-2xl p-6 text-white relative overflow-hidden shadow-lg">
            <div class="relative z-10">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="bi bi-graph-up text-white text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-center mb-2">Click Earnings</h2>
                <p class="text-white/90 text-center">Track your daily PTC earnings and clicks</p>
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        </div>
    </div>

    <!-- Summary Stats -->
    @php
        $totalClicks = $viewads->sum('total_clicks');
        $totalEarned = $viewads->sum('total_earned');
        $avgDaily = $viewads->count() > 0 ? $totalEarned / $viewads->count() : 0;
    @endphp
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-light border border-border-light rounded-xl p-6 hover-scale shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-mouse text-blue-600 text-xl"></i>
                </div>
                <span class="text-xs text-gray-500 bg-blue-50 px-2 py-1 rounded-full">Total</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ number_format($totalClicks) }}</h3>
            <p class="text-sm text-gray-500">Total Clicks</p>
        </div>

        <div class="bg-light border border-border-light rounded-xl p-6 hover-scale shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-cash text-green-600 text-xl"></i>
                </div>
                <span class="text-xs text-gray-500 bg-green-50 px-2 py-1 rounded-full">Earned</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">Rs {{ number_format($totalEarned, 2) }}</h3>
            <p class="text-sm text-gray-500">Total Earned</p>
        </div>

        <div class="bg-light border border-border-light rounded-xl p-6 hover-scale shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-calendar-day text-purple-600 text-xl"></i>
                </div>
                <span class="text-xs text-gray-500 bg-purple-50 px-2 py-1 rounded-full">Average</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">Rs {{ number_format($avgDaily, 2) }}</h3>
            <p class="text-sm text-gray-500">Daily Average</p>
        </div>
    </div>

    <!-- Earnings History -->
    <div class="bg-light border border-border-light rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border-light">
            <h3 class="text-lg font-semibold text-gray-800">Daily Earnings History</h3>
        </div>
        
        @if($viewads->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-light-gray">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Clicks</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Earned</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Per Click</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-light">
                        @foreach($viewads as $view)
                        <tr class="hover:bg-light-gray transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="bi bi-calendar text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ showDateTime($view->date, 'M d, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ showDateTime($view->date, 'l') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <i class="bi bi-mouse text-orange-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">{{ number_format($view->total_clicks) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="bi bi-cash text-green-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-green-600">Rs {{ showAmount($view->total_earned) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">
                                    Rs {{ $view->total_clicks > 0 ? number_format($view->total_earned / $view->total_clicks, 4) : '0.0000' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4 p-4">
                @foreach($viewads as $view)
                <div class="bg-light-gray rounded-lg p-4 border">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="bi bi-calendar text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ showDateTime($view->date, 'M d, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ showDateTime($view->date, 'l') }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-green-600">Rs {{ showAmount($view->total_earned) }}</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="bi bi-mouse text-orange-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Clicks</p>
                                <p class="text-gray-800 font-medium">{{ number_format($view->total_clicks) }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-500">Per Click</p>
                            <p class="text-gray-800 font-medium">
                                Rs {{ $view->total_clicks > 0 ? number_format($view->total_earned / $view->total_clicks, 4) : '0.0000' }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="bi bi-graph-up text-gray-400 text-5xl mb-4"></i>
                <h4 class="text-gray-600 font-semibold mb-2">No Earnings Yet</h4>
                <p class="text-gray-500 mb-4">Start clicking ads to see your earnings history here.</p>
                <a href="{{ route('user.ptc.index') }}" class="gradient-bg px-6 py-3 rounded-lg text-white font-medium hover:opacity-90 transition-opacity shadow-sm">
                    Start Earning
                </a>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($viewads->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Showing {{ $viewads->firstItem() }} to {{ $viewads->lastItem() }} of {{ $viewads->total() }} results
        </div>
        <div class="pagination-container">
            {{ paginateLinks($viewads) }}
        </div>
    </div>
    @endif
</div>
@endsection
