<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_presensi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jadwal_id')
                ->constrained('jadwal')
                ->cascadeOnDelete();
            $table->unique(['jadwal_id', 'tanggal'], 'unique_session_per_jadwal_per_day');

            $table->date('tanggal');

            $table->string('qr_token')->unique();

            $table->timestamp('qr_expired_at');

            $table->decimal('kelas_latitude', 10, 7);
            $table->decimal('kelas_longitude', 10, 7);

            $table->integer('radius_meter')
                ->default(10);

            $table->enum('status', [
                'aktif',
                'selesai',
                'expired'
            ])->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_presensi', function (Blueprint $table) {
            $table->dropUnique('unique_session_per_jadwal_per_day');
        });
    }
};
