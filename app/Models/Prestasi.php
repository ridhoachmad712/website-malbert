<?php

namespace App\Models;

use App\Casts\Tanggal;
use App\Models\Concerns\CatatAktivitas;
use App\Models\Concerns\HapusBerkas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    use CatatAktivitas, HapusBerkas;

    protected $table = 'prestasi';

    protected $fillable = [
        'judul', 'peraih', 'jenis_peraih', 'peringkat', 'tingkat', 'penyelenggara', 'tanggal', 'keterangan', 'foto', 'terbit', 'foto_diperiksa',
    ];

    protected array $kolomBerkas = ['foto'];

    public const JENIS_PERAIH = [
        'murid' => 'Murid',
        'guru' => 'Guru',
        'sekolah' => 'Sekolah',
    ];

    public const TINGKAT = [
        'sekolah' => 'Sekolah',
        'kecamatan' => 'Kecamatan',
        'kota' => 'Kota',
        'provinsi' => 'Provinsi',
        'nasional' => 'Nasional',
        'internasional' => 'Internasional',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => Tanggal::class,
            'terbit' => 'boolean',
            'foto_diperiksa' => 'boolean',
        ];
    }

    public function scopeTerbit(Builder $query): void
    {
        $query->where('terbit', true);
    }

    public function namaTingkat(): string
    {
        return self::TINGKAT[$this->tingkat] ?? $this->tingkat;
    }
}
