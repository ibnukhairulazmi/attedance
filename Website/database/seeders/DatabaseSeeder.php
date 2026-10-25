<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan seeder mengikuti urutan foreign key: Dosen, Kelas, MataKuliah
     * (tidak saling bergantung) -> Mahasiswa (butuh Kelas) -> Jadwal
     * (butuh Kelas + Dosen + MataKuliah).
     *
     * `sesi_presensi` dan `presensi` SENGAJA TIDAK di-seed di sini sesuai
     * ketentuan — kedua tabel itu berisi data transaksional (sesi kelas
     * yang benar-benar dibuka dosen & kehadiran mahasiswa yang benar-benar
     * discan), bukan data master, jadi dibiarkan kosong dan hanya akan
     * terisi lewat pemakaian aplikasi yang sebenarnya.
     */
    public function run(): void
    {
        $this->call([
            DosenSeeder::class,
            KelasSeeder::class,
            MataKuliahSeeder::class,
            MahasiswaSeeder::class,
            JadwalSeeder::class,
            PresensiSessionSeeder::class,
            PresensiSeeder::class,
        ]);

        $this->command?->info('Seeding selesai.');
    }
}
