<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-slate-200 bg-white">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-8">
            <a href="{{ route('home') }}" class="text-lg font-extrabold tracking-tight text-slate-950" aria-label="PortfolioGen home">
                Portfolio<span class="text-brand">Gen</span>
            </a>
            <div class="hidden items-center gap-6 sm:flex">
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold {{ request()->routeIs('dashboard') ? 'text-brand' : 'text-slate-600 hover:text-brand' }}">Dashboard</a>
                <a href="{{ route('portfolios.manage') }}" class="text-sm font-semibold {{ request()->routeIs('portfolios.manage') ? 'text-brand' : 'text-slate-600 hover:text-brand' }}">My portfolios</a>
                <a href="{{ route('portfolios.create') }}" class="text-sm font-semibold {{ request()->routeIs('portfolios.create') ? 'text-brand' : 'text-slate-600 hover:text-brand' }}">Create portfolio</a>
            </div>
        </div>

        <div class="hidden items-center gap-4 sm:flex">
            <a href="{{ route('profile.edit') }}" class="text-sm font-semibold text-slate-600 hover:text-brand">{{ Auth::user()->name }}</a>
            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Are you sure you want to log out?')">
                @csrf
                <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Log out</button>
            </form>
        </div>

        <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="mobile-navigation" class="rounded-lg p-2 text-slate-700 hover:bg-slate-100 focus-visible:outline-brand sm:hidden">
            <span class="sr-only">Toggle navigation</span>
            <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
            <svg x-cloak x-show="open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <div id="mobile-navigation" x-cloak x-show="open" class="space-y-1 border-t border-slate-200 px-4 py-3 sm:hidden">
        <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-brand-light hover:text-brand">Dashboard</a>
        <a href="{{ route('portfolios.manage') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-brand-light hover:text-brand">My portfolios</a>
        <a href="{{ route('portfolios.create') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('portfolios.create') ? 'text-brand' : 'text-slate-700 hover:bg-brand-light hover:text-brand' }}">Create portfolio</a>
        <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-brand-light hover:text-brand">Profile settings</a>
        <form method="POST" action="{{ route('logout') }}" class="px-3 py-2" onsubmit="return confirm('Are you sure you want to log out?')">
            @csrf
            <button type="submit" class="text-sm font-semibold text-slate-700 hover:text-brand">Log out</button>
        </form>
    </div>
</nav>
