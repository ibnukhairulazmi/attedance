<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PresensiSession;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        PresensiSession::where('status', 'aktif')
            ->whereHas('jadwal', fn($q) => $q->where('dosen_id', $user->id))
            ->whereDate('tanggal', '<', today())
            ->update(['status' => 'selesai']);

        PresensiSession::where('status', 'aktif')
            ->whereHas('jadwal', function ($q) use ($user) {
                $q->where('dosen_id', $user->id)
                    ->whereTime('jam_selesai', '<', now()->toTimeString());
            })
            ->whereDate('tanggal', today())
            ->update(['status' => 'selesai']);
        $schedules = $user->todaySchedules();

        $stats = [
            'total_schedules_today' => $schedules->count(),
            'active_sessions'       => $schedules->filter(
                fn($s) => $s->activeSession !== null
            )->count(),
        ];

        return view('dashboard', compact('schedules', 'stats'));
    }
}
