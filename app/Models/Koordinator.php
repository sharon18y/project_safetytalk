<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Koordinator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
    ];

    // Relasi: Profil Koordinator dimiliki oleh satu Akun User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Koordinator ditugaskan ke banyak Unit Kerja
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'koordinator_unit');
    }

    // Relasi: Koordinator bisa membawakan/tercatat di banyak Laporan Safety Talk
    public function safetyTalks()
    {
        return $this->belongsToMany(SafetyTalk::class, 'safety_talk_koordinator');
    }
}