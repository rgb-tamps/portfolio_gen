<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div><h1 class="text-xl font-extrabold text-slate-900">Portfolio preview</h1><p class="mt-1 text-sm text-slate-600">Review your selected design before generating the public page.</p></div>
            <a href="{{ route('portfolios.manage') }}" class="text-sm font-semibold text-slate-600 hover:text-brand">← Manage portfolios</a>
        </div>
    </x-slot>
    @if (session('success')) <div role="status" class="mx-auto mt-5 max-w-5xl rounded-xl bg-green-50 p-4 text-green-800">{{ session('success') }}</div> @endif
    @include('portfolios._portfolio-content', ['portfolio' => $portfolio, 'isPublic' => false])
    <div class="border-t border-slate-200 bg-white px-4 py-6">
        <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-slate-600">{{ $portfolio->is_published ? 'Your portfolio is public.' : 'Generating publishes your portfolio at /p/'.$portfolio->slug.'.' }}</p>
            @if ($portfolio->is_published)
                <a href="{{ route('portfolios.public', $portfolio->slug) }}" target="_blank" rel="noopener" class="rounded-xl border border-slate-300 px-4 py-2.5 font-bold text-slate-700">Open public page ↗</a>
                <button type="button" onclick="navigator.clipboard.writeText(@js(route('portfolios.public', $portfolio->slug))).then(() => alert('Public link copied.')).catch(() => alert('Copy failed. Open the public page and copy the address.'))" class="rounded-xl bg-brand px-4 py-2.5 font-bold text-white hover:bg-brand-hover">Copy public link</button>
                <form method="POST" action="{{ route('portfolios.unpublish', $portfolio) }}">@csrf<button class="rounded-xl border border-slate-300 px-4 py-2.5 font-bold text-slate-700">Unpublish</button></form>
            @else
                <form method="POST" action="{{ route('portfolios.publish', $portfolio) }}">@csrf<button class="rounded-xl bg-brand px-4 py-2.5 font-bold text-white hover:bg-brand-hover">Generate and publish portfolio</button></form>
            @endif
        </div>
    </div>
</x-app-layout>
