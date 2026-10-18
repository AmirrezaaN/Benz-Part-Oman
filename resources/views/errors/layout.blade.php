<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') | بنز پارت عمان</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <div class="text-7xl font-black bg-gradient-to-l from-sky-400 to-blue-600 bg-clip-text text-transparent">@yield('code')</div>
        <h1 class="text-lg font-black text-white mt-4">@yield('title')</h1>
        <p class="text-sm text-slate-400 mt-3 leading-7">@yield('message')</p>
        <div class="flex items-center justify-center gap-2 mt-8">
            <a href="/" class="btn-primary"><i class="fa-solid fa-house"></i> صفحه اصلی</a>
            <a href="/products" class="btn-ghost"><i class="fa-solid fa-border-all"></i> قطعات</a>
        </div>
    </div>
</body>
</html>
