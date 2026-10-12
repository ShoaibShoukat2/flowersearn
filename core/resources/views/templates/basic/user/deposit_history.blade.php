@php
    $pageTitle = 'Deposit History';
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
                        <i class="bi bi-clock-history text-white text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-center mb-2">Deposit History</h2>
                <p class="text-white/90 text-center">Track all your deposit transactions</p>
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-light border border-border-light rounded-xl p-6 mb-6 shadow-sm">
        <form action="">
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request()->search }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="Search by transaction ID or gateway">
                </div>
                <button type="submit" class="gradient-bg px-6 py-2 rounded-lg text-white font-medium hover:opacity-90 transition-opacity">
                    <i class="bi bi-search mr-2"></i>Search
                </button>
            </div>
        </form>
    </div>

    <!-- Deposits List -->
    <div class="bg-light border border-border-light rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border-light">
            <h3 class="text-lg font-semibold text-gray-800">Deposit Transactions</h3>
        </div>
        
        @if($deposits->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-light-gray">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gateway | Transaction</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conversion</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-light">
                        @foreach($deposits as $deposit)
                        <tr class="hover:bg-light-gray transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-primary">{{ __($deposit->gateway?->name) }}</p>
                                    <p class="text-xs text-gray-500">{{ $deposit->trx }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-800">{{ showDateTime($deposit->created_at, 'M d, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ diffForHumans($deposit->created_at) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-800">{{ __($general->cur_sym) }}{{ showAmount($deposit->amount) }}</p>
                                <p class="text-xs text-red-600">+ {{ showAmount($deposit->charge) }} charge</p>
                                <p class="text-xs text-gray-500 font-medium">Total: {{ showAmount($deposit->amount+$deposit->charge) }} {{ __($general->cur_text) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-gray-600">1 {{ __($general->cur_text) }} = {{ showAmount($deposit->rate) }} {{ __($deposit->method_currency) }}</p>
                                <p class="text-sm font-medium text-gray-800">{{ showAmount($deposit->final_amo) }} {{ __($deposit->method_currency) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($deposit->status == 1)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Success</span>
                                @elseif($deposit->status == 2)
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>
                                @elseif($deposit->status == 3)
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rejected</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Initiated</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $details = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
                                @endphp
                                <button class="w-8 h-8 bg-primary/10 text-primary rounded-lg flex items-center justify-center hover:bg-primary/20 transition-colors @if($deposit->method_code >= 1000) detailBtn @else opacity-50 cursor-not-allowed @endif"
                                        @if($deposit->method_code >= 1000)
                                            data-info="{{ $details }}"
                                        @endif
                                        @if ($deposit->status == 3)
                                            data-admin_feedback="{{ $deposit->admin_feedback }}"
                                        @endif
                                        @if($deposit->method_code < 1000) disabled @endif>
                                    <i class="bi bi-eye text-sm"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4 p-4">
                @foreach($deposits as $deposit)
                <div class="bg-light-gray rounded-lg p-4 border">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-sm font-medium text-primary">{{ __($deposit->gateway?->name) }}</p>
                            <p class="text-xs text-gray-500">{{ $deposit->trx }}</p>
                        </div>
                        @if($deposit->status == 1)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Success</span>
                        @elseif($deposit->status == 2)
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>
                        @elseif($deposit->status == 3)
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rejected</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Initiated</span>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Amount</p>
                            <p class="text-gray-800 font-semibold">{{ __($general->cur_sym) }}{{ showAmount($deposit->amount+$deposit->charge) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Date</p>
                            <p class="text-gray-800">{{ showDateTime($deposit->created_at, 'M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between items-center">
                        <div class="text-xs text-gray-600">
                            {{ showAmount($deposit->final_amo) }} {{ __($deposit->method_currency) }}
                        </div>
                        @php
                            $details = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
                        @endphp
                        <button class="w-8 h-8 bg-primary/10 text-primary rounded-lg flex items-center justify-center hover:bg-primary/20 transition-colors @if($deposit->method_code >= 1000) detailBtn @else opacity-50 cursor-not-allowed @endif"
                                @if($deposit->method_code >= 1000)
                                    data-info="{{ $details }}"
                                @endif
                                @if ($deposit->status == 3)
                                    data-admin_feedback="{{ $deposit->admin_feedback }}"
                                @endif
                                @if($deposit->method_code < 1000) disabled @endif>
                            <i class="bi bi-eye text-sm"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="bi bi-clock-history text-gray-400 text-5xl mb-4"></i>
                <h4 class="text-gray-600 font-semibold mb-2">No Deposits Found</h4>
                <p class="text-gray-500 mb-4">You haven't made any deposits yet or no results match your search.</p>
                <a href="{{ route('user.deposit') }}" class="gradient-bg px-6 py-3 rounded-lg text-white font-medium hover:opacity-90 transition-opacity shadow-sm">
                    Make First Deposit
                </a>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($deposits->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Showing {{ $deposits->firstItem() }} to {{ $deposits->lastItem() }} of {{ $deposits->total() }} results
        </div>
        <div class="pagination-container">
            {{ $deposits->links() }}
        </div>
    </div>
    @endif
</div>

{{-- DETAILS MODAL --}}
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-light rounded-xl max-w-md w-full max-h-[85vh] overflow-y-auto shadow-2xl">
        <div class="p-6 border-b border-border-light">
            <div class="flex items-center justify-between">
                <h5 class="text-lg font-semibold text-gray-800">Transaction Details</h5>
                <button type="button" class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors" onclick="closeModal()">
                    <i class="bi bi-x text-gray-600"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="userData space-y-3 mb-4"></div>
            <div class="feedback"></div>
        </div>
        <div class="p-6 border-t border-border-light">
            <button type="button" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded-lg transition-colors" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
(function ($) {
    "use strict";
    
    $('.detailBtn').on('click', function () {
        var modal = $('#detailModal');
        var userData = $(this).data('info');
        var html = '';
        
        var verifyBase = @json(asset(getFilePath('verify')));
        if(userData){
            userData.forEach(element => {
                if(element.type == 'file'){
                    if(element.value){
                        var fileName = String(element.value).replace(/[^a-zA-Z0-9_./-]/g, '');
                        var src = verifyBase.replace(/\/$/, '') + '/' + fileName.replace(/^\/+/, '');
                        html += `
                        <div class="py-2 border-b border-gray-100">
                            <span class="text-gray-600 text-sm">${$('<div>').text(element.name || 'Payment Screenshot').html()}</span>
                            <a href="${src}" target="_blank" rel="noopener">
                                <img src="${src}" alt="Payment screenshot" class="mt-2 w-full rounded-lg border border-gray-200" style="max-height:280px;object-fit:contain;background:#fff;">
                            </a>
                        </div>`;
                    }
                    return;
                }
                var shown = Array.isArray(element.value) ? element.value.join(', ') : (element.value == null ? '' : element.value);
                html += `
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 text-sm">${$('<div>').text(element.name || '').html()}</span>
                    <span class="text-gray-800 text-sm font-medium">${$('<div>').text(shown).html()}</span>
                </div>`;
            });
        }

        modal.find('.userData').html(html);

        if($(this).data('admin_feedback') != undefined){
            var adminFeedback = `
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h6 class="text-red-800 font-semibold mb-2">Admin Feedback</h6>
                    <p class="text-red-700 text-sm">${$(this).data('admin_feedback')}</p>
                </div>
            `;
        } else {
            var adminFeedback = '';
        }

        modal.find('.feedback').html(adminFeedback);
        modal.removeClass('hidden');
    });
    
    function closeModal() {
        $('#detailModal').addClass('hidden');
    }
    
    // Make closeModal globally available
    window.closeModal = closeModal;
    
})(jQuery);
</script>
@endpush
