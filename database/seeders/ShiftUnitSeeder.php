<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class ShiftUnitSeeder extends Seeder
{
    /**
     * Jumlah shift tiap unit, disusun dari lembar monitoring (kertas) Juni-Agustus 2026.
     * Unit yang tidak tercantum di sini tetap "Day" (default migrasi).
     * Mohon dicek lagi dengan lembar asli sebelum dipakai.
     */
    public function run(): void
    {
        $peta = [
            7  => 'I,II,III',  // Pemuatan Clinker BKS
            8  => 'I,II,III',  // Pemuatan Semen Curah BKS
            9  => 'I,II,III',  // Operator Excavator D2 Bks
            10 => 'I,II,III',  // Silo Pabrik
            11 => 'I,II,III',  // Pengantongan Biringkassi (Packer)
            12 => 'I,II,III',  // Pengantongan Pabrik 2/3
            13 => 'I,II,III',  // Pengantongan Pabrik 5
            14 => 'I,II,III',  // Master loading / pengawas kade
            15 => 'I,II',      // UPS Palu
            16 => 'I,II,III',  // PP Bali
            17 => 'I,II,III',  // PP Banjarmasin
            18 => 'I,II',      // PP Balikpapan
            19 => 'I,II',      // UPS Makassar
            20 => 'I,II,III',  // Alat Berat Dermaga 2
            21 => 'I,II',      // Penjumboan T4
        ];

        foreach ($peta as $noUrut => $shifts) {
            Unit::where('no_urut', $noUrut)->update(['shifts' => $shifts]);
        }
    }
}