<x-guest-layout>

    <div class="mb-6">
        <a href="{{ route('home') }}" class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-brand">
            <span aria-hidden="true">←</span> Back to home
        </a>
        <div class="text-center">
            <h1 class="text-3xl font-bold text-[#E93F1A]">
                Welcome back
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                Log in to manage your portfolio.
            </p>
        </div>
    </div>

    <!-- Keep the rest of your existing login code below -->

<x-auth-session-status class="mb-4" :status="session('status')" />

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input
            id="email"
            class="block mt-1 w-full"
            type="email"
            name="email"
            :value="old('email')"
            required
            autofocus
            autocomplete="username"
        />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input
            id="password"
            class="block mt-1 w-full"
            type="password"
            name="password"
            required
            autocomplete="current-password"
        />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="mt-4">
        <label for="remember_me" class="inline-flex items-center">
            <input
                id="remember_me"
                type="checkbox"
                class="rounded border-gray-300 text-[#E93F1A] shadow-sm focus:ring-[#E93F1A]"
                name="remember"
            >
            <span class="ms-2 text-sm text-gray-600">Remember me</span>
        </label>
    </div>

    <div class="mt-5">
        <x-primary-button class="w-full justify-center bg-[#E93F1A] hover:bg-[#C83214]">
            {{ __('Log in') }}
        </x-primary-button>
    </div>

    @if (Route::has('password.request'))
        <div class="mt-4 text-center">
            <a href="{{ route('password.request') }}"
               class="text-sm text-gray-600 underline hover:text-[#E93F1A]">
                Forgot your password?
            </a>
        </div>
    @endif

  <div class="mt-6 border-t border-slate-200 pt-5 text-center">
    <p class="text-sm text-slate-600">
        Don't have an account?
        <a href="{{ route('register') }}"
           class="font-semibold text-brand hover:text-brand-hover">
            Create an account
        </a>
    </p>
</div>
</form>

</x-guest-layout>