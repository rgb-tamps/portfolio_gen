<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Portfolio Generator') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-page font-sans text-slate-800 antialiased">
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-10">

        <a href="{{ url('/') }}" class="mb-8 flex items-center gap-3">
            
            <span class="text-xl font-bold tracking-tight text-slate-900">
                Portfolio<span class="text-brand">Generator</span>
            </span>
        </a>

        <section class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-xl sm:p-8">
            {{ $slot }}
        </section>

        <p class="mt-6 text-center text-sm text-slate-500">
            Build your portfolio. Share your story.
        </p>
    </main>
</body>
</html>