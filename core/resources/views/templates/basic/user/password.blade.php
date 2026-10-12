@php
    $pageTitle = 'Change Password';
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
                        <i class="bi bi-shield-lock text-white text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-center mb-2">Change Password</h2>
                <p class="text-white/90 text-center">Update your account password for security</p>
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        </div>
    </div>

    <!-- Password Change Form -->
    <div class="max-w-md mx-auto">
        <div class="bg-light border border-border-light rounded-xl p-6 shadow-sm">
            <form action="" method="post" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <div class="relative">
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent pl-12"
                               placeholder="Enter current password">
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                            <i class="bi bi-lock text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <div class="relative">
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent pl-12"
                               placeholder="Enter new password">
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                            <i class="bi bi-key text-gray-400"></i>
                        </div>
                    </div>
                    
                    @if($general->secure_password)
                        <div class="input-popup mt-2 p-3 bg-gray-50 rounded-lg border hidden">
                            <p class="text-xs text-gray-600 mb-2 font-medium">Password Requirements:</p>
                            <div class="space-y-1">
                                <p class="error lower text-xs flex items-center">
                                    <i class="bi bi-x-circle text-red-500 mr-2"></i>
                                    1 small letter minimum
                                </p>
                                <p class="error capital text-xs flex items-center">
                                    <i class="bi bi-x-circle text-red-500 mr-2"></i>
                                    1 capital letter minimum
                                </p>
                                <p class="error number text-xs flex items-center">
                                    <i class="bi bi-x-circle text-red-500 mr-2"></i>
                                    1 number minimum
                                </p>
                                <p class="error special text-xs flex items-center">
                                    <i class="bi bi-x-circle text-red-500 mr-2"></i>
                                    1 special character minimum
                                </p>
                                <p class="error minimum text-xs flex items-center">
                                    <i class="bi bi-x-circle text-red-500 mr-2"></i>
                                    6 character minimum
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent pl-12"
                               placeholder="Confirm new password">
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                            <i class="bi bi-check-circle text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full gradient-bg text-white py-3 px-6 rounded-lg font-medium hover:opacity-90 transition-opacity shadow-sm">
                    <i class="bi bi-shield-check mr-2"></i>Update Password
                </button>
            </form>
        </div>

        <!-- Security Tips -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-info-circle text-blue-600"></i>
                </div>
                <div>
                    <h4 class="text-blue-800 font-semibold text-sm mb-2">Security Tips</h4>
                    <ul class="text-blue-700 text-xs space-y-1">
                        <li>• Use a strong, unique password</li>
                        <li>• Don't share your password with anyone</li>
                        <li>• Change your password regularly</li>
                        <li>• Enable 2FA for extra security</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-lib')
<script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush

@push('script')
<script>
(function ($) {
    "use strict";
    
    @if($general->secure_password)
        $('input[name=password]').on('input', function(){
            secure_password($(this));
        });

        $('[name=password]').focus(function () {
            $(this).closest('.relative').find('.input-popup').removeClass('hidden');
        });

        $('[name=password]').focusout(function () {
            $(this).closest('.relative').find('.input-popup').addClass('hidden');
        });
    @endif
    
})(jQuery);
</script>

<style>
.input-popup .error {
    transition: all 0.3s ease;
}

.input-popup .error.valid {
    color: #10b981 !important;
}

.input-popup .error.valid i {
    color: #10b981 !important;
}

.input-popup .error.valid i:before {
    content: "\f26b" !important; /* bi-check-circle */
}
</style>
@endpush
