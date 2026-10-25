<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    public const KODE_FAKULTAS = [
        'IF' => 'SA',
        'SI' => 'SA',
        'TI' => 'SA',
        'BD' => 'SB',
        'IK' => 'SB',
    ];

    public const KODE_JURUSAN_NUMERIK = [
        'IF' => '11A',
        'SI' => '21A',
        'TI' => '31A',
        'BD' => '21A',
        'IK' => '11A',
    ];

    private const JUMLAH_PER_KELAS = 12;

    private const NAMA_DEPAN = [
        'Ahmad',
        'Budi',
        'Citra',
        'Dewi',
        'Eka',
        'Fajar',
        'Gita',
        'Hadi',
        'Indah',
        'Joko',
        'Kartika',
        'Lestari',
        'Made',
        'Nadia',
        'Oki',
        'Putri',
        'Qori',
        'Rizki',
        'Sari',
        'Tono',
        'Umar',
        'Vina',
        'Wulan',
        'Yusuf',
    ];

    private const NAMA_BELAKANG = [
        'Pratama',
        'Wijaya',
        'Santoso',
        'Kurniawan',
        'Anggraini',
        'Saputra',
        'Ramadhani',
        'Hidayat',
        'Permata',
        'Nugroho',
        'Wardani',
        'Setiawan',
    ];

    private function buatNim(int $angkatan, string $kodeJurusan, int $urut): string
    {
        $angkatanYY   = substr((string) $angkatan, -2);
        $kodeFakultas = self::KODE_FAKULTAS[$kodeJurusan];
        $kodeNumerik  = self::KODE_JURUSAN_NUMERIK[$kodeJurusan];
        $nomorUrut    = str_pad((string) $urut, 3, '0', STR_PAD_LEFT);

        return "{$angkatanYY}{$kodeFakultas}{$kodeNumerik}{$nomorUrut}";
    }

    public function run(): void
    {
        $totalDibuat = 0;

        foreach (KelasSeeder::DAFTAR_KELAS as $rowKelas) {
            $angkatanYY = substr((string) $rowKelas['angkatan'], -2);
            $namaKelas  = "{$rowKelas['kode_jurusan']}{$angkatanYY}{$rowKelas['paralel']}";

            $kelas = Kelas::where('nama_kelas', $namaKelas)->first();
            if (! $kelas) {
                $this->command?->warn("MahasiswaSeeder: kelas {$namaKelas} tidak ditemukan, dilewati.");
                continue;
            }

            for ($urut = 1; $urut <= self::JUMLAH_PER_KELAS; $urut++) {
                $nim  = $this->buatNim($rowKelas['angkatan'], $rowKelas['kode_jurusan'], $urut);
                $nama = self::NAMA_DEPAN[($totalDibuat) % count(self::NAMA_DEPAN)]
                    . ' ' .
                    self::NAMA_BELAKANG[($totalDibuat + $urut) % count(self::NAMA_BELAKANG)];

                Mahasiswa::updateOrCreate(
                    ['nim' => $nim],
                    [
                        'kelas_id' => $kelas->id,
                        'nama'     => $nama,
                        'email'    => strtolower($nim) . '@student.kampus.ac.id',
                        'password' => Hash::make('password123'),
                        'is_locked' => false,
                    ]
                );

                $totalDibuat++;
            }
        }

        $this->command?->info("MahasiswaSeeder: {$totalDibuat} mahasiswa berhasil dibuat.");
    }
}
