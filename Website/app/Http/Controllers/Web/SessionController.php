<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\PresensiSession;
use App\Services\AttendanceService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function __construct(
        private readonly QrCodeService     $qrService,
        private readonly AttendanceService $attendanceService,
    ) {}

    public function start(Jadwal $jadwal)
    {
        if (Auth::id() !== $jadwal->dosen_id) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $todaySession = PresensiSession::where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal', today())
            ->whereIn('status', ['aktif', 'selesai'])
            ->first();

        if ($todaySession?->status === 'selesai') {
            return redirect()
                ->route('sessions.report', $todaySession)
                ->with('error', 'Sesi presensi hari ini sudah ditutup dan tidak dapat dibuka kembali.');
        }

        if ($todaySession?->status === 'aktif') {
            return redirect()
                ->route('sessions.show', $todaySession)
                ->with('info', 'Sesi presensi hari ini sedang berlangsung.');
        }

        $session = $this->qrService->createSession($jadwal);
        $this->attendanceService->initializeSessionAttendances($session);

        return redirect()
            ->route('sessions.show', $session)
            ->with('success', 'Sesi presensi dimulai!');
    }

    public function show(PresensiSession $session)
    {
        if (Auth::id() !== $session->jadwal->dosen_id) {
            abort(403);
        }

        if ($session->status === 'aktif' && $session->jadwal->isFinished()) {
            $session->update(['status' => 'selesai']);
            return redirect()
                ->route('sessions.report', $session)
                ->with('info', 'Sesi otomatis ditutup karena jam pembelajaran telah selesai.');
        }
        $session->load(['jadwal.kelas', 'presensi.mahasiswa']);

        $qrImage      = $this->qrService->generateQrImage($session->qr_token);
        $students     = $session->presensi->sortBy('mahasiswa.nama');
        $presentCount = $students->where('status', 'hadir')->count();
        $totalCount   = $students->count();

        return view('sessions.show', compact(
            'session',
            'qrImage',
            'students',
            'presentCount',
            'totalCount'
        ));
    }

    public function refresh(PresensiSession $session)
    {
        if (Auth::id() !== $session->jadwal->dosen_id) {
            abort(403);
        }

        if ($session->status !== 'aktif') {
            return back()->with('error', 'Sesi sudah tidak aktif.');
        }

        $newToken = $this->qrService->generateToken(
            $session->jadwal_id,
            $session->tanggal->toDateString()
        );

        $session->update([
            'qr_token'     => $newToken,
            'qr_expired_at' => now()->addMinutes(config('attendance.qr_refresh_minutes', 15)),
        ]);

        return back()->with('success', 'QR Code berhasil diperbarui.');
    }

    public function close(PresensiSession $session)
    {
        if (Auth::id() !== $session->jadwal->dosen_id) {
            abort(403);
        }

        $session->update(['status' => 'selesai']);

        return redirect()
            ->route('sessions.report', $session)
            ->with('success', 'Sesi presensi berhasil ditutup.');
    }

    public function overrideAttendance(PresensiSession $session, Request $request)
    {
        if (Auth::id() !== $session->jadwal->dosen_id) {
            abort(403);
        }

        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'status'       => 'required|in:hadir,izin,sakit,alpha',
        ]);

        $session->presensi()
            ->where('mahasiswa_id', $request->mahasiswa_id)
            ->update([
                'status'    => $request->status,
                'scan_time' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Status presensi diperbarui.']);
    }

    public function report(PresensiSession $session)
    {
        if (Auth::id() !== $session->jadwal->dosen_id) {
            abort(403);
        }

        $session->load(['jadwal.kelas', 'presensi.mahasiswa']);
        $summary = $session->presensi->countBy('status');

        return view('sessions.report', compact('session', 'summary'));
    }

    public function exportSession(PresensiSession $session, string $format)
    {
        if (Auth::id() !== $session->jadwal->dosen_id) {
            abort(403);
        }

        $session->load(['jadwal.kelas', 'jadwal.matakuliah', 'presensi.mahasiswa']);
        $presensi = $session->presensi->sortBy('mahasiswa.nim');

        $mataKuliah = $session->jadwal->matakuliah->nama_mk;
        $kelas      = $session->jadwal->kelas->nama_kelas;
        $tanggal    = $session->tanggal->format('d-m-Y');
        $filename   = "presensi_{$kelas}_{$tanggal}";

        if ($format === 'excel') {
            $headers = [
                'Content-Type'        => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
            ];
            $callback = function () use ($presensi, $mataKuliah, $kelas, $tanggal, $session) {
                $mataKuliahSafe = htmlspecialchars($mataKuliah);
                $kelasSafe      = htmlspecialchars($kelas);
                $tanggalSafe    = htmlspecialchars($tanggal);

                echo chr(0xEF) . chr(0xBB) . chr(0xBF);
                echo "<table border='1'>";
                echo "<tr><th>Mata Kuliah</th><th>Kelas</th><th>Tanggal</th><th>Jam</th></tr>";
                echo "<tr><td>{$mataKuliahSafe}</td><td>{$kelasSafe}</td><td>{$tanggalSafe}</td><td>"
                    . substr($session->jadwal->jam_mulai, 0, 5) . ' - ' . substr($session->jadwal->jam_selesai, 0, 5)
                    . "</td></tr>";
                echo "<tr></tr>";
                echo "<tr><th>No</th><th>NIM</th><th>Nama Mahasiswa</th><th>Status</th><th>Waktu Scan</th><th>Jarak (m)</th></tr>";
                foreach ($presensi as $i => $att) {
                    $status    = htmlspecialchars(ucfirst($att->status));
                    $nama      = htmlspecialchars($att->mahasiswa->nama);
                    $nim       = htmlspecialchars($att->mahasiswa->nim);
                    $scanTime  = $att->scan_time ? $att->scan_time->format('H:i:s') : '-';
                    $jarak     = $att->jarak_meter ?? '-';
                    echo "<tr><td>" . ($i + 1) . "</td><td>{$nim}</td><td>{$nama}</td><td>{$status}</td><td>{$scanTime}</td><td>{$jarak}</td></tr>";
                }
                echo "</table>";
            };
            return response()->stream($callback, 200, $headers);
        }

        if ($format === 'pdf') {
            $summary = $session->presensi->countBy('status');

            return \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'exports.session-pdf',
                compact('session', 'presensi', 'summary')
            )->download("{$filename}.pdf");
        }

        abort(404);
    }
}
