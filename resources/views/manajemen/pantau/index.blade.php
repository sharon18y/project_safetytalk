<x-app-layout>
    @php
        $teksHari = ['ok' => 'Sudah melapor', 'late' => 'Terlambat', 'partial' => 'Sebagian', 'pending' => 'Belum melapor', 'miss' => 'Belum melapor', 'libur' => 'Libur'];
        $warnaPersen = fn ($p) => $p === null ? 'btn-soft' : ($p >= 90 ? 'pill-ok' : ($p >= 75 ? 'pill-warn' : 'pill-bad'));
    @endphp

    <style>
        .ic-libur{background:rgba(255,204,0,.28);color:#7A5B00}
        .dark .ic-libur{background:rgba(255,204,0,.22);color:#FFD84D}
    </style>

    <div class="py-6 sm:py-10"
         x-data="{
            q: '', f: 'semua',
            tampil(el) {
                const k = this.q.toLowerCase().trim();
                if (k && !el.dataset.nama.includes(k)) return false;
                if (this.f === 'sudah') return ['ok', 'late', 'partial'].includes(el.dataset.hari);
                if (this.f === 'merah') return Number(el.dataset.merah) > 0;
                return true;
            },
            get kosong() {
                return ![...this.$root.querySelectorAll('[data-nama]')].some(el => this.tampil(el));
            }
         }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Judul + pilih bulan --}}
            <div class="space-y-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight t1">Pantau Unit</h1>
                    <p class="mt-1 text-[15px] t2">Pilih unit untuk melihat laporan per tanggal.</p>
                </div>

                <div class="flex items-center justify-between rounded-2xl p-1" style="background-color:var(--soft)">
                    <a href="{{ route('manajemen.pantau.index', ['bulan' => $prev]) }}" data-nav="tab" aria-label="Bulan sebelumnya"
                       class="w-11 h-11 rounded-xl inline-flex items-center justify-center t1 active:scale-95 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                    </a>
                    <span class="text-[17px] font-semibold t1">{{ $bulanLabel }}</span>
                    @if($next)
                        <a href="{{ route('manajemen.pantau.index', ['bulan' => $next]) }}" data-nav="tab" aria-label="Bulan berikutnya"
                           class="w-11 h-11 rounded-xl inline-flex items-center justify-center t1 active:scale-95 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span class="w-11 h-11" aria-hidden="true"></span>
                    @endif
                </div>
            </div>

            {{-- Lihat semua sekaligus (PDF) --}}
            <div class="glass p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <span class="pill-info w-11 h-11 rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h8l4 4v14H7z"/><path d="M15 3v4h4M10 13h6M10 17h6"/></svg>
                    </span>
                    <div>
                        <p class="font-semibold t1">Lihat semua unit sekaligus</p>
                        <p class="text-sm t2">Matriks {{ $bulanLabel }} dalam satu lembar PDF.</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('manajemen.pantau.pdf', ['bulan' => $bulan->format('Y-m')]) }}" target="_blank" rel="noopener"
                       class="btn-soft flex-1 sm:flex-none inline-flex items-center justify-center min-h-[44px] px-5 rounded-full text-sm font-semibold active:scale-[.97] transition">Pratinjau</a>
                    <a href="{{ route('manajemen.pantau.pdf', ['bulan' => $bulan->format('Y-m'), 'unduh' => 1]) }}"
                       class="flex-1 sm:flex-none inline-flex items-center justify-center min-h-[44px] px-5 rounded-full text-sm font-semibold bg-[#0A66D8] text-white hover:bg-[#0954B0] active:scale-[.97] transition">Unduh PDF</a>
                </div>
            </div>

            {{-- Cari + filter --}}
            <div class="space-y-3">
                <div class="relative">
                    <svg class="w-5 h-5 t2 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                    <input type="search" x-model="q" placeholder="Cari unit kerja" aria-label="Cari unit kerja" class="field w-full h-12 pl-12 pr-4 rounded-2xl text-[16px] transition">
                </div>
                <div class="flex gap-2 overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0 pb-1" role="group" aria-label="Filter unit">
                    @foreach([['semua', 'Semua'], ['sudah', 'Sudah melapor'], ['merah', 'Ada merah']] as [$kode, $teks])
                        @if($kode !== 'sudah' || $bulanIni)
                            <button type="button" @click="f = '{{ $kode }}'" :aria-pressed="f === '{{ $kode }}'"
                                    :style="f === '{{ $kode }}' ? 'background-color:var(--pillbg);color:var(--pilltxt)' : ''"
                                    class="btn-soft shrink-0 min-h-[44px] px-5 rounded-full text-sm font-semibold transition active:scale-[.97]">{{ $teks }}</button>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Daftar unit --}}
            <div class="glass p-2 sm:p-3 overflow-hidden">
              <div class="flex flex-col" style="margin-top:-1px">
                @foreach($baris as $b)
                    <a href="{{ $b['url'] }}" data-nav="fwd"
                       data-nama="{{ strtolower($b['no'] . '. ' . $b['nama']) }}" data-hari="{{ $b['hari_ini'] }}" data-merah="{{ $b['merah'] }}"
                       data-ts="{{ $b['waktu'] }}" data-urut="{{ $loop->index }}"
                       x-show="tampil($el)"
                       :style="{ order: f === 'sudah' ? -Number($el.dataset.ts) : Number($el.dataset.urut) }"
                       class="sep hov border-t flex items-center gap-3 px-3 py-3 rounded-xl transition active:scale-[.99]">

                        {{-- Ikon status hari ini (bulan berjalan) atau nomor unit (bulan lalu) --}}
                        @if($b['hari_ini'] === 'ok' || $b['hari_ini'] === 'late')
                            <span class="pill-ok relative w-10 h-10 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                                @if($b['hari_ini'] === 'late')<span class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full bg-[#FF9500] border-2" style="border-color:var(--glass-strong)"></span>@endif
                            </span>
                        @elseif($b['hari_ini'] === 'partial' || $b['hari_ini'] === 'pending')
                            <span class="pill-warn w-10 h-10 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l2.5 1.5"/></svg>
                            </span>
                        @elseif($b['hari_ini'] === 'miss')
                            <span class="pill-bad w-10 h-10 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 18L18 6M6 6l12 12"/></svg>
                            </span>
                        @elseif($b['hari_ini'] === 'libur')
                            <span class="ic-libur w-10 h-10 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 12h12"/></svg>
                            </span>
                        @else
                            <span class="btn-soft w-10 h-10 rounded-full flex items-center justify-center shrink-0 text-sm font-bold">{{ $b['no'] }}</span>
                        @endif

                        <div class="min-w-0 flex-1">
                            <p class="text-[16px] font-semibold t1 truncate">{{ $b['nama'] }}</p>
                            <p class="text-sm t2 truncate">
                                {{ $b['shift_label'] }} · {{ $b['persen'] === null ? 'Belum ada data' : $b['persen'] . '% bulan ini' }}{{ $b['jam'] ? ' · lapor ' . $b['jam'] : '' }}
                            </p>
                        </div>

                        @if($b['hari_ini'] && isset($teksHari[$b['hari_ini']]))
                            <span class="{{ in_array($b['hari_ini'], ['ok', 'late']) ? 'pill-ok' : (in_array($b['hari_ini'], ['miss']) ? 'pill-bad' : ($b['hari_ini'] === 'libur' ? 'ic-libur' : 'pill-warn')) }} shrink-0 text-[11px] font-semibold px-2.5 py-1 rounded-full">{{ $teksHari[$b['hari_ini']] }}</span>
                        @else
                            <span class="{{ $warnaPersen($b['persen']) }} shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $b['persen'] === null ? '-' : $b['persen'] . '%' }}</span>
                        @endif
                        <svg class="w-5 h-5 t2 shrink-0" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endforeach
              </div>

                @if($baris->isEmpty())
                    <p class="py-10 text-center t2">Belum ada unit kerja.</p>
                @endif
                <p x-show="kosong && {{ $baris->isEmpty() ? 'false' : 'true' }}" x-cloak class="py-10 text-center t2">Unit tidak ditemukan.</p>
            </div>
        </div>
    </div>
</x-app-layout>