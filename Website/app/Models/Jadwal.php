<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';

    protected $fillable = [
        'kelas_id',
        'dosen_id',
        'mata_kuliah_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function matakuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class, 'jadwal_id');
    }

    public function bobotPenilaian(): HasOne
    {
        return $this->hasOne(BobotPenilaian::class, 'jadwal_id');
    }

    public function nilaiMahasiswa(): HasMany
    {
        return $this->hasMany(NilaiMahasiswa::class, 'jadwal_id');
    }

    public function activeSession(): HasOne
    {
        return $this->hasOne(PresensiSession::class, 'jadwal_id')
            ->where('tanggal', today())
            ->where('status', 'aktif');
    }

    public function isActiveNow(): bool
    {
        $day = $this->getDayInIndonesian();

        $now = now();

        $start = now()->setTimeFromTimeString($this->jam_mulai);
        $end   = now()->setTimeFromTimeString($this->jam_selesai);

        return $this->hari === $day
            && $now->between($start, $end);
    }

    private function getDayInIndonesian(): string
    {
        return match (now()->format('l')) {
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            default     => '',
        };
    }

    public function todaySession(): HasOne
    {
        return $this->hasOne(PresensiSession::class, 'jadwal_id')
            ->whereDate('tanggal', today())
            ->whereIn('status', ['aktif', 'selesai'])
            ->latest();
    }

    public function isFinished(): bool
    {
        $day = $this->getDayInIndonesian();
        $end = now('Asia/Jakarta')->setTimeFromTimeString($this->jam_selesai);

        return $this->hari === $day && now('Asia/Jakarta')->isAfter($end);
    }
}
