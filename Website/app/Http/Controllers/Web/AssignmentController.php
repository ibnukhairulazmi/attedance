<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Tugas;
use App\Models\TugasPengumpulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Tampilkan daftar tugas untuk dosen yang sedang login
     */
    public function index(Request $request)
    {
        $dosen = $request->user();

        // Dapatkan semua jadwal yang diampu dosen ini
        $jadwals = Jadwal::with(['kelas', 'matakuliah'])
            ->where('dosen_id', $dosen->id)
            ->get();

        $selectedJadwalId = $request->get('jadwal_id');

        $assignmentsQuery = Tugas::with(['jadwal.kelas', 'jadwal.matakuliah'])
            ->withCount('pengumpulan')
            ->whereHas('jadwal', fn($q) => $q->where('dosen_id', $dosen->id));

        if ($selectedJadwalId) {
            $assignmentsQuery->where('jadwal_id', $selectedJadwalId);
        }

        $assignments = $assignmentsQuery->latest()->get();

        return view('assignments.index', compact('jadwals', 'assignments', 'selectedJadwalId'));
    }

    /**
     * Form buat tugas baru
     */
    public function create(Request $request)
    {
        $dosen = $request->user();
        $jadwals = Jadwal::with(['kelas', 'matakuliah'])
            ->where('dosen_id', $dosen->id)
            ->get();

        $defaultJadwalId = $request->get('jadwal_id');

        return view('assignments.create', compact('jadwals', 'defaultJadwalId'));
    }

    /**
     * Simpan tugas baru
     */
    public function store(Request $request)
    {
        $dosen = $request->user();

        $validated = $request->validate([
            'jadwal_id'        => 'required|exists:jadwal,id',
            'judul'            => 'required|string|max:255',
            'deskripsi'        => 'nullable|string',
            'deadline'         => 'required|date',
            'max_score'        => 'required|integer|min:1|max:100',
            'tipe_pengumpulan' => 'required|in:file,link,keduanya',
            'file_petunjuk'    => 'nullable|file|mimes:pdf,doc,docx,zip,rar,ppt,pptx,xls,xlsx|max:10240', // max 10MB
        ]);

        // Verifikasi kepemilikan jadwal
        $jadwal = Jadwal::where('id', $validated['jadwal_id'])
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        $filePath = null;
        if ($request->hasFile('file_petunjuk')) {
            $filePath = $request->file('file_petunjuk')->store('assignments/guidelines', 'public');
        }

        Tugas::create([
            'jadwal_id'        => $jadwal->id,
            'judul'            => $validated['judul'],
            'deskripsi'        => $validated['deskripsi'] ?? null,
            'deadline'         => $validated['deadline'],
            'max_score'        => $validated['max_score'],
            'tipe_pengumpulan' => $validated['tipe_pengumpulan'],
            'file_petunjuk'    => $filePath,
            'is_active'        => true,
        ]);

        return redirect()->route('assignments.index', ['jadwal_id' => $jadwal->id])
            ->with('success', 'Tugas berhasil dibuat dan dipublikasikan ke mahasiswa!');
    }

    /**
     * Detail tugas & daftar pengumpulan mahasiswa
     */
    public function show(Request $request, Tugas $assignment)
    {
        $dosen = $request->user();

        // Pastikan dosen yang login adalah pengampu jadwal ini
        if ($assignment->jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $assignment->load(['jadwal.kelas.mahasiswa', 'jadwal.matakuliah', 'pengumpulan.mahasiswa']);

        // Data mahasiswa di kelas
        $allMahasiswa = $assignment->jadwal->kelas->mahasiswa ?? collect();
        $submissionsByMhs = $assignment->pengumpulan->keyBy('mahasiswa_id');

        return view('assignments.show', compact('assignment', 'allMahasiswa', 'submissionsByMhs'));
    }

    /**
     * Input / update nilai dan feedback tugas mahasiswa
     */
    public function grade(Request $request, TugasPengumpulan $submission)
    {
        $dosen = $request->user();

        // Validasi kepemilikan tugas
        if ($submission->tugas->jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'nilai'         => 'required|numeric|min:0|max:' . $submission->tugas->max_score,
            'catatan_dosen' => 'nullable|string',
        ]);

        $submission->update([
            'nilai'         => $validated['nilai'],
            'catatan_dosen' => $validated['catatan_dosen'] ?? null,
        ]);

        return back()->with('success', 'Nilai dan catatan berhasil disimpan!');
    }

    /**
     * Hapus tugas
     */
    public function destroy(Request $request, Tugas $assignment)
    {
        $dosen = $request->user();

        if ($assignment->jadwal->dosen_id !== $dosen->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        // Hapus file petunjuk jika ada
        if ($assignment->file_petunjuk) {
            Storage::disk('public')->delete($assignment->file_petunjuk);
        }

        $assignment->delete();

        return redirect()->route('assignments.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }
}
