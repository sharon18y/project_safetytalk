<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\SafetyTalk;
use App\Models\SafetyTalkAbsence;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        // 1. Tentukan tanggal yang dipantau (Default: Hari ini)
        $selectedDateStr = $request->query('tanggal', Carbon::today()->toDateString());
        try {
            $selectedDate = Carbon::parse($selectedDateStr)->startOfDay();
        } catch (\Exception $e) {
            $selectedDate = Carbon::today();
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Mode segmented control: 'today', 'yesterday', atau 'custom'
        if ($selectedDate->equalTo($today)) {
            $dateMode = 'today';
        } elseif ($selectedDate->equalTo($yesterday)) {
            $dateMode = 'yesterday';
        } else {
            $dateMode = 'custom';
        }

        // Cek apakah hari Minggu atau tanggal libur nasional
        $daftarLiburNasional = [
            // Format 'YYYY-MM-DD' jika ingin menambahkan libur nasional
        ];
        $isHoliday = $selectedDate->isSunday() || in_array($selectedDate->toDateString(), $daftarLiburNasional);
        $isFuture = $selectedDate->gt($today);

        // 2. Query 1: Ambil seluruh unit kerja
        $units = Unit::orderBy('no_urut', 'asc')->get();

        // 3. Query 2: Ambil seluruh laporan terkirim pada tanggal terpilih (Eager load unit & creator)
        $reportsOnDate = SafetyTalk::with(['unit', 'creator'])
            ->whereDate('tanggal', $selectedDate->toDateString())
            ->where('status', 'terkirim')
            ->get()
            ->keyBy(function ($item) {
                return $item->unit_id . '_' . $item->shift;
            });

        // 4. Query 3: Ambil koordinator terakhir untuk setiap Unit + Shift (1 Query dengan Subquery)
        $latestIds = SafetyTalk::selectRaw('MAX(id) as id')
            ->where('status', 'terkirim')
            ->groupBy('unit_id', 'shift')
            ->pluck('id');

        $lastCoordinators = SafetyTalk::with('creator')
            ->whereIn('id', $latestIds)
            ->get()
            ->keyBy(function ($item) {
                return $item->unit_id . '_' . $item->shift;
            });

        // 5. Bangun matriks ekspektasi Unit x Shift
        $sudahMelapor = [];
        $belumMelapor = [];

        foreach ($units as $unit) {
            $activeShifts = $this->resolveUnitShifts($unit);

            foreach ($activeShifts as $shift) {
                $key = $unit->id . '_' . $shift;

                if ($reportsOnDate->has($key)) {
                    $rep = $reportsOnDate->get($key);

                    // Evaluasi keterlambatan (Dikirim pada H+1 atau lebih dari tanggal laporan)
                    $tanggalLaporan = Carbon::parse($rep->tanggal)->startOfDay();
                    $tanggalKirim = Carbon::parse($rep->created_at)->startOfDay();
                    $isLate = $tanggalKirim->gt($tanggalLaporan);

                    $persenHadir = $rep->jml_personil > 0
                        ? (int) round(($rep->jml_hadir / $rep->jml_personil) * 100)
                        : 0;

                    $namaKoordinator = $rep->nama_koordinator
                        ?? optional($rep->creator)->name
                        ?? 'Koordinator Lapangan';

                    $sudahMelapor[] = [
                        'id' => $rep->id,
                        'unit_id' => $unit->id,
                        'no_urut' => $unit->no_urut,
                        'unit_nama' => $unit->nama,
                        'shift' => $shift,
                        'jam_kirim' => Carbon::parse($rep->created_at)->format('H:i'),
                        'jml_personil' => (int) $rep->jml_personil,
                        'jml_hadir' => (int) $rep->jml_hadir,
                        'jml_tidak_hadir' => (int) $rep->jml_tidak_hadir,
                        'persen_hadir' => $persenHadir,
                        'is_late' => $isLate,
                        'koordinator' => $namaKoordinator,
                        'url_detail' => route('manajemen.laporan.show', $rep->id),
                    ];
                } else {
                    // Hanya masukkan ke daftar belum melapor jika bukan hari libur
                    if (!$isHoliday) {
                        $lastRecord = $lastCoordinators->get($key);
                        $lastKoordinatorName = 'Belum tercatat';

                        if ($lastRecord) {
                            $lastKoordinatorName = $lastRecord->nama_koordinator
                                ?? optional($lastRecord->creator)->name
                                ?? 'Belum tercatat';
                        }

                        $belumMelapor[] = [
                            'unit_id' => $unit->id,
                            'no_urut' => $unit->no_urut,
                            'unit_nama' => $unit->nama,
                            'shift' => $shift,
                            'koordinator_terakhir' => $lastKoordinatorName,
                            'cell_status' => $isFuture ? 'future' : 'missing',
                        ];
                    }
                }
            }
        }

        // Hitung ringkasan kepatuhan
        $totalWajib = $isHoliday ? count($sudahMelapor) : (count($sudahMelapor) + count($belumMelapor));
        $totalSudah = count($sudahMelapor);
        $totalBelum = count($belumMelapor);
        $totalTerlambat = collect($sudahMelapor)->where('is_late', true)->count();

        $persentaseKepatuhan = $totalWajib > 0
            ? (int) round(($totalSudah / $totalWajib) * 100)
            : ($isHoliday ? 100 : 0);

        return view('manajemen.laporan.index', [
            'selectedDate' => $selectedDate,
            'formattedDate' => $selectedDate->translatedFormat('l, d F Y'),
            'dateMode' => $dateMode,
            'todayStr' => $today->toDateString(),
            'yesterdayStr' => $yesterday->toDateString(),
            'isHoliday' => $isHoliday,
            'isFuture' => $isFuture,
            'sudahMelapor' => $sudahMelapor,
            'belumMelapor' => $belumMelapor,
            'totalWajib' => $totalWajib,
            'totalSudah' => $totalSudah,
            'totalBelum' => $totalBelum,
            'totalTerlambat' => $totalTerlambat,
            'persentaseKepatuhan' => $persentaseKepatuhan,
            // Tetap kirim variabel $reports agar kompatibel jika ada kode lain yang memanggilnya
            'reports' => $reportsOnDate->values(),
        ]);
    }

    public function show($id)
    {
        $safetyTalk = SafetyTalk::with('unit')->findOrFail($id);
        $absensi = SafetyTalkAbsence::where('safety_talk_id', $id)->get();

        return view('manajemen.laporan.show', compact('safetyTalk', 'absensi'));
    }

    /**
     * Menentukan daftar shift aktif per unit.
     * Mendukung kolom JSON/string `shifts`, `tipe_shift`, atau `jml_shift` jika sudah ada di tabel `units`.
     */
    private function resolveUnitShifts($unit): array
    {
        if (!empty($unit->shifts)) {
            if (is_array($unit->shifts)) {
                return $unit->shifts;
            }
            $decoded = json_decode($unit->shifts, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
            return array_map('trim', explode(',', $unit->shifts));
        }

        if (isset($unit->jml_shift) && (int) $unit->jml_shift === 3) {
            return ['I', 'II', 'III'];
        }

        if (isset($unit->tipe_shift) && strtolower($unit->tipe_shift) === 'shift') {
            return ['I', 'II', 'III'];
        }

        // Default jika unit hanya berjalan di jam kerja normal (Day)
        return ['Day'];
    }
}