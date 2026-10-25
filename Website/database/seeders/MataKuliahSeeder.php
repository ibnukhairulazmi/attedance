<?php

namespace Database\Seeders;

use App\Models\MataKuliah;
use Illuminate\Database\Seeder;

class MataKuliahSeeder extends Seeder
{
    /**
     * Mata kuliah dikelompokkan per kode jurusan. Kode MK diberi prefix
     * kode jurusan (mis. IF101) supaya asal-jurusannya jelas — tabel
     * `mata_kuliah` sendiri tidak punya kolom jurusan/prodi, jadi
     * pemetaan "mata kuliah sesuai jurusan" ditegakkan di level seeder ini
     * (dipakai lagi oleh JadwalSeeder saat memilih mata kuliah untuk kelas
     * jurusan tertentu).
     *
     * Setiap jurusan sengaja diberi campuran 2 SKS & 3 SKS supaya aturan
     * pembuatan jadwal (teori saja vs teori+praktikum) sama-sama terwakili.
     */
    public const DAFTAR_MATA_KULIAH = [
        'IF' => [
            ['kode_mk' => 'IF101', 'nama_mk' => 'Algoritma dan Pemrograman',        'sks' => 3],
            ['kode_mk' => 'IF102', 'nama_mk' => 'Struktur Data',                    'sks' => 3],
            ['kode_mk' => 'IF103', 'nama_mk' => 'Basis Data',                       'sks' => 2],
        ],
        'SI' => [
            ['kode_mk' => 'SI101', 'nama_mk' => 'Analisis & Perancangan Sistem Informasi', 'sks' => 3],
            ['kode_mk' => 'SI102', 'nama_mk' => 'Pemrograman Web',                  'sks' => 3],
            ['kode_mk' => 'SI103', 'nama_mk' => 'Manajemen Basis Data',             'sks' => 2],
        ],
        'TI' => [
            ['kode_mk' => 'TI101', 'nama_mk' => 'Jaringan Komputer',                'sks' => 3],
            ['kode_mk' => 'TI102', 'nama_mk' => 'Administrasi Sistem Jaringan',     'sks' => 3],
            ['kode_mk' => 'TI103', 'nama_mk' => 'Keamanan Jaringan',                'sks' => 2],
        ],
        'BD' => [
            ['kode_mk' => 'BD101', 'nama_mk' => 'Digital Marketing',                'sks' => 2],
            ['kode_mk' => 'BD102', 'nama_mk' => 'E-Commerce',                       'sks' => 3],
            ['kode_mk' => 'BD103', 'nama_mk' => 'Analisis Bisnis Digital',          'sks' => 2],
        ],
        'IK' => [
            ['kode_mk' => 'IK101', 'nama_mk' => 'Pengantar Ilmu Komunikasi',        'sks' => 2],
            ['kode_mk' => 'IK102', 'nama_mk' => 'Produksi Media',                   'sks' => 3],
            ['kode_mk' => 'IK103', 'nama_mk' => 'Public Relations',                 'sks' => 2],
        ],
    ];

    public function run(): void
    {
        $total = 0;

        foreach (self::DAFTAR_MATA_KULIAH as $daftar) {
            foreach ($daftar as $mk) {
                MataKuliah::updateOrCreate(
                    ['kode_mk' => $mk['kode_mk']],
                    ['nama_mk' => $mk['nama_mk'], 'sks' => $mk['sks']]
                );
                $total++;
            }
        }

        $this->command?->info("MataKuliahSeeder: {$total} mata kuliah berhasil dibuat.");
    }
}
