{{--
    Pemakaian:
        <x-glass-login-button />
        <x-glass-login-button href="{{ route('login') }}">Login Petugas</x-glass-login-button>

    Catatan: efek blur baru terlihat jika di belakang tombol ada
    gambar / gradient (bukan latar putih polos).
--}}
@props(['href' => null])

<a href="{{ $href ?? route('login') }}"
   {{ $attributes->merge([
        'class' => 'inline-flex items-center gap-3 rounded-full border border-white/40 bg-white/20 py-2 pl-2 pr-6
                    text-slate-800 font-medium shadow-lg shadow-black/10 ring-1 ring-inset ring-white/30
                    backdrop-blur-xl backdrop-saturate-150 transition
                    hover:bg-white/30 active:scale-95',
   ]) }}>
    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-600 text-white shadow-md">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3" />
        </svg>
    </span>

    {{ $slot->isEmpty() ? 'Login Petugas' : $slot }}
</a>