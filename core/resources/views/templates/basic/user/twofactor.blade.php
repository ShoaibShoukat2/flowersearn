@php
    $pageTitle = '2FA Security';
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
                <h2 class="text-2xl font-bold text-center mb-2">2FA Security</h2>
                <p class="text-white/90 text-center">Secure your account with two-factor authentication</p>
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @if(!auth()->user()->ts)
        <!-- Setup 2FA -->
        <div class="bg-light border border-border-light rounded-xl p-6 shadow-sm">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-qr-code text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Setup Google Authenticator</h3>
                <p class="text-sm text-gray-600">Scan the QR code or use the setup key</p>
            </div>

            <!-- QR Code -->
            <div class="text-center mb-6">
                <div class="inline-block p-4 bg-white border border-gray-200 rounded-lg shadow-sm relative">
                    <!-- Loading indicator -->
                    <div id="qr-loading" class="w-48 h-48 mx-auto flex items-center justify-center bg-gray-50 rounded-lg">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
                            <p class="text-gray-500 text-sm">Loading QR Code...</p>
                        </div>
                    </div>
                    
                    <img src="{{$qrCodeUrl}}" alt="QR Code" class="w-48 h-48 mx-auto hidden" id="qr-image"
                         onerror="handleQRError()" 
                         onload="handleQRLoad()">
                    
                    <!-- Error fallback -->
                    <div id="qr-error" style="display: none;" class="w-48 h-48 mx-auto flex flex-col items-center justify-center bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg">
                        <i class="bi bi-exclamation-triangle text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500 text-sm text-center">QR Code failed to load.<br>Please use the setup key below.</p>
                    </div>
                </div>
                
                <!-- Alternative QR Code URLs -->
                <div class="mt-4 space-y-2">
                    <p class="text-xs text-gray-500">If QR code doesn't load, try these alternatives:</p>
                    <div class="flex flex-col space-y-1">
                        <a href="{{$qrCodeUrl}}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800">Open QR Code in new tab</a>
                        <button type="button" onclick="tryAlternativeQR()" class="text-xs text-blue-600 hover:text-blue-800">Try alternative QR service</button>
                    </div>
                </div>
            </div>

            <!-- Setup Key -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Setup Key</label>
                <div class="flex">
                    <input type="text" value="{{$secret}}" 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg bg-gray-50 text-sm font-mono" 
                           readonly id="setupKey">
                    <button type="button" 
                            class="px-4 py-2 bg-primary text-white rounded-r-lg hover:bg-primary/90 transition-colors"
                            onclick="copySetupKey()">
                        <i class="bi bi-copy"></i>
                    </button>
                </div>
            </div>

            <!-- Help Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-info-circle text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="text-blue-800 font-semibold text-sm mb-2">How to Setup</h4>
                        <p class="text-blue-700 text-xs mb-2">
                            Google Authenticator is a multifactor app for mobile devices. It generates timed codes used during the 2-step verification process.
                        </p>
                        <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en" 
                           target="_blank" 
                           class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                            Download Google Authenticator →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Enable/Disable 2FA -->
        <div class="bg-light border border-border-light rounded-xl p-6 shadow-sm">
            @if(auth()->user()->ts)
                <!-- Disable 2FA -->
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-shield-x text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Disable 2FA Security</h3>
                    <p class="text-sm text-gray-600">Enter your authenticator code to disable 2FA</p>
                </div>

                <form action="{{route('user.twofactor.disable')}}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="key" value="{{$secret}}">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Google Authenticator OTP</label>
                        <div class="relative">
                            <input type="text" name="code" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent pl-12"
                                   placeholder="Enter 6-digit code">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <i class="bi bi-key text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg font-medium transition-colors shadow-sm">
                        <i class="bi bi-shield-x mr-2"></i>Disable 2FA
                    </button>
                </form>

                <!-- Current Status -->
                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="text-green-800 font-semibold text-sm">2FA is Currently Enabled</h4>
                            <p class="text-green-700 text-xs">Your account is protected with two-factor authentication</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Enable 2FA -->
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-shield-check text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Enable 2FA Security</h3>
                    <p class="text-sm text-gray-600">Enter the code from your authenticator app</p>
                </div>

                <form action="{{ route('user.twofactor.enable') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="key" value="{{$secret}}">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Google Authenticator OTP</label>
                        <div class="relative">
                            <input type="text" name="code" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent pl-12"
                                   placeholder="Enter 6-digit code">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <i class="bi bi-key text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full gradient-bg text-white py-3 px-6 rounded-lg font-medium hover:opacity-90 transition-opacity shadow-sm">
                        <i class="bi bi-shield-check mr-2"></i>Enable 2FA
                    </button>
                </form>

                <!-- Current Status -->
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <div>
                            <h4 class="text-yellow-800 font-semibold text-sm">2FA is Currently Disabled</h4>
                            <p class="text-yellow-700 text-xs">Enable 2FA to add an extra layer of security to your account</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
function copySetupKey() {
    const setupKey = document.getElementById('setupKey');
    setupKey.select();
    setupKey.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Show feedback
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="bi bi-check"></i>';
    button.classList.add('bg-green-600');
    button.classList.remove('bg-primary');
    
    setTimeout(() => {
        button.innerHTML = originalContent;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-primary');
    }, 1500);
}

function tryAlternativeQR() {
    const qrImg = document.getElementById('qr-image');
    const secret = document.getElementById('setupKey').value;
    
    // Show loading
    document.getElementById('qr-loading').style.display = 'flex';
    qrImg.style.display = 'none';
    document.getElementById('qr-error').style.display = 'none';
    
    // Use the backend route to get multiple QR code URLs
    fetch(`{{ route('user.generate.qr.code') }}?secret=${secret}`)
        .then(response => response.json())
        .then(data => {
            if (data.urls) {
                // Try QR Server first
                qrImg.src = data.urls.qrserver;
                qrImg.onload = handleQRLoad;
                qrImg.onerror = function() {
                    // Try Google Charts
                    this.src = data.urls.google;
                    this.onerror = function() {
                        // Try QuickChart
                        this.src = data.urls.quickchart;
                        this.onerror = function() {
                            // All failed, show manual setup
                            showManualSetup();
                        };
                    };
                };
            } else {
                showManualSetup();
            }
        })
        .catch(error => {
            console.error('Error fetching QR codes:', error);
            showManualSetup();
        });
}

function showManualSetup() {
    const secret = document.getElementById('setupKey').value;
    const username = '{{ auth()->user()->username }}';
    
    document.getElementById('qr-loading').style.display = 'none';
    document.getElementById('qr-image').style.display = 'none';
    
    const errorDiv = document.getElementById('qr-error');
    errorDiv.style.display = 'flex';
    errorDiv.innerHTML = `
        <div class="text-center">
            <i class="bi bi-info-circle text-blue-400 text-3xl mb-2"></i>
            <p class="text-gray-600 text-sm text-center mb-2">Manual Setup Required</p>
            <p class="text-xs text-gray-500 text-center">
                Open your authenticator app and manually add:<br>
                <strong>Account:</strong> ${username}<br>
                <strong>Key:</strong> ${secret}
            </p>
        </div>
    `;
}

// Auto-retry QR code loading after 3 seconds if it fails initially
document.addEventListener('DOMContentLoaded', function() {
    const qrImg = document.getElementById('qr-image');
    if (qrImg) {
        // Show the image and hide loading after a short delay
        setTimeout(() => {
            qrImg.classList.remove('hidden');
            document.getElementById('qr-loading').style.display = 'none';
        }, 1000);
        
        // Auto-retry if still not loaded after 5 seconds
        setTimeout(() => {
            if (qrImg.style.display === 'none' || document.getElementById('qr-error').style.display === 'block') {
                tryAlternativeQR();
            }
        }, 5000);
    }
});

function handleQRLoad() {
    document.getElementById('qr-loading').style.display = 'none';
    document.getElementById('qr-image').classList.remove('hidden');
    document.getElementById('qr-error').style.display = 'none';
}

function handleQRError() {
    document.getElementById('qr-loading').style.display = 'none';
    document.getElementById('qr-image').style.display = 'none';
    document.getElementById('qr-error').style.display = 'block';
}
</script>
@endpush
