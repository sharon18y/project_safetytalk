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
        Schema::create('safety_talk_koordinator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('safety_talk_id')->constrained('safety_talks')->cascadeOnDelete();
            $table->foreignId('koordinator_id')->constrained('koordinators')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safety_talk_koordinator');
    }
};