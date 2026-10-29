<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\SafetyTalk;
use App\Models\SafetyTalkAbsence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SafetyTalkController extends Controller
{
    public function index()
    {
        $reports = SafetyTalk::with('unit')
                    ->where('created_by', Auth::id())
                    ->orderBy('tanggal', 'desc')
                    ->get();

        return view('koordinator.safety_talk.index', compact('reports'));
    }

    public function create()
    {
        $units = Unit::orderBy('no_urut', 'asc')->get();
        return view('koordinator.safety_talk.create', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'unit_id' => 'required|exists:units,id',
            'shift' => 'required|in:Day,I,II,III',
            'nama_koordinator' => 'nullable|string|max:255',
            'materi' => 'required|string',
            'jml_personil' => 'required|integer|min:1',
            'jml_hadir' => 'required|integer|min:0',
            'jml_tidak_hadir' => 'required|integer|min:0',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'keterangan_absen' => 'nullable|string', // Menangkap teks alasan
        ]);

        $exists = SafetyTalk::where('tanggal', $request->tanggal)
                    ->where('unit_id', $request->unit_id)
                    ->where('shift', $request->shift)
                    ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['Laporan untuk Unit dan Shift ini pada tanggal tersebut sudah dibuat!']);
        }

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            // Upload foto langsung ke Cloudinary
            $fotoPath = cloudinary()->upload($request->file('foto')->getRealPath(), [
                'folder' => 'safety_talk_biringkassi'
            ])->getSecurePath();
        }

        // Simpan Data Utama
        $safetyTalk = SafetyTalk::create([
            'tanggal' => $request->tanggal,
            'unit_id' => $request->unit_id,
            'shift' => $request->shift,
            'materi' => $request->materi,
            'jml_personil' => $request->jml_personil,
            'jml_hadir' => $request->jml_hadir,
            'jml_tidak_hadir' => $request->jml_tidak_hadir,
            'foto_path' => $fotoPath,
            'status' => 'terkirim', // Langsung terkirim (1 halaman)
            'created_by' => Auth::id(),
        ]);

        // Simpan Alasan ke Database jika ada absen
        if ($request->jml_tidak_hadir > 0) {
            SafetyTalkAbsence::create([
                'safety_talk_id' => $safetyTalk->id,
                'kategori' => 'Lain-lain',
                'jumlah' => $request->jml_tidak_hadir,
                'keterangan_nama' => $request->keterangan_absen ?? '-', // Teks bebas dimasukkan ke sini
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Laporan berhasil dikirim!');
    }

    public function show($id)
    {
        $safetyTalk = SafetyTalk::with('unit')->findOrFail($id);
        
        if (Auth::user()->role === 'koordinator' && $safetyTalk->created_by !== Auth::id()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke laporan ini.');
        }

        $absensi = SafetyTalkAbsence::where('safety_talk_id', $id)->get();

        return view('koordinator.safety_talk.show', compact('safetyTalk', 'absensi'));
    }
}