<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BobotPenilaian;
use App\Models\Jadwal;
use App\Models\NilaiMahasiswa;
use App\Models\Presensi;
use App\Models\PresensiSession;
use Illuminate\Http\Request;

class GradebookController extends Controller
{
    /**
     * Tampilkan buku nilai / rekap nilai kelas
     */
    public function index(Request $request)
    {
        $dosen = $request->user();

        $jadwals = Jadwal::with(['kelas', 'matakuliah'])
            ->where('dosen_id', $dosen->id)
            ->get();

        $selectedJadwalId = $request->get('jadwal_id', $jadwals->first()?->id);

        $selectedJadwal = null;
        $gradeData = [];
        $bobot = null;

        if ($selectedJadwalId) {
            $selectedJadwal = Jadwal::with(['kelas.mahasiswa', 'matakuliah', 'tugas.pengumpulan', 'bobotPenilaian', 'nilaiMahasiswa'])
                ->where('id', $selectedJadwalId)
                ->where('dosen_id', $dosen->id)
                ->first();

            if ($selectedJadwal) {
                // Ambil atau inisialisasi bobot standar (10%, 20%, 30%, 40%)
                $bobot = $selectedJadwal->bobotPenilaian ?? BobotPenilaian::firstOrCreate(
                    ['jadwal_id' => $selectedJadwal->id],
                    [
                        'bobot_presensi' => 10,
                        'bobot_tugas'    => 20,
                        'bobot_uts'      => 30,
                        'bobot_uas'      => 40,
                    ]
                );

                // Hitung total sesi presensi yang sudah selesai
                $totalSesiPresensi = PresensiSession::where('jadwal_id', $selectedJadwal->id)
                    ->whereIn('status', ['selesai', 'aktif'])
                    ->count();

                $nilaiMahasiswaMap = $selectedJadwal->nilaiMahasiswa->keyBy('mahasiswa_id');
                $tugasList = $selectedJadwal->tugas;
                $mahasiswaList = $selectedJadwal->kelas->mahasiswa ?? collect();

                foreach ($mahasiswaList as $mhs) {
                    // 1. Hitung Nilai Presensi (0 - 100)
                    // Menggunakan relasi 'sesi' sesuai Model Presensi
                    $hadirCount = Presensi::where('mahasiswa_id', $mhs->id)
                        ->whereHas('sesi', fn($q) => $q->where('jadwal_id', $selectedJadwal->id))
                        ->where('status', 'hadir')
                        ->count();

                    // Jika belum pernah ada sesi presensi sama sekali, nilai presensi 100 (netral/penuh)
                    // Jika sudah ada sesi, dihitung persentase kehadiran
                    $nilaiPresensi = $totalSesiPresensi > 0
                        ? round(($hadirCount / $totalSesiPresensi) * 100, 2)
                        : 100;

                    // 2. Hitung Rata-rata Nilai Tugas
                    $totalNilaiTugas = 0;
                    $tugasDinilaiCount = 0;
                    foreach ($tugasList as $tgs) {
                        $pengumpulan = $tgs->pengumpulan->firstWhere('mahasiswa_id', $mhs->id);
                        if ($pengumpulan && !is_null($pengumpulan->nilai)) {
                            // Konversi ke skala 100 jika max_score berbeda
                            $normalizedScore = ($pengumpulan->nilai / $tgs->max_score) * 100;
                            $totalNilaiTugas += $normalizedScore;
                            $tugasDinilaiCount++;
                        }
                    }
                    $nilaiTugas = $tugasDinilaiCount > 0 ? round($totalNilaiTugas / $tugasDinilaiCount, 2) : 0;

                    // 3. Nilai UTS & UAS dari input dosen
                    $recordNilai = $nilaiMahasiswaMap->get($mhs->id);
                    $nilaiUts = $recordNilai?->nilai_uts;
                    $nilaiUas = $recordNilai?->nilai_uas;

                    // 4. Hitung Nilai Akhir Angka
                    $utsVal = $nilaiUts ?? 0;
                    $uasVal = $nilaiUas ?? 0;

                    $totalBobot = ($bobot->bobot_presensi + $bobot->bobot_tugas + $bobot->bobot_uts + $bobot->bobot_uas);
                    $totalBobot = $totalBobot > 0 ? $totalBobot : 100;

                    $nilaiAkhir = (
                        ($nilaiPresensi * $bobot->bobot_presensi) +
                        ($nilaiTugas * $bobot->bobot_tugas) +
                        ($utsVal * $bobot->bobot_uts) +
                        ($uasVal * $bobot->bobot_uas)
                    ) / $totalBobot;

                    $nilaiAkhir = round($nilaiAkhir, 2);

                    // 5. Konversi Nilai Huruf
                    $nilaiHuruf = $this->convertToGradeLetter($nilaiAkhir);

                    $gradeData[] = [
                        'mahasiswa'       => $mhs,
                        'hadir_count'     => $hadirCount,
                        'total_sesi'      => $totalSesiPresensi,
                        'nilai_presensi'  => $nilaiPresensi,
                        'nilai_tugas'     => $nilaiTugas,
                        'tugas_selesai'   => $tugasDinilaiCount,
                        'total_tugas'     => $tugasList->count(),
                        'nilai_uts'       => $nilaiUts,
                        'nilai_uas'       => $nilaiUas,
                        'nilai_akhir'     => $nilaiAkhir,
                        'nilai_huruf'     => $nilaiHuruf,
                    ];
                }
            }
        }

        return view('gradebook.index', compact('jadwals', 'selectedJadwal', 'gradeData', 'bobot', 'selectedJadwalId'));
    }

    /**
     * Update Bobot Penilaian
     */
    public function updateWeights(Request $request, Jadwal $jadwal)
    {
        $dosen = $request->user();
        if ($jadwal->dosen_id !== $dosen->id) {
            abort(403);
        }

        $validated = $request->validate([
            'bobot_presensi' => 'required|integer|min:0|max:100',
            'bobot_tugas'    => 'required|integer|min:0|max:100',
            'bobot_uts'      => 'required|integer|min:0|max:100',
            'bobot_uas'      => 'required|integer|min:0|max:100',
        ]);

        $sum = array_sum($validated);
        if ($sum !== 100) {
            return back()->with('error', "Total bobot harus tepat 100%! (Total saat ini: {$sum}%)");
        }

        BobotPenilaian::updateOrCreate(
            ['jadwal_id' => $jadwal->id],
            $validated
        );

        return back()->with('success', 'Bobot penilaian berhasil diperbarui!');
    }

    /**
     * Simpan / Update Nilai UTS & UAS
     */
    public function updateScores(Request $request, Jadwal $jadwal)
    {
        $dosen = $request->user();
        if ($jadwal->dosen_id !== $dosen->id) {
            abort(403);
        }

        $scores = $request->input('scores', []);

        foreach ($scores as $mahasiswaId => $vals) {
            $uts = isset($vals['uts']) && $vals['uts'] !== '' ? (float) $vals['uts'] : null;
            $uas = isset($vals['uas']) && $vals['uas'] !== '' ? (float) $vals['uas'] : null;

            NilaiMahasiswa::updateOrCreate(
                [
                    'jadwal_id'    => $jadwal->id,
                    'mahasiswa_id' => $mahasiswaId,
                ],
                [
                    'nilai_uts' => $uts,
                    'nilai_uas' => $uas,
                ]
            );
        }

        return back()->with('success', 'Nilai UTS & UAS mahasiswa berhasil disimpan!');
    }

    /**
     * Konversi nilai angka ke huruf standar perguruan tinggi
     */
    private function convertToGradeLetter(float $score): string
    {
        return match (true) {
            $score >= 85 => 'A',
            $score >= 80 => 'A-',
            $score >= 75 => 'B+',
            $score >= 70 => 'B',
            $score >= 65 => 'B-',
            $score >= 60 => 'C+',
            $score >= 55 => 'C',
            $score >= 40 => 'D',
            default      => 'E',
        };
    }
}
