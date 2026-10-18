<?php

namespace App\Models;

use App\Models\Concerns\HapusBerkas;
use App\Support\Teks;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Alumni yang ditampilkan di halaman Alumni. Data dari formulir publik masuk dengan status "menunggu"
 * dan baru tampil setelah admin memeriksanya. Kontak alumni tidak pernah ditampilkan.
 */
class Alumni extends Model
{
    use HapusBerkas;

    protected $table = 'alumni';

    protected $fillable = ['nama', 'tahun_lulus', 'kegiatan', 'cerita', 'kontak', 'foto', 'sumber', 'status', 'setuju_tampil', 'urutan'];

    protected array $kolomBerkas = ['foto'];

    public const STATUS = [
        'menunggu' => 'Menunggu diperiksa',
        'tampil' => 'Ditampilkan',
        'disembunyikan' => 'Disembunyikan',
    ];

    public const SUMBER = [
        'admin' => 'Ditulis admin',
        'formulir' => 'Dari formulir alumni',
    ];

    protected function casts(): array
    {
        return [
            'tahun_lulus' => 'integer',
            'setuju_tampil' => 'boolean',
        ];
    }

    public function scopeTampil(Builder $query): void
    {
        $query->where('status', 'tampil')->orderBy('urutan')->orderByDesc('tahun_lulus')->orderBy('nama');
    }

    public function scopeMenunggu(Builder $query): void
    {
        $query->where('status', 'menunggu');
    }

    public function inisial(): string
    {
        return Teks::inisial($this->nama);
    }
}
