<?php

namespace App\Filament\Support;

use App\Services\SimpanFoto;
use App\Support\Tautan;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/** Isian yang dipakai berulang di panel admin, agar aturannya seragam. */
class Isian
{
    /** Unggah satu foto: dikecilkan dan data lokasi (EXIF) dibuang sebelum disimpan. */
    public static function foto(string $nama, string $folder, int $sisiTerpanjang = SimpanFoto::SISI_BAWAAN): FileUpload
    {
        return FileUpload::make($nama)
            ->image()
            ->disk('public')
            ->directory($folder)
            ->maxSize(SimpanFoto::MAKS_KB)
            ->imageEditor()
            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file) => SimpanFoto::simpan($file, $folder, $sisiTerpanjang));
    }

    /**
     * Isian tujuan tautan: halaman website sendiri atau alamat luar. Dipakai di menu, tombol, dan kaki halaman.
     * $awalan untuk isian pengaturan datar (misalnya "kepala_tombol_"); kosong untuk isi Repeater.
     *
     * @return array<int, Component>
     */
    public static function tautan(string $awalan = '', bool $denganOtomatis = false, bool $wajib = true): array
    {
        return [
            Radio::make($awalan.'jenis')->label('Menuju')->inline()->default('internal')->live()
                ->options(['internal' => 'Halaman website ini', 'url' => 'Alamat luar (https://...)'])
                ->afterStateUpdated(fn ($state, Set $set) => $set($awalan.'tab_baru', $state === 'url')),
            Select::make($awalan.'tujuan')->label('Halaman')->native(false)->searchable()
                ->options(fn () => Tautan::pilihan($denganOtomatis))
                ->visible(fn (Get $get) => $get($awalan.'jenis') !== 'url')
                ->required(fn (Get $get) => $wajib && $get($awalan.'jenis') !== 'url'),
            TextInput::make($awalan.'url')->label('Alamat')->url()->maxLength(255)->placeholder('https://')
                ->visible(fn (Get $get) => $get($awalan.'jenis') === 'url')
                ->required(fn (Get $get) => $wajib && $get($awalan.'jenis') === 'url'),
            Toggle::make($awalan.'tab_baru')->label('Buka di tab baru')->inline(false),
        ];
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
