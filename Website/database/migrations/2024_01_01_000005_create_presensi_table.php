<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sesi_presensi_id')
                ->constrained('sesi_presensi')
                ->cascadeOnDelete();

            $table->foreignId('mahasiswa_id')
                ->constrained('mahasiswa')
                ->cascadeOnDelete();

            $table->enum('status', [
                'hadir',
                'izin',
                'sakit',
                'alpha'
            ]);

            $table->decimal('scan_latitude', 10, 7)->nullable();
            $table->decimal('scan_longitude', 10, 7)->nullable();

            $table->decimal('jarak_meter', 8, 2)->nullable();

            $table->timestamp('scan_time')->nullable();

            $table->string('ip_address')->nullable();
            $table->string('device_id')->nullable();

            $table->timestamps();

            $table->unique([
                'sesi_presensi_id',
                'mahasiswa_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
