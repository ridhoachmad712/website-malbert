<?php

namespace App\Models;

use App\Models\Concerns\CatatAktivitas;
use App\Support\Foto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Sarana dan prasarana sekolah, masing-masing dengan beberapa foto. */
class Sarana extends Model
{
    use CatatAktivitas;

    protected $table = 'sarana';

    protected $fillable = ['nama', 'jumlah', 'keterangan', 'foto', 'urutan', 'tampil'];

    protected function casts(): array
    {
        return [
            'foto' => 'array',
            'tampil' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Foto yang dilepas dari daftar atau ikut terhapus bersama datanya dihapus dari penyimpanan
        static::updated(function (Sarana $sarana) {
            $lama = (array) json_decode((string) $sarana->getRawOriginal('foto'), true);
            Foto::hapus(array_diff($lama, $sarana->foto ?? []));
        });
        static::deleted(fn (Sarana $sarana) => Foto::hapus($sarana->foto ?? []));
    }

    public function scopeTampil(Builder $query): void
    {
        $query->where('tampil', true)->orderBy('urutan')->orderBy('nama');
    }

    /** @return array<int, string> */
    public function daftarFoto(): array
    {
        return array_values(array_filter($this->foto ?? []));
    }
}
