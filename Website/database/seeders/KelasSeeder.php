<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Peta kode jurusan (2 huruf) -> nilai enum kolom `kelas.prodi`.
     * Dipakai bersama oleh KelasSeeder, MataKuliahSeeder, MahasiswaSeeder,
     * dan JadwalSeeder supaya konsisten.
     */
    public const PRODI_MAP = [
        'IF' => 'Informatika',
        'SI' => 'Sistem Informasi',
        'TI' => 'Teknologi Informasi',
        'BD' => 'Bisnis Digital',
        'IK' => 'Ilmu Komunikasi',
    ];

    /**
     * Daftar kelas yang dibuat: beberapa angkatan berbeda untuk beberapa
     * jurusan berbeda, sesuai contoh dari pemilik sistem: TI22A, IF23A, SI24A.
     * Nama kelas = {KodeJurusan}{2 digit angkatan}{Huruf kelas paralel}.
     */
    public const DAFTAR_KELAS = [
        ['kode_jurusan' => 'TI', 'angkatan' => 2022, 'paralel' => 'A'],
        ['kode_jurusan' => 'TI', 'angkatan' => 2023, 'paralel' => 'A'],
        ['kode_jurusan' => 'IF', 'angkatan' => 2023, 'paralel' => 'A'],
        ['kode_jurusan' => 'IF', 'angkatan' => 2024, 'paralel' => 'A'],
        ['kode_jurusan' => 'SI', 'angkatan' => 2023, 'paralel' => 'A'],
        ['kode_jurusan' => 'SI', 'angkatan' => 2024, 'paralel' => 'A'],
        ['kode_jurusan' => 'BD', 'angkatan' => 2023, 'paralel' => 'A'],
        ['kode_jurusan' => 'IK', 'angkatan' => 2023, 'paralel' => 'A'],
    ];

    public function run(): void
    {
        foreach (self::DAFTAR_KELAS as $row) {
            $angkatanYY = substr((string) $row['angkatan'], -2);
            $namaKelas  = "{$row['kode_jurusan']}{$angkatanYY}{$row['paralel']}";

            Kelas::updateOrCreate(
                ['nama_kelas' => $namaKelas],
                [
                    'prodi'    => self::PRODI_MAP[$row['kode_jurusan']],
                    'angkatan' => $row['angkatan'],
                ]
            );
        }

        $this->command?->info('KelasSeeder: ' . count(self::DAFTAR_KELAS) . ' kelas berhasil dibuat.');
    }
}
