<?php

namespace App\Models;

use App\Casts\Tanggal;
use App\Models\Concerns\CatatAktivitas;
use App\Models\Concerns\PunyaSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Album extends Model
{
    use CatatAktivitas, PunyaSlug;

    protected $table = 'album';

    protected $fillable = ['judul', 'slug', 'tanggal', 'keterangan', 'video_youtube', 'sampul_foto_id', 'terbit', 'foto_diperiksa'];

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

    public function foto(): HasMany
    {
        return $this->hasMany(Foto::class)->orderBy('urutan')->orderBy('id');
    }

    /** Foto sampul yang dipilih admin. */
    public function fotoSampul(): BelongsTo
    {
        return $this->belongsTo(Foto::class, 'sampul_foto_id');
    }

    public function fotoPertama(): HasOne
    {
        return $this->hasOne(Foto::class)->orderBy('urutan')->orderBy('id');
    }

    /** Sampul album di website: pilihan admin, atau foto pertama bila admin belum memilih. */
    protected function sampul(): Attribute
    {
        return Attribute::get(fn () => $this->fotoSampul?->album_id === $this->id ? $this->fotoSampul : $this->fotoPertama);
    }

    /** Memuat sampul sekaligus untuk banyak album, agar tidak ada kueri berulang. */
    public function scopeDenganSampul(Builder $query): void
    {
        $query->with(['fotoSampul', 'fotoPertama']);
    }

    /** Kode video dari tautan YouTube biasa, youtu.be, atau shorts; null bila tautan tidak dikenali. */
    public function kodeYoutube(): ?string
    {
        if (blank($this->video_youtube)) {
            return null;
        }

        preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/))([A-Za-z0-9_-]{11})~', $this->video_youtube, $cocok);

        return $cocok[1] ?? null;
    }

    public function url(): string
    {
        return route('galeri.detail', $this->slug);
    }
}
