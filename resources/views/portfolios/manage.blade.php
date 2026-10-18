<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><h1 class="text-xl font-extrabold text-slate-900">Manage portfolios</h1><p class="mt-1 text-sm text-slate-500">Edit, preview, publish, or delete your work.</p></div>
            <a href="{{ route('portfolios.create') }}" class="rounded-xl bg-brand px-4 py-2.5 font-bold text-white hover:bg-brand-hover">+ Create portfolio</a>
        </div>
    </x-slot>
    <div class="min-h-screen bg-page px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-7xl">
            @if (session('success')) <div role="status" class="mb-5 rounded-xl bg-green-50 p-4 text-green-800">{{ session('success') }}</div> @endif
            @if ($portfolios->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                    <h2 class="text-xl font-bold text-slate-900">No portfolios yet</h2>
                    <p class="mt-2 text-slate-600">Create your first portfolio to get started.</p>
                    <a href="{{ route('portfolios.create') }}" class="mt-5 inline-flex rounded-xl bg-brand px-5 py-3 font-bold text-white hover:bg-brand-hover">Create Portfolio</a>
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($portfolios as $portfolio)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    @if ($portfolio->profile_picture)
                                        <img src="{{ $portfolio->mediaUrl($portfolio->profile_picture) }}" alt="" class="h-12 w-12 rounded-full object-cover">
                                    @else
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-light font-bold text-brand">{{ strtoupper(substr($portfolio->full_name, 0, 2)) }}</div>
                                    @endif
                                    <div class="min-w-0"><h2 class="truncate font-bold text-slate-900">{{ $portfolio->full_name }}</h2><p class="text-sm text-slate-500">{{ ucfirst($portfolio->template) }} template</p></div>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $portfolio->is_published ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">{{ $portfolio->is_published ? 'Published' : 'Draft' }}</span>
                            </div>
                            <p class="mt-4 line-clamp-3 min-h-12 text-sm leading-6 text-slate-600">{{ $portfolio->about_me ?: 'Add an introduction to make this portfolio stand out.' }}</p>
                            <div class="mt-5 flex flex-wrap gap-2">
                                <a href="{{ route('portfolios.preview', $portfolio) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold hover:bg-slate-50">View</a>
                                <a href="{{ route('portfolios.edit', $portfolio) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold hover:bg-slate-50">Edit</a>
                                @if ($portfolio->is_published)
                                    <a href="{{ route('portfolios.public', $portfolio->slug) }}" target="_blank" rel="noopener" class="rounded-lg bg-brand-light px-3 py-2 text-sm font-semibold text-brand">Public link ↗</a>
                                @endif
                            </div>
                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                                @if ($portfolio->is_published)
                                    <form method="POST" action="{{ route('portfolios.unpublish', $portfolio) }}">@csrf<button class="text-sm font-semibold text-amber-700 hover:underline">Unpublish</button></form>
                                @else
                                    <form method="POST" action="{{ route('portfolios.publish', $portfolio) }}">@csrf<button class="text-sm font-semibold text-green-700 hover:underline">Publish</button></form>
                                @endif
                                <form method="POST" action="{{ route('portfolios.destroy', $portfolio) }}" onsubmit="return confirm('Delete this portfolio permanently?')">
                                    @csrf @method('DELETE')
                                    <button class="text-sm font-semibold text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                            @if ($portfolio->is_published)
                                @include('portfolios._qr-code', ['portfolio' => $portfolio])
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
