<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choose a template | {{ config('app.name', 'PortfolioGen') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-page text-slate-900">
    @php
        $templates = [
            ['key' => 'simple', 'name' => 'Simple', 'description' => 'A clean, minimal design for students and job applicants.', 'preview' => 'bg-white text-slate-900'],
            ['key' => 'modern', 'name' => 'Modern', 'description' => 'A polished layout with warm backgrounds and orange accents.', 'preview' => 'bg-brand-light text-slate-900'],
            ['key' => 'creative', 'name' => 'Creative', 'description' => 'A bold dark layout with expressive typography.', 'preview' => 'bg-slate-950 text-white'],
        ];
        $selectedTemplate = old('template', $portfolio->template ?: 'simple');
    @endphp

    <header class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="text-xl font-extrabold tracking-tight">Portfolio<span class="text-brand">Gen</span></a>
            <a href="{{ route('portfolios.manage') }}" class="text-sm font-semibold text-slate-600 hover:text-brand">Back to portfolios</a>
        </nav>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <header>
            <p class="text-sm font-bold uppercase tracking-widest text-brand">Make it yours</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Choose a template</h1>
            <p class="mt-3 text-slate-600">Select one of the three designs for {{ $portfolio->full_name }}. You can change it anytime.</p>
        </header>

        @if ($errors->any())
            <div role="alert" class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <p class="font-bold">Unable to save the template.</p>
                @error('template') <p class="mt-1">{{ $message }}</p> @enderror
            </div>
        @endif

        <form method="POST" action="{{ route('portfolios.template.choose', ['portfolio' => $portfolio->id]) }}" x-data="{ template: @js($selectedTemplate) }" class="mt-8 space-y-7">
            @csrf
            @method('PATCH')
            <fieldset>
                <legend class="sr-only">Choose one portfolio template</legend>
                <div class="grid gap-5 md:grid-cols-3">
                    @foreach ($templates as $item)
                        <label class="group block cursor-pointer overflow-hidden rounded-2xl border bg-white transition hover:-translate-y-1 hover:shadow-lg focus-within:ring-2 focus-within:ring-brand"
                               :class="template === '{{ $item['key'] }}' ? 'border-brand ring-2 ring-brand/20' : 'border-slate-200'">
                            <input class="sr-only" type="radio" name="template" value="{{ $item['key'] }}" x-model="template" @checked($selectedTemplate === $item['key'])>
                            <div class="{{ $item['preview'] }} flex h-48 flex-col justify-between overflow-hidden p-5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black tracking-widest">PORTFOLIO<span class="text-brand">.</span></span>
                                    <span class="h-2 w-2 rounded-full bg-brand"></span>
                                </div>
                                <div>
                                    <div class="text-2xl font-extrabold">{{ $portfolio->full_name }}</div>
                                    <div class="mt-2 line-clamp-2 text-xs opacity-70">{{ $portfolio->about_me ?: 'A preview of your portfolio introduction.' }}</div>
                                    <div class="mt-4 flex gap-2">
                                        <span class="h-8 flex-1 rounded border border-current/20"></span>
                                        <span class="h-8 flex-1 rounded border border-current/20"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <h2 class="text-xl font-extrabold">{{ $item['name'] }}</h2>
                                    <span x-show="template === '{{ $item['key'] }}'" class="rounded-full bg-brand-light px-2.5 py-1 text-xs font-bold text-brand">Selected</span>
                                </div>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['description'] }}</p>
                                <p class="mt-3 text-xs font-semibold text-slate-500">Choose {{ $item['name'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-200 pt-6">
                <a href="{{ route('portfolios.preview', $portfolio) }}" class="text-sm font-semibold text-slate-600 hover:text-brand">Preview current design</a>
                <button type="submit" class="rounded-xl bg-brand px-6 py-3 font-bold text-white hover:bg-brand-hover focus-visible:outline-brand">Use selected template</button>
            </div>
        </form>
    </main>
</body>
</html>
