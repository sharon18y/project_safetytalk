<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Services\PantauBulanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PantauUnitController extends Controller
{
    public function __construct(private PantauBulanan $pantau)
    {
    }

    /** Bulan dari ?bulan=2026-10, tidak boleh melewati bulan berjalan. */
    private function bulan(Request $request): Carbon
    {
        $sekarang = now()->startOfMonth();

        try {
            $bulan = Carbon::createFromFormat('!Y-m', (string) $request->query('bulan', $sekarang->format('Y-m')));
        } catch (\Throwable $e) {
            $bulan = $sekarang->copy();
        }

        return $bulan->gt($sekarang) ? $sekarang->copy() : $bulan->startOfMonth();
    }

    /** Daftar semua unit + status hari ini. */
    public function index(Request $request)
    {
        $bulan = $this->bulan($request);
        $hariIni = now()->startOfDay();
        $bulanIni = $bulan->isSameMonth($hariIni);

        $peta = $this->pantau->petaSemua($bulan, Unit::orderBy('no_urut')->get());

        $baris = collect($peta)->map(function ($p) use ($bulan, $hariIni, $bulanIni) {
            $u = $p['unit'];

            // Laporan terbaru hari ini (untuk urutan "Sudah melapor")
            $terbaru = $bulanIni
                ? collect($p['hari'][$hariIni->day]['shifts'])->filter(fn ($s) => $s['waktu'])->sortByDesc('waktu')->first()
                : null;

            return [
                'id'          => $u->id,
                'no'          => $u->no_urut,
                'nama'        => $u->nama,
                'shift_label' => PantauBulanan::ringkasShift($p['shifts']),
                'hari_ini'    => $bulanIni ? $p['hari'][$hariIni->day]['kode'] : null,
                'waktu'       => $terbaru['waktu'] ?? 0,
                'jam'         => $terbaru['jam'] ?? null,
                'persen'      => $p['persen'],
                'merah'       => $p['merah'],
                'url'         => route('manajemen.pantau.show', ['unit' => $u->id, 'bulan' => $bulan->format('Y-m')]),
            ];
        })->values();

        return view('manajemen.pantau.index', [
            'baris'      => $baris,
            'bulan'      => $bulan,
            'bulanLabel' => $bulan->copy()->locale('id')->isoFormat('MMMM YYYY'),
            'prev'       => $bulan->copy()->subMonthNoOverflow()->format('Y-m'),
            'next'       => $bulanIni ? null : $bulan->copy()->addMonthNoOverflow()->format('Y-m'),
            'bulanIni'   => $bulanIni,
        ]);
    }

    /** Detail satu unit: tab "Hari ini" dan "Kalender". */
    public function show(Request $request, Unit $unit)
    {
        $bulan = $this->bulan($request);
        $hariIni = now()->startOfDay();
        $bulanIni = $bulan->isSameMonth($hariIni);

        $laporan = $this->pantau->ambilLaporan($bulan, $unit->id)->get($unit->id, collect());
        $peta = $this->pantau->petaUnit($unit, $bulan, $laporan);

        // Status "hari ini" selalu dihitung dari hari ini, walau yang dibuka bulan lalu
        if ($bulanIni) {
            $hariIniInfo = $peta['hari'][$hariIni->day];
        } else {
            $laporanSekarang = $this->pantau->ambilLaporan($hariIni, $unit->id)->get($unit->id, collect());
            $hariIniInfo = $this->pantau->petaUnit($unit, $hariIni->copy()->startOfMonth(), $laporanSekarang)['hari'][$hariIni->day];
        }

        return view('manajemen.pantau.show', [
            'unit'        => $unit,
            'bulan'       => $bulan,
            'bulanLabel'  => $bulan->copy()->locale('id')->isoFormat('MMMM YYYY'),
            'prev'        => $bulan->copy()->subMonthNoOverflow()->format('Y-m'),
            'next'        => $bulanIni ? null : $bulan->copy()->addMonthNoOverflow()->format('Y-m'),
            'bulanIni'    => $bulanIni,
            'hariIniNo'   => $bulanIni ? $hariIni->day : null,
            'hariIniInfo' => $hariIniInfo,
            'peta'        => $peta,
            'shiftLabel'  => PantauBulanan::ringkasShift($peta['shifts']),
            'offset'      => $bulan->copy()->startOfMonth()->dayOfWeekIso - 1, // Senin = 0
            'jumlahHari'  => $bulan->daysInMonth,
        ]);
    }

    /** Matriks semua unit dalam satu bulan (pratinjau atau unduh PDF). */
    public function pdf(Request $request)
    {
        $bulan = $this->bulan($request);
        $peta = $this->pantau->petaSemua($bulan, Unit::orderBy('no_urut')->get());

        $pdf = Pdf::loadView('manajemen.pantau.pdf', [
            'peta'          => $peta,
            'bulan'         => $bulan,
            'bulanLabel'    => $bulan->copy()->locale('id')->isoFormat('MMMM YYYY'),
            'jumlahHari'    => $bulan->daysInMonth,
            'penandatangan' => config('safety.penandatangan'),
            'libur'         => collect(range(1, $bulan->daysInMonth))
                ->filter(fn ($d) => $this->pantau->libur($bulan->copy()->startOfMonth()->day($d)))
                ->values()->all(),
        ])->setPaper('a4', 'landscape');

        $nama = 'monitoring-safety-talk-' . $bulan->format('Y-m') . '.pdf';

        return $request->boolean('unduh') ? $pdf->download($nama) : $pdf->stream($nama);
    }
}