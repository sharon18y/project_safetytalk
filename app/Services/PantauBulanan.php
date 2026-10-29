<?php

namespace App\Services;

use App\Models\SafetyTalk;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Menghitung status pelaporan Safety Talk per unit, per shift, per tanggal.
 *
 * Kode status satu tanggal:
 *  ok       semua shift wajib sudah melapor tepat waktu
 *  late     semua shift sudah melapor, minimal satu terlambat (dikirim H+1)
 *  partial  sebagian shift saja yang melapor
 *  miss     tanggal sudah lewat, bukan libur, tidak ada laporan
 *  pending  hari ini, belum ada laporan (belum dihitung merah)
 *  libur    hari Minggu / libur
 *  future   tanggal belum tiba
 *  pra      tanggal sebelum aplikasi mulai dipakai (config safety.tanggal_mulai), tidak dihitung
 */
class PantauBulanan
{
    /** Daftar shift wajib sebuah unit, misal ['Day'] atau ['I','II','III']. */
    public function shiftWajib(Unit $unit): array
    {
        $daftar = array_values(array_filter(array_map('trim', explode(',', (string) ($unit->shifts ?? 'Day')))));

        return $daftar ?: ['Day'];
    }

    public static function labelShift(string $shift): string
    {
        return in_array($shift, ['I', 'II', 'III'], true) ? 'Shift ' . $shift : $shift;
    }

    /** "Day" atau "Shift I, II, III" */
    public static function ringkasShift(array $shifts): string
    {
        if ($shifts === ['Day']) {
            return 'Day';
        }

        return 'Shift ' . implode(', ', $shifts);
    }

    public function libur(Carbon $tgl): bool
    {
        return $tgl->isSunday() || in_array($tgl->toDateString(), config('safety.hari_libur', []), true);
    }

    /**
     * Ambil laporan TERKIRIM satu bulan, dikelompokkan: unit_id => tanggal(Y-m-d) => koleksi laporan.
     */
    public function ambilLaporan(Carbon $bulan, $unitIds = null): Collection
    {
        $awal = $bulan->copy()->startOfMonth();
        $akhir = $bulan->copy()->addMonthNoOverflow()->startOfMonth();

        $q = SafetyTalk::query()
            ->where('status', 'terkirim')
            ->where('tanggal', '>=', $awal->toDateString())
            ->where('tanggal', '<', $akhir->toDateString());

        if ($unitIds) {
            $q->whereIn('unit_id', (array) $unitIds);
        }

        return $q->get()
            ->groupBy('unit_id')
            ->map(fn ($perUnit) => $perUnit->groupBy(fn ($r) => Carbon::parse($r->tanggal)->toDateString()));
    }

    /** Status satu tanggal untuk satu unit. */
    public function hari(array $wajib, Collection $rows, Carbon $tgl, Carbon $hariIni): array
    {
        $libur = $this->libur($tgl);
        $future = $tgl->gt($hariIni);
        $mulai = config('safety.tanggal_mulai');
        $pra = $mulai && $tgl->lt(Carbon::parse($mulai)->startOfDay());
        $adaHariIni = $tgl->equalTo($hariIni);
        $harian = $wajib === ['Day']; // unit tanpa shift: laporan apa pun pada tanggal itu dianggap sah

        $shifts = [];
        $jumlahLapor = 0;
        $adaTelat = false;

        foreach ($wajib as $s) {
            $r = $harian ? $rows->sortBy('created_at')->first() : $rows->firstWhere('shift', $s);

            if ($r) {
                $telat = Carbon::parse($r->created_at)->startOfDay()->gt($tgl);
                $jumlahLapor++;
                $adaTelat = $adaTelat || $telat;

                $shifts[] = [
                    'shift'       => $s,
                    'label'       => self::labelShift($s),
                    'kode'        => $telat ? 'late' : 'ok',
                    'jam'         => Carbon::parse($r->created_at)->format('H.i'),
                    'waktu'       => Carbon::parse($r->created_at)->timestamp,
                    'hadir'       => (int) $r->jml_hadir,
                    'personil'    => (int) $r->jml_personil,
                    'koordinator' => $r->nama_koordinator,
                    'url'         => route('manajemen.laporan.show', $r->id),
                ];
            } else {
                $shifts[] = [
                    'shift'       => $s,
                    'label'       => self::labelShift($s),
                    'kode'        => $pra ? 'pra' : ($libur ? 'libur' : ($future ? 'future' : ($adaHariIni ? 'pending' : 'miss'))),
                    'jam'         => null,
                    'waktu'       => null,
                    'hadir'       => null,
                    'personil'    => null,
                    'koordinator' => null,
                    'url'         => null,
                ];
            }
        }

        if ($jumlahLapor === count($wajib)) {
            $kode = $adaTelat ? 'late' : 'ok';
        } elseif ($jumlahLapor > 0) {
            $kode = 'partial';
        } else {
            $kode = $pra ? 'pra' : ($libur ? 'libur' : ($future ? 'future' : ($adaHariIni ? 'pending' : 'miss')));
        }

        return [
            'kode'   => $kode,
            'label'  => $tgl->copy()->locale('id')->isoFormat('dddd, D MMMM YYYY'),
            'shifts' => $shifts,
            // tanggal ikut dihitung kewajiban?
            'hitung' => !$libur && !$future && !$pra && $kode !== 'pending',
        ];
    }

    /** Peta satu bulan untuk satu unit beserta ringkasannya. */
    public function petaUnit(Unit $unit, Carbon $bulan, Collection $laporanUnit, ?Carbon $hariIni = null): array
    {
        $hariIni = ($hariIni ?? now())->copy()->startOfDay();
        $wajib = $this->shiftWajib($unit);

        $hari = [];
        $jmlWajib = $selesai = $merah = $terlambat = 0;

        for ($d = 1; $d <= $bulan->daysInMonth; $d++) {
            $tgl = $bulan->copy()->startOfMonth()->day($d)->startOfDay();
            $rows = $laporanUnit->get($tgl->toDateString(), collect());
            $info = $this->hari($wajib, $rows, $tgl, $hariIni);
            $hari[$d] = $info;

            if ($info['hitung']) {
                $jmlWajib++;
                if (in_array($info['kode'], ['ok', 'late'], true)) {
                    $selesai++;
                }
                if ($info['kode'] === 'late') {
                    $terlambat++;
                }
                if (in_array($info['kode'], ['miss', 'partial'], true)) {
                    $merah++;
                }
            }
        }

        return [
            'shifts'    => $wajib,
            'hari'      => $hari,
            'wajib'     => $jmlWajib,
            'selesai'   => $selesai,
            'merah'     => $merah,
            'terlambat' => $terlambat,
            'persen'    => $jmlWajib > 0 ? (int) round($selesai / $jmlWajib * 100) : null,
        ];
    }

    /** Peta satu bulan untuk banyak unit (satu query saja). */
    public function petaSemua(Carbon $bulan, Collection $units): array
    {
        $laporan = $this->ambilLaporan($bulan);

        return $units->map(function (Unit $u) use ($bulan, $laporan) {
            return ['unit' => $u] + $this->petaUnit($u, $bulan, $laporan->get($u->id, collect()));
        })->all();
    }
}