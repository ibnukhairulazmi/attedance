<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Perbaikan performa & keamanan:
     * 1. api_token diubah dari TEXT -> VARCHAR(64) + index unik.
     *    Sebelumnya setiap request API (scan/today/history/me) melakukan
     *    full table scan pada kolom TEXT tanpa index. Dengan index unik,
     *    lookup menjadi O(log n) walaupun jumlah mahasiswa bertambah banyak.
     * 2. Tambah kolom api_token_expires_at agar token bisa kedaluwarsa
     *    (sebelumnya token API berlaku selamanya sampai reset manual).
     * 3. Index pada presensi.mahasiswa_id untuk mempercepat query
     *    riwayat presensi per mahasiswa (endpoint /today, /history).
     * 4. Index pada jadwal.dosen_id untuk mempercepat dashboard & laporan
     *    dosen (query where('dosen_id', ...) dijalankan di setiap load
     *    dashboard).
     * 5. Index komposit pada sesi_presensi(status, qr_expired_at) untuk
     *    mempercepat job terjadwal attendance:expire-sessions yang
     *    berjalan setiap menit.
     */
    public function up(): void
    {
        // MySQL/PostgreSQL: kolom TEXT tidak bisa langsung di-index unik
        // tanpa panjang tertentu, jadi kita ubah dulu ke VARCHAR(64)
        // (panjang tetap hash sha256 dalam hex = 64 karakter).
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->string('api_token', 64)->nullable()->change();
            $table->timestamp('api_token_expires_at')->nullable()->after('api_token');
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->unique('api_token', 'mahasiswa_api_token_unique');
        });

        Schema::table('presensi', function (Blueprint $table) {
            $table->index('mahasiswa_id', 'presensi_mahasiswa_id_index');
        });

        Schema::table('jadwal', function (Blueprint $table) {
            $table->index('dosen_id', 'jadwal_dosen_id_index');
        });

        Schema::table('sesi_presensi', function (Blueprint $table) {
            $table->index(['status', 'qr_expired_at'], 'sesi_presensi_status_expired_index');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropUnique('mahasiswa_api_token_unique');
            $table->dropColumn('api_token_expires_at');
            $table->text('api_token')->nullable()->change();
        });

        Schema::table('presensi', function (Blueprint $table) {
            $table->dropIndex('presensi_mahasiswa_id_index');
        });

        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropIndex('jadwal_dosen_id_index');
        });

        Schema::table('sesi_presensi', function (Blueprint $table) {
            $table->dropIndex('sesi_presensi_status_expired_index');
        });
    }
};
