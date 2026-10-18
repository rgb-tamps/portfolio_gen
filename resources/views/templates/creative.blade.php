<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $portfolio->full_name }} | Portfolio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#111111] text-white">
    <main class="mx-auto max-w-7xl px-5 py-8 sm:px-8 md:px-12">
        <nav class="flex flex-wrap items-center justify-between gap-4 border-b border-white/15 pb-5">
            <a href="{{ route('portfolios.manage') }}" class="font-bold tracking-widest">
                MY WORLD<span class="text-[#e93f1a]">.</span>
            </a>
            <a href="#contact" class="text-sm font-semibold text-orange-400">LET'S TALK ↗</a>
        </nav>

        <section class="grid items-center gap-10 py-16 md:grid-cols-2 md:py-24">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-orange-400">
                    PERSONAL PORTFOLIO / {{ date('Y') }}
                </p>
                <h1 class="mt-6 break-words text-5xl font-black uppercase leading-none tracking-tight sm:text-6xl md:text-8xl">
                    {{ $portfolio->full_name }}<span class="text-[#e93f1a]">.</span>
                </h1>
                <p class="mt-7 whitespace-pre-line leading-8 text-gray-400">{{ $portfolio->about_me }}</p>
                <a href="#projects" class="mt-8 inline-flex rounded-full bg-[#e93f1a] px-6 py-3 font-bold hover:bg-orange-500">
                    VIEW MY WORK ↗
                </a>
            </div>

            <div class="relative">
                <div class="absolute -right-2 -top-2 h-20 w-20 rounded-full bg-[#e93f1a] sm:h-24 sm:w-24"></div>
                @if ($portfolio->profile_picture)
                    <img src="{{ $portfolio->mediaUrl($portfolio->profile_picture) }}"
                         alt="{{ $portfolio->full_name }}"
                         class="relative h-80 w-full rounded-2xl object-cover md:h-[450px]">
                @else
                    <div class="relative flex h-80 items-center justify-center rounded-2xl bg-[#242424] text-8xl font-black text-[#e93f1a] md:h-[450px]">
                        {{ strtoupper(substr($portfolio->full_name, 0, 1)) }}
                    </div>
                @endif
            </div>
        </section>

        <section class="border-y border-white/15 py-10">
            <h2 class="text-sm font-bold uppercase tracking-[0.25em] text-orange-400">My Toolkit</h2>
            <div class="mt-6 flex flex-wrap gap-3">
                @forelse (($portfolio->skills ?? []) as $skill)
                    <span class="rounded-full border border-white/20 px-4 py-2 text-sm text-gray-300 transition hover:border-[#e93f1a] hover:text-white">
                        {{ $skill }}
                    </span>
                @empty
                    <p class="text-gray-400">No skills added yet.</p>
                @endforelse
            </div>
        </section>

        <section id="projects" class="py-16">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <h2 class="text-4xl font-black uppercase sm:text-6xl">Selected Work.</h2>
                <span class="text-sm tracking-widest text-gray-500">PROJECT GALLERY</span>
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-2">
                @forelse (($portfolio->projects ?? []) as $project)
                    <article class="overflow-hidden rounded-2xl border border-white/10 bg-[#1b1b1b] transition hover:border-[#e93f1a]">
                        @if (!empty($project['screenshot']))
                            <img src="{{ $portfolio->mediaUrl($project['screenshot'] ?? null) }}"
                                 alt="{{ $project['title'] ?? 'Project screenshot' }}"
                                 class="h-52 w-full object-cover">
                        @endif

                        <div class="p-6 sm:p-8">
                            <span class="text-xs font-bold tracking-widest text-orange-400">FEATURED PROJECT</span>
                            <h3 class="mt-4 break-words text-2xl font-bold">{{ $project['title'] ?? 'Untitled project' }}</h3>
                            <p class="mt-4 whitespace-pre-line leading-7 text-gray-400">{{ $project['description'] ?? '' }}</p>

                            @if (!empty($project['tech_stack']))
                                <p class="mt-4 text-sm text-gray-500">TOOLS: {{ $project['tech_stack'] }}</p>
                            @endif

                            <div class="mt-6 flex flex-wrap gap-4 text-sm font-semibold text-orange-400">
                                @if (!empty($project['github_url']))
                                    <a href="{{ $project['github_url'] }}" target="_blank" rel="noopener noreferrer">GITHUB ↗</a>
                                @endif
                                @if (!empty($project['live_demo_url']))
                                    <a href="{{ $project['live_demo_url'] }}" target="_blank" rel="noopener noreferrer">LIVE DEMO ↗</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-gray-400">Your projects will appear here.</p>
                @endforelse
            </div>
        </section>

        <section class="grid gap-10 border-t border-white/15 py-14 md:grid-cols-2">
            <div>
                <h2 class="text-2xl font-bold">Education<span class="text-[#e93f1a]">.</span></h2>
                @forelse (($portfolio->education ?? []) as $item)
                    <p class="mt-4 whitespace-pre-line leading-7 text-gray-400">{{ $item }}</p>
                @empty
                    <p class="mt-4 text-gray-500">No education details added.</p>
                @endforelse
            </div>
            <div>
                <h2 class="text-2xl font-bold">Experience<span class="text-[#e93f1a]">.</span></h2>
                @forelse (($portfolio->experience ?? []) as $item)
                    <p class="mt-4 whitespace-pre-line leading-7 text-gray-400">{{ $item }}</p>
                @empty
                    <p class="mt-4 text-gray-500">No experience details added.</p>
                @endforelse
            </div>
        </section>

        <footer id="contact" class="rounded-2xl bg-[#e93f1a] p-8 sm:p-12">
            <p class="text-sm font-bold tracking-widest text-white/75">HAVE A PROJECT IN MIND?</p>
            <h2 class="mt-4 break-words text-4xl font-black sm:text-6xl">LET'S CONNECT.</h2>
            <a href="mailto:{{ $portfolio->email }}"
               class="mt-7 inline-block rounded-full bg-white px-6 py-3 font-bold text-gray-900 transition hover:bg-gray-200">
                EMAIL ME ↗
            </a>

            <p class="mt-5 break-all text-sm text-white/90">{{ $portfolio->email }}</p>
            @if ($portfolio->contact_number)
                <p class="mt-2 text-sm text-white/90">{{ $portfolio->contact_number }}</p>
            @endif
            @if ($portfolio->address)
                <p class="mt-2 text-sm text-white/90">{{ $portfolio->address }}</p>
            @endif

            @if (!empty($portfolio->social_links))
                <div class="mt-5 flex flex-wrap gap-4">
                    @foreach ($portfolio->social_links as $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="break-all text-sm text-white underline">
                            {{ parse_url($url, PHP_URL_HOST) ?: $url }}
                        </a>
                    @endforeach
                </div>
            @endif

            <p class="mt-10 text-xs text-white/70">© {{ date('Y') }} {{ $portfolio->full_name }}</p>
        </footer>
    </main>
</body>
</html>