
@php
    $isPublic = $isPublic ?? false;

    $template = in_array($portfolio->template, ['simple', 'modern', 'creative'])
        ? $portfolio->template
        : 'simple';

    $isSimple = $template === 'simple';
    $isModern = $template === 'modern';
    $isCreative = $template === 'creative';

    $skills = $portfolio->skills ?? [];
    $projects = $portfolio->projects ?? [];
    $education = $portfolio->education ?? [];
    $experience = $portfolio->experience ?? [];
    $socialLinks = $portfolio->social_links ?? [];

    $profileImage = $portfolio->mediaUrl($portfolio->profile_picture);
@endphp

{{-- =========================================================
     SIMPLE TEMPLATE
     Clean layout, white background, compact project cards
========================================================= --}}
@if ($isSimple)

<div class="min-h-screen bg-white text-slate-900">
    <header class="border-b border-slate-200">
        <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4 px-5 py-5">
            <a href="{{ $isPublic ? route('home') : route('portfolios.manage') }}"
               class="text-lg font-black tracking-tight">
                {{ $portfolio->full_name }}<span class="text-brand">.</span>
            </a>

            @unless ($isPublic)
                <nav class="flex flex-wrap gap-2">
                    <a href="{{ route('portfolios.edit', $portfolio) }}"
                       class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold hover:bg-slate-50">
                        Edit Details
                    </a>
                    <a href="{{ route('portfolios.templates.select', $portfolio) }}"
                       class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                        Change Template
                    </a>
                </nav>
            @else
                <span class="text-sm text-slate-500">Portfolio</span>
            @endunless
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-5 py-14 sm:py-20">
        <section class="grid items-center gap-10 md:grid-cols-[1fr_220px]">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-brand">
                    Personal Portfolio
                </p>

                <h1 class="mt-4 break-words text-4xl font-black tracking-tight sm:text-6xl">
                    {{ $portfolio->full_name }}
                </h1>

                <p class="mt-5 max-w-2xl whitespace-pre-line text-lg leading-8 text-slate-600">
                    {{ $portfolio->about_me ?: 'Welcome to my portfolio. Here you can learn about my skills, education, and work.' }}
                </p>

                <div class="mt-7 flex flex-wrap gap-3">
                    @if ($portfolio->email)
                        <a href="mailto:{{ $portfolio->email }}"
                           class="rounded-lg bg-slate-900 px-5 py-3 font-bold text-white hover:bg-slate-700">
                            Contact Me
                        </a>
                    @endif

                    @if ($portfolio->contact_number)
                        <span class="rounded-lg border border-slate-200 px-4 py-3 text-sm">
                            {{ $portfolio->contact_number }}
                        </span>
                    @endif
                </div>

                @if ($portfolio->address)
                    <p class="mt-4 text-sm text-slate-500">{{ $portfolio->address }}</p>
                @endif
            </div>

            <div class="flex justify-start md:justify-end">
                @if ($profileImage)
                    <img src="{{ $profileImage }}" alt="{{ $portfolio->full_name }}"
                         class="h-48 w-48 rounded-2xl border border-slate-200 object-cover">
                @else
                    <div class="flex h-48 w-48 items-center justify-center rounded-2xl bg-slate-100 text-5xl font-black text-slate-400">
                        {{ strtoupper(substr($portfolio->full_name ?: 'P', 0, 2)) }}
                    </div>
                @endif
            </div>
        </section>

        @if (!empty($skills))
            <section class="mt-16 border-t border-slate-200 pt-8">
                <h2 class="text-xl font-extrabold">Skills</h2>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($skills as $skill)
                        <span class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                            {{ $skill }}
                        </span>
                    @endforeach
                </div>
            </section>
        @endif

        @if (!empty($projects))
            <section class="mt-16 border-t border-slate-200 pt-8">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <h2 class="text-2xl font-black">Projects</h2>
                    <span class="text-sm text-slate-500">{{ count($projects) }} projects</span>
                </div>

                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    @foreach ($projects as $project)
                        <article class="overflow-hidden rounded-2xl border border-slate-200">
                            @if (!empty($project['screenshot']))
                                <img src="{{ $portfolio->mediaUrl($project['screenshot'] ?? null) }}"
                                     alt="{{ $project['title'] ?? 'Project screenshot' }}"
                                     class="h-48 w-full object-cover">
                            @endif

                            <div class="p-5">
                                <h3 class="text-lg font-bold">{{ $project['title'] ?? 'Project' }}</h3>

                                @if (!empty($project['description']))
                                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                                        {{ $project['description'] }}
                                    </p>
                                @endif

                                @if (!empty($project['tech_stack']))
                                    <p class="mt-3 text-xs font-bold uppercase tracking-wide text-brand">
                                        {{ $project['tech_stack'] }}
                                    </p>
                                @endif

                                <div class="mt-4 flex flex-wrap gap-4 text-sm font-bold">
                                    @if (!empty($project['github_url']))
                                        <a href="{{ $project['github_url'] }}" target="_blank" rel="noopener noreferrer"
                                           class="text-brand hover:underline">GitHub ↗</a>
                                    @endif
                                    @if (!empty($project['live_demo_url']))
                                        <a href="{{ $project['live_demo_url'] }}" target="_blank" rel="noopener noreferrer"
                                           class="text-brand hover:underline">Live Demo ↗</a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if (!empty($education) || !empty($experience))
            <section class="mt-16 grid gap-10 border-t border-slate-200 pt-8 md:grid-cols-2">
                @if (!empty($education))
                    <div>
                        <h2 class="text-xl font-extrabold">Education</h2>
                        <div class="mt-5 space-y-4">
                            @foreach ($education as $item)
                                <div class="border-l-2 border-slate-300 pl-4">
                                    <p class="whitespace-pre-line leading-6 text-slate-600">{{ $item }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (!empty($experience))
                    <div>
                        <h2 class="text-xl font-extrabold">Experience</h2>
                        <div class="mt-5 space-y-4">
                            @foreach ($experience as $item)
                                <div class="border-l-2 border-slate-300 pl-4">
                                    <p class="whitespace-pre-line leading-6 text-slate-600">{{ $item }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @endif

        @if (!empty($socialLinks))
            <section class="mt-16 border-t border-slate-200 pt-8">
                <h2 class="text-xl font-extrabold">Find Me Online</h2>
                <div class="mt-4 flex flex-wrap gap-3">
                    @foreach ($socialLinks as $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                           class="break-all text-sm font-semibold text-brand hover:underline">
                            {{ parse_url($url, PHP_URL_HOST) ?: $url }} ↗
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <footer class="border-t border-slate-200 px-5 py-6 text-center text-xs text-slate-500">
        Made with PortfolioGen · {{ now()->year }}
    </footer>
</div>


{{-- =========================================================
     MODERN TEMPLATE
     Cream background, orange accents, editorial feature layout
========================================================= --}}
@elseif ($isModern)

<div class="min-h-screen bg-orange-50 text-stone-900">
    <header class="border-b border-orange-200">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-5 py-6">
            <a href="{{ $isPublic ? route('home') : route('portfolios.manage') }}"
               class="text-xl font-black tracking-tight">
                P<span class="text-orange-600">/</span>F
            </a>

            <span class="text-xs font-bold uppercase tracking-[0.2em] text-stone-500">
                Portfolio · {{ now()->year }}
            </span>

            @unless ($isPublic)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('portfolios.edit', $portfolio) }}"
                       class="rounded-full border border-orange-200 px-4 py-2 text-sm font-bold hover:bg-white">
                        Edit
                    </a>
                    <a href="{{ route('portfolios.templates.select', $portfolio) }}"
                       class="rounded-full bg-orange-600 px-4 py-2 text-sm font-bold text-white hover:bg-orange-700">
                        Change Template
                    </a>
                </div>
            @endunless
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-5 py-12 sm:py-20">
        <section class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-orange-700">
                    01 / Introduction
                </p>

                <h1 class="mt-6 break-words text-5xl font-black leading-[0.98] tracking-tight sm:text-7xl">
                    {{ $portfolio->full_name }}<span class="text-orange-600">.</span>
                </h1>

                <p class="mt-7 max-w-2xl whitespace-pre-line text-lg leading-8 text-stone-600">
                    {{ $portfolio->about_me ?: 'A little about me, the work I do, and the things I am learning.' }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @if ($portfolio->email)
                        <a href="mailto:{{ $portfolio->email }}"
                           class="rounded-full bg-orange-600 px-6 py-3 font-bold text-white hover:bg-orange-700">
                            Let's Talk ↗
                        </a>
                    @endif

                    @if ($portfolio->contact_number)
                        <span class="rounded-full border border-orange-200 px-5 py-3 text-sm font-semibold">
                            {{ $portfolio->contact_number }}
                        </span>
                    @endif
                </div>

                @if ($portfolio->address)
                    <p class="mt-4 text-sm text-stone-500">{{ $portfolio->address }}</p>
                @endif
            </div>

            <div class="relative">
                <div class="absolute -right-3 -top-3 h-full w-full rounded-[2rem] border-2 border-orange-300"></div>
                @if ($profileImage)
                    <img src="{{ $profileImage }}" alt="{{ $portfolio->full_name }}"
                         class="relative h-80 w-full rounded-[2rem] object-cover sm:h-[28rem]">
                @else
                    <div class="relative flex h-80 items-center justify-center rounded-[2rem] bg-orange-200 text-7xl font-black text-orange-800 sm:h-[28rem]">
                        {{ strtoupper(substr($portfolio->full_name ?: 'P', 0, 2)) }}
                    </div>
                @endif
                <span class="absolute -bottom-4 left-5 rounded-full bg-white px-5 py-3 text-xs font-bold uppercase tracking-widest shadow-sm">
                    Get to know me
                </span>
            </div>
        </section>

        @if (!empty($skills))
            <section class="mt-24 border-t border-orange-200 pt-8">
                <div class="grid gap-6 md:grid-cols-[0.7fr_1.3fr]">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-widest text-orange-700">02 / Toolkit</p>
                        <h2 class="mt-3 text-3xl font-black">What I bring</h2>
                    </div>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($skills as $skill)
                            <div class="flex min-h-24 items-end rounded-2xl border border-orange-200 bg-white p-4">
                                <span class="text-sm font-bold">{{ $skill }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if (!empty($projects))
            <section class="mt-24 border-t border-orange-200 pt-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-widest text-orange-700">03 / Selected Work</p>
                        <h2 class="mt-3 text-3xl font-black sm:text-4xl">Projects with purpose.</h2>
                    </div>
                    <span class="text-sm text-stone-500">{{ count($projects) }} selected projects</span>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-12">
                    @foreach ($projects as $project)
                        <article class="overflow-hidden rounded-3xl border border-orange-200 bg-white
                            {{ $loop->first ? 'md:col-span-12' : 'md:col-span-6' }}">
                            @if (!empty($project['screenshot']))
                                <img src="{{ $portfolio->mediaUrl($project['screenshot'] ?? null) }}"
                                     alt="{{ $project['title'] ?? 'Project screenshot' }}"
                                     class="{{ $loop->first ? 'h-64 sm:h-96' : 'h-52' }} w-full object-cover">
                            @endif

                            <div class="p-6 sm:p-8">
                                <p class="text-xs font-bold uppercase tracking-widest text-orange-700">
                                    Project {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                </p>
                                <h3 class="mt-3 text-2xl font-black">
                                    {{ $project['title'] ?? 'Project' }}
                                </h3>

                                @if (!empty($project['description']))
                                    <p class="mt-3 max-w-2xl whitespace-pre-line leading-7 text-stone-600">
                                        {{ $project['description'] }}
                                    </p>
                                @endif

                                @if (!empty($project['tech_stack']))
                                    <p class="mt-4 text-xs font-bold uppercase tracking-wide text-orange-700">
                                        {{ $project['tech_stack'] }}
                                    </p>
                                @endif

                                <div class="mt-5 flex flex-wrap gap-4 text-sm font-bold text-orange-700">
                                    @if (!empty($project['github_url']))
                                        <a href="{{ $project['github_url'] }}" target="_blank" rel="noopener noreferrer"
                                           class="hover:underline">GitHub ↗</a>
                                    @endif
                                    @if (!empty($project['live_demo_url']))
                                        <a href="{{ $project['live_demo_url'] }}" target="_blank" rel="noopener noreferrer"
                                           class="hover:underline">Live Demo ↗</a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if (!empty($education) || !empty($experience))
            <section class="mt-24 border-t border-orange-200 pt-8">
                <p class="text-sm font-bold uppercase tracking-widest text-orange-700">04 / Background</p>
                <div class="mt-6 grid gap-10 md:grid-cols-2">
                    @if (!empty($education))
                        <div>
                            <h2 class="text-2xl font-black">Education</h2>
                            <div class="mt-5 space-y-3">
                                @foreach ($education as $item)
                                    <div class="rounded-2xl border border-orange-200 bg-white p-5">
                                        <p class="whitespace-pre-line leading-7 text-stone-600">{{ $item }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (!empty($experience))
                        <div>
                            <h2 class="text-2xl font-black">Experience</h2>
                            <div class="mt-5 space-y-3">
                                @foreach ($experience as $item)
                                    <div class="rounded-2xl border border-orange-200 bg-white p-5">
                                        <p class="whitespace-pre-line leading-7 text-stone-600">{{ $item }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @if (!empty($socialLinks))
            <section class="mt-24 border-t border-orange-200 pt-8">
                <p class="text-sm font-bold uppercase tracking-widest text-orange-700">05 / Elsewhere</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    @foreach ($socialLinks as $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                           class="break-all rounded-full border border-orange-200 bg-white px-5 py-3 text-sm font-bold text-orange-700 hover:bg-orange-100">
                            {{ parse_url($url, PHP_URL_HOST) ?: $url }} ↗
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <footer class="border-t border-orange-200 px-5 py-8 text-center text-sm text-stone-500">
        {{ $portfolio->full_name }} · Designed with PortfolioGen · {{ now()->year }}
    </footer>
</div>


{{-- =========================================================
     CREATIVE TEMPLATE
     Dark canvas, oversized typography, numbered sections
========================================================= --}}
@else

<div class="min-h-screen overflow-hidden bg-slate-950 text-white">
    <header class="border-b border-slate-800">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-5 py-5">
            <a href="{{ $isPublic ? route('home') : route('portfolios.manage') }}"
               class="text-xl font-black tracking-tight">
                KC<span class="text-orange-400">®</span>
            </a>

            <span class="text-xs font-bold uppercase tracking-[0.25em] text-slate-500">
                Independent Portfolio
            </span>

            @unless ($isPublic)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('portfolios.edit', $portfolio) }}"
                       class="rounded-lg border border-slate-700 px-3 py-2 text-sm font-bold text-white hover:bg-slate-900">
                        Edit Details
                    </a>
                    <a href="{{ route('portfolios.templates.select', $portfolio) }}"
                       class="rounded-lg bg-orange-400 px-3 py-2 text-sm font-bold text-slate-950 hover:bg-orange-300">
                        Change Template
                    </a>
                </div>
            @endunless
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-5 py-14 sm:py-20">
        <section class="grid gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-orange-400">
                    Creative Portfolio / 01
                </p>

                <h1 class="mt-6 break-words text-6xl font-black uppercase leading-[0.9] tracking-tight sm:text-8xl">
                    {{ $portfolio->full_name }}
                    <span class="block text-orange-400">Makes.</span>
                </h1>

                <p class="mt-7 max-w-xl whitespace-pre-line text-lg leading-8 text-slate-400">
                    {{ $portfolio->about_me ?: 'Ideas, experiments, and projects. This is a collection of my work and what I care about.' }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @if ($portfolio->email)
                        <a href="mailto:{{ $portfolio->email }}"
                           class="rounded-none bg-orange-400 px-6 py-4 font-black uppercase tracking-wide text-slate-950 hover:bg-orange-300">
                            Contact Me ↗
                        </a>
                    @endif

                    @if ($portfolio->contact_number)
                        <span class="border border-slate-700 px-5 py-4 text-sm text-slate-300">
                            {{ $portfolio->contact_number }}
                        </span>
                    @endif
                </div>

                @if ($portfolio->address)
                    <p class="mt-4 text-sm text-slate-500">{{ $portfolio->address }}</p>
                @endif
            </div>

            <div class="relative">
                <div class="absolute -right-4 -top-4 h-full w-full border-2 border-orange-400"></div>

                @if ($profileImage)
                    <img src="{{ $profileImage }}" alt="{{ $portfolio->full_name }}"
                         class="relative h-80 w-full object-cover sm:h-[28rem]">
                @else
                    <div class="relative flex h-80 items-center justify-center bg-slate-900 text-7xl font-black text-orange-400 sm:h-[28rem]">
                        {{ strtoupper(substr($portfolio->full_name ?: 'P', 0, 2)) }}
                    </div>
                @endif

                <span class="absolute -bottom-4 -left-4 bg-orange-400 px-5 py-3 text-xs font-black uppercase tracking-widest text-slate-950">
                    The person behind the work
                </span>
            </div>
        </section>

        @if (!empty($skills))
            <section class="mt-24 border-t border-slate-800 pt-8">
                <p class="text-sm font-bold uppercase tracking-widest text-orange-400">02 / Arsenal</p>
                <h2 class="mt-3 text-4xl font-black uppercase">Skills & Tools</h2>

                <div class="mt-8 grid gap-0 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($skills as $skill)
                        <div class="flex items-center gap-4 border-b border-r border-slate-800 p-5">
                            <span class="text-sm font-black text-orange-400">
                                {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="text-lg font-bold">{{ $skill }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if (!empty($projects))
            <section class="mt-24 border-t border-slate-800 pt-8">
                <p class="text-sm font-bold uppercase tracking-widest text-orange-400">03 / Selected Experiments</p>
                <h2 class="mt-3 text-4xl font-black uppercase sm:text-5xl">Things I've Made.</h2>

                <div class="mt-10 space-y-10">
                    @foreach ($projects as $project)
                        <article class="grid gap-0 border border-slate-800 md:grid-cols-12">
                            <div class="flex flex-col justify-between bg-slate-900 p-6 md:col-span-4 sm:p-8">
                                <span class="text-5xl font-black text-orange-400">
                                    {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                </span>

                                <div class="mt-8">
                                    <h3 class="break-words text-2xl font-black uppercase">
                                        {{ $project['title'] ?? 'Project' }}
                                    </h3>

                                    @if (!empty($project['tech_stack']))
                                        <p class="mt-3 text-xs font-bold uppercase tracking-widest text-orange-400">
                                            {{ $project['tech_stack'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="md:col-span-8">
                                @if (!empty($project['screenshot']))
                                    <img src="{{ $portfolio->mediaUrl($project['screenshot'] ?? null) }}"
                                         alt="{{ $project['title'] ?? 'Project screenshot' }}"
                                         class="h-56 w-full object-cover sm:h-72">
                                @endif

                                <div class="p-6 sm:p-8">
                                    @if (!empty($project['description']))
                                        <p class="whitespace-pre-line leading-7 text-slate-400">
                                            {{ $project['description'] }}
                                        </p>
                                    @endif

                                    <div class="mt-6 flex flex-wrap gap-5 text-sm font-bold">
                                        @if (!empty($project['github_url']))
                                            <a href="{{ $project['github_url'] }}" target="_blank" rel="noopener noreferrer"
                                               class="text-orange-400 hover:text-orange-300">Explore Code ↗</a>
                                        @endif
                                        @if (!empty($project['live_demo_url']))
                                            <a href="{{ $project['live_demo_url'] }}" target="_blank" rel="noopener noreferrer"
                                               class="text-orange-400 hover:text-orange-300">View Live ↗</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if (!empty($education) || !empty($experience))
            <section class="mt-24 border-t border-slate-800 pt-8">
                <p class="text-sm font-bold uppercase tracking-widest text-orange-400">04 / The Journey</p>

                <div class="mt-8 grid gap-12 md:grid-cols-2">
                    @if (!empty($education))
                        <div>
                            <h2 class="text-2xl font-black uppercase">Education</h2>
                            <div class="mt-6 space-y-0">
                                @foreach ($education as $item)
                                    <div class="border-l-2 border-orange-400 py-3 pl-5">
                                        <span class="text-xs font-black uppercase tracking-widest text-orange-400">
                                            Education {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <p class="mt-2 whitespace-pre-line leading-7 text-slate-300">{{ $item }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (!empty($experience))
                        <div>
                            <h2 class="text-2xl font-black uppercase">Experience</h2>
                            <div class="mt-6 space-y-0">
                                @foreach ($experience as $item)
                                    <div class="border-l-2 border-slate-700 py-3 pl-5">
                                        <span class="text-xs font-black uppercase tracking-widest text-orange-400">
                                            Chapter {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <p class="mt-2 whitespace-pre-line leading-7 text-slate-300">{{ $item }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @if (!empty($socialLinks))
            <section class="mt-24 border-t border-slate-800 pt-8">
                <p class="text-sm font-bold uppercase tracking-widest text-orange-400">05 / Find Me</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    @foreach ($socialLinks as $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                           class="break-all border border-slate-700 px-5 py-4 text-sm font-bold text-white hover:border-orange-400 hover:text-orange-400">
                            {{ parse_url($url, PHP_URL_HOST) ?: $url }} ↗
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <footer class="border-t border-slate-800 px-5 py-8 text-center text-xs uppercase tracking-widest text-slate-500">
        {{ $portfolio->full_name }} · Made with PortfolioGen · {{ now()->year }}
    </footer>
</div>

@endif
