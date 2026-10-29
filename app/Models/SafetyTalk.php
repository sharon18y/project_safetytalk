<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SafetyTalk extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'unit_id',
        'shift',
        'materi',
        'jml_personil',
        'jml_hadir',
        'jml_tidak_hadir',
        'foto_path',
        'status',
        'created_by',
    ];

    // Relasi: Laporan ini milik satu Unit Kerja
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Relasi: Laporan ini dibuat oleh satu User
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi: Satu Laporan memiliki banyak Rincian Alasan Tidak Hadir
    public function absences()
    {
        return $this->hasMany(SafetyTalkAbsence::class);
    }

    // Relasi: Laporan ini dibawakan/dihadiri oleh satu atau lebih Koordinator
    public function koordinators()
    {
        return $this->belongsToMany(Koordinator::class, 'safety_talk_koordinator');
    }
}