<x-app-layout>
    @php
        $persen = $safetyTalk->jml_personil > 0 ? round($safetyTalk->jml_hadir / $safetyTalk->jml_personil * 100) : 0;
        $warna = $persen >= 90 ? ['bar' => '#34C759', 'pill' => 'pill-ok'] : ($persen >= 75 ? ['bar' => '#FF9500', 'pill' => 'pill-warn'] : ['bar' => '#FF3B30', 'pill' => 'pill-bad']);
    @endphp

    <div class="py-6 sm:py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Judul + tombol kembali --}}
            <div class="space-y-3">
                <a href="{{ str_contains(url()->previous(), '/manajemen/pantau') ? url()->previous() : route('manajemen.pantau.index') }}" data-nav="back"
                   class="btn-soft inline-flex items-center gap-1 min-h-[44px] pl-3 pr-5 rounded-full text-[15px] font-semibold active:scale-[.97] transition motion-reduce:transition-none focus:outline-none focus-visible:ring-4 focus-visible:ring-gray-300/60">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                    Kembali
                </a>
                <div>
                    <h3 class="text-3xl sm:text-4xl font-bold tracking-tight t1">Detail Pantauan Safety Talk</h3>
                    <p class="mt-1 text-[15px] t2">{{ $safetyTalk->unit->nama }} &middot; {{ $safetyTalk->shift }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Kolom kanan di desktop, tampil pertama di HP: statistik + foto --}}
                <div class="space-y-5 md:order-2">

                    <div class="glass p-5 sm:p-6">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-bold tracking-tight t1">Statistik Kehadiran</h4>
                            <span class="{{ $warna['pill'] }} text-xs font-semibold px-2.5 py-1 rounded-full">{{ $persen }}% hadir</span>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-3 text-center">
                            <div class="pill-info rounded-2xl py-4 px-1">
                                <p class="text-sm font-semibold">Target</p>
                                <p class="mt-1 text-3xl font-bold tracking-tight">{{ $safetyTalk->jml_personil }}</p>
                            </div>
                            <div class="pill-ok rounded-2xl py-4 px-1">
                                <p class="text-sm font-semibold">Hadir</p>
                                <p class="mt-1 text-3xl font-bold tracking-tight">{{ $safetyTalk->jml_hadir }}</p>
                            </div>
                            <div class="pill-bad rounded-2xl py-4 px-1">
                                <p class="text-sm font-semibold">Absen</p>
                                <p class="mt-1 text-3xl font-bold tracking-tight">{{ $safetyTalk->jml_tidak_hadir }}</p>
                            </div>
                        </div>
                        <div class="track mt-5 h-2 rounded-full overflow-hidden">
                            <div class="h-full rounded-full" style="width: {{ $persen }}%; background: {{ $warna['bar'] }}"></div>
                        </div>
                    </div>

                    <div class="glass p-5 sm:p-6">
                        <h4 class="text-lg font-bold tracking-tight t1 mb-4">Foto Dokumentasi</h4>
                        @if($safetyTalk->foto_path)
                            <a href="{{ asset('storage/' . $safetyTalk->foto_path) }}" target="_blank" rel="noopener" class="block rounded-2xl overflow-hidden focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300/60">
                                <!-- Mengambil foto dari folder storage -->
                                <img src="{{ asset('storage/' . $safetyTalk->foto_path) }}" alt="Dokumentasi Safety Talk" class="w-full h-auto object-cover max-h-72">
                            </a>
                        @else
                            <div class="rounded-2xl p-8 text-center" style="background:var(--soft)">
                                <svg class="w-8 h-8 t2 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/></svg>
                                <p class="text-sm font-medium t2">Tidak ada foto dokumentasi yang dilampirkan.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Kolom kiri di desktop: informasi + materi --}}
                <div class="space-y-5 md:order-1">

                    <div class="glass p-5 sm:p-6">
                        <h4 class="text-lg font-bold tracking-tight t1 mb-2">Informasi Laporan</h4>
                        <dl class="text-[15px]">
                            <div class="sep flex justify-between gap-4 py-3 border-b">
                                <dt class="t2">Tanggal</dt>
                                <dd class="t1 font-medium text-right">{{ \Carbon\Carbon::parse($safetyTalk->tanggal)->locale('id')->isoFormat('D MMMM YYYY') }}</dd>
                            </div>
                            <div class="sep flex justify-between gap-4 py-3 border-b">
                                <dt class="t2">Unit Kerja</dt>
                                <dd class="t1 font-medium text-right">{{ $safetyTalk->unit->nama }}</dd>
                            </div>
                            <div class="sep flex justify-between gap-4 py-3 border-b">
                                <dt class="t2">Shift</dt>
                                <dd class="t1 font-medium text-right">{{ $safetyTalk->shift }}</dd>
                            </div>
                            <div class="flex justify-between items-center gap-4 pt-3">
                                <dt class="t2">Status</dt>
                                <dd>
                                    @if($safetyTalk->status == 'terkirim')
                                        <span class="pill-ok text-xs font-semibold px-3 py-1.5 rounded-full">Terkirim</span>
                                    @else
                                        <span class="btn-soft text-xs font-semibold px-3 py-1.5 rounded-full">Draft</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="glass p-5 sm:p-6">
                        <h4 class="text-lg font-bold tracking-tight t1 mb-3">Materi / Topik</h4>
                        <p class="t1 rounded-2xl p-4 text-[16px] leading-relaxed whitespace-pre-line" style="background:var(--soft)">{{ $safetyTalk->materi }}</p>
                    </div>
                </div>
            </div>

            {{-- Rincian Absensi (hanya dirender jika ada karyawan absen) --}}
            @if($safetyTalk->jml_tidak_hadir > 0)
                <div class="glass p-5 sm:p-6">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="pill-warn w-10 h-10 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4l9 16H3z"/><path d="M12 10v4M12 17v.5"/></svg>
                        </span>
                        <h4 class="text-lg font-bold tracking-tight t1">Rincian Karyawan Tidak Hadir</h4>
                    </div>

                    <ul>
                        @foreach($absensi as $absen)
                            <li class="sep flex items-start justify-between gap-4 py-4 border-b last:border-0">
                                <div class="min-w-0">
                                    <p class="text-[16px] font-semibold t1">{{ $absen->kategori }}</p>
                                    <p class="mt-0.5 text-sm t2 break-words">{{ $absen->keterangan_nama ?? '-' }}</p>
                                </div>
                                <span class="pill-bad shrink-0 text-sm font-bold px-3.5 py-1.5 rounded-full">{{ $absen->jumlah }} orang</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>