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
        Schema::create('safety_talks', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->enum('shift', ['Day', 'I', 'II', 'III']);
            $table->text('materi');
            $table->integer('jml_personil');
            $table->integer('jml_hadir');
            $table->integer('jml_tidak_hadir');
            $table->string('foto_path')->nullable();
            $table->enum('status', ['draft', 'terkirim'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            // Mencegah duplikat unit dan shift di hari yang sama
            $table->unique(['tanggal', 'unit_id', 'shift']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safety_talks');
    }
};