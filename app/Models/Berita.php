<?php

namespace App\Models;

use App\Casts\Tanggal;
use App\Models\Concerns\CatatAktivitas;
use App\Models\Concerns\HapusBerkas;
use App\Models\Concerns\PunyaSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Berita extends Model
{
    use CatatAktivitas, HapusBerkas, PunyaSlug;

    protected $table = 'berita';

    protected $fillable = [
        'judul', 'slug', 'kategori', 'ringkasan', 'isi', 'gambar', 'keterangan_gambar', 'penulis',
        'status', 'terbit_pada', 'disematkan', 'sematkan_sampai', 'foto_diperiksa', 'user_id',
    ];

    protected array $kolomBerkas = ['gambar'];

    public const KATEGORI = [
        'kegiatan' => 'Kegiatan',
        'pengumuman' => 'Pengumuman',
        'artikel' => 'Artikel',
    ];

    public const STATUS = [
        'draf' => 'Draf',
        'terbit' => 'Terbit',
    ];

    protected function casts(): array
    {
        return [
            'terbit_pada' => 'datetime',
            'sematkan_sampai' => Tanggal::class,
            'disematkan' => 'boolean',
            'foto_diperiksa' => 'boolean',
        ];
    }

    /** Berita yang sudah terbit dan waktu terbitnya sudah lewat (berita terjadwal belum tampil). */
    public function scopeTerbit(Builder $query): void
    {
        $query->where('status', 'terbit')->where('terbit_pada', '<=', now());
    }

    /** Pengumuman yang disematkan di Beranda dan belum melewati tanggal berakhirnya. */
    public function scopeDisematkan(Builder $query): void
    {
        $query->terbit()->where('disematkan', true)
            ->where(fn ($query) => $query->whereNull('sematkan_sampai')->orWhere('sematkan_sampai', '>=', today()->toDateString()));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function namaPenulis(): string
    {
        return $this->penulis ?: 'Admin Sekolah';
    }

    public function namaKategori(): string
    {
        return self::KATEGORI[$this->kategori] ?? $this->kategori;
    }

    /** Ringkasan untuk kartu dan pratinjau tautan; diambil dari isi bila admin tidak mengisinya. */
    public function cuplikan(int $panjang = 160): string
    {
        $teks = strip_tags(str_replace('<', ' <', (string) $this->isi));

        return $this->ringkasan ?: Str::limit(trim(preg_replace('/\s+/', ' ', html_entity_decode($teks))), $panjang);
    }

    public function url(): string
    {
        return route('berita.detail', $this->slug);
    }
}
