<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div><h1 class="text-xl font-extrabold text-slate-900">Create portfolio</h1><p class="mt-1 text-sm text-slate-500">Add your details in six short steps.</p></div>
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-brand">← Dashboard</a>
        </div>
    </x-slot>
    <div class="min-h-screen bg-page px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-7xl">
            @if (session('success')) <div role="status" class="mb-5 rounded-xl bg-green-50 p-4 text-green-800">{{ session('success') }}</div> @endif
            @include('portfolios._form', ['portfolio' => $portfolio])
        </div>
    </div>
</x-app-layout>
