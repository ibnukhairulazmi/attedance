<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('dosen_id')
                ->constrained('dosen')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('mata_kuliah_id')
                ->constrained('mata_kuliah')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('hari', [
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat',
                'Sabtu'
            ]);

            $table->time('jam_mulai');
            $table->time('jam_selesai');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
