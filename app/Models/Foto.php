<?php

namespace App\Models;

use App\Models\Concerns\HapusBerkas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Foto extends Model
{
    use HapusBerkas;

    protected $table = 'foto';

    protected $fillable = ['album_id', 'path', 'keterangan', 'urutan'];

    protected array $kolomBerkas = ['path'];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function url(): ?string
    {
        return $this->urlBerkas('path');
    }
}
