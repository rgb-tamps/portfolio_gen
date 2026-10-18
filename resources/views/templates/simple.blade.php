<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $portfolio->full_name }} | Portfolio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800">
    <main class="mx-auto max-w-4xl px-6 py-12 sm:py-16">
        <nav class="mb-10 flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 pb-5">
            <a href="{{ route('portfolios.manage') }}" class="text-sm text-gray-500">← My Portfolios</a>
            <a href="#contact" class="text-sm font-semibold hover:text-[#e93f1a]">Contact</a>
        </nav>

        <header class="border-b border-gray-200 pb-10">
            @if ($portfolio->profile_picture)
                <img src="{{ $portfolio->mediaUrl($portfolio->profile_picture) }}"
                     alt="{{ $portfolio->full_name }}"
                     class="mb-6 h-28 w-28 rounded-full border border-gray-200 object-cover">
            @endif

            <p class="text-sm uppercase tracking-widest text-gray-500">Personal Portfolio</p>
            <h1 class="mt-3 break-words text-4xl font-bold sm:text-5xl">
                {{ $portfolio->full_name }}
            </h1>

            <p class="mt-5 max-w-2xl whitespace-pre-line leading-8 text-gray-600">
                {{ $portfolio->about_me }}
            </p>

            <div class="mt-5 space-y-1 text-sm text-gray-600">
                <p>{{ $portfolio->email }}</p>
                @if ($portfolio->contact_number)
                    <p>{{ $portfolio->contact_number }}</p>
                @endif
                @if ($portfolio->address)
                    <p>{{ $portfolio->address }}</p>
                @endif
            </div>
        </header>

        <section class="border-b border-gray-200 py-8">
            <h2 class="text-xl font-bold">Education</h2>
            @forelse (($portfolio->education ?? []) as $item)
                <p class="mt-3 whitespace-pre-line leading-7 text-gray-600">{{ $item }}</p>
            @empty
                <p class="mt-3 text-sm text-gray-400">No education details added.</p>
            @endforelse
        </section>

        <section class="border-b border-gray-200 py-8">
            <h2 class="text-xl font-bold">Skills</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                @forelse (($portfolio->skills ?? []) as $skill)
                    <span class="rounded-md bg-gray-100 px-3 py-2 text-sm">{{ $skill }}</span>
                @empty
                    <p class="text-sm text-gray-400">No skills added.</p>
                @endforelse
            </div>
        </section>

        <section id="projects" class="border-b border-gray-200 py-8">
            <h2 class="text-xl font-bold">Projects</h2>
            @forelse (($portfolio->projects ?? []) as $project)
                <article class="mt-6">
                    @if (!empty($project['screenshot']))
                        <img src="{{ $portfolio->mediaUrl($project['screenshot'] ?? null) }}"
                             alt="{{ $project['title'] ?? 'Project screenshot' }}"
                             class="mb-4 max-h-72 w-full rounded-lg border border-gray-200 object-cover">
                    @endif
                    <h3 class="font-semibold">{{ $project['title'] ?? 'Untitled project' }}</h3>
                    <p class="mt-2 whitespace-pre-line leading-7 text-gray-600">{{ $project['description'] ?? '' }}</p>

                    @if (!empty($project['tech_stack']))
                        <p class="mt-2 text-sm text-gray-500">Tools: {{ $project['tech_stack'] }}</p>
                    @endif

                    <div class="mt-3 flex flex-wrap gap-4 text-sm">
                        @if (!empty($project['github_url']))
                            <a href="{{ $project['github_url'] }}" target="_blank" rel="noopener noreferrer" class="underline">GitHub</a>
                        @endif
                        @if (!empty($project['live_demo_url']))
                            <a href="{{ $project['live_demo_url'] }}" target="_blank" rel="noopener noreferrer" class="underline">Live Demo</a>
                        @endif
                    </div>
                </article>
            @empty
                <p class="mt-3 text-sm text-gray-400">No projects added.</p>
            @endforelse
        </section>

        <section class="border-b border-gray-200 py-8">
            <h2 class="text-xl font-bold">Experience</h2>
            @forelse (($portfolio->experience ?? []) as $item)
                <p class="mt-3 whitespace-pre-line leading-7 text-gray-600">{{ $item }}</p>
            @empty
                <p class="mt-3 text-sm text-gray-400">No experience details added.</p>
            @endforelse
        </section>

        <footer id="contact" class="py-8">
            <h2 class="text-xl font-bold">Contact</h2>
            <a href="mailto:{{ $portfolio->email }}" class="mt-3 inline-block underline">{{ $portfolio->email }}</a>

            @if (!empty($portfolio->social_links))
                <div class="mt-4 flex flex-wrap gap-4">
                    @foreach ($portfolio->social_links as $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="break-all text-sm underline">
                            {{ parse_url($url, PHP_URL_HOST) ?: $url }}
                        </a>
                    @endforeach
                </div>
            @endif

            <p class="mt-8 text-xs text-gray-400">© {{ date('Y') }} {{ $portfolio->full_name }}</p>
        </footer>
    </main>
</body>
</html>