<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $jadwalId = $request->filled('jadwal_id')
            ? (int) $request->jadwal_id
            : null;

        $jadwals = Jadwal::with([
            'kelas',
            'matakuliah'
        ])
            ->where('dosen_id', Auth::id())
            ->orderBy('kelas_id')
            ->get();

        $report = [];

        if ($jadwalId) {

            $records = Presensi::with('mahasiswa')
                ->whereHas('sesi', function ($q) use ($jadwalId) {
                    $q->where('jadwal_id', $jadwalId);
                })
                ->get();

            $report = $records
                ->groupBy('mahasiswa_id')
                ->map(function ($rows) {

                    $counts = $rows->countBy('status');

                    $hadir = $counts->get('hadir', 0);
                    $izin  = $counts->get('izin', 0);
                    $sakit = $counts->get('sakit', 0);
                    $alpha = $counts->get('alpha', 0);

                    $total = $rows->count();

                    return [
                        'mahasiswa' => $rows->first()->mahasiswa,
                        'hadir'     => $hadir,
                        'izin'      => $izin,
                        'sakit'     => $sakit,
                        'alpha'     => $alpha,
                        'total'     => $total,
                        'persen'    => $total > 0
                            ? round(($hadir / $total) * 100, 1)
                            : 0,
                    ];
                })
                ->values()
                ->toArray();
        }

        return view('reports.reports-index', compact(
            'jadwals',
            'jadwalId',
            'report'
        ));
    }

    public function export(Request $request, string $format)
    {
        $jadwalId = $request->filled('jadwal_id') ? (int) $request->jadwal_id : null;

        if (! $jadwalId) {
            return redirect()->route('reports.index')->with('error', 'Pilih kelas terlebih dahulu.');
        }

        $jadwal = Jadwal::with(['kelas', 'matakuliah'])->findOrFail($jadwalId);

        if ($jadwal->dosen_id !== Auth::id()) {
            abort(403);
        }

        $records = Presensi::with('mahasiswa')
            ->whereHas('sesi', fn($q) => $q->where('jadwal_id', $jadwalId))
            ->get();

        $report = $records
            ->groupBy('mahasiswa_id')
            ->map(function ($rows) {
                $counts = $rows->countBy('status');
                $hadir  = $counts->get('hadir', 0);
                $total  = $rows->count();
                return [
                    'mahasiswa' => $rows->first()->mahasiswa,
                    'hadir'     => $hadir,
                    'izin'      => $counts->get('izin', 0),
                    'sakit'     => $counts->get('sakit', 0),
                    'alpha'     => $counts->get('alpha', 0),
                    'total'     => $total,
                    'persen'    => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
                ];
            })
            ->values();

        $mataKuliah = $jadwal->matakuliah->nama_mk;
        $kelas      = $jadwal->kelas->nama_kelas;
        $filename   = 'laporan_' . str_replace(' ', '_', $kelas);

        if ($format === 'excel') {
            $headers = [
                'Content-Type'        => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
            ];
            $callback = function () use ($report, $mataKuliah, $kelas) {
                $mataKuliahSafe = htmlspecialchars($mataKuliah);
                $kelasSafe      = htmlspecialchars($kelas);

                echo chr(0xEF) . chr(0xBB) . chr(0xBF);
                echo "<table border='1'>";
                echo "<tr><th>Mata Kuliah</th><td>{$mataKuliahSafe}</td></tr>";
                echo "<tr><th>Kelas</th><td>{$kelasSafe}</td></tr>";
                echo "<tr></tr>";
                echo "<tr><th>No</th><th>NIM</th><th>Nama Mahasiswa</th><th>Hadir</th><th>Sakit</th><th>Izin</th><th>Alpha</th><th>Total</th><th>Kehadiran (%)</th></tr>";
                foreach ($report as $i => $row) {
                    $nama  = htmlspecialchars($row['mahasiswa']->nama);
                    $nim   = htmlspecialchars($row['mahasiswa']->nim);
                    echo "<tr>
                        <td>" . ($i + 1) . "</td>
                        <td>{$nim}</td>
                        <td>{$nama}</td>
                        <td>{$row['hadir']}</td>
                        <td>{$row['sakit']}</td>
                        <td>{$row['izin']}</td>
                        <td>{$row['alpha']}</td>
                        <td>{$row['total']}</td>
                        <td>{$row['persen']}%</td>
                    </tr>";
                }
                echo "</table>";
            };
            return response()->stream($callback, 200, $headers);
        }

        if ($format === 'pdf') {
            // Sebelumnya mengirim HTML mentah dengan nama file .pdf.
            // Sekarang benar-benar di-render menjadi PDF via DomPDF.
            return \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'exports.report-pdf',
                compact('report', 'jadwal', 'mataKuliah', 'kelas')
            )->download("{$filename}.pdf");
        }

        abort(404);
    }
}
