<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Tugas
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('file_petunjuk')->nullable();
            $table->dateTime('deadline');
            $table->integer('max_score')->default(100);
            $table->enum('tipe_pengumpulan', ['file', 'link', 'keduanya'])->default('file');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Pengumpulan Tugas Mahasiswa
        Schema::create('tugas_pengumpulan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->string('file_path')->nullable();
            $table->string('link_url')->nullable();
            $table->text('catatan_mahasiswa')->nullable();
            $table->dateTime('waktu_kumpul');
            $table->enum('status', ['tepat_waktu', 'terlambat'])->default('tepat_waktu');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('catatan_dosen')->nullable();
            $table->timestamps();

            $table->unique(['tugas_id', 'mahasiswa_id']);
        });

        // 3. Tabel Bobot Penilaian Mata Kuliah
        Schema::create('bobot_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->unique()->constrained('jadwal')->cascadeOnDelete();
            $table->integer('bobot_presensi')->default(10);
            $table->integer('bobot_tugas')->default(20);
            $table->integer('bobot_uts')->default(30);
            $table->integer('bobot_uas')->default(40);
            $table->timestamps();
        });

        // 4. Tabel Nilai Mahasiswa (UTS, UAS, dll)
        Schema::create('nilai_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->decimal('nilai_uts', 5, 2)->nullable();
            $table->decimal('nilai_uas', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['jadwal_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_mahasiswa');
        Schema::dropIfExists('bobot_penilaian');
        Schema::dropIfExists('tugas_pengumpulan');
        Schema::dropIfExists('tugas');
    }
};
