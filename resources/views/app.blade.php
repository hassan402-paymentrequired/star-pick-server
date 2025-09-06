<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="VibezSync is a social media scheduling and content management platform that allows creators and businesses to plan, schedule, and publish posts across multiple platforms, including TikTok, Instagram, and YouTube.">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://starpick.com.ng">
    <meta property="og:title" content="Content Title">
    <meta property="og:image" content="https://starpick.com.ng/image.jpg">
    <meta property="og:description" content="Description Here" />
    <meta property="og:site_name" content="Site Name">
    <meta property="og:locale" content="en_US">
    <!-- Next tags are optional but recommended -->
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <link rel="dns-prefetch" href="https://starpick.com.ng/" />
    <link rel="preconnect" href="https://starpick.com.ng/" />

    <title inertia>{{ config('app.name', 'Starpick') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @routes
    {{-- @viteReactRefresh --}}
    @vite('resources/js/app.tsx')
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
