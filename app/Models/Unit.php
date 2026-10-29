<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_urut',
        'nama',
        'jumlah_shift',
    ];

    // Relasi: Satu Unit bisa memiliki banyak Laporan Safety Talk
    public function safetyTalks()
    {
        return $this->hasMany(SafetyTalk::class);
    }

    // Relasi: Satu Unit bisa diawasi oleh banyak Koordinator (Many-to-Many lewat pivot koordinator_unit)
    public function koordinators()
    {
        return $this->belongsToMany(Koordinator::class, 'koordinator_unit');
    }
}