@php
    $flowerIconUrl = asset('assets/images/logoIcon/favicon.png') . '?v=20260907';
    $flowerLogoUrl = asset('assets/images/logoIcon/logo.png') . '?v=20260907';
    $flowerShareUrl = asset('assets/images/seo/flower-share.png') . '?v=20260907';
@endphp
<link rel="icon" href="{{ $flowerIconUrl }}" type="image/png" sizes="128x128">
<link rel="shortcut icon" href="{{ $flowerIconUrl }}" type="image/png">
<link rel="apple-touch-icon" href="{{ $flowerLogoUrl }}">

@if($seo)
    <meta name="title" Content="{{ $general->siteName(__($pageTitle)) }}">
    <meta name="description" content="Flowers Earn">
    <meta name="keywords" content="{{ implode(',',$seo->keywords) }}">

    {{--<!-- Apple Stuff -->--}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ $general->siteName($pageTitle) }}">
    {{--<!-- Google / Search Engine Tags -->--}}
    <meta itemprop="name" content="{{ $general->siteName($pageTitle) }}">
    <meta itemprop="description" content="{{ $general->seo_description }}">
    <meta itemprop="image" content="{{ $flowerShareUrl }}">
    {{--<!-- Facebook / WhatsApp Meta Tags -->--}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="Flowers Earn">
    <meta property="og:description" content="Flowers Earn">
    <meta property="og:image" content="{{ $flowerShareUrl }}"/>
    <meta property="og:image:secure_url" content="{{ $flowerShareUrl }}"/>
    <meta property="og:image:type" content="image/png" />
    <meta property="og:image:width" content="1180" />
    <meta property="og:image:height" content="600" />
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Flower">
    {{--<!-- Twitter Meta Tags -->--}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Flowers Earn">
    <meta name="twitter:description" content="Flowers Earn">
    <meta name="twitter:image" content="{{ $flowerShareUrl }}">
@endif
