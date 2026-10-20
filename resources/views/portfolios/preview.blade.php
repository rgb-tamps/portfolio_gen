<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div><h1 class="text-xl font-extrabold text-slate-900">Portfolio preview</h1><p class="mt-1 text-sm text-slate-600">Review your selected design before generating the public page.</p></div>
            <a href="{{ route('portfolios.manage') }}" class="text-sm font-semibold text-slate-600 hover:text-brand">← Manage portfolios</a>
        </div>
    </x-slot>
    @if (session('success')) <div role="status" class="mx-auto mt-5 max-w-5xl rounded-xl bg-green-50 p-4 text-green-800">{{ session('success') }}</div> @endif
    @include('portfolios._portfolio-content', ['portfolio' => $portfolio, 'isPublic' => false])
    @if ($portfolio->is_published)
        <div class="border-t border-slate-200 bg-white px-4 py-6">
            <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-slate-600">Your portfolio is public.</p>
                <button type="button" onclick="navigator.clipboard.writeText(@js(route('portfolios.public', $portfolio->slug))).then(() => alert('Public link copied.')).catch(() => alert('Copy failed. Open the public page and copy the address.'))" class="rounded-xl bg-brand px-4 py-2.5 font-bold text-white hover:bg-brand-hover">Copy public link</button>
            </div>
        </div>
    @endif
</x-app-layout>
