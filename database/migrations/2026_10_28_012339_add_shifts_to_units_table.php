<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Shift yang wajib melapor per unit, dipisah koma. Contoh: "Day" atau "I,II,III"
        Schema::table('units', function (Blueprint $table) {
            $table->string('shifts')->default('Day')->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('shifts');
        });
    }
};