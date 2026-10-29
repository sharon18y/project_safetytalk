<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SafetyTalk;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();

        // Jika belum login ATAU masih tersangkut sesi role 'manajemen', gunakan profil default pemantauan
        if (!$authUser || $authUser->role === 'manajemen') {
            $user = (object) [
                'name' => 'Direktur Operasi',
                'email' => 'Pemantauan Publik',
                'role' => 'manajemen',
            ];
        } else {
            $user = $authUser;
        }
        
        // Mengambil jumlah laporan yang masuk khusus hari ini
        $hariIni = Carbon::today();
        
        $laporanHariIni = SafetyTalk::whereDate('tanggal', $hariIni)
                            ->where('status', 'terkirim')
                            ->count();

        // Mengirim data user dan jumlah laporan ke view dashboard
        return view('dashboard', compact('user', 'laporanHariIni'));
    }
}