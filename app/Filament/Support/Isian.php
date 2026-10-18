<?php

namespace App\Filament\Support;

use App\Services\SimpanFoto;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/** Isian yang dipakai berulang di panel admin, agar aturannya seragam. */
class Isian
{
    /** Unggah satu foto: dikecilkan dan data lokasi (EXIF) dibuang sebelum disimpan. */
    public static function foto(string $nama, string $folder): FileUpload
    {
        return FileUpload::make($nama)
            ->image()
            ->disk('public')
            ->directory($folder)
            ->maxSize(SimpanFoto::MAKS_KB)
            ->imageEditor()
            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file) => SimpanFoto::simpan($file, $folder));
    }

    /** Editor teks tanpa unggah gambar di dalam isi, agar semua foto melewati pemeriksaan privasi. */
    public static function teks(string $nama): RichEditor
    {
        return RichEditor::make($nama)
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'link'],
                ['h2', 'h3', 'blockquote', 'bulletList', 'orderedList'],
                ['undo', 'redo'],
            ]);
    }

    /** Centang yang wajib dilakukan admin sendiri sebelum foto tampil di website. */
    public static function fotoDiperiksa(): Checkbox
    {
        return Checkbox::make('foto_diperiksa')
            ->label('Saya sudah memeriksa foto: murid yang tampil sudah mendapat izin orang tua dan tidak ada data pribadi yang terlihat')
            ->helperText('Wajib dicentang bila ada foto yang menampilkan murid.');
    }
}
