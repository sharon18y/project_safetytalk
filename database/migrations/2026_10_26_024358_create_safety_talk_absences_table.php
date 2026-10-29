<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('safety_talk_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('safety_talk_id')->constrained('safety_talks')->cascadeOnDelete();
            $table->enum('kategori', ['Cuti', 'Sakit', 'Terlambat', 'Izin', 'Lain-lain']);
            $table->integer('jumlah');
            $table->string('keterangan_nama')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safety_talk_absences');
    }
};