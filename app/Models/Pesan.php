<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Pesan dari formulir Kontak. Hanya dibaca admin, tidak pernah tampil di halaman publik. */
class Pesan extends Model
{
    protected $table = 'pesan';

    protected $fillable = ['nama', 'kontak', 'kategori', 'isi', 'status', 'catatan'];

    public const KATEGORI = [
        'pertanyaan' => 'Pertanyaan',
        'saran' => 'Saran',
        'pengaduan' => 'Pengaduan',
        'spmb' => 'Pendaftaran murid baru',
        'lainnya' => 'Lainnya',
    ];

    public const STATUS = [
        'baru' => 'Baru',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
    ];

    public function scopeBaru(Builder $query): void
    {
        $query->where('status', 'baru');
    }
}
