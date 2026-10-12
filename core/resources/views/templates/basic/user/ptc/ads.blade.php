@php
    $pageTitle = 'My Advertisements';
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
                        <i class="bi bi-megaphone text-white text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-center mb-2">My Advertisements</h2>
                <p class="text-white/90 text-center">Manage your created ads and track performance</p>
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        </div>
    </div>

    <!-- Create Ad Button -->
    <div class="flex justify-end mb-6">
        <a href="{{ route('user.ptc.create') }}" class="gradient-bg text-white px-6 py-3 rounded-lg font-medium hover:opacity-90 transition-opacity shadow-sm">
            <i class="bi bi-plus-circle mr-2"></i>Create Advertisement
        </a>
    </div>

    <!-- Ads List -->
    <div class="bg-light border border-border-light rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border-light">
            <h3 class="text-lg font-semibold text-gray-800">Your Advertisements</h3>
        </div>
        
        @if($ads->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-light-gray">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-light">
                        @foreach($ads as $ptc)
                        <tr class="hover:bg-light-gray transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ strLimit($ptc->title, 30) }}</p>
                                    <p class="text-xs text-gray-500">{{ $ptc->duration }} seconds</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($ptc->type == 1)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Link</span>
                                @elseif($ptc->type == 2)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Banner</span>
                                @else
                                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Script</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-800">{{ $ptc->duration }}s</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="text-gray-800 font-medium">{{ $ptc->showed }} / {{ $ptc->max_show }}</p>
                                    <p class="text-xs text-gray-500">{{ $ptc->remain }} remaining</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-gray-800">Rs {{ showAmount($ptc->amount) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($ptc->status == 1)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Active</span>
                                @elseif($ptc->status == 0)
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Inactive</span>
                                @elseif($ptc->status == 2)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Pending</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    @if ($ptc->status == 3)
                                        <button class="w-8 h-8 bg-gray-100 text-gray-400 rounded-lg flex items-center justify-center cursor-not-allowed" disabled>
                                            <i class="bi bi-pencil text-sm"></i>
                                        </button>
                                    @else
                                        <a href="{{route('user.ptc.edit',$ptc->id)}}" class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-200 transition-colors">
                                            <i class="bi bi-pencil text-sm"></i>
                                        </a>
                                    @endif
                                    
                                    @if($ptc->status == 1 || $ptc->status == 0)
                                        @if($ptc->status == 1)
                                            <a href="{{route('user.ptc.status',$ptc->id)}}" class="w-8 h-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition-colors">
                                                <i class="bi bi-eye-slash text-sm"></i>
                                            </a>
                                        @else
                                            <a href="{{route('user.ptc.status',$ptc->id)}}" class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-200 transition-colors">
                                                <i class="bi bi-eye text-sm"></i>
                                            </a>
                                        @endif
                                    @else
                                        <button class="w-8 h-8 bg-gray-100 text-gray-400 rounded-lg flex items-center justify-center cursor-not-allowed" disabled>
                                            <i class="bi bi-eye-slash text-sm"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden space-y-4 p-4">
                @foreach($ads as $ptc)
                <div class="bg-light-gray rounded-lg p-4 border">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-800 mb-1">{{ strLimit($ptc->title, 25) }}</h4>
                            <div class="flex items-center space-x-2 mb-2">
                                @if($ptc->type == 1)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Link</span>
                                @elseif($ptc->type == 2)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Banner</span>
                                @else
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Script</span>
                                @endif
                                <span class="text-xs text-gray-500">{{ $ptc->duration }}s</span>
                            </div>
                        </div>
                        @if($ptc->status == 1)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Active</span>
                        @elseif($ptc->status == 0)
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Inactive</span>
                        @elseif($ptc->status == 2)
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Pending</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rejected</span>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                        <div>
                            <p class="text-gray-500">Views</p>
                            <p class="text-gray-800 font-medium">{{ $ptc->showed }} / {{ $ptc->max_show }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Amount</p>
                            <p class="text-gray-800 font-medium">Rs {{ showAmount($ptc->amount) }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <span class="text-xs text-gray-500">{{ $ptc->remain }} views remaining</span>
                        <div class="flex items-center space-x-2">
                            @if ($ptc->status == 3)
                                <button class="w-8 h-8 bg-gray-100 text-gray-400 rounded-lg flex items-center justify-center cursor-not-allowed" disabled>
                                    <i class="bi bi-pencil text-sm"></i>
                                </button>
                            @else
                                <a href="{{route('user.ptc.edit',$ptc->id)}}" class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-200 transition-colors">
                                    <i class="bi bi-pencil text-sm"></i>
                                </a>
                            @endif
                            
                            @if($ptc->status == 1 || $ptc->status == 0)
                                @if($ptc->status == 1)
                                    <a href="{{route('user.ptc.status',$ptc->id)}}" class="w-8 h-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition-colors">
                                        <i class="bi bi-eye-slash text-sm"></i>
                                    </a>
                                @else
                                    <a href="{{route('user.ptc.status',$ptc->id)}}" class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-200 transition-colors">
                                        <i class="bi bi-eye text-sm"></i>
                                    </a>
                                @endif
                            @else
                                <button class="w-8 h-8 bg-gray-100 text-gray-400 rounded-lg flex items-center justify-center cursor-not-allowed" disabled>
                                    <i class="bi bi-eye-slash text-sm"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="bi bi-megaphone text-gray-400 text-5xl mb-4"></i>
                <h4 class="text-gray-600 font-semibold mb-2">No Advertisements Yet</h4>
                <p class="text-gray-500 mb-4">You haven't created any advertisements yet.</p>
                <a href="{{ route('user.ptc.create') }}" class="gradient-bg px-6 py-3 rounded-lg text-white font-medium hover:opacity-90 transition-opacity shadow-sm">
                    Create First Advertisement
                </a>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($ads->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Showing {{ $ads->firstItem() }} to {{ $ads->lastItem() }} of {{ $ads->total() }} results
        </div>
        <div class="pagination-container">
            {{ $ads->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
