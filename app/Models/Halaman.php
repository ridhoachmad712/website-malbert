<?php

namespace App\Models;

use App\Models\Concerns\CatatAktivitas;
use App\Models\Concerns\HapusBerkas;
use App\Models\Concerns\PunyaSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Halaman berisi teks tetap, misalnya Sejarah, Visi dan Misi, Struktur Organisasi, Kebijakan Privasi. */
class Halaman extends Model
{
    use CatatAktivitas, HapusBerkas, PunyaSlug;

    protected $table = 'halaman';

    protected $fillable = ['judul', 'slug', 'grup', 'ringkasan', 'isi', 'gambar', 'urutan', 'terbit'];

    protected array $kolomBerkas = ['gambar'];

    public const GRUP = [
        'profil' => 'Profil sekolah (tampil di menu Profil)',
        'lainnya' => 'Lainnya (hanya lewat tautan, misalnya Kebijakan Privasi)',
    ];

    protected function casts(): array
    {
        return ['terbit' => 'boolean'];
    }

    public function scopeTerbit(Builder $query): void
    {
        $query->where('terbit', true);
    }

    public function url(): string
    {
        return route('halaman', $this->slug);
    }
}
