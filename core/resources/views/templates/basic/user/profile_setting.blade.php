@php
    $pageTitle = 'Profile Settings';
@endphp

@extends($activeTemplate.'layouts.dashboard')

@section('content')
<div class="fade-in">
    <div class="max-w-2xl mx-auto">
        <div class="bg-light border border-border-light rounded-xl p-6 shadow-sm">
            <div class="flex flex-col items-center mb-8">
                <div class="w-24 h-24 mb-4 rounded-full gradient-bg flex items-center justify-center">
                    <i class="bi bi-person-fill text-white text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $user->fullname }}</h2>
                <p class="text-gray-500">{{$user->email}}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-light-gray rounded-lg p-4 border">
                    <h3 class="text-gray-800 font-semibold mb-2">Contact Information</h3>
                    <div class="space-y-2">
                        <p class="text-sm text-gray-500">Email: <span class="text-gray-800">{{$user->email}}</span></p>
                        <p class="text-sm text-gray-500">Mobile: <span class="text-gray-800">{{$user->mobile}}</span></p>
                    </div>
                </div>
                
                <div class="bg-light-gray rounded-lg p-4 border">
                    <h3 class="text-gray-800 font-semibold mb-2">Account Details</h3>
                    <div class="space-y-2">
                        <p class="text-sm text-gray-500">Level: <span class="text-gray-800">{{$user->level}}</span></p>
                        <p class="text-sm text-gray-500">Daily Limit: <span class="text-gray-800">{{$user->daily_limit}}</span></p>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex justify-center">
                <a href="{{ route('user.profile.setting') }}" class="gradient-bg px-6 py-3 rounded-lg text-white font-medium hover:opacity-90 transition-opacity shadow-sm">
                    Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection