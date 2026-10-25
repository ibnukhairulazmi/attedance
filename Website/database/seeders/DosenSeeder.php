<?php

namespace Database\Seeders;

use App\Models\Dosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    /**
     * Format NIDN (Nomor Induk Dosen Nasional) standar Indonesia, 10 digit:
     *   digit 1     : kode kewarganegaraan (0 = WNI)
     *   digit 2-3   : tanggal lahir (DD)
     *   digit 4-5   : bulan lahir (MM)
     *   digit 6-7   : dua digit terakhir tahun lahir (YY)
     *   digit 8-10  : nomor urut registrasi (3 digit)
     *
     * Contoh: dosen lahir 12 April 1985, urut ke-7 -> 0 12 04 85 007
     *         => "0120485007"
     *
     * Dibuat public static supaya JadwalSeeder bisa menghitung ulang NIDN
     * yang sama persis untuk mencari record dosen yang bersangkutan,
     * tanpa perlu menyimpan kolom jurusan di tabel `dosen`.
     */
    public static function buatNidn(string $tanggalLahir, int $urut): string
    {
        $tgl = \Carbon\Carbon::parse($tanggalLahir);

        return '0'
            . $tgl->format('d')
            . $tgl->format('m')
            . $tgl->format('y')
            . str_pad((string) $urut, 3, '0', STR_PAD_LEFT);
    }
    public const DAFTAR_DOSEN = [
        // Informatika (IF)
        ['kode_jurusan' => 'IF', 'nama' => 'Dr. Ahmad Fauzan, M.Kom.',        'lahir' => '1980-03-12'],
        ['kode_jurusan' => 'IF', 'nama' => 'Rina Wijayanti, M.T.',            'lahir' => '1988-07-25'],

        // Sistem Informasi (SI)
        ['kode_jurusan' => 'SI', 'nama' => 'Dr. Bambang Kurniawan, M.Kom.',   'lahir' => '1979-11-02'],
        ['kode_jurusan' => 'SI', 'nama' => 'Dewi Anggraini, M.Kom.',          'lahir' => '1990-01-17'],

        // Teknologi Informasi (TI)
        ['kode_jurusan' => 'TI', 'nama' => 'Hendra Saputra, M.T.',            'lahir' => '1985-04-30'],
        ['kode_jurusan' => 'TI', 'nama' => 'Yulia Ramadhani, M.Kom.',         'lahir' => '1991-09-08'],

        // Bisnis Digital (BD)
        ['kode_jurusan' => 'BD', 'nama' => 'Dr. Fitriani Anwar, M.M.',        'lahir' => '1982-06-19'],
        ['kode_jurusan' => 'BD', 'nama' => 'Muhammad Rizky Pratama, M.M.',    'lahir' => '1989-12-05'],

        // Ilmu Komunikasi (IK)
        ['kode_jurusan' => 'IK', 'nama' => 'Dr. Siti Nurhaliza, M.I.Kom.',    'lahir' => '1983-02-14'],
        ['kode_jurusan' => 'IK', 'nama' => 'Taufik Hidayat, M.I.Kom.',        'lahir' => '1987-10-22'],
    ];

    public function run(): void
    {
        foreach (self::DAFTAR_DOSEN as $urut => $row) {
            $emailSlug = \Illuminate\Support\Str::slug(
                preg_replace('/\b(Dr\.|M\.Kom\.|M\.T\.|M\.M\.|M\.I\.Kom\.)\b/', '', $row['nama'])
            );

            Dosen::updateOrCreate(
                ['nidn' => self::buatNidn($row['lahir'], $urut + 1)],
                [
                    'nama'      => $row['nama'],
                    'email'     => "{$emailSlug}@dosen.kampus.ac.id",
                    'password'  => Hash::make('password123'),
                    'is_active' => true,
                ]
            );
        }

        $this->command?->info('DosenSeeder: ' . count(self::DAFTAR_DOSEN) . ' dosen berhasil dibuat.');
    }
}
