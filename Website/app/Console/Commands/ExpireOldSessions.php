<?php

namespace App\Console\Commands;

use App\Models\PresensiSession;
use Illuminate\Console\Command;

class ExpireOldSessions extends Command
{
    protected $signature   = 'attendance:expire-sessions';
    protected $description = 'Expire semua sesi QR yang sudah melewati jam berakhir.';

    public function handle(): int
    {
        $expired = PresensiSession::where('status', 'aktif')
            ->where('qr_expired_at', '<', now())
            ->update(['status' => 'expired']);

        $this->info("Expired {$expired} sesi presensi.");
        return self::SUCCESS;
    }
}
