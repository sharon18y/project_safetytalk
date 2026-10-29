<x-app-layout>
    @php
        // Data awal untuk form (nilai lama tetap dipertahankan saat validasi gagal)
        $cfg = [
            'units'  => collect($units)->map(fn ($u) => ['id' => $u->id, 'no' => $u->no_urut, 'nama' => $u->nama])->values(),
            'unitId' => old('unit_id', ''),
            'p'      => old('jml_personil', ''),
            'h'      => old('jml_hadir', ''),
            't'      => old('jml_tidak_hadir', 0),
            'manual' => old('jml_tidak_hadir') !== null,
        ];
    @endphp

    <style>
        .lbl{display:block;font-size:14px;font-weight:600;color:var(--t2);margin-bottom:8px}
        .in{width:100%;min-height:52px;padding:0 16px;border:0;border-radius:16px;background-color:var(--soft);color:var(--t1);font-size:16px;box-shadow:none}
        .in::placeholder{color:var(--t2)}
        .in:focus{background-color:var(--glass-strong);box-shadow:0 0 0 2px #0A84FF;outline:none}
        textarea.in{padding:14px 16px;line-height:1.55;resize:vertical}

        /* Shift: segmented control */
        .seg-opt{position:relative;display:block}
        .seg-opt span{display:flex;align-items:center;justify-content:center;min-height:46px;border-radius:12px;font-size:14px;font-weight:600;color:var(--t2);
            transition:background-color .25s,color .25s,box-shadow .25s;-webkit-tap-highlight-color:transparent}
        .seg-opt input:checked + span{background-color:var(--pillbg);color:var(--pilltxt);box-shadow:0 2px 8px rgba(0,0,0,.18)}
        .seg-opt input:focus-visible + span{outline:3px solid rgba(10,132,255,.55)}

        /* Baris angka kehadiran */
        .row-in{display:flex;align-items:center;justify-content:space-between;gap:12px;min-height:62px;padding:4px 8px 4px 16px}
        .row-in + .row-in{border-top:1px solid var(--sep)}
        .row-in > span{font-size:16px;font-weight:500;color:var(--t1)}
        .row-in input.num{width:6.5rem;min-height:48px;padding:0 12px;border:0;border-radius:12px;background-color:transparent;color:var(--t1);
            text-align:right;font-size:24px;font-weight:700;box-shadow:none;-moz-appearance:textfield}
        .row-in input.num:focus{background-color:var(--track);box-shadow:0 0 0 2px #0A84FF;outline:none}
        .dark .row-in input.num{background-color:transparent}
        .row-in > span.txt-bad,.row-in input.num.txt-bad{color:#C0271B}
        .dark .row-in > span.txt-bad,.dark .row-in input.num.txt-bad{color:#FF9A92}
        .dark .row-in input.num:focus{background-color:var(--track)}

        /* Pemilih unit kerja: bottom sheet di HP, dialog di tengah di desktop */
        .pk{position:fixed;z-index:70;left:0;right:0;bottom:0;max-height:86vh;max-height:86dvh;display:flex;flex-direction:column;
            background:var(--glass-strong);-webkit-backdrop-filter:blur(20px);backdrop-filter:blur(20px);
            border:1px solid var(--line);border-bottom:0;border-radius:28px 28px 0 0;box-shadow:0 -12px 40px rgba(0,0,0,.2);
            padding-bottom:env(safe-area-inset-bottom,0px);pointer-events:none;transform:translate3d(0,105%,0);will-change:transform;
            transition:transform .55s cubic-bezier(.32,.72,0,1)}
        .pk.open{pointer-events:auto;transform:translate3d(0,0,0)}
        .pk-list{flex:1;overflow-y:auto;overscroll-behavior:contain;-webkit-overflow-scrolling:touch;padding:4px 0 12px}
        .pk-item{display:flex;align-items:center;gap:12px;width:100%;min-height:56px;padding:8px 20px;text-align:left;color:var(--t1);font-size:16px;font-weight:500;
            -webkit-tap-highlight-color:transparent;transition:background-color .15s}
        .pk-item:hover{background-color:var(--hover)}
        .pk-item:active{background-color:var(--track)}
        .pk-item.sel{background-color:rgba(52,199,89,.15);font-weight:700}
        .pk-no{flex:none;display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;border-radius:10px;background-color:var(--soft);color:var(--t2);font-size:13px;font-weight:700}
        .pk-name{flex:1;min-width:0;line-height:1.3}
        @media (min-width:640px){
            .pk{left:50%;right:auto;top:50%;bottom:auto;width:30rem;max-height:80vh;border-radius:28px;border-bottom:1px solid var(--line);
                opacity:0;transform:translate(-50%,-46%) scale(.96);transition:transform .38s cubic-bezier(.32,.72,0,1),opacity .25s ease}
            .pk.open{opacity:1;transform:translate(-50%,-50%) scale(1)}
        }
        @media (prefers-reduced-motion:reduce){.pk,.seg-opt span{transition:none}}
    </style>

    <script>
        window.stForm = function (c) {
            return {
                units: c.units, unitId: c.unitId, q: '', pk: false, errUnit: false,
                p: c.p, h: c.h, t: c.t, manual: c.manual,
                nama: '', url: '', ukuran: '', kirim: false,
                lbl(u) { return (u.no ? u.no + '. ' : '') + u.nama; },
                get unit() { var id = String(this.unitId); return this.units.find(function (u) { return String(u.id) === id; }) || null; },
                get hasil() {
                    var k = this.q.toLowerCase().trim(), self = this;
                    return k ? this.units.filter(function (u) { return self.lbl(u).toLowerCase().indexOf(k) > -1; }) : this.units;
                },
                buka() {
                    this.pk = true; this.q = '';
                    if (window.innerWidth >= 640) this.$nextTick(() => this.$refs.cari && this.$refs.cari.focus());
                },
                pilih(u) { this.unitId = u.id; this.errUnit = false; setTimeout(() => { this.pk = false; }, 150); },
                hitung() { if (!this.manual) this.t = Math.max(0, (+this.p || 0) - (+this.h || 0)); },
                get persen() { return +this.p > 0 ? Math.min(100, Math.round(+this.h / +this.p * 100)) : 0; },
                get lebih() { return +this.p > 0 && +this.h > +this.p; },
                foto(e) {
                    var f = e.target.files && e.target.files[0];
                    if (!f) { this.url = ''; this.nama = ''; this.ukuran = ''; return; }
                    this.nama = f.name; this.ukuran = (f.size / 1048576).toFixed(1) + ' MB'; this.url = URL.createObjectURL(f);
                },
                cek(e) {
                    if (!this.unitId) { e.preventDefault(); this.errUnit = true; this.buka(); return; }
                    this.kirim = true;
                }
            };
        };
    </script>

    <div class="py-6 sm:py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <div>
                <h3 class="text-3xl sm:text-4xl font-bold tracking-tight t1">Formulir Laporan Harian</h3>
                <p class="mt-2 text-[15px] leading-relaxed t2">Isi data Safety Talk di bawah ini, lalu kirim laporan.</p>
            </div>

            <form action="{{ route('koordinator.safety-talk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5"
                  x-data="stForm(@js($cfg))" @submit="cek($event)" @pageshow.window="kirim = false" @keydown.escape.window="pk = false"
                  x-effect="document.body.style.overflow = pk ? 'hidden' : ''">

                @if ($errors->any())
                    <div class="pill-bad rounded-2xl p-4" role="alert">
                        <ul class="list-disc pl-5 text-sm font-medium space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @csrf

                {{-- Kartu 1: Informasi umum --}}
                <div class="glass p-5 sm:p-6 space-y-5">
                    <h4 class="text-lg font-bold tracking-tight t1">Informasi Umum</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="lbl" for="tanggal">Tanggal</label>
                            <input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="in" required>
                        </div>

                        <div>
                            <span class="lbl" id="lbl-unit">Unit Kerja</span>
                            <input type="hidden" name="unit_id" :value="unitId">
                            <button type="button" @click="buka()" aria-haspopup="dialog" aria-labelledby="lbl-unit"
                                    class="in flex items-center justify-between gap-3 text-left transition"
                                    :style="errUnit ? 'box-shadow:0 0 0 2px #FF3B30' : ''">
                                <span class="flex items-center gap-3 min-w-0">
                                    <template x-if="unit">
                                        <span class="flex items-center gap-3 min-w-0">
                                            <span class="pk-no" x-text="unit.no ?? '•'"></span>
                                            <span class="truncate font-medium" x-text="unit.nama"></span>
                                        </span>
                                    </template>
                                    <span x-show="!unit" class="t2">Pilih Unit Kerja</span>
                                </span>
                                <svg class="w-5 h-5 t2 shrink-0" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 9l4-4 4 4M8 15l4 4 4-4"/></svg>
                            </button>
                            <p x-show="errUnit" x-cloak class="mt-2 text-sm font-medium txt-bad">Pilih unit kerja terlebih dahulu.</p>
                        </div>
                    </div>

                    <fieldset>
                        <legend class="lbl">Shift</legend>
                        <div class="grid grid-cols-4 gap-1 p-1 rounded-2xl" style="background-color:var(--soft)">
                            @foreach(['Day' => 'Day', 'I' => 'Shift I', 'II' => 'Shift II', 'III' => 'Shift III'] as $nilai => $teks)
                                <label class="seg-opt">
                                    <input type="radio" name="shift" value="{{ $nilai }}" class="sr-only" {{ old('shift', 'Day') == $nilai ? 'checked' : '' }} required>
                                    <span>{{ $teks }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs t2">Day = jam kerja normal.</p>
                    </fieldset>

                    <div>
                        <label class="lbl" for="nama_koordinator">Nama Koordinator</label>
                        <input id="nama_koordinator" type="text" name="nama_koordinator" value="{{ old('nama_koordinator', Auth::user()->name) }}" class="in" required>
                        <p class="mt-2 text-xs t2">Bisa diubah jika Anda mewakili orang lain.</p>
                    </div>
                </div>

                {{-- Kartu 2: Materi --}}
                <div class="glass p-5 sm:p-6">
                    <label class="text-lg font-bold tracking-tight t1 block mb-3" for="materi">Materi Safety Talk</label>
                    <textarea id="materi" name="materi" rows="4" class="in" required placeholder="Tuliskan poin-poin materi K3 yang disampaikan...">{{ old('materi') }}</textarea>
                </div>

                {{-- Kartu 3: Kehadiran --}}
                <div class="glass p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-bold tracking-tight t1">Kehadiran</h4>
                        <span x-show="+p > 0" x-cloak class="pill-ok text-xs font-semibold px-2.5 py-1 rounded-full" :class="lebih && 'pill-bad'" x-text="persen + '% hadir'"></span>
                    </div>

                    <div class="rounded-2xl overflow-hidden" style="background-color:var(--soft)">
                        <label class="row-in">
                            <span>Jumlah Personil (Target)</span>
                            <input type="number" name="jml_personil" min="1" inputmode="numeric" x-model="p" @input="hitung()" class="num" placeholder="0" required>
                        </label>
                        <label class="row-in">
                            <span>Jumlah Hadir</span>
                            <input type="number" name="jml_hadir" min="0" inputmode="numeric" x-model="h" @input="hitung()" class="num" placeholder="0" required>
                        </label>
                        <label class="row-in">
                            <span class="txt-bad !font-bold">Jumlah Tidak Hadir</span>
                            <input type="number" name="jml_tidak_hadir" min="0" inputmode="numeric" x-model="t" @input="manual = true" class="num txt-bad" required>
                        </label>
                    </div>

                    <p x-show="lebih" x-cloak class="mt-3 text-sm font-medium txt-bad">Jumlah hadir melebihi jumlah personil. Mohon periksa kembali.</p>
                    <p class="mt-3 text-xs t2">Jumlah tidak hadir terisi otomatis dari Target dikurangi Hadir, dan masih bisa diubah.</p>

                    <div class="mt-5">
                        <label class="lbl" for="keterangan_absen">Alasan Tidak Hadir <span class="font-normal">(Opsional)</span></label>
                        <textarea id="keterangan_absen" name="keterangan_absen" rows="3" class="in" placeholder="Contoh: 2 Sakit (Budi dan Andi), 1 Izin. Boleh dikosongkan.">{{ old('keterangan_absen') }}</textarea>
                    </div>
                </div>

                {{-- Kartu 4: Foto --}}
                <div class="glass p-5 sm:p-6">
                    <h4 class="text-lg font-bold tracking-tight t1 mb-3">Foto Dokumentasi Kegiatan</h4>
                    <label class="block rounded-2xl border-2 border-dashed sep p-4 text-center cursor-pointer hov transition focus-within:ring-4 focus-within:ring-blue-300/60">
                        <input type="file" name="foto" accept="image/*" class="sr-only" @change="foto($event)">
                        <template x-if="!url">
                            <div class="py-6">
                                <svg class="w-9 h-9 t2 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/></svg>
                                <p class="font-semibold t1">Ketuk untuk memilih foto</p>
                                <p class="mt-1 text-xs t2">Maksimal 2MB. Format: JPG, PNG.</p>
                            </div>
                        </template>
                        <template x-if="url">
                            <div>
                                <img :src="url" alt="Pratinjau foto" class="w-full max-h-64 object-cover rounded-xl">
                                <p class="mt-3 text-sm font-semibold t1 truncate" x-text="nama"></p>
                                <p class="text-xs t2"><span x-text="ukuran"></span> &middot; Ketuk untuk mengganti</p>
                            </div>
                        </template>
                    </label>
                </div>

                {{-- Kirim --}}
                <button type="submit" :disabled="kirim"
                        class="w-full sm:w-auto sm:float-right inline-flex items-center justify-center gap-2 min-h-[54px] px-8 rounded-full bg-emerald-700 text-white text-[16px] font-semibold hover:bg-emerald-800 active:scale-[.98] disabled:opacity-60 transition motion-reduce:transition-none focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-300/60">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span x-text="kirim ? 'Mengirim...' : 'Kirim Laporan Safety Talk'">Kirim Laporan Safety Talk</span>
                </button>
                <div class="clear-both"></div>

                {{-- Pemilih unit kerja --}}
                <div class="scrim" :class="{ 'open': pk }" @click="pk = false" aria-hidden="true"></div>
                <div class="pk" :class="{ 'open': pk }" role="dialog" aria-modal="true" aria-label="Pilih Unit Kerja" x-bind:inert="!pk">
                    <div class="sm:hidden mx-auto mt-2 mb-1 w-10 h-1.5 rounded-full" style="background-color:var(--track)"></div>
                    <div class="flex items-center justify-between gap-3 px-5 pt-3 pb-3">
                        <h4 class="text-xl font-bold tracking-tight t1">Pilih Unit Kerja</h4>
                        <button type="button" @click="pk = false" aria-label="Tutup"
                                class="w-11 h-11 rounded-full btn-soft inline-flex items-center justify-center active:scale-95 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="px-5 pb-3 relative">
                        <svg class="w-5 h-5 t2 absolute left-9 top-1/2 -translate-y-1/2 pointer-events-none" style="margin-top:-6px" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                        <input type="search" x-model="q" x-ref="cari" placeholder="Cari unit kerja" aria-label="Cari unit kerja" class="in !pl-12">
                    </div>
                    <div class="pk-list">
                        <template x-for="u in hasil" :key="u.id">
                            <button type="button" class="pk-item" :class="{ 'sel': String(u.id) === String(unitId) }" :aria-pressed="String(u.id) === String(unitId)" @click="pilih(u)">
                                <span class="pk-no" x-text="u.no ?? '•'"></span>
                                <span class="pk-name" x-text="u.nama"></span>
                                <svg x-show="String(u.id) === String(unitId)" class="w-5 h-5 shrink-0" style="color:#34C759" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                            </button>
                        </template>
                        <p x-show="hasil.length === 0" x-cloak class="py-10 text-center t2">Unit tidak ditemukan.</p>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>