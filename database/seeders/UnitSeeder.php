<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['no_urut' => 1, 'nama' => 'Penunjang Workshop / DC BKS', 'jumlah_shift' => 1],
            ['no_urut' => 2, 'nama' => 'Cleaning Industrial Biringkassi', 'jumlah_shift' => 1],
            ['no_urut' => 3, 'nama' => 'Silo Pantai', 'jumlah_shift' => 1],
            ['no_urut' => 4, 'nama' => 'Pemeliharaan Otonomus', 'jumlah_shift' => 1],
            ['no_urut' => 5, 'nama' => 'Pemeliharaan Dust Collector Tonasa/PUP', 'jumlah_shift' => 1],
            ['no_urut' => 6, 'nama' => 'Pemeliharaan Peralatan Pabrik & Koreseri', 'jumlah_shift' => 1],
            ['no_urut' => 7, 'nama' => 'Pemuatan Clinker BKS', 'jumlah_shift' => 3],
            ['no_urut' => 8, 'nama' => 'Pemuatan Semen Curah BKS', 'jumlah_shift' => 3],
            ['no_urut' => 9, 'nama' => 'Operator Excavator D2 Bks', 'jumlah_shift' => 3],
            ['no_urut' => 10, 'nama' => 'Silo Pabrik', 'jumlah_shift' => 3],
            ['no_urut' => 11, 'nama' => 'Pengantongan Biringkassi (Packer)', 'jumlah_shift' => 3],
            ['no_urut' => 12, 'nama' => 'Pengantongan Pabrik 2/3', 'jumlah_shift' => 3],
            ['no_urut' => 13, 'nama' => 'Pengantongan Pabrik 5', 'jumlah_shift' => 3],
            ['no_urut' => 14, 'nama' => 'Master loading/pengawas kade Biringkassi', 'jumlah_shift' => 3],
            ['no_urut' => 15, 'nama' => 'UPS Palu', 'jumlah_shift' => 2],
            ['no_urut' => 16, 'nama' => 'PP Bali', 'jumlah_shift' => 3],
            ['no_urut' => 17, 'nama' => 'PP Banjarmasin', 'jumlah_shift' => 3],
            ['no_urut' => 18, 'nama' => 'PP Balikpapan', 'jumlah_shift' => 2],
            ['no_urut' => 19, 'nama' => 'UPS Makassar', 'jumlah_shift' => 2],
            ['no_urut' => 20, 'nama' => 'Alat Berat Dermaga 2', 'jumlah_shift' => 3],
            ['no_urut' => 21, 'nama' => 'Penjumbuan T4', 'jumlah_shift' => 2],
            ['no_urut' => 22, 'nama' => 'PP Sorong', 'jumlah_shift' => 1],
            ['no_urut' => 23, 'nama' => 'PP Bitung', 'jumlah_shift' => 1],
            ['no_urut' => 24, 'nama' => 'PP Mamuju', 'jumlah_shift' => 1],
            ['no_urut' => 25, 'nama' => 'PP Samarinda', 'jumlah_shift' => 1],
            ['no_urut' => 26, 'nama' => 'PP Oba Maluku Utara', 'jumlah_shift' => 1],
            ['no_urut' => 27, 'nama' => 'PP Ambon', 'jumlah_shift' => 1],
            ['no_urut' => 28, 'nama' => 'PP Pontianak', 'jumlah_shift' => 1],
            ['no_urut' => 29, 'nama' => 'PP Kendari', 'jumlah_shift' => 1],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}