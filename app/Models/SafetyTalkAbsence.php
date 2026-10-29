<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SafetyTalkAbsence extends Model
{
    use HasFactory;

    protected $fillable = [
        'safety_talk_id',
        'kategori',
        'jumlah',
        'keterangan_nama',
    ];

    // Relasi: Rincian ini milik satu Laporan Safety Talk
    public function safetyTalk()
    {
        return $this->belongsTo(SafetyTalk::class);
    }
}