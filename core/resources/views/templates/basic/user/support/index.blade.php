@php
    $pageTitle = 'Support Tickets';
@endphp

@extends($activeTemplate.'layouts.dashboard')

@section('content')
<div class="fade-in">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="gradient-bg rounded-2xl p-6 text-white relative overflow-hidden shadow-lg">
            <div class="relative z-10">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="bi bi-headset text-white text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-center mb-2">Support Tickets</h2>
                <p class="text-white/90 text-center">Get help from our support team</p>
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        </div>
    </div>

    <!-- New Ticket Button -->
    <div class="flex justify-end mb-6">
        <a href="{{route('ticket.open')}}" class="gradient-bg text-white px-6 py-3 rounded-lg font-medium hover:opacity-90 transition-opacity shadow-sm">
            <i class="bi bi-plus-circle mr-2"></i>New Ticket
        </a>
    </div>

    <!-- Tickets List -->
    <div class="bg-light border border-border-light rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border-light">
            <h3 class="text-lg font-semibold text-gray-800">Your Support Tickets</h3>
        </div>
        
        @if($supports->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-light-gray">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Reply</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-light">
                        @foreach($supports as $support)
                        <tr class="hover:bg-light-gray transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <a href="{{ route('ticket.view', $support->ticket) }}" class="text-primary hover:text-primary/80 font-medium">
                                        [Ticket#{{ $support->ticket }}]
                                    </a>
                                    <p class="text-sm text-gray-600 mt-1">{{ __($support->subject) }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($support->status == 0)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Open</span>
                                @elseif($support->status == 1)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Answered</span>
                                @elseif($support->status == 2)
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Customer Reply</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Closed</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($support->priority == 1)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Low</span>
                                @elseif($support->priority == 2)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Medium</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">High</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-800">{{ \Carbon\Carbon::parse($support->last_reply)->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('ticket.view', $support->ticket) }}" 
                                   class="w-8 h-8 bg-primary/10 text-primary rounded-lg flex items-center justify-center hover:bg-primary/20 transition-colors">
                                    <i class="bi bi-eye text-sm"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4 p-4">
                @foreach($supports as $support)
                <div class="bg-light-gray rounded-lg p-4 border">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <a href="{{ route('ticket.view', $support->ticket) }}" class="text-primary hover:text-primary/80 font-medium text-sm">
                                [Ticket#{{ $support->ticket }}]
                            </a>
                            <p class="text-sm text-gray-800 mt-1">{{ __($support->subject) }}</p>
                        </div>
                        @if($support->status == 0)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Open</span>
                        @elseif($support->status == 1)
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Answered</span>
                        @elseif($support->status == 2)
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Reply</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Closed</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            @if($support->priority == 1)
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Low</span>
                            @elseif($support->priority == 2)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Medium</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">High</span>
                            @endif
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($support->last_reply)->diffForHumans() }}</span>
                        </div>
                        <a href="{{ route('ticket.view', $support->ticket) }}" 
                           class="w-8 h-8 bg-primary/10 text-primary rounded-lg flex items-center justify-center hover:bg-primary/20 transition-colors">
                            <i class="bi bi-eye text-sm"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="bi bi-headset text-gray-400 text-5xl mb-4"></i>
                <h4 class="text-gray-600 font-semibold mb-2">No Support Tickets</h4>
                <p class="text-gray-500 mb-4">You haven't created any support tickets yet.</p>
                <a href="{{route('ticket.open')}}" class="gradient-bg px-6 py-3 rounded-lg text-white font-medium hover:opacity-90 transition-opacity shadow-sm">
                    Create First Ticket
                </a>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($supports->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Showing {{ $supports->firstItem() }} to {{ $supports->lastItem() }} of {{ $supports->total() }} results
        </div>
        <div class="pagination-container">
            {{ $supports->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
