<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'PortfolioGen') }} | Build your portfolio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-page text-slate-800">
    <header class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="text-xl font-extrabold tracking-tight text-slate-950">Portfolio<span class="text-brand">Gen</span></a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="font-semibold text-slate-700 hover:text-brand">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-slate-700 hover:text-brand">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-brand px-4 py-2 font-semibold text-white hover:bg-brand-hover">Get started</a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        <section class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 sm:px-6 md:grid-cols-2 lg:px-8 lg:py-24">
            <div>
                <span class="inline-flex rounded-full bg-brand-light px-3 py-1 text-sm font-bold text-brand">Your work. Your story.</span>
                <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-slate-950 sm:text-6xl">Build a portfolio that opens doors.</h1>
                <p class="mt-5 max-w-xl text-lg leading-8 text-slate-600">Create, preview, and publish a professional portfolio in simple steps. No design experience required.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('portfolios.create') }}" class="inline-flex min-h-12 items-center rounded-xl bg-brand px-6 py-3 font-bold text-white hover:bg-brand-hover">Create Portfolio</a>
                        <a href="{{ route('portfolios.manage') }}" class="inline-flex min-h-12 items-center rounded-xl border border-slate-300 bg-white px-6 py-3 font-bold text-slate-700 hover:bg-slate-50">Manage portfolios</a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center rounded-xl bg-brand px-6 py-3 font-bold text-white hover:bg-brand-hover">Create Portfolio</a>
                        <a href="#recent" class="inline-flex min-h-12 items-center rounded-xl border border-slate-300 bg-white px-6 py-3 font-bold text-slate-700 hover:bg-slate-50">Explore portfolios</a>
                    @endauth
                </div>
                <p class="mt-4 text-sm text-slate-500">Three responsive templates · Save drafts · Share with a public link</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/60 sm:p-8">
                <div class="rounded-2xl bg-slate-950 p-6 text-white">
                    <div class="h-2 w-16 rounded-full bg-brand"></div>
                    <div class="mt-6 flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-light text-xl font-bold text-brand">KC</div>
                        <div><div class="text-xl font-bold">Your Name</div><div class="mt-1 text-sm text-slate-300">Student · Developer · Creator</div></div>
                    </div>
                    <div class="mt-7 h-2 w-full rounded bg-slate-700"></div>
                    <div class="mt-2 h-2 w-4/5 rounded bg-slate-700"></div>
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-xl bg-slate-800 p-4"><div class="text-sm font-bold">Projects</div><div class="mt-3 h-2 rounded bg-slate-600"></div></div>
                        <div class="rounded-xl bg-slate-800 p-4"><div class="text-sm font-bold">Skills</div><div class="mt-3 h-2 rounded bg-slate-600"></div></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="recent" class="border-t border-slate-200 bg-white py-14 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div><p class="text-sm font-bold uppercase tracking-widest text-brand">Community</p><h2 class="mt-2 text-3xl font-extrabold text-slate-950">Recently published portfolios</h2><p class="mt-2 text-slate-600">Get inspired by work shared by the community.</p></div>
                </div>
                @if ($recentPortfolios->isEmpty())
                    <div class="mt-8 rounded-2xl border border-dashed border-slate-300 p-10 text-center text-slate-500">No public portfolios yet. Be the first to publish one.</div>
                @else
                    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($recentPortfolios as $portfolio)
                            <a href="{{ route('portfolios.public', $portfolio->slug) }}" class="rounded-2xl border border-slate-200 bg-page p-5 transition hover:-translate-y-1 hover:border-brand hover:shadow-md">
                                <div class="flex items-center gap-4">
                                    @if ($portfolio->profile_picture)
                                        <img src="{{ $portfolio->mediaUrl($portfolio->profile_picture) }}" alt="" class="h-14 w-14 rounded-xl object-cover">
                                    @else
                                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-brand-light text-xl font-bold text-brand">{{ strtoupper(substr($portfolio->full_name, 0, 1)) }}</div>
                                    @endif
                                    <div class="min-w-0"><h3 class="truncate font-bold text-slate-950">{{ $portfolio->full_name }}</h3><p class="mt-1 text-sm text-slate-500">{{ ucfirst($portfolio->template) }} template</p></div>
                                </div>
                                <p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-600">{{ $portfolio->about_me ?: 'View this published portfolio.' }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </main>
    <footer class="border-t border-slate-200 bg-page px-4 py-6 text-center text-sm text-slate-500">© {{ date('Y') }} PortfolioGen</footer>
</body>
</html>
