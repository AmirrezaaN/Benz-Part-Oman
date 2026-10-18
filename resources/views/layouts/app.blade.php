<!DOCTYPE html>
<html lang="fa" dir="rtl" data-currency="{{ $currency }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') | @endif{{ setting('site_name') }}</title>
    <meta name="description" content="@yield('description', setting('about_short'))">
    <meta name="theme-color" content="#040711">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen flex flex-col pb-16 lg:pb-0">
    @include('partials.topbar')
    @include('partials.header')
    @include('partials.flash')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.mobile-menu')
    @include('partials.mobile-bottom-nav')
</body>
</html>
