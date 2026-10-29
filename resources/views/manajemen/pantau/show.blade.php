<x-app-layout>
    @php
        $teksKode = [
            'ok' => 'Terkirim', 'late' => 'Terkirim terlambat', 'partial' => 'Sebagian shift',
            'miss' => 'Belum lapor', 'pending' => 'Belum melapor', 'libur' => 'Libur', 'future' => 'Belum tiba', 'pra' => 'Sebelum dipakai',
        ];
        $hariJson = collect($peta['hari'])->map(fn ($h) => ['k' => $h['kode'], 'label' => $h['label'], 'shifts' => $h['shifts']])->all();
        $namaHari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $persen = $peta['persen'];
        $kelasPersen = $persen === null ? 'btn-soft' : ($persen >= 90 ? 'pill-ok' : ($persen >= 75 ? 'pill-warn' : 'pill-bad'));
    @endphp

    <style>
        .cal{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:6px}
        .cal .hd{text-align:center;font-size:12px;font-weight:600;color:var(--t2);padding-bottom:2px}
        .cell{position:relative;aspect-ratio:1;min-height:44px;border-radius:14px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1px;
            font-size:15px;font-weight:600;border:0;-webkit-tap-highlight-color:transparent;transition:transform .15s,outline-color .2s}
        .cell:active{transform:scale(.93)}
        .cell svg{width:12px;height:12px}
        .cell.sel{outline:2px solid var(--t1);outline-offset:2px}
        .cell.today{box-shadow:inset 0 0 0 2px #0A84FF}
        .k-ok,.k-late{background:rgba(52,199,89,.24);color:#14602C}
        .k-partial{background:linear-gradient(135deg,rgba(52,199,89,.3) 50%,rgba(255,59,48,.24) 50%);color:#5A3A00}
        .k-miss{background:rgba(255,59,48,.2);color:#A8231A}
        .k-libur,.ic-libur{background:rgba(255,204,0,.3);color:#7A5B00}
        .k-future,.k-pra{background:var(--soft);color:var(--t2)}
        .k-pending{background:transparent;box-shadow:inset 0 0 0 2px var(--sep);color:var(--t1)}
        .dark .k-ok,.dark .k-late{background:rgba(52,199,89,.26);color:#8CEBAA}
        .dark .k-partial{color:#FFD9A0}
        .dark .k-miss{background:rgba(255,69,58,.28);color:#FFA8A0}
        .dark .k-libur,.dark .ic-libur{background:rgba(255,204,0,.22);color:#FFD84D}
        .dot-late{position:absolute;top:6px;right:6px;width:8px;height:8px;border-radius:50%;background:#FF9500}
        .lg{display:flex;flex-wrap:wrap;gap:8px 16px;font-size:12px;color:var(--t2)}
        .lg b{display:inline-block;width:12px;height:12px;border-radius:4px;margin-right:6px;vertical-align:-2px}
    </style>

    <div class="py-6 sm:py-10"
         x-data="{
            tab: 'hari',
            sel: @js($hariIniNo),
            hari: @js($hariJson),
            teks: @js($teksKode),
            get d() { return this.sel ? this.hari[this.sel] : null; },
            pill(k) { return { ok: 'pill-ok', late: 'pill-ok', partial: 'pill-warn', miss: 'pill-bad', pending: 'pill-warn', libur: 'ic-libur', future: 'btn-soft', pra: 'btn-soft' }[k]; }
         }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Judul --}}
            <div class="space-y-3">
                <a href="{{ route('manajemen.pantau.index', ['bulan' => $bulan->format('Y-m')]) }}" data-nav="back"
                   class="btn-soft inline-flex items-center gap-1 min-h-[44px] pl-3 pr-5 rounded-full text-[15px] font-semibold active:scale-[.97] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                    Semua unit
                </a>
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight t1">{{ $unit->nama }}</h1>
                    <p class="mt-1 text-[15px] t2">{{ $shiftLabel }}</p>
                </div>

                {{-- Pilihan: Hari ini | Kalender --}}
                <div class="grid grid-cols-2 gap-1 p-1 rounded-2xl sm:max-w-sm" style="background-color:var(--soft)" role="group" aria-label="Tampilan">
                    <button type="button" @click="tab = 'hari'" :aria-pressed="tab === 'hari'"
                            :style="tab === 'hari' ? 'background-color:var(--pillbg);color:var(--pilltxt)' : 'color:var(--t2)'"
                            class="min-h-[44px] rounded-xl text-sm font-semibold transition">Hari ini</button>
                    <button type="button" @click="tab = 'kalender'" :aria-pressed="tab === 'kalender'"
                            :style="tab === 'kalender' ? 'background-color:var(--pillbg);color:var(--pilltxt)' : 'color:var(--t2)'"
                            class="min-h-[44px] rounded-xl text-sm font-semibold transition">Kalender</button>
                </div>
            </div>

            {{-- TAB: HARI INI --}}
            <section x-show="tab === 'hari'" x-transition.opacity.duration.200ms class="glass p-5 sm:p-6">
                <p class="text-sm font-medium t2">{{ $hariIniInfo['label'] }}</p>
                <div class="mt-1 flex items-center justify-between gap-3">
                    <h2 class="text-xl font-bold tracking-tight t1">Laporan hari ini</h2>
                    <span :class="pill('{{ $hariIniInfo['kode'] }}')" class="text-xs font-semibold px-3 py-1.5 rounded-full">{{ $teksKode[$hariIniInfo['kode']] }}</span>
                </div>

                <div class="mt-3">
                    @foreach($hariIniInfo['shifts'] as $s)
                        <div class="sep border-t flex items-center gap-3 py-3">
                            <div class="min-w-0 flex-1">
                                @if(count($hariIniInfo['shifts']) > 1)
                                    <p class="text-[16px] font-semibold t1">{{ $s['label'] }}</p>
                                @endif
                                <p class="text-sm t2">
                                    @if($s['url'])
                                        {{ $s['jam'] }} · {{ $s['hadir'] }} dari {{ $s['personil'] }} hadir{{ $s['kode'] === 'late' ? ' · Terlambat' : '' }}
                                    @else
                                        {{ $s['kode'] === 'libur' ? 'Hari libur, tidak ada kewajiban.' : 'Belum ada laporan hari ini.' }}
                                    @endif
                                </p>
                            </div>
                            @if($s['url'])
                                <a href="{{ $s['url'] }}" data-nav="fwd" class="pill-info shrink-0 inline-flex items-center gap-1 min-h-[44px] px-4 rounded-full text-sm font-semibold active:scale-[.97] transition">
                                    Lihat laporan
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- TAB: KALENDER --}}
            <section x-show="tab === 'kalender'" x-cloak x-transition.opacity.duration.200ms class="space-y-5">

                <div class="glass p-5 sm:p-6">
                    {{-- Pilih bulan --}}
                    <div class="flex items-center justify-between">
                        <a href="{{ route('manajemen.pantau.show', ['unit' => $unit->id, 'bulan' => $prev]) }}" data-nav="tab" aria-label="Bulan sebelumnya"
                           class="btn-soft w-11 h-11 rounded-full inline-flex items-center justify-center active:scale-95 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                        </a>
                        <div class="text-center">
                            <p class="text-lg font-bold tracking-tight t1">{{ $bulanLabel }}</p>
                            <p class="text-sm t2">
                                {{ $peta['selesai'] }} dari {{ $peta['wajib'] }} hari kerja terkirim
                            </p>
                        </div>
                        @if($next)
                            <a href="{{ route('manajemen.pantau.show', ['unit' => $unit->id, 'bulan' => $next]) }}" data-nav="tab" aria-label="Bulan berikutnya"
                               class="btn-soft w-11 h-11 rounded-full inline-flex items-center justify-center active:scale-95 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <span class="w-11 h-11" aria-hidden="true"></span>
                        @endif
                    </div>

                    <div class="mt-3 flex items-center gap-2 flex-wrap">
                        <span class="{{ $kelasPersen }} text-xs font-semibold px-3 py-1 rounded-full">{{ $persen === null ? 'Belum ada data' : $persen . '% kepatuhan' }}</span>
                        @if($peta['merah'] > 0)<span class="pill-bad text-xs font-semibold px-3 py-1 rounded-full">{{ $peta['merah'] }} hari belum lapor</span>@endif
                        @if($peta['terlambat'] > 0)<span class="pill-warn text-xs font-semibold px-3 py-1 rounded-full">{{ $peta['terlambat'] }} terlambat</span>@endif
                    </div>

                    {{-- Kalender --}}
                    <div class="cal mt-5">
                        @foreach($namaHari as $n)
                            <div class="hd">{{ $n }}</div>
                        @endforeach
                        @for($i = 0; $i < $offset; $i++)
                            <div></div>
                        @endfor
                        @for($d = 1; $d <= $jumlahHari; $d++)
                            @php $k = $peta['hari'][$d]['kode']; @endphp
                            <button type="button" @click="sel = {{ $d }}" :class="{ 'sel': sel === {{ $d }} }" :aria-pressed="sel === {{ $d }}"
                                    class="cell k-{{ $k }} {{ $hariIniNo === $d ? 'today' : '' }}"
                                    aria-label="{{ $peta['hari'][$d]['label'] }}, {{ $teksKode[$k] }}">
                                <span>{{ $d }}</span>
                                @if($k === 'ok' || $k === 'late')
                                    <svg fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                                @elseif($k === 'miss')
                                    <svg fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                @elseif($k === 'partial')
                                    <svg fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 12h12"/></svg>
                                @endif
                                @if($k === 'late')<span class="dot-late" aria-hidden="true"></span>@endif
                            </button>
                        @endfor
                    </div>

                    <div class="lg mt-5">
                        <span><b style="background:rgba(52,199,89,.5)"></b>Terkirim</span>
                        <span><b style="background:#FF9500;border-radius:50%"></b>Terlambat</span>
                        <span><b style="background:rgba(255,59,48,.45)"></b>Belum lapor</span>
                        <span><b style="background:rgba(255,204,0,.6)"></b>Libur</span>
                    </div>
                </div>

                {{-- Ringkasan tanggal yang diketuk --}}
                <template x-if="d">
                    <div class="glass p-5 sm:p-6">
                        <p class="text-sm font-medium t2" x-text="d.label"></p>
                        <div class="mt-1 flex items-center justify-between gap-3">
                            <h2 class="text-xl font-bold tracking-tight t1">Laporan tanggal ini</h2>
                            <span :class="pill(d.k)" class="text-xs font-semibold px-3 py-1.5 rounded-full" x-text="teks[d.k]"></span>
                        </div>

                        <div class="mt-3">
                            <template x-for="s in d.shifts" :key="s.shift">
                                <div class="sep border-t flex items-center gap-3 py-3">
                                    <div class="min-w-0 flex-1">
                                        <p x-show="d.shifts.length > 1" class="text-[16px] font-semibold t1" x-text="s.label"></p>
                                        <p class="text-sm t2"
                                           x-text="s.url ? (s.jam + ' · ' + s.hadir + ' dari ' + s.personil + ' hadir' + (s.kode === 'late' ? ' · Terlambat' : '')) : (s.kode === 'libur' ? 'Hari libur, tidak ada kewajiban.' : (s.kode === 'future' ? 'Tanggal ini belum tiba.' : (s.kode === 'pra' ? 'Sebelum aplikasi mulai dipakai.' : 'Belum ada laporan pada tanggal ini.')))"></p>
                                    </div>
                                    <a x-show="s.url" :href="s.url" data-nav="fwd" class="pill-info shrink-0 inline-flex items-center gap-1 min-h-[44px] px-4 rounded-full text-sm font-semibold active:scale-[.97] transition">
                                        Lihat laporan
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
                <template x-if="!d">
                    <p class="text-center text-sm t2">Ketuk sebuah tanggal untuk melihat laporannya.</p>
                </template>
            </section>
        </div>
    </div>
</x-app-layout>