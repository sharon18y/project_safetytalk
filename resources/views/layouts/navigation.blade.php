@php
    $authUser = Auth::user();
    // Anggap sebagai mode publik/manajemen jika belum login ATAU jika sesi lama masih role 'manajemen'
    $isPublicMode = !$authUser || $authUser->role === 'manajemen';

    $currentUser = $isPublicMode ? (object) [
        'name' => 'Direktur Operasi',
        'email' => 'Pemantauan Publik',
        'role' => 'manajemen',
    ] : $authUser;

    // Menu per role (kondisi sama dengan versi sebelumnya)
    $role = $currentUser->role;
    $menu = [
        ['label' => 'Dashboard', 'short' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard',
         'icon' => '<path d="M3 11.5L12 4l9 7.5"/><path d="M5 10v10h5v-6h4v6h5V10"/>'],
    ];
    if ($role === 'admin') {
        $menu[] = ['label' => 'Master Data', 'short' => 'Master', 'route' => 'admin.master.index', 'active' => 'admin.master.*',
         'icon' => '<path d="M4 6h16M4 12h16M4 18h16"/><circle cx="9" cy="6" r="2"/><circle cx="15" cy="12" r="2"/><circle cx="8" cy="18" r="2"/>'];
    }
    if ($role === 'koordinator' || $role === 'admin') {
        $menu[] = ['label' => 'Riwayat Laporan', 'short' => 'Riwayat', 'route' => 'koordinator.safety-talk.index', 'active' => ['koordinator.safety-talk.index', 'koordinator.safety-talk.show'],
         'icon' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'];
        $menu[] = ['label' => 'Buat Laporan', 'short' => 'Buat', 'route' => 'koordinator.safety-talk.create', 'active' => 'koordinator.safety-talk.create',
         'icon' => '<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>'];
    }
    if ($role === 'manajemen' || $role === 'admin') {
        $menu[] = ['label' => 'Laporan', 'short' => 'Laporan', 'route' => 'manajemen.pantau.index', 'active' => ['manajemen.pantau.*', 'manajemen.laporan.*'],
         'icon' => '<path d="M7 3h8l4 4v14H7z"/><path d="M15 3v4h4M10 13h6M10 17h6"/>'];
    }
    $inisial = strtoupper(mb_substr($currentUser->name, 0, 1));
    $aktif = collect($menu)->first(fn ($m) => request()->routeIs(...(array) $m['active']));
@endphp

{{-- Terapkan tema tersimpan secepat mungkin (cegah kedipan) --}}
<script>
    (function () {
        var t = localStorage.getItem('theme');
        if (t === 'dark' || (t === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    })();
</script>

<style>
    [x-cloak]{display:none!important}

    /* ===== Token tema ===== */
    :root{
        --bg:#E9EEF5; --glass:rgba(255,255,255,.62); --glass-strong:rgba(255,255,255,.9);
        --line:rgba(255,255,255,.8); --sep:rgba(60,60,67,.14); --shadow:0 8px 32px rgba(31,41,55,.08);
        --t1:#1C1C1E; --t2:#5B5B63; --track:rgba(60,60,67,.14); --hover:rgba(15,23,42,.06); --soft:rgba(120,120,128,.16);
        --pillbg:#1C1C1E; --pilltxt:#fff;
        --g1:rgba(52,199,89,.24); --g2:rgba(10,132,255,.20); --g3:rgba(255,149,0,.16);
        color-scheme:light;
    }
    .dark{
        --bg:#080C12; --glass:rgba(30,35,46,.55); --glass-strong:rgba(26,31,41,.94);
        --line:rgba(255,255,255,.12); --sep:rgba(255,255,255,.11); --shadow:0 8px 32px rgba(0,0,0,.45);
        --t1:#F5F5F7; --t2:#A9A9B2; --track:rgba(255,255,255,.16); --hover:rgba(255,255,255,.08); --soft:rgba(255,255,255,.12);
        --pillbg:#F5F5F7; --pilltxt:#0B0F14;
        --g1:rgba(52,199,89,.14); --g2:rgba(10,132,255,.16); --g3:rgba(255,149,0,.08);
        color-scheme:dark;
    }
    html{background:var(--bg)}
    body{
        color:var(--t1);
        background:
            radial-gradient(60rem 40rem at 8% -8%, var(--g1), transparent 60%),
            radial-gradient(50rem 40rem at 100% 0%, var(--g2), transparent 60%),
            radial-gradient(50rem 40rem at 50% 115%, var(--g3), transparent 60%),
            var(--bg);
        background-attachment:fixed;
    }
    .bg-gray-100{background:transparent!important}

    /* ===== Komponen glass ===== */
    .glass{background:var(--glass);-webkit-backdrop-filter:blur(24px) saturate(160%);backdrop-filter:blur(24px) saturate(160%);border:1px solid var(--line);box-shadow:var(--shadow);border-radius:24px}
    @supports not ((backdrop-filter:blur(1px)) or (-webkit-backdrop-filter:blur(1px))){.glass,.st-nav,.st-sheet{background:var(--glass-strong)}}
    .t1{color:var(--t1)} .t2{color:var(--t2)}
    .track{background:var(--track)}
    .sep{border-color:var(--sep)}
    .hov:hover{background:var(--hover)}
    .field{background:var(--soft);color:var(--t1);border:0}
    .field::placeholder{color:var(--t2)}
    .field:focus{background:var(--glass-strong);box-shadow:0 0 0 2px #0A84FF;outline:none}
    .btn-soft{background:var(--soft);color:var(--t1)}
    .btn-soft:hover{background:var(--track)}

    /* ===== Tombol kaca ala iOS (dipakai tombol Login Petugas) ===== */
    .btn-glass{
        color:var(--t1);
        background:linear-gradient(135deg,rgba(255,255,255,.85),rgba(255,255,255,.38));
        -webkit-backdrop-filter:blur(16px) saturate(180%);backdrop-filter:blur(16px) saturate(180%);
        border:1px solid rgba(255,255,255,.75);
        box-shadow:0 6px 18px rgba(31,41,55,.14),
                   inset 0 1px 0 rgba(255,255,255,.95),
                   inset 0 -1px 0 rgba(255,255,255,.3);
        transition:transform .15s,box-shadow .25s,background .25s;
    }
    .btn-glass:hover{
        background:linear-gradient(135deg,rgba(255,255,255,.95),rgba(255,255,255,.55));
        box-shadow:0 10px 26px rgba(31,41,55,.18),
                   inset 0 1px 0 #fff,
                   inset 0 -1px 0 rgba(255,255,255,.4);
    }
    .dark .btn-glass{
        background:linear-gradient(135deg,rgba(255,255,255,.20),rgba(255,255,255,.06));
        border:1px solid rgba(255,255,255,.22);
        box-shadow:0 6px 18px rgba(0,0,0,.45),
                   inset 0 1px 0 rgba(255,255,255,.30),
                   inset 0 -1px 0 rgba(255,255,255,.06);
    }
    .dark .btn-glass:hover{
        background:linear-gradient(135deg,rgba(255,255,255,.28),rgba(255,255,255,.10));
    }

    .pill-ok{background:rgba(52,199,89,.18);color:#1B7A37}
    .pill-warn{background:rgba(255,149,0,.18);color:#9A4F00}
    .pill-bad{background:rgba(255,59,48,.15);color:#C0271B}
    .pill-info{background:rgba(0,122,255,.15);color:#0A5CC2}
    .dark .pill-ok{background:rgba(52,199,89,.24);color:#6EE08C}
    .dark .pill-warn{background:rgba(255,159,10,.24);color:#FFB340}
    .dark .pill-bad{background:rgba(255,69,58,.26);color:#FF9A92}
    .dark .pill-info{background:rgba(10,132,255,.28);color:#8CC0FF}
    .txt-bad{color:#C0271B} .dark .txt-bad{color:#FF9A92}

    /* ===== Navigasi atas ===== */
    .st-nav{background:var(--glass);-webkit-backdrop-filter:blur(24px) saturate(180%);backdrop-filter:blur(24px) saturate(180%);border-bottom:1px solid var(--sep)}
    .nav-pill{position:absolute;top:0;bottom:0;left:0;border-radius:999px;background:var(--pillbg);opacity:0;pointer-events:none;will-change:transform,width;
        transition:transform .5s cubic-bezier(.32,.72,0,1),width .5s cubic-bezier(.32,.72,0,1),opacity .2s}
    .nav-pill.r20{border-radius:20px}
    .nav-link{position:relative;z-index:1;color:var(--t1);transition:color .3s}
    .nav-link[aria-current="page"]{color:var(--pilltxt)}
    .nav-link:not([aria-current="page"]):hover{color:var(--t2)}
    .dark .i-moon,html:not(.dark) .i-sun{display:none}

    /* ===== Tab bar bawah (HP) ===== */
    .tabbar{position:fixed;left:12px;right:12px;bottom:calc(12px + env(safe-area-inset-bottom,0px));z-index:40;padding:6px;border-radius:28px}
    .tab-link{position:relative;z-index:1;flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;
        min-height:56px;padding:6px 4px;border-radius:20px;color:var(--t2);font-size:12px;font-weight:600;line-height:1.1;
        -webkit-tap-highlight-color:transparent;transition:color .3s,transform .15s}
    .tab-link:active{transform:scale(.94)}
    .tab-link[aria-current="page"]{color:var(--pilltxt)}
    @media (max-width:639px){main{padding-bottom:calc(6.5rem + env(safe-area-inset-bottom,0px))}}

    /* ===== Sheet akun (HP) ===== */
    .scrim{position:fixed;inset:0;z-index:60;background:rgba(0,0,0,.42);opacity:0;pointer-events:none;transition:opacity .4s ease}
    .scrim.open{opacity:1;pointer-events:auto}
    .sheet{position:fixed;left:0;right:0;bottom:0;z-index:70;background:var(--glass-strong);-webkit-backdrop-filter:blur(20px);backdrop-filter:blur(20px);
        border-radius:28px 28px 0 0;border:1px solid var(--line);border-bottom:0;box-shadow:0 -12px 40px rgba(0,0,0,.2);
        padding:8px 16px calc(20px + env(safe-area-inset-bottom,0px));
        transform:translate3d(0,105%,0);will-change:transform;transition:transform .55s cubic-bezier(.32,.72,0,1)}
    .sheet.open{transform:translate3d(0,0,0)}
    .sheet .row{display:flex;align-items:center;justify-content:space-between;min-height:56px;padding:0 16px;font-size:17px;font-weight:500;width:100%;
        -webkit-tap-highlight-color:transparent;transition:background .15s}
    .sheet .row:active{background:var(--track)}

    /* ===== Transisi antar halaman (masuk detail / kembali / pindah tab) ===== */
    @keyframes st-in-fwd{from{opacity:0;transform:translate3d(32px,0,0)}to{opacity:1;transform:none}}
    @keyframes st-in-back{from{opacity:0;transform:translate3d(-32px,0,0)}to{opacity:1;transform:none}}
    @keyframes st-in-tab{from{opacity:0}to{opacity:1}}
    @keyframes st-out-fwd{to{opacity:0;transform:translate3d(-32px,0,0)}}
    @keyframes st-out-back{to{opacity:0;transform:translate3d(32px,0,0)}}
    @keyframes st-out-tab{to{opacity:0}}
    html.st-in-fwd main{animation:st-in-fwd .45s cubic-bezier(.32,.72,0,1) both}
    html.st-in-back main{animation:st-in-back .45s cubic-bezier(.32,.72,0,1) both}
    html.st-in-tab main{animation:st-in-tab .3s ease-out both}
    html.st-out-fwd main{animation:st-out-fwd .24s cubic-bezier(.4,0,1,1) both}
    html.st-out-back main{animation:st-out-back .24s cubic-bezier(.4,0,1,1) both}
    html.st-out-tab main{animation:st-out-tab .2s ease-in both}
    @media (prefers-reduced-motion:reduce){html[class*="st-in-"] main,html[class*="st-out-"] main{animation:none}}

    /* ===== Animasi ganti tema (lingkaran dari titik klik) ===== */
    ::view-transition-old(root),::view-transition-new(root){animation:none;mix-blend-mode:normal}
    ::view-transition-old(root){z-index:1}
    ::view-transition-new(root){z-index:2}
    html.st-fade,html.st-fade *{transition:background-color .8s ease,color .8s ease,border-color .8s ease!important}
    @media (prefers-reduced-motion:reduce){.nav-pill,.nav-link,.tab-link,.sheet,.scrim{transition:none}}

    /* ===== Penyesuaian mode malam untuk halaman lama ===== */
    header.bg-white{background:var(--glass);-webkit-backdrop-filter:blur(24px);backdrop-filter:blur(24px);box-shadow:none;border-bottom:1px solid var(--sep)}
    .dark .bg-white{background-color:rgba(30,35,46,.78)}
    .dark .bg-gray-50,.dark .bg-blue-50,.dark .bg-emerald-50,.dark .bg-amber-50{background-color:rgba(255,255,255,.06)}
    .dark .text-gray-900,.dark .text-gray-800,.dark .text-gray-700,.dark .text-emerald-800,.dark .text-blue-800{color:var(--t1)}
    .dark .text-gray-600,.dark .text-gray-500,.dark .text-gray-400{color:var(--t2)}
    .dark .border-gray-100,.dark .border-gray-200,.dark .border-gray-300{border-color:var(--sep)}
    .dark .hover\:bg-gray-100:hover,.dark .hover\:bg-gray-50:hover{background-color:rgba(255,255,255,.08)}
    .dark input:not([type=checkbox]):not([type=radio]),.dark select,.dark textarea{background-color:rgba(255,255,255,.07);color:var(--t1);border-color:var(--sep)}
</style>

{{-- display:contents menjaga nav tetap sticky, sementara tab bar & sheet berada di luar nav --}}
<div class="contents"
     x-data="{
        open: false, dy: 0, y0: null,
        start(e) { this.y0 = e.touches[0].clientY },
        move(e) { if (this.y0 !== null) this.dy = Math.max(0, e.touches[0].clientY - this.y0) },
        end() { if (this.dy > 90) this.open = false; this.dy = 0; this.y0 = null }
     }"
     x-effect="document.body.style.overflow = open ? 'hidden' : ''"
     @keydown.escape.window="open = false">

    <nav class="st-nav sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">

                {{-- Menu desktop (pill hitam bergeser mengikuti halaman aktif) --}}
                <div id="nav-links" class="relative hidden sm:flex items-center gap-1">
                    <span id="nav-pill" class="nav-pill" aria-hidden="true"></span>
                    @foreach($menu as $item)
                        <a href="{{ route($item['route']) }}"
                           @if(request()->routeIs(...(array) $item['active'])) aria-current="page" @endif
                           class="nav-link px-4 py-2 rounded-full text-[15px] font-semibold whitespace-nowrap focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-300/60">
                            {{ __($item['label']) }}
                        </a>
                    @endforeach
                </div>

                {{-- Judul halaman (HP) --}}
                <p class="sm:hidden text-[22px] font-bold tracking-tight t1">{{ $aktif ? __($aktif['label']) : 'Dashboard' }}</p>

                <div class="flex items-center gap-2">
                    {{-- Mode malam --}}
                    <button type="button" data-theme-toggle onclick="stToggleTheme(event)" aria-label="Ganti mode terang atau malam" aria-pressed="false"
                            class="w-11 h-11 rounded-full inline-flex items-center justify-center t1 hov transition focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-300/60">
                        <svg class="i-moon w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z"/></svg>
                        <svg class="i-sun w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                    </button>

                    @if($isPublicMode)
                        {{-- TOMBOL LOGIN LANGSUNG (Saat mode publik / Manajemen) - gaya iOS glass --}}
                        <a href="{{ route('login') }}"
                           data-nav="fwd"
                           class="inline-flex items-center gap-2.5 pl-2 pr-4 py-1.5 min-h-[44px] rounded-full btn-glass active:scale-95 focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-300/60">
                            <span class="w-8 h-8 rounded-full bg-emerald-700 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-900/30 ring-1 ring-inset ring-white/25">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                    <polyline points="10 17 15 12 10 7"/>
                                    <line x1="15" y1="12" x2="3" y2="12"/>
                                </svg>
                            </span>
                            <span class="text-sm font-semibold t1">Login Petugas</span>
                        </a>
                    @else
                        {{-- Akun Koordinator / Admin (desktop) --}}
                        <div class="hidden sm:flex sm:items-center">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center gap-3 pl-1.5 pr-3 py-1.5 rounded-full btn-soft transition focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-300/60">
                                        <span class="w-8 h-8 rounded-full bg-emerald-700 text-white text-sm font-bold flex items-center justify-center">{{ $inisial }}</span>
                                        <span class="text-sm font-semibold">{{ $currentUser->name }}</span>
                                        <span class="text-xs font-semibold t2">{{ strtoupper($currentUser->role) }}</span>
                                        <svg class="h-4 w-4 t2" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('login')">
                                        {{ __('Ganti Akun (Login)') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Kembali ke Mode Publik (Log Out)') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        {{-- Avatar: buka sheet akun (HP) --}}
                        <button type="button" @click="open = true" aria-label="Buka akun"
                                class="sm:hidden w-11 h-11 rounded-full bg-emerald-700 text-white font-bold inline-flex items-center justify-center active:scale-95 transition focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-300/60">
                            {{ $inisial }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- Tab bar bawah (HP) --}}
    <div class="tabbar glass sm:hidden">
        <div id="tab-links" class="relative flex items-stretch">
            <span id="tab-pill" class="nav-pill r20" aria-hidden="true"></span>
            @foreach($menu as $item)
                <a href="{{ route($item['route']) }}"
                   @if(request()->routeIs(...(array) $item['active'])) aria-current="page" @endif
                   class="tab-link">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">{!! $item['icon'] !!}</svg>
                    <span>{{ __($item['short']) }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Sheet akun (HP): khusus saat Koordinator / Admin sedang login --}}
    @if(!$isPublicMode)
        <div class="scrim sm:hidden" :class="{ 'open': open }" @click="open = false" aria-hidden="true"></div>
        <div class="sheet sm:hidden" :class="{ 'open': open }"
             :style="dy ? 'transform:translate3d(0,' + dy + 'px,0);transition:none' : ''"
             role="dialog" aria-modal="true" aria-label="Akun" x-bind:inert="!open">

            <div @touchstart.passive="start($event)" @touchmove.passive="move($event)" @touchend="end()" @touchcancel="end()" style="touch-action:none">
                <div class="mx-auto mt-1 mb-4 w-10 h-1.5 rounded-full" style="background:var(--track)"></div>
                <div class="flex items-center gap-4 pb-5">
                    <span class="w-14 h-14 rounded-full bg-emerald-700 text-white text-xl font-bold flex items-center justify-center shrink-0">{{ $inisial }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="text-xl font-bold tracking-tight t1 truncate">{{ $currentUser->name }}</div>
                        <div class="text-sm t2 truncate">{{ $currentUser->email }}</div>
                        <span class="pill-ok mt-1.5 inline-block uppercase text-[11px] font-semibold px-2.5 py-1 rounded-full">{{ $currentUser->role }}</span>
                    </div>
                    <button type="button" @click="open = false" aria-label="Tutup"
                            class="w-11 h-11 rounded-full btn-soft inline-flex items-center justify-center shrink-0 active:scale-95 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="rounded-2xl overflow-hidden" style="background:var(--soft)">
                <a href="{{ route('login') }}" class="row t1">
                    <span>{{ __('Ganti Akun (Login)') }}</span>
                    <svg class="w-5 h-5 t2" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                </a>
                <div class="sep border-t ml-4"></div>
                <a href="{{ route('profile.edit') }}" class="row t1">
                    <span>{{ __('Profile') }}</span>
                    <svg class="w-5 h-5 t2" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                </a>
                <div class="sep border-t ml-4"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="row txt-bad" style="text-align:left">{{ __('Log Out') }}</button>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
    /* Transisi halaman: halaman lama bergeser keluar, halaman baru masuk seperti iOS */
    (function () {
        var root = document.documentElement, K = 'navdir', d = null;
        try { d = sessionStorage.getItem(K); sessionStorage.removeItem(K); } catch (e) {}
        if (d === 'fwd' || d === 'back' || d === 'tab') root.classList.add('st-in-' + d);
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) root.classList.remove('st-out-fwd', 'st-out-back', 'st-out-tab');
        });
        window.stGo = function (href, dir) {
            try { sessionStorage.setItem(K, dir); } catch (e) {}
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { window.location.href = href; return; }
            root.classList.add('st-out-' + dir);
            setTimeout(function () { window.location.href = href; }, 240);
        };
        document.addEventListener('click', function (e) {
            var a = e.target.closest && e.target.closest('a[data-nav]');
            if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0 || a.target === '_blank') return;
            e.preventDefault();
            window.stGo(a.href, a.getAttribute('data-nav'));
        });
    })();
</script>

<script>
    /* Ganti tema: gelap menyebar perlahan dari titik yang diklik */
    function stToggleTheme(ev) {
        var root = document.documentElement;
        var next = !root.classList.contains('dark');
        var apply = function () {
            root.classList.toggle('dark', next);
            try { localStorage.setItem('theme', next ? 'dark' : 'light'); } catch (e) {}
            document.querySelectorAll('[data-theme-toggle]').forEach(function (b) { b.setAttribute('aria-pressed', next); });
        };
        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduce) { apply(); return; }
        if (!document.startViewTransition) {
            root.classList.add('st-fade'); apply();
            setTimeout(function () { root.classList.remove('st-fade'); }, 900);
            return;
        }
        var r = ev.currentTarget.getBoundingClientRect();
        var x = ev.clientX || r.left + r.width / 2, y = ev.clientY || r.top + r.height / 2;
        var rad = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));
        var t = document.startViewTransition(apply);
        t.ready.then(function () {
            root.animate(
                { clipPath: ['circle(0px at ' + x + 'px ' + y + 'px)', 'circle(' + rad + 'px at ' + x + 'px ' + y + 'px)'] },
                { duration: 900, easing: 'cubic-bezier(.45,0,.2,1)', pseudoElement: '::view-transition-new(root)' }
            );
        });
    }
    document.querySelectorAll('[data-theme-toggle]').forEach(function (b) {
        b.setAttribute('aria-pressed', document.documentElement.classList.contains('dark'));
    });

    /* Pill menu: geser mulus ke menu yang diketuk, lalu pindah halaman (desktop & tab bar HP) */
    function stPill(wrapId, pillId) {
        var wrap = document.getElementById(wrapId), pill = document.getElementById(pillId);
        if (!wrap || !pill) return;
        function place(el, anim) {
            pill.style.transition = anim ? '' : 'none';
            pill.style.width = el.offsetWidth + 'px';
            pill.style.transform = 'translate3d(' + el.offsetLeft + 'px,0,0)';
        }
        function current() { return wrap.querySelector('[aria-current="page"]'); }
        function init() { var a = current(); if (a && a.offsetWidth) { place(a, false); pill.style.opacity = 1; } }
        init();
        window.addEventListener('resize', init);
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(init);
        wrap.addEventListener('click', function (e) {
            var a = e.target.closest('a');
            if (!a || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
            if (a.getAttribute('aria-current') === 'page') return;
            e.preventDefault();
            wrap.querySelectorAll('a').forEach(function (x) { x.removeAttribute('aria-current'); });
            a.setAttribute('aria-current', 'page');
            pill.style.opacity = 1;
            place(a, true);
            if (navigator.vibrate) navigator.vibrate(8);
            try { sessionStorage.setItem('navdir', 'tab'); } catch (err) {}
            document.documentElement.classList.add('st-out-tab');
            setTimeout(function () { window.location.href = a.href; }, 340);
        });
    }
    stPill('nav-links', 'nav-pill');
    stPill('tab-links', 'tab-pill');
</script>