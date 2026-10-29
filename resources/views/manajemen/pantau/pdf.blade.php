@php
    // ===== Ringkasan keseluruhan =====
    $totalWajib = $totalSelesai = $totalTelat = $totalMiss = 0;

    foreach ($peta as $p) {
        foreach ($p['shifts'] as $i => $s) {
            for ($d = 1; $d <= $jumlahHari; $d++) {
                $h = $p['hari'][$d];
                if (!$h['hitung']) { continue; }
                $kode = $h['shifts'][$i]['kode'];
                if (in_array($kode, ['pending', 'future', 'libur', 'pra'], true)) { continue; }
                $totalWajib++;
                if (in_array($kode, ['ok', 'late'], true)) {
                    $totalSelesai++;
                    if ($kode === 'late') { $totalTelat++; }
                } elseif ($kode === 'miss') {
                    $totalMiss++;
                }
            }
        }
    }
    $kepatuhan = $totalWajib > 0 ? (int) round($totalSelesai / $totalWajib * 100) : 0;

    // ===== Bagi unit ke halaman (satu unit tidak pernah terpotong antar halaman) =====
    // Unit tanpa shift = 1 baris. Unit dengan shift = 1 baris nama + 1 baris per shift.
    $grup = [];
    foreach ($peta as $ui => $p) {
        $grup[$ui] = $p['shifts'] === ['Day'] ? 1 : 1 + count($p['shifts']);
    }
    $total = array_sum($grup);
    $kapasitas = 31;   // baris per halaman
    $cadangan = 9;     // baris setara untuk legenda + tanda tangan di halaman terakhir
    $n = max(1, (int) ceil(($total + $cadangan) / $kapasitas));

    while (true) {
        $target = min($kapasitas, (int) ceil(($total + $cadangan) / $n));
        $halaman = [[]];
        $isi = 0;
        foreach ($grup as $ui => $r) {
            if ($isi + $r > $target && $isi > 0 && count($halaman) < $n) {
                $halaman[] = [];
                $isi = 0;
            }
            $halaman[count($halaman) - 1][] = $ui;
            $isi += $r;
        }
        $barisTerakhir = array_sum(array_map(fn ($u) => $grup[$u], end($halaman)));
        if ($barisTerakhir + $cadangan <= $kapasitas || $n >= 8) { break; }
        $n++;
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Monitoring Safety Talk {{ $bulanLabel }}</title>
<style>
    @page { margin: 8mm 10mm 12mm 10mm; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #111; line-height: 1.1; }

    .bln { font-size: 8.5px; margin-bottom: 1mm; }
    .kop { text-align: center; margin-bottom: 3mm; }
    .judul { font-size: 12.5px; font-weight: bold; text-decoration: underline; letter-spacing: .3px; }
    .ring { font-size: 7.5px; color: #444; margin-top: 1.5mm; }
    .ring b { color: #111; }

    table.m { width: 100%; border-collapse: collapse; }
    table.m th, table.m td { border: .5px solid #555; padding: 0; text-align: center; vertical-align: middle; }
    table.m th { background: #92D050; color: #000; font-size: 8px; font-weight: bold; height: 7mm; white-space: nowrap; }
    table.m th.lb { background: #FFD966; }
    table.m td { height: 5mm; }
    table.m tr { page-break-inside: avoid; }
    tr.u td { border-top: 1.1px solid #222; }
    td.no { font-weight: bold; font-size: 8px; }
    td.l { text-align: left; padding: 0 4px; }
    td.nm { font-weight: bold; font-size: 8px; }
    td.sh { font-size: 7.5px; padding-left: 14px; color: #222; }
    td.ket { font-size: 7px; }

    .ok   { font-size: 9px; font-weight: bold; color: #14602c; }
    .late { font-size: 9px; font-weight: bold; color: #8a4b00; text-decoration: underline; }
    .miss { font-size: 9px; font-weight: bold; color: #c0271b; }
    td.lb { background: #FFF0B3; }

    .legend { margin-top: 2.5mm; font-size: 7px; color: #333; }
    .legend span.k { display: inline-block; width: 9px; height: 9px; border: .4px solid #777; text-align: center; font-size: 7px; font-weight: bold; line-height: 9px; margin-right: 2px; }
    table.ttd { width: 100%; margin-top: 3mm; text-align: center; font-size: 8px; border-collapse: collapse; page-break-inside: avoid; }
    table.ttd td { width: 33.3%; height: 24mm; vertical-align: top; padding: 0; }
    .nama { font-weight: bold; text-decoration: underline; }
    .foot { position: fixed; bottom: -8mm; left: 0; right: 0; font-size: 6.5px; color: #777; border-top: .4px solid #bbb; padding-top: 1mm; }
    .foot .r { float: right; }
    .pn:before { content: counter(page); }
</style>
</head>
<body>
    <div class="foot">
        <span class="r">Halaman <span class="pn"></span></span>
        Monitoring Pelaksanaan Safety Talk &middot; {{ $bulanLabel }} &middot; Dicetak {{ now()->locale('id')->isoFormat('D MMMM YYYY, HH.mm') }}
    </div>

    @foreach($halaman as $daftarUnit)
        <div @if(!$loop->last) style="page-break-after: always" @endif>

            <div class="bln">Bulan {{ $bulanLabel }}</div>
            <div class="kop">
                <div class="judul">MONITORING PELAKSANAAN SAFETY TALK PT. BIRINGKASSI RAYA</div>
                <div class="ring">
                    Kepatuhan <b>{{ $kepatuhan }}%</b> &nbsp;&middot;&nbsp; Terkirim <b>{{ $totalSelesai }}</b> &nbsp;&middot;&nbsp;
                    Terlambat <b>{{ $totalTelat }}</b> &nbsp;&middot;&nbsp; Belum lapor <b>{{ $totalMiss }}</b>
                </div>
            </div>

            <table class="m">
                <thead>
                    <tr>
                        <th style="width:8mm">No</th>
                        <th style="width:72mm">Unit/Shift</th>
                        @for($d = 1; $d <= $jumlahHari; $d++)
                            <th class="{{ in_array($d, $libur) ? 'lb' : '' }}" style="width:5.3mm">{{ $d }}</th>
                        @endfor
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($daftarUnit as $ui)
                        @php
                            $p = $peta[$ui];
                            $harian = $p['shifts'] === ['Day'];
                            $jml = $harian ? 1 : 1 + count($p['shifts']);
                        @endphp

                        {{-- Baris nama unit (unit tanpa shift: centang ada di baris ini) --}}
                        <tr class="u">
                            <td rowspan="{{ $jml }}" class="no">{{ $p['unit']->no_urut }}</td>
                            <td class="l nm">{{ $p['unit']->nama }}</td>
                            @for($d = 1; $d <= $jumlahHari; $d++)
                                @php
                                    $kode = $harian ? $p['hari'][$d]['shifts'][0]['kode'] : '';
                                    $kelas = in_array($kode, ['ok', 'late', 'miss'], true) ? $kode : '';
                                    $kelas .= in_array($d, $libur) ? ' lb' : '';
                                @endphp
                                <td class="{{ trim($kelas) }}">@if($kode === 'ok' || $kode === 'late')&#10003;@elseif($kode === 'miss')&#10007;@endif</td>
                            @endfor
                            <td rowspan="{{ $jml }}" class="ket"></td>
                        </tr>

                        {{-- Baris per shift --}}
                        @if(!$harian)
                            @foreach($p['shifts'] as $i => $s)
                                <tr>
                                    <td class="l sh">Shift {{ $s }}</td>
                                    @for($d = 1; $d <= $jumlahHari; $d++)
                                        @php
                                            $kode = $p['hari'][$d]['shifts'][$i]['kode'];
                                            $kelas = in_array($kode, ['ok', 'late', 'miss'], true) ? $kode : '';
                                            $kelas .= in_array($d, $libur) ? ' lb' : '';
                                        @endphp
                                        <td class="{{ trim($kelas) }}">@if($kode === 'ok' || $kode === 'late')&#10003;@elseif($kode === 'miss')&#10007;@endif</td>
                                    @endfor
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>

            @if($loop->last)
                <div class="legend">
                    <span class="k ok">&#10003;</span> Terkirim tepat waktu &nbsp;&nbsp;
                    <span class="k late">&#10003;</span> Terkirim terlambat (H+1) &nbsp;&nbsp;
                    <span class="k miss">&#10007;</span> Tidak melapor &nbsp;&nbsp;
                    <span class="k" style="background:#FFF0B3"></span> Hari libur (tidak dihitung) &nbsp;&nbsp;
                    Kolom kosong = belum tiba / belum dihitung
                </div>

                <table class="ttd">
                    <tr>
                        <td>Diketahui,<br>{{ $penandatangan['diketahui']['jabatan'] }}<br><br><br><br><br><span class="nama">{{ $penandatangan['diketahui']['nama'] }}</span></td>
                        <td>Mengetahui,<br>{{ $penandatangan['mengetahui']['jabatan'] }}<br><br><br><br><br><span class="nama">{{ $penandatangan['mengetahui']['nama'] }}</span></td>
                        <td>Dibuat oleh,<br>{{ $penandatangan['dibuat']['jabatan'] }}<br><br><br><br><br><span class="nama">{{ $penandatangan['dibuat']['nama'] }}</span></td>
                    </tr>
                </table>
            @endif
        </div>
    @endforeach
</body>
</html>