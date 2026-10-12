@php $pageTitle = 'Contact Us'; @endphp
@extends($activeTemplate.'layouts.dashboard')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.uc-wrap{font-family:'Poppins',sans-serif;max-width:520px;margin:0 auto;padding:0 0 2rem;}
.uc-hero{
    background:linear-gradient(145deg,#ff6ec7 0%,#e91e8c 50%,#ad1457 100%);
    border-radius:0 0 28px 28px;padding:2rem 1.4rem 2.2rem;text-align:center;color:#fff;
    box-shadow:0 10px 30px rgba(233,30,140,0.25);margin-bottom:1.2rem;
}
.uc-hero-icon{font-size:3rem;margin-bottom:0.6rem;}
.uc-hero h1{font-size:1.6rem;font-weight:800;margin-bottom:0.35rem;}
.uc-hero p{font-size:0.86rem;opacity:0.9;line-height:1.5;}
.uc-admin{
    background:#fff;border:1.5px solid rgba(233,30,140,0.12);border-radius:18px;
    padding:1rem 1.05rem;margin:0 1rem 0.85rem;
    box-shadow:0 4px 18px rgba(233,30,140,0.08);
}
.uc-admin h3{font-size:1.02rem;font-weight:800;color:#1a1a2e;margin:0 0 4px;}
.uc-cat{font-size:0.84rem;font-weight:700;color:#e91e8c;margin-bottom:6px;}
.uc-note{font-size:0.8rem;color:#666;line-height:1.5;margin:0 0 10px;}
.uc-line{
    display:flex;align-items:center;gap:8px;
    text-decoration:none;color:#128C7E;font-weight:700;font-size:0.86rem;
    margin-top:6px;
}
.uc-line.call{color:#1976d2;}
.uc-empty{
    margin:0 1rem;text-align:center;padding:1.5rem 1rem;color:#888;font-size:0.88rem;line-height:1.6;
    background:#fff;border-radius:16px;border:1.5px dashed #f0d0e4;
}
</style>

<div class="uc-wrap">
    <div class="uc-hero">
        <div class="uc-hero-icon">💬</div>
        <h1>Contact Us</h1>
        <p>Choose the right admin for your question.</p>
    </div>

    @forelse($admins as $admin)
        @php
            $showWhatsapp = in_array($admin->contact_type, ['whatsapp', 'both'], true) && ($admin->whatsapp_number || $admin->contact_number);
            $whatsappNumber = $admin->whatsapp_number ?: $admin->contact_number;
            $showCall = in_array($admin->contact_type, ['call', 'both'], true) && $admin->contact_number;
        @endphp
        <div class="uc-admin">
            <h3>{{ $admin->name }}</h3>
            <div class="uc-cat">{{ $admin->category }}</div>
            @if($admin->instructions)
                <p class="uc-note">{{ $admin->instructions }}</p>
            @endif
            @if($showWhatsapp)
                <a class="uc-line" href="{{ whatsappChatLink($whatsappNumber) }}" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-whatsapp"></i> WhatsApp: {{ $whatsappNumber }}
                </a>
            @endif
            @if($showCall)
                <a class="uc-line call" href="tel:{{ preg_replace('/\s+/', '', $admin->contact_number) }}">
                    <i class="fas fa-phone"></i> Contact: {{ $admin->contact_number }}
                </a>
            @endif
        </div>
    @empty
        <div class="uc-empty">Contact admins have not been added yet. Please check back soon.</div>
    @endforelse
</div>
@endsection
