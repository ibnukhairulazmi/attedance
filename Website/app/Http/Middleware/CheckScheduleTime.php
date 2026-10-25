<?php

namespace App\Http\Middleware;

use App\Models\Jadwal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckScheduleTime
{
    public function handle(Request $request, Closure $next): Response
    {
        $jadwalId = $request->route('jadwal') instanceof Jadwal
            ? $request->route('jadwal')->id
            : $request->route('jadwal');

        if ($jadwalId) {
            $jadwal = Jadwal::find($jadwalId);

            if ($jadwal && ! $jadwal->isActiveNow()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Fitur presensi hanya dapat diakses pada jam kuliah.',
                    ], 403);
                }

                return redirect()->back()->with(
                    'error',
                    "Presensi untuk {$jadwal->matakuliah->nama_mk} hanya tersedia pada "
                        . "{$jadwal->jam_mulai} – {$jadwal->jam_selesai}."
                );
            }
        }

        return $next($request);
    }
}
