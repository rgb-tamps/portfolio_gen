<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div><h1 class="text-xl font-extrabold text-slate-900">Edit portfolio</h1><p class="mt-1 text-sm text-slate-500">Update your details and save your changes.</p></div>
            <a href="{{ route('portfolios.preview', $portfolio) }}" class="text-sm font-semibold text-slate-600 hover:text-brand">← Back to preview</a>
        </div>
    </x-slot>
    <div class="min-h-screen bg-page px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-7xl">
            @include('portfolios._form', ['portfolio' => $portfolio])
        </div>
    </div>
</x-app-layout>
