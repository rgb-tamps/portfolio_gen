<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($portfolio->about_me ?: ($portfolio->full_name.' portfolio')), 155) }}">
    <title>{{ $portfolio->full_name }} | Portfolio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    @include('portfolios._portfolio-content', ['portfolio' => $portfolio, 'isPublic' => true])
    <section class="border-t border-slate-200 bg-white px-4 py-10 sm:px-6">
        <div class="mx-auto max-w-5xl">
            @include('portfolios._qr-code', ['portfolio' => $portfolio])
        </div>
    </section>
</body>
</html>
