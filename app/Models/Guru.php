<?php

namespace App\Models;

use App\Models\Concerns\CatatAktivitas;
use App\Models\Concerns\HapusBerkas;
use App\Models\Concerns\PunyaSlug;
use App\Support\Teks;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Guru dan tenaga kependidikan yang ditampilkan di halaman profil. Bukan akun pengguna. */
class Guru extends Model
{
    use CatatAktivitas, HapusBerkas, PunyaSlug;

    protected $table = 'guru';

    protected static string $sumberSlug = 'nama';

    protected $fillable = ['nama', 'slug', 'jabatan', 'kelompok', 'nip', 'motto', 'profil', 'foto', 'media_sosial', 'tautan', 'urutan', 'tampil'];

    protected array $kolomBerkas = ['foto'];

    public const KELOMPOK = [
        'pimpinan' => 'Pimpinan',
        'guru' => 'Guru',
        'tendik' => 'Tenaga kependidikan',
    ];

    /** Jenis media sosial yang bisa dipilih admin; dipakai sebagai label tautan. */
    public const MEDIA_SOSIAL = [
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'x' => 'X',
        'linkedin' => 'LinkedIn',
        'lainnya' => 'Lainnya',
    ];

    protected function casts(): array
    {
        return [
            'media_sosial' => 'array',
            'tautan' => 'array',
            'tampil' => 'boolean',
        ];
    }

    public function scopeTampil(Builder $query): void
    {
        $query->where('tampil', true)->orderBy('urutan')->orderBy('nama');
    }

    public function url(): string
    {
        return route('guru.detail', $this->slug);
    }

    public function urlFoto(): ?string
    {
        return $this->urlBerkas('foto');
    }

    /** Huruf awal nama sebagai pengganti foto, misalnya "SR" untuk Siti Rahmah, S.Pd. */
    public function inisial(): string
    {
        return Teks::inisial($this->nama);
    }

    /** @return array<int, array{label: string, url: string}> */
    public function daftarMediaSosial(): array
    {
        return collect($this->media_sosial ?? [])
            ->filter(fn ($item) => filled($item['url'] ?? null))
            ->map(fn ($item) => ['label' => self::MEDIA_SOSIAL[$item['jenis'] ?? 'lainnya'] ?? 'Tautan', 'url' => $item['url']])
            ->values()->all();
    }

    /** Tombol tautan luar yang diatur admin, misalnya blog atau karya guru. */
    public function daftarTautan(): array
    {
        return collect($this->tautan ?? [])
            ->filter(fn ($item) => filled($item['url'] ?? null) && filled($item['label'] ?? null))
            ->values()->all();
    }
}
