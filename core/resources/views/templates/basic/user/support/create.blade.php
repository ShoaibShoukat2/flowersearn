@php
    $pageTitle = 'Create Support Ticket';
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
                        <i class="bi bi-plus-circle text-white text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-center mb-2">Create Support Ticket</h2>
                <p class="text-white/90 text-center">Get help from our support team</p>
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        </div>
    </div>

    <!-- Back to Tickets Button -->
    <div class="flex justify-end mb-6">
        <a href="{{route('ticket')}}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors shadow-sm">
            <i class="bi bi-arrow-left mr-2"></i>My Support Tickets
        </a>
    </div>

    <!-- Create Ticket Form -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-light border border-border-light rounded-xl p-6 shadow-sm">
            <form action="{{route('ticket.store')}}" method="post" enctype="multipart/form-data" onsubmit="return submitUserForm();" class="space-y-6">
                @csrf
                
                <!-- Personal Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <div class="relative">
                            <input type="text" name="name" value="{{ $user->fullname }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent pl-12" 
                                   required readonly>
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <i class="bi bi-person text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <input type="email" name="email" value="{{@$user->email}}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent pl-12" 
                                   required readonly>
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <i class="bi bi-envelope text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ticket Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <div class="relative">
                            <input type="text" name="subject" value="{{old('subject')}}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent pl-12" 
                                   placeholder="Enter ticket subject" required>
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <i class="bi bi-chat-text text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <div class="relative">
                            <select name="priority" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent pl-12" required>
                                <option value="3">High Priority</option>
                                <option value="2" selected>Medium Priority</option>
                                <option value="1">Low Priority</option>
                            </select>
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <i class="bi bi-flag text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea name="message" id="inputMessage" rows="6" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" 
                              placeholder="Describe your issue in detail..." required>{{old('message')}}</textarea>
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
                                <input type="file" name="attachments[]" id="inputAttachments" 
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

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" id="recaptcha" class="w-full gradient-bg text-white py-3 px-6 rounded-lg font-medium hover:opacity-90 transition-opacity shadow-sm">
                        <i class="bi bi-send mr-2"></i>Submit Support Ticket
                    </button>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-lightbulb text-yellow-600"></i>
                </div>
                <div>
                    <h4 class="text-yellow-800 font-semibold text-sm mb-2">Tips for Better Support</h4>
                    <ul class="text-yellow-700 text-xs space-y-1">
                        <li>• Be specific about your issue</li>
                        <li>• Include screenshots if relevant</li>
                        <li>• Mention steps you've already tried</li>
                        <li>• Check our FAQ before creating a ticket</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
(function ($) {
    "use strict";
    var fileAdded = 0;
    
    $('.addFile').on('click', function(){
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
    
    $(document).on('click', '.remove-btn', function(){
        fileAdded--;
        $(this).closest('.file-upload-item').remove();
    });
})(jQuery);
</script>
@endpush
