<?php

use Illuminate\Support\Facades\Schedule;

// Expire sesi QR setiap menit
Schedule::command('attendance:expire-sessions')->everyMinute();

// Rekap bulanan: jalankan tanggal 1 setiap bulan jam 01:00
Schedule::command('attendance:monthly-report')->monthlyOn(1, '01:00');
