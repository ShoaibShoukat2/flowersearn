@php $pageTitle = 'Official Channels'; @endphp
@extends($activeTemplate.'layouts.dashboard')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.oc-wrap{font-family:'Poppins',sans-serif;max-width:520px;margin:0 auto;padding:0 0 2rem;}
.oc-hero{
    background:linear-gradient(145deg,#25d366 0%,#128C7E 100%);
    border-radius:0 0 28px 28px;padding:2rem 1.4rem 2.2rem;text-align:center;color:#fff;
    margin-bottom:1.2rem;
}
.oc-hero h1{font-size:1.55rem;font-weight:800;margin:0.4rem 0;}
.oc-hero p{font-size:0.86rem;opacity:.92;margin:0;}
.oc-card{margin:0 1rem;}
.oc-link{
    display:flex;align-items:center;gap:12px;padding:1rem;border-radius:16px;
    text-decoration:none;margin-bottom:0.8rem;background:#fff;
    border:1.5px solid #d9f5e5;box-shadow:0 4px 16px rgba(18,140,126,.08);
}
.oc-link.channel{border-color:#dbeafe;}
.oc-link.disabled{opacity:.6;pointer-events:none;}
.oc-icon{
    width:46px;height:46px;border-radius:14px;display:flex;align-items:center;justify-content:center;
    background:#25d366;color:#fff;font-size:1.25rem;flex-shrink:0;
}
.oc-link.channel .oc-icon{background:#1976d2;}
.oc-link h4{margin:0 0 2px;font-size:.95rem;font-weight:700;color:#222;}
.oc-link p{margin:0;font-size:.78rem;color:#777;}
</style>

<div class="oc-wrap">
    <div class="oc-hero">
        <div style="font-size:2.4rem;">📢</div>
        <h1>Official Channels</h1>
        <p>Join both channels for updates and training.</p>
    </div>
    <div class="oc-card">
        <a href="{{ $communityLink ?: '#' }}" target="_blank" rel="noopener noreferrer" class="oc-link {{ $communityLink ? '' : 'disabled' }}">
            <div class="oc-icon"><i class="fab fa-whatsapp"></i></div>
            <div>
                <h4>Official Community</h4>
                <p>{{ $communityLink ? 'Open the WhatsApp community' : 'Community link is not added yet' }}</p>
            </div>
        </a>
        <a href="{{ $channelLink ?: '#' }}" target="_blank" rel="noopener noreferrer" class="oc-link channel {{ $channelLink ? '' : 'disabled' }}">
            <div class="oc-icon"><i class="fas fa-broadcast-tower"></i></div>
            <div>
                <h4>Official Channel</h4>
                <p>{{ $channelLink ? 'Open the WhatsApp channel' : 'Channel link is not added yet' }}</p>
            </div>
        </a>
    </div>
</div>
@endsection
