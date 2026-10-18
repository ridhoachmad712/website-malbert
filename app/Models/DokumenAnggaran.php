<?php

namespace App\Models;

use App\Casts\Tanggal;
use App\Models\Concerns\CatatAktivitas;
use App\Models\Concerns\HapusBerkas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

/**
 * Dokumen transparansi anggaran sekolah: RKAS dan laporan realisasi penggunaan dana BOSP
 * yang wajib dipublikasikan kepada masyarakat sesuai petunjuk teknis BOSP.
 */
class DokumenAnggaran extends Model
{
    use CatatAktivitas, HapusBerkas;

    protected $table = 'dokumen_anggaran';

    protected $fillable = [
        'tahun', 'jenis', 'sumber_dana', 'judul', 'periode', 'tanggal', 'penerimaan', 'belanja',
        'keterangan', 'berkas', 'nama_berkas', 'ukuran', 'terbit',
    ];

    protected array $kolomBerkas = ['berkas'];

    public const JENIS = [
        'rkas' => 'RKAS (Rencana Kegiatan dan Anggaran Sekolah)',
        'realisasi_tahap1' => 'Laporan realisasi tahap 1',
        'realisasi_tahap2' => 'Laporan realisasi tahap 2',
        'realisasi_tahunan' => 'Laporan realisasi tahunan',
        'pertanggungjawaban' => 'Laporan pertanggungjawaban',
        'lainnya' => 'Dokumen lainnya',
    ];

    public const SUMBER_DANA = [
        'bosp_reguler' => 'BOSP Reguler',
        'bosp_kinerja' => 'BOSP Kinerja',
        'bop_daerah' => 'BOP Daerah',
        'lainnya' => 'Sumber lain',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => Tanggal::class,
            'penerimaan' => 'integer',
            'belanja' => 'integer',
            'terbit' => 'boolean',
        ];
    }

    public function scopeTerbit(Builder $query): void
    {
        $query->where('terbit', true);
    }

    public function namaJenis(): string
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }

    public function namaSumber(): string
    {
        return self::SUMBER_DANA[$this->sumber_dana] ?? $this->sumber_dana;
    }

    /** Sisa dana; null bila penerimaan atau belanja belum diisi. */
    public function sisa(): ?int
    {
        return $this->penerimaan !== null && $this->belanja !== null ? $this->penerimaan - $this->belanja : null;
    }

    public static function rupiah(?int $nilai): ?string
    {
        return $nilai === null ? null : 'Rp'.number_format($nilai, 0, ',', '.');
    }

    public function url(): ?string
    {
        return $this->urlBerkas('berkas');
    }

    public function ukuranTeks(): ?string
    {
        return $this->ukuran ? Number::fileSize($this->ukuran, precision: 1) : null;
    }
}
