<?php

namespace App\Models;

use App\Models\Concerns\CatatAktivitas;
use App\Models\Concerns\HapusBerkas;
use App\Models\Concerns\PunyaSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ekstrakurikuler extends Model
{
    use CatatAktivitas, HapusBerkas, PunyaSlug;

    protected $table = 'ekstrakurikuler';

    protected static string $sumberSlug = 'nama';

    protected $fillable = [
        'nama', 'slug', 'ringkasan', 'deskripsi', 'jadwal', 'tempat', 'pembina', 'peserta',
        'foto', 'album_id', 'urutan', 'tampil', 'foto_diperiksa',
    ];

    protected array $kolomBerkas = ['foto'];

    protected function casts(): array
    {
        return [
            'tampil' => 'boolean',
            'foto_diperiksa' => 'boolean',
        ];
    }

    public function scopeTampil(Builder $query): void
    {
        $query->where('tampil', true)->orderBy('urutan')->orderBy('nama');
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function url(): string
    {
        return route('ekskul.detail', $this->slug);
    }
}
