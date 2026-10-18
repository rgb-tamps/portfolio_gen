<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $portfolio->full_name }} | Portfolio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#faf7f2] text-gray-900">
    <main class="mx-auto max-w-6xl px-5 py-8 sm:px-8">
        <nav class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 pb-5">
            <a href="{{ route('portfolios.manage') }}" class="font-bold tracking-tight">PORTFOLIO<span class="text-[#e93f1a]">.</span></a>
            <a href="#contact" class="rounded-full bg-[#e93f1a] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#c83214]">Contact Me</a>
        </nav>

        <section class="grid items-center gap-10 py-16 md:grid-cols-2 md:py-24">
            <div>
                <p class="font-semibold tracking-widest text-[#e93f1a]">HELLO, I'M</p>
                <h1 class="mt-4 break-words text-5xl font-bold tracking-tight sm:text-6xl">{{ $portfolio->full_name }}</h1>
                <p class="mt-6 whitespace-pre-line leading-8 text-gray-600">{{ $portfolio->about_me }}</p>
                <a href="#projects" class="mt-8 inline-block rounded-lg bg-gray-900 px-6 py-3 font-semibold text-white hover:bg-gray-700">Explore My Work</a>
            </div>

            @if ($portfolio->profile_picture)
                <img src="{{ $portfolio->mediaUrl($portfolio->profile_picture) }}"
                     alt="{{ $portfolio->full_name }}"
                     class="h-80 w-full rounded-3xl object-cover shadow-lg md:h-[420px]">
            @else
                <div class="flex h-80 items-center justify-center rounded-3xl bg-orange-100 text-8xl font-bold text-[#e93f1a] md:h-[420px]">
                    {{ strtoupper(substr($portfolio->full_name, 0, 1)) }}
                </div>
            @endif
        </section>

        <section class="py-12">
            <h2 class="text-3xl font-bold">My Skills</h2>
            <div class="mt-6 flex flex-wrap gap-3">
                @forelse (($portfolio->skills ?? []) as $skill)
                    <span class="rounded-full border border-gray-200 bg-white px-4 py-2 text-sm">{{ $skill }}</span>
                @empty
                    <p class="text-gray-400">No skills added yet.</p>
                @endforelse
            </div>
        </section>

        <section id="projects" class="py-12">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <h2 class="text-3xl font-bold">Featured Projects</h2>
                <span class="text-sm text-gray-500">{{ count($portfolio->projects ?? []) }} projects</span>
            </div>

            <div class="mt-7 grid gap-6 md:grid-cols-2">
                @forelse (($portfolio->projects ?? []) as $project)
                    <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:-translate-y-1 hover:shadow-lg">
                        @if (!empty($project['screenshot']))
                            <img src="{{ $portfolio->mediaUrl($project['screenshot'] ?? null) }}"
                                 alt="{{ $project['title'] ?? 'Project screenshot' }}"
                                 class="h-48 w-full object-cover">
                        @endif
                        <div class="p-6">
                            <p class="text-xs font-bold uppercase tracking-widest text-[#e93f1a]">PROJECT</p>
                            <h3 class="mt-3 text-xl font-bold">{{ $project['title'] ?? 'Untitled project' }}</h3>
                            <p class="mt-3 whitespace-pre-line leading-7 text-gray-600">{{ $project['description'] ?? '' }}</p>
                            @if (!empty($project['tech_stack']))
                                <p class="mt-3 text-sm text-gray-500">{{ $project['tech_stack'] }}</p>
                            @endif
                            <div class="mt-5 flex flex-wrap gap-4 text-sm font-semibold text-[#e93f1a]">
                                @if (!empty($project['github_url']))
                                    <a href="{{ $project['github_url'] }}" target="_blank" rel="noopener noreferrer">GitHub ↗</a>
                                @endif
                                @if (!empty($project['live_demo_url']))
                                    <a href="{{ $project['live_demo_url'] }}" target="_blank" rel="noopener noreferrer">Live Demo ↗</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-gray-500">Add projects to your portfolio to display them here.</p>
                @endforelse
            </div>
        </section>

        <section class="grid gap-10 border-t border-gray-200 py-12 md:grid-cols-2">
            <div>
                <h2 class="text-2xl font-bold">Education</h2>
                @forelse (($portfolio->education ?? []) as $item)
                    <p class="mt-4 whitespace-pre-line leading-7 text-gray-600">{{ $item }}</p>
                @empty
                    <p class="mt-4 text-gray-400">No education details added.</p>
                @endforelse
            </div>
            <div>
                <h2 class="text-2xl font-bold">Experience</h2>
                @forelse (($portfolio->experience ?? []) as $item)
                    <p class="mt-4 whitespace-pre-line leading-7 text-gray-600">{{ $item }}</p>
                @empty
                    <p class="mt-4 text-gray-400">No experience details added.</p>
                @endforelse
            </div>
        </section>

        <footer id="contact" class="rounded-3xl bg-gray-900 p-8 text-white md:p-12">
            <p class="text-sm font-bold tracking-widest text-orange-400">LET'S CONNECT</p>
            <h2 class="mt-3 text-3xl font-bold sm:text-4xl">Have a project in mind?</h2>
            <p class="mt-4 text-gray-300">{{ $portfolio->email }}</p>
            @if ($portfolio->contact_number)
                <p class="mt-2 text-gray-300">{{ $portfolio->contact_number }}</p>
            @endif
            @if ($portfolio->address)
                <p class="mt-2 text-gray-300">{{ $portfolio->address }}</p>
            @endif
            @if (!empty($portfolio->social_links))
                <div class="mt-5 flex flex-wrap gap-4">
                    @foreach ($portfolio->social_links as $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="break-all text-sm text-orange-300 underline">
                            {{ parse_url($url, PHP_URL_HOST) ?: $url }}
                        </a>
                    @endforeach
                </div>
            @endif
            <a href="mailto:{{ $portfolio->email }}" class="mt-6 inline-block rounded-lg bg-[#e93f1a] px-5 py-3 font-semibold text-white">Send an Email</a>
        </footer>
    </main>
</body>
</html>