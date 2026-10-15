@php $pageTitle = 'Official Channels'; @endphp
@extends($activeTemplate.'layouts.dashboard')

@section('content')
@php
    $channels = isset($channels) ? $channels : officialChannels();
@endphp
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.oc-wrap{font-family:'Poppins',sans-serif;max-width:520px;margin:0 auto;padding:0 0 2.4rem;}
.oc-hero{
    background:linear-gradient(145deg,#25d366 0%,#128C7E 100%);
    border-radius:0 0 28px 28px;padding:2rem 1.4rem 2.2rem;text-align:center;color:#fff;
    margin-bottom:1.2rem;
}
.oc-hero h1{font-size:1.55rem;font-weight:800;margin:0.4rem 0;}
.oc-hero p{font-size:0.86rem;opacity:.92;margin:0;}
.oc-list{padding:0 1rem;display:flex;flex-direction:column;gap:12px;}
.oc-card{
    background:#fff;border:1.5px solid #d9f5e5;border-radius:18px;
    padding:1.05rem 1.05rem 1rem;box-shadow:0 4px 16px rgba(18,140,126,.08);
}
.oc-top{display:flex;align-items:flex-start;gap:12px;}
.oc-icon{
    width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;
    background:#25d366;color:#fff;font-size:1.25rem;flex-shrink:0;
}
.oc-photo{
    width:56px;height:56px;border-radius:50%;object-fit:cover;flex-shrink:0;
    border:2px solid #d9f5e5;background:#fff;
}
.oc-card h4{margin:0 0 4px;font-size:1rem;font-weight:800;color:#16382f;}
.oc-purpose{margin:0;font-size:.8rem;font-weight:700;color:#128C7E;line-height:1.45;}
.oc-desc{margin:.55rem 0 0;font-size:.8rem;color:#667;line-height:1.5;}
.oc-join{
    display:inline-flex;align-items:center;justify-content:center;gap:8px;
    margin-top:.9rem;background:#128C7E;color:#fff;text-decoration:none;
    border-radius:12px;padding:10px 16px;font-size:.86rem;font-weight:700;
}
.oc-empty{
    margin:0 1rem;background:#fff;border:1.5px solid #d9f5e5;border-radius:18px;
    padding:1.4rem;text-align:center;color:#888;font-size:.86rem;
}
</style>

<div class="oc-wrap">
    <div class="oc-hero">
        <div style="font-size:2.4rem;">📢</div>
        <h1>Official Channels</h1>
        <p>See what each channel is for, then join the one you need.</p>
    </div>

    @if($channels->count())
        <div class="oc-list">
            @foreach($channels as $channel)
                @php
                    $channelPhoto = officialChannelImageUrl($channel);
                    $icon = trim((string) ($channel->icon ?: 'fab fa-whatsapp'));
                    $isIcon = str_contains($icon, 'fa-');
                @endphp
                <div class="oc-card">
                    <div class="oc-top">
                        @if($channelPhoto)
                            <img src="{{ $channelPhoto }}" alt="{{ $channel->title }}" class="oc-photo">
                        @else
                        <div class="oc-icon">
                            @if($isIcon)
                                <i class="{{ $icon }}"></i>
                            @else
                                <span>{{ $icon }}</span>
                            @endif
                        </div>
                        @endif
                        <div>
                            <h4>{{ $channel->title }}</h4>
                            @if($channel->purpose)
                                <p class="oc-purpose">{{ $channel->purpose }}</p>
                            @endif
                        </div>
                    </div>
                    @if($channel->description)
                        <p class="oc-desc">{{ $channel->description }}</p>
                    @endif
                    <a href="{{ $channel->link }}" target="_blank" rel="noopener noreferrer" class="oc-join">
                        Join Now <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="oc-empty">Official channels will appear here once they are added.</div>
    @endif
</div>
@endsection
