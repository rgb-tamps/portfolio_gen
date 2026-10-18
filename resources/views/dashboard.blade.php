
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-bold text-slate-900">Dashboard</h2>

            <a href="{{ route('portfolios.create') }}"
               class="rounded-xl bg-brand px-4 py-2 font-bold text-white hover:bg-brand-hover">
                + Create portfolio
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-page px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-7xl">

            @if (session('success'))
                <div role="status" class="mb-5 rounded-xl bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <p class="text-sm font-bold uppercase tracking-widest text-brand">
                Your workspace
            </p>

            <h1 class="mt-2 text-3xl font-extrabold text-slate-950">
                Welcome, {{ auth()->user()->name }}
            </h1>

            <p class="mt-2 text-slate-600">
                Create a portfolio, choose a design, preview it, and publish when it is ready.
            </p>

            <div class="mt-7 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border bg-white p-5">
                    <p class="text-sm text-slate-500">My portfolios</p>
                    <p class="mt-2 text-3xl font-extrabold">
                        {{ $portfolios->count() }}
                    </p>
                </div>

                <div class="rounded-2xl border bg-white p-5">
                    <p class="text-sm text-slate-500">Published</p>
                    <p class="mt-2 text-3xl font-extrabold">
                        {{ $portfolios->where('is_published', true)->count() }}
                    </p>
                </div>

                <div class="rounded-2xl border bg-white p-5">
                    <p class="text-sm text-slate-500">Templates</p>
                    <p class="mt-2 text-3xl font-extrabold">3</p>
                    <p class="text-xs text-slate-500">Simple · Modern · Creative</p>
                </div>
            </div>

            <div class="mt-8 flex items-end justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold">Recent portfolios</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Your saved work loads from the database.
                    </p>
                </div>

                <a href="{{ route('portfolios.manage') }}"
                   class="font-semibold text-brand hover:underline">
                    Manage all →
                </a>
            </div>

            @if ($portfolios->isEmpty())
                <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-light text-3xl font-bold text-brand">
                        +
                    </div>

                    <h3 class="mt-4 text-xl font-bold">
                        Your first portfolio starts here
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-slate-600">
                        Add your information, select one of three templates, and publish a shareable portfolio.
                    </p>

                    <a href="{{ route('portfolios.create') }}"
                       class="mt-5 inline-flex rounded-xl bg-brand px-5 py-3 font-bold text-white hover:bg-brand-hover">
                        Create Portfolio
                    </a>
                </div>
            @else
                <div class="mt-5 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($portfolios as $portfolio)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-center gap-3">
                                @if ($portfolio->profile_picture)
                                    <img
                                        src="{{ $portfolio->mediaUrl($portfolio->profile_picture) }}"
                                        alt="{{ $portfolio->full_name }}'s profile picture"
                                        class="h-14 w-14 rounded-full object-cover"
                                    >
                                @else
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-light font-bold text-brand">
                                        {{ strtoupper(substr($portfolio->full_name, 0, 2)) }}
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <h3 class="truncate font-bold">
                                        {{ $portfolio->full_name }}
                                    </h3>

                                    <p class="text-sm text-slate-500">
                                        {{ ucfirst($portfolio->template) }}
                                        ·
                                        {{ $portfolio->is_published ? 'Published' : 'Draft' }}
                                    </p>
                                </div>
                            </div>

                            <p class="mt-4 line-clamp-3 text-sm text-slate-600">
                                {{ $portfolio->about_me ?: 'Add an introduction to your portfolio.' }}
                            </p>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('portfolios.preview', $portfolio) }}"
                                   class="rounded-lg border px-3 py-2 text-sm font-semibold">
                                    Preview
                                </a>

                                <a href="{{ route('portfolios.edit', $portfolio) }}"
                                   class="rounded-lg border px-3 py-2 text-sm font-semibold">
                                    Edit
                                </a>

                                <a href="{{ route('portfolios.manage') }}"
                                   class="rounded-lg bg-brand-light px-3 py-2 text-sm font-semibold text-brand">
                                    Manage
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
