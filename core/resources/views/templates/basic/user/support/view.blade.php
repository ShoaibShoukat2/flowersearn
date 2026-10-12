@php
    $pageTitle = 'Support Ticket #' . $myTicket->ticket;
@endphp

@extends($activeTemplate.'layouts.dashboard')

@section('content')
<div class="fade-in">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="gradient-bg rounded-2xl p-6 text-white relative overflow-hidden shadow-lg">
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="bi bi-chat-dots text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold mb-1">Ticket #{{ $myTicket->ticket }}</h2>
                            <p class="text-white/90">{{ $myTicket->subject }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($myTicket->status == 0)
                            <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">Open</span>
                        @elseif($myTicket->status == 1)
                            <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">Answered</span>
                        @elseif($myTicket->status == 2)
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">Customer Reply</span>
                        @else
                            <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-medium">Closed</span>
                        @endif
                        
                        @if ($myTicket->status != 3 && $myTicket->user)
                            <button class="confirmationBtn bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-lg transition-colors" 
                                    data-question="Are you sure to close this ticket?" 
                                    data-action="{{ route('ticket.close', $myTicket->id) }}">
                                <i class="bi bi-x-circle text-lg"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="flex justify-start mb-6">
        <a href="{{route('ticket')}}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors shadow-sm">
            <i class="bi bi-arrow-left mr-2"></i>Back to Tickets
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Reply Form -->
        @if ($myTicket->status != 4)
        <div class="lg:col-span-2">
            <div class="bg-light border border-border-light rounded-xl p-6 shadow-sm mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Reply to Ticket</h3>
                
                <form method="post" action="{{ route('ticket.reply', $myTicket->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Reply</label>
                        <textarea name="message" rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" 
                                  placeholder="Type your reply here...">{{ old('message') }}</textarea>
                    </div>

                    <!-- File Attachments -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Attachments</label>
                                <p class="text-xs text-gray-500">Max 5 files. Maximum upload size: {{ ini_get('upload_max_filesize') }}</p>
                            </div>
                            <button type="button" class="addFile bg-primary/10 text-primary px-4 py-2 rounded-lg hover:bg-primary/20 transition-colors text-sm font-medium">
                                <i class="bi bi-plus mr-2"></i>Add File
                            </button>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="flex-1">
                                    <input type="file" name="attachments[]" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"/>
                                </div>
                            </div>
                            <div id="fileUploadsContainer" class="space-y-3"></div>
                        </div>
                        
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-blue-700 text-xs">
                                <i class="bi bi-info-circle mr-1"></i>
                                Allowed file types: .jpg, .jpeg, .png, .pdf, .doc, .docx
                            </p>
                        </div>
                    </div>

                    <button type="submit" class="w-full gradient-bg text-white py-3 px-6 rounded-lg font-medium hover:opacity-90 transition-opacity shadow-sm">
                        <i class="bi bi-reply mr-2"></i>Send Reply
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="lg:col-span-2">
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
                <i class="bi bi-lock text-red-600 text-4xl mb-4"></i>
                <h3 class="text-red-800 font-semibold mb-2">Ticket Closed</h3>
                <p class="text-red-700">This ticket has been closed and no longer accepts replies.</p>
            </div>
        </div>
        @endif

        <!-- Ticket Info Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-light border border-border-light rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ticket Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Ticket ID</p>
                        <p class="font-semibold text-gray-800">#{{ $myTicket->ticket }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Subject</p>
                        <p class="font-semibold text-gray-800">{{ $myTicket->subject }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Priority</p>
                        @if($myTicket->priority == 1)
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Low</span>
                        @elseif($myTicket->priority == 2)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Medium</span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">High</span>
                        @endif
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Created</p>
                        <p class="font-semibold text-gray-800">{{ $myTicket->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Last Reply</p>
                        <p class="font-semibold text-gray-800">{{ $myTicket->last_reply ? \Carbon\Carbon::parse($myTicket->last_reply)->format('M d, Y h:i A') : 'No replies yet' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    <div class="mt-8">
        <div class="bg-light border border-border-light rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border-light">
                <h3 class="text-lg font-semibold text-gray-800">Conversation</h3>
            </div>
            
            <div class="p-6 space-y-6">
                @foreach ($messages as $message)
                    @if ($message->admin_id == 0)
                        <!-- User Message -->
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-person text-blue-600"></i>
                            </div>
                            <div class="flex-1 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-800">{{ $message->ticket->name }}</h4>
                                    <span class="text-xs text-gray-500">{{ $message->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <p class="text-gray-700 mb-3">{{ $message->message }}</p>
                                
                                @if ($message->attachments->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($message->attachments as $k => $image)
                                            <a href="{{ route('ticket.download', encrypt($image->id)) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs hover:bg-blue-200 transition-colors">
                                                <i class="bi bi-paperclip mr-1"></i>
                                                Attachment {{ ++$k }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Admin Message -->
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-person-badge text-orange-600"></i>
                            </div>
                            <div class="flex-1 bg-orange-50 border border-orange-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $message->admin->name }}</h4>
                                        <span class="text-xs text-orange-600 font-medium">Support Staff</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $message->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <p class="text-gray-700 mb-3">{{ $message->message }}</p>
                                
                                @if ($message->attachments->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($message->attachments as $k => $image)
                                            <a href="{{ route('ticket.download', encrypt($image->id)) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-xs hover:bg-orange-200 transition-colors">
                                                <i class="bi bi-paperclip mr-1"></i>
                                                Attachment {{ ++$k }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

<x-confirmation-modal></x-confirmation-modal>
@endsection

@push('script')
<script>
(function($) {
    "use strict";
    var fileAdded = 0;
    
    $('.addFile').on('click', function() {
        if (fileAdded >= 4) {
            if (typeof Toastify !== 'undefined') {
                Toastify({
                    text: "You've added maximum number of files (5 total)",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444",
                }).showToast();
            } else {
                alert("You've added maximum number of files");
            }
            return false;
        }
        fileAdded++;
        $("#fileUploadsContainer").append(`
            <div class="flex items-center space-x-3 file-upload-item">
                <div class="flex-1">
                    <input type="file" name="attachments[]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required />
                </div>
                <button type="button" class="remove-btn w-10 h-10 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors flex items-center justify-center">
                    <i class="bi bi-x text-lg"></i>
                </button>
            </div>
        `);
    });
    
    $(document).on('click', '.remove-btn', function() {
        fileAdded--;
        $(this).closest('.file-upload-item').remove();
    });
})(jQuery);
</script>
@endpush
