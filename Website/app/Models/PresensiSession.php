<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiSession extends Model
{
    use HasFactory;

    protected $table = 'sesi_presensi';

    protected $fillable = [
        'jadwal_id',
        'tanggal',
        'qr_token',
        'qr_expired_at',
        'kelas_latitude',
        'kelas_longitude',
        'radius_meter',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'qr_expired_at'   => 'datetime',
            'tanggal'         => 'date',
            'kelas_latitude'  => 'float',
            'kelas_longitude' => 'float',
        ];
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'sesi_presensi_id');
    }

    public function isExpired(): bool
    {
        return now()->isAfter($this->qr_expired_at)
            || $this->status !== 'aktif'
            || $this->jadwal->isFinished();
    }

    public function presentCount(): int
    {
        return $this->presensi()->where('status', 'hadir')->count();
    }

    public function totalStudents(): int
    {
        return $this->jadwal->kelas->mahasiswa()->count();
    }
}
