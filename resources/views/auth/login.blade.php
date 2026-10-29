<x-guest-layout>
    <div class="mb-9 text-center">
        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-indigo-700/70">Safety Talk</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">Selamat datang</h1>
        <p class="mt-2 text-sm text-slate-500">Masuk sebagai petugas untuk melanjutkan</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-5 rounded-full bg-emerald-50/80 px-4 py-2 text-center text-sm text-emerald-700 backdrop-blur">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="mb-1.5 block px-4 text-sm font-medium text-slate-600">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username" placeholder="nama@email.com"
                   class="block w-full rounded-full border border-white/70 bg-white/60 px-5 py-3.5
                          text-slate-800 placeholder-slate-400 shadow-sm shadow-indigo-900/5
                          backdrop-blur-md transition
                          focus:border-indigo-300 focus:bg-white/85 focus:ring-4 focus:ring-indigo-200/60" />
            @error('email')
                <p class="mt-2 px-4 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="mb-1.5 block px-4 text-sm font-medium text-slate-600">Password</label>
            <div x-data="{ show: false }" class="relative">
                <input id="password" :type="show ? 'text' : 'password'" name="password"
                       required autocomplete="current-password" placeholder="Masukkan password"
                       class="block w-full rounded-full border border-white/70 bg-white/60 py-3.5 pl-5 pr-12
                              text-slate-800 placeholder-slate-400 shadow-sm shadow-indigo-900/5
                              backdrop-blur-md transition
                              focus:border-indigo-300 focus:bg-white/85 focus:ring-4 focus:ring-indigo-200/60" />
                <button type="button" @click="show = !show"
                        :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition hover:text-slate-600 focus:outline-none">
                    <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg x-show="show" style="display:none" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M17.9 17.9A10.9 10.9 0 0112 19c-6.4 0-10-7-10-7a18.5 18.5 0 015.1-5.9M9.9 5.2A10.4 10.4 0 0112 5c6.4 0 10 7 10 7a18.6 18.6 0 01-2.2 3.2M1 1l22 22"/><path d="M14.1 14.1a3 3 0 11-4.2-4.2"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-2 px-4 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me + Lupa password -->
        <div class="flex items-center justify-between px-2">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                       class="rounded-md border-slate-300 bg-white/70 text-indigo-600 focus:ring-indigo-300">
                <span class="ms-2 text-sm text-slate-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm font-medium text-indigo-600 transition hover:text-indigo-800 focus:outline-none">
                    Lupa password?
                </a>
            @endif
        </div>

        <button type="submit"
                class="w-full rounded-full bg-gradient-to-r from-indigo-600 to-violet-600 px-4 py-3.5
                       font-semibold text-white shadow-lg shadow-indigo-500/30 transition
                       hover:from-indigo-500 hover:to-violet-500 hover:shadow-xl hover:shadow-indigo-500/40
                       focus:outline-none focus:ring-4 focus:ring-indigo-300/60 active:scale-[0.98]">
            Masuk
        </button>
    </form>
</x-guest-layout>