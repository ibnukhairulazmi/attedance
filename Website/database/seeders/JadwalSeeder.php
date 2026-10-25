<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataKuliah;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    /**
     * Aturan durasi sesuai ketentuan:
     *   - 2 SKS  -> 1 pertemuan teori,  100 menit
     *   - 3 SKS  -> 1 pertemuan teori (100 menit) + 1 pertemuan
     *               praktikum (100 menit), di hari yang berbeda.
     *
     * Catatan: tabel `jadwal` tidak punya kolom jenis pertemuan
     * (teori/praktikum) — mata kuliah 3 SKS karena itu direpresentasikan
     * sebagai DUA baris jadwal yang menunjuk ke `mata_kuliah_id` yang sama,
     * masing-masing 100 menit. Jika perlu label "Teori"/"Praktikum" yang
     * eksplisit di tampilan, tambahkan kolom `jenis` pada migration
     * `jadwal` terlebih dahulu.
     */
    private const DURASI_MENIT = 100;

    /** Lima hari kerja perkuliahan, dipakai bergantian tanpa tabrakan. */
    private const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

    /**
     * Dua "jalur jam" berbeda. Kelas paralel ke-2 pada jurusan yang sama
     * dijadwalkan di jalur jam yang berbeda dari kelas paralel ke-1, supaya
     * dosen yang sama (lihat DosenSeeder, 2 dosen/jurusan) tidak pernah
     * mengajar dua kelas pada hari & jam yang persis sama.
     */
    private const JALUR_JAM = ['08:00', '11:00'];

    private function tambahMenit(string $jamMulai, int $menit): string
    {
        return Carbon::createFromFormat('H:i', $jamMulai)
            ->addMinutes($menit)
            ->format('H:i');
    }

    public function run(): void
    {
        // Hitung index kelas ke berapa (0/1/2..) di dalam jurusan yang sama,
        // supaya bisa dipetakan ke JALUR_JAM & pasangan dosen jurusan itu.
        $urutanDalamJurusan = [];
        $totalDibuat        = 0;

        foreach (KelasSeeder::DAFTAR_KELAS as $rowKelas) {
            $kodeJurusan = $rowKelas['kode_jurusan'];
            $indexKelas  = $urutanDalamJurusan[$kodeJurusan] ?? 0;
            $urutanDalamJurusan[$kodeJurusan] = $indexKelas + 1;

            $angkatanYY = substr((string) $rowKelas['angkatan'], -2);
            $namaKelas  = "{$kodeJurusan}{$angkatanYY}{$rowKelas['paralel']}";
            $kelas      = Kelas::where('nama_kelas', $namaKelas)->first();

            if (! $kelas) {
                $this->command?->warn("JadwalSeeder: kelas {$namaKelas} tidak ditemukan, dilewati.");
                continue;
            }

            // Ambil 2 dosen milik jurusan ini (urutan sesuai DosenSeeder::DAFTAR_DOSEN).
            $dosenJurusan = [];
            foreach (DosenSeeder::DAFTAR_DOSEN as $urutGlobal => $rowDosen) {
                if ($rowDosen['kode_jurusan'] === $kodeJurusan) {
                    $nidn = DosenSeeder::buatNidn($rowDosen['lahir'], $urutGlobal + 1);
                    $dosenJurusan[] = Dosen::where('nidn', $nidn)->first();
                }
            }
            $dosenJurusan = array_values(array_filter($dosenJurusan));

            if (empty($dosenJurusan)) {
                $this->command?->warn("JadwalSeeder: dosen jurusan {$kodeJurusan} tidak ditemukan, dilewati.");
                continue;
            }

            // Jalur jam berbeda untuk tiap kelas paralel dalam jurusan yang sama.
            $jamAwal = self::JALUR_JAM[$indexKelas % count(self::JALUR_JAM)];

            $daftarMk = MataKuliahSeeder::DAFTAR_MATA_KULIAH[$kodeJurusan] ?? [];
            $hariIdx  = 0;

            foreach ($daftarMk as $mkIndex => $mk) {
                $mataKuliah = MataKuliah::where('kode_mk', $mk['kode_mk'])->first();
                if (! $mataKuliah) {
                    continue;
                }

                // Dosen dirotasi per mata kuliah supaya kedua dosen jurusan
                // sama-sama kebagian mengajar di kelas ini.
                $dosen = $dosenJurusan[$mkIndex % count($dosenJurusan)];

                $jamMulai   = $jamAwal;
                $jamSelesai = $this->tambahMenit($jamMulai, self::DURASI_MENIT);

                // Pertemuan teori (berlaku untuk 2 SKS maupun 3 SKS).
                Jadwal::updateOrCreate(
                    [
                        'kelas_id'       => $kelas->id,
                        'mata_kuliah_id' => $mataKuliah->id,
                        'hari'           => self::HARI[$hariIdx % count(self::HARI)],
                    ],
                    [
                        'dosen_id'    => $dosen->id,
                        'jam_mulai'   => $jamMulai,
                        'jam_selesai' => $jamSelesai,
                    ]
                );
                $hariIdx++;
                $totalDibuat++;

                // 3 SKS -> tambahan pertemuan praktikum di hari berikutnya,
                // durasi 100 menit, jam & dosen tetap sama.
                if ($mk['sks'] >= 3) {
                    Jadwal::updateOrCreate(
                        [
                            'kelas_id'       => $kelas->id,
                            'mata_kuliah_id' => $mataKuliah->id,
                            'hari'           => self::HARI[$hariIdx % count(self::HARI)],
                        ],
                        [
                            'dosen_id'    => $dosen->id,
                            'jam_mulai'   => $jamMulai,
                            'jam_selesai' => $jamSelesai,
                        ]
                    );
                    $hariIdx++;
                    $totalDibuat++;
                }
            }
        }

        $this->command?->info("JadwalSeeder: {$totalDibuat} baris jadwal berhasil dibuat.");
    }
}
