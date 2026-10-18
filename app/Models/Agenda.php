<?php

namespace App\Models;

use App\Casts\Tanggal;
use App\Models\Concerns\CatatAktivitas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use CatatAktivitas;

    protected $table = 'agenda';

    protected $fillable = ['judul', 'mulai', 'selesai', 'waktu', 'tempat', 'keterangan', 'libur'];

    protected function casts(): array
    {
        return [
            'mulai' => Tanggal::class,
            'selesai' => Tanggal::class,
            'libur' => 'boolean',
        ];
    }

    /** Agenda yang belum berakhir, termasuk yang sedang berlangsung hari ini. */
    public function scopeMendatang(Builder $query): void
    {
        $hariIni = today()->toDateString();
        $query->where(fn ($query) => $query->where('mulai', '>=', $hariIni)->orWhere('selesai', '>=', $hariIni))
            ->orderBy('mulai');
    }

    public function scopeLewat(Builder $query): void
    {
        $hariIni = today()->toDateString();
        $query->where('mulai', '<', $hariIni)
            ->where(fn ($query) => $query->whereNull('selesai')->orWhere('selesai', '<', $hariIni))
            ->orderByDesc('mulai');
    }

    /** Contoh: "Senin, 20 Oktober 2026" atau "20–24 Oktober 2026". */
    public function tanggalTeks(): string
    {
        if (! $this->selesai || $this->selesai->equalTo($this->mulai)) {
            return $this->mulai->translatedFormat('l, j F Y');
        }

        if ($this->mulai->isSameMonth($this->selesai)) {
            return $this->mulai->format('j').'–'.$this->selesai->translatedFormat('j F Y');
        }

        return $this->mulai->translatedFormat('j F').' – '.$this->selesai->translatedFormat('j F Y');
    }
}
