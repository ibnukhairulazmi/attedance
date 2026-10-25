<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Dosen extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'dosen';

    protected $fillable = [
        'nidn',
        'nama',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'dosen_id');
    }

    public function todaySchedules()
    {
        $dayMap = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];
        $day = $dayMap[now()->format('l')] ?? '';

        return $this->jadwal()
            ->with([
                'kelas',
                'matakuliah',
                'activeSession',
                'todaySession' => fn($q) => $q
                    ->whereDate('tanggal', today())
                    ->whereIn('status', ['aktif', 'selesai']),
            ])
            ->where('hari', $day)
            ->get();
    }
}
