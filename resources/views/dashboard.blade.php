<x-app-layout>
    <div class="py-6 sm:py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Sapaan --}}
            <div class="space-y-3">
                <p class="text-sm font-medium t2">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                <h3 class="text-3xl sm:text-4xl font-bold tracking-tight t1">Selamat datang, {{ $user->name }}!</h3>
                <p class="flex items-center gap-2 text-[15px] t2">
                    <span>Anda login sebagai</span>
                    @if($user->role === 'admin')
                        <span class="pill-ok uppercase text-xs font-semibold px-3 py-1.5 rounded-full">{{ $user->role }}</span>
                    @elseif($user->role === 'koordinator')
                        <span class="pill-warn uppercase text-xs font-semibold px-3 py-1.5 rounded-full">{{ $user->role }}</span>
                    @else
                        <span class="pill-info uppercase text-xs font-semibold px-3 py-1.5 rounded-full">{{ $user->role }}</span>
                    @endif
                </p>
            </div>

            {{-- Statistik (admin & manajemen: ketuk untuk melihat siapa saja yang sudah melapor) --}}
            @php $bisaLihat = in_array($user->role, ['admin', 'manajemen'], true); @endphp
            <{{ $bisaLihat ? 'a' : 'div' }}
                @if($bisaLihat) href="{{ route('manajemen.pantau.index', ['filter' => 'sudah']) }}" data-nav="fwd" @endif
                class="glass p-6 flex items-center justify-between gap-4 {{ $bisaLihat ? 'block active:scale-[.99] transition motion-reduce:transition-none' : '' }}">
                <div>
                    <p class="text-sm font-medium t2">Laporan masuk hari ini</p>
                    <p class="mt-1 flex items-baseline gap-2">
                        <span class="text-5xl font-bold tracking-tight t1">{{ $laporanHariIni }}</span>
                        <span class="text-[15px] font-medium t2">Unit / Shift</span>
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <div class="pill-ok w-14 h-14 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>
                    </div>
                    @if($bisaLihat)
                        <svg class="w-5 h-5 t2" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                    @endif
                </div>
            </{{ $bisaLihat ? 'a' : 'div' }}>

            {{-- Kartu aksi sesuai role --}}
            <div class="glass p-6 sm:p-8">

                @if($user->role === 'admin')
                    <div class="flex items-start gap-4">
                        <div class="pill-ok w-12 h-12 rounded-2xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/><circle cx="9" cy="6" r="2"/><circle cx="15" cy="12" r="2"/><circle cx="8" cy="18" r="2"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold tracking-tight t1">Admin K3</h4>
                            <p class="mt-1 text-[15px] leading-relaxed t2">Sebagai Admin K3, Anda dapat mengelola Master Data dan melihat seluruh matriks laporan Safety Talk.</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.master.index') }}" class="mt-6 inline-flex items-center justify-center w-full sm:w-auto min-h-[48px] px-7 rounded-full bg-emerald-700 text-white font-semibold hover:bg-emerald-800 active:scale-[.98] transition motion-reduce:transition-none focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-300/60">
                        Kelola Master Data
                    </a>

                @elseif($user->role === 'koordinator')
                    <div class="flex items-start gap-4">
                        <div class="pill-warn w-12 h-12 rounded-2xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h8l4 4v14H7z"/><path d="M15 3v4h4M10 13h6M10 17h6"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold tracking-tight t1">Laporan harian</h4>
                            <p class="mt-1 text-[15px] leading-relaxed t2">Sebagai Koordinator, silakan isi laporan harian Safety Talk untuk unit kerja yang ditugaskan kepada Anda (maksimal H+1).</p>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('koordinator.safety-talk.create') }}" class="inline-flex items-center justify-center w-full sm:w-auto min-h-[48px] px-7 rounded-full bg-[#FF9500] text-[#1C1C1E] font-semibold hover:bg-[#E68600] active:scale-[.98] transition motion-reduce:transition-none focus:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/60">
                            Buat Laporan Baru
                        </a>
                        <a href="{{ route('koordinator.safety-talk.index') }}" class="btn-soft inline-flex items-center justify-center w-full sm:w-auto min-h-[48px] px-7 rounded-full font-semibold active:scale-[.98] transition motion-reduce:transition-none focus:outline-none focus-visible:ring-4 focus-visible:ring-gray-300/60">
                            Riwayat Saya
                        </a>
                    </div>

                @elseif($user->role === 'manajemen')
                    <div class="flex items-start gap-4">
                        <div class="pill-info w-12 h-12 rounded-2xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12s3.5-7 9-7 9 7 9 7-3.5 7-9 7-9-7-9-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold tracking-tight t1">Pemantauan</h4>
                            <p class="mt-1 text-[15px] leading-relaxed t2">Sebagai Manajemen, Anda dapat memantau tingkat kepatuhan dan matriks Safety Talk bulanan.</p>
                        </div>
                    </div>
                    <a href="{{ route('manajemen.pantau.index') }}" data-nav="fwd" class="mt-6 inline-flex items-center justify-center w-full sm:w-auto min-h-[48px] px-7 rounded-full bg-[#0A66D8] text-white font-semibold hover:bg-[#0954B0] active:scale-[.98] transition motion-reduce:transition-none focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300/60">
                        Lihat Laporan
                    </a>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>