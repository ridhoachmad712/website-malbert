<?php

namespace App\Models;

use App\Models\Concerns\CatatAktivitas;
use App\Models\Concerns\HapusBerkas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class Unduhan extends Model
{
    use CatatAktivitas, HapusBerkas;

    protected $table = 'unduhan';

    protected $fillable = ['judul', 'kategori', 'keterangan', 'berkas', 'nama_berkas', 'ukuran', 'urutan', 'terbit'];

    protected array $kolomBerkas = ['berkas'];

    protected function casts(): array
    {
        return ['terbit' => 'boolean'];
    }

    public function scopeTerbit(Builder $query): void
    {
        $query->where('terbit', true);
    }

    public function url(): ?string
    {
        return $this->urlBerkas('berkas');
    }

    public function jenisBerkas(): string
    {
        return strtoupper(pathinfo((string) $this->berkas, PATHINFO_EXTENSION));
    }

    public function ukuranTeks(): ?string
    {
        return $this->ukuran ? Number::fileSize($this->ukuran, precision: 1) : null;
    }
}
