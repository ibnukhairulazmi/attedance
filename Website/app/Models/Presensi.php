<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'sesi_presensi_id',
        'mahasiswa_id',
        'status',
        'scan_latitude',
        'scan_longitude',
        'jarak_meter',
        'scan_time',
        'ip_address',
        'device_id',
    ];

    protected function casts(): array
    {
        return [
            'scan_time'      => 'datetime',
            'scan_latitude'  => 'float',
            'scan_longitude' => 'float',
        ];
    }

    public function sesi()
    {
        return $this->belongsTo(
            PresensiSession::class,
            'sesi_presensi_id'
        );
    }

    public function mahasiswa()
    {
        return $this->belongsTo(
            Mahasiswa::class,
            'mahasiswa_id'
        );
    }
}
