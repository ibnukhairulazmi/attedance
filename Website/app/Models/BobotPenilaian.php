<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BobotPenilaian extends Model
{
    use HasFactory;

    protected $table = 'bobot_penilaian';

    protected $fillable = [
        'jadwal_id',
        'bobot_presensi',
        'bobot_tugas',
        'bobot_uts',
        'bobot_uas',
    ];

    protected function casts(): array
    {
        return [
            'bobot_presensi' => 'integer',
            'bobot_tugas'    => 'integer',
            'bobot_uts'      => 'integer',
            'bobot_uas'      => 'integer',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }
}
