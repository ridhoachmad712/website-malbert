<?php

namespace App\Filament\Pages;

use App\Filament\Support\HalamanPengaturan;
use App\Filament\Support\Isian;
use App\Support\PaletWarna;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use UnitEnum;

/**
 * Teks dan foto pembuka Beranda, bagian yang ditampilkan, serta warna website.
 * Huruf tidak bisa diubah agar tampilan tetap konsisten (DESIGN.md).
 */
class TampilanBeranda extends HalamanPengaturan
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Beranda & Warna';

    protected static ?string $title = 'Beranda dan Warna';

    protected static ?string $slug = 'tampilan-beranda';

    protected static ?int $navigationSort = 1;

    protected static array $kunci = [
        'beranda_judul', 'beranda_paragraf', 'beranda_foto', 'beranda_keterangan_foto',
        'beranda_tampil_sambutan', 'beranda_tampil_agenda', 'beranda_tampil_program', 'beranda_tampil_prestasi', 'beranda_tampil_galeri',
        'tema_warna',
    ];

    protected static array $saklar = [
        'beranda_tampil_sambutan', 'beranda_tampil_agenda', 'beranda_tampil_program', 'beranda_tampil_prestasi', 'beranda_tampil_galeri',
    ];

    protected static array $berkas = ['beranda_foto'];

    protected static string $pesanTersimpan = 'Tampilan Beranda disimpan';

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Section::make('Pembuka')->description('Bagian paling atas Beranda.')->schema([
                TextInput::make('beranda_judul')->label('Judul')->required()->maxLength(90)
                    ->helperText('Kalimat singkat, sebaiknya tidak lebih dari 10 kata.'),
                Textarea::make('beranda_paragraf')->label('Paragraf pembuka')->required()->rows(3)->maxLength(400),
                Isian::foto('beranda_foto', 'pengaturan')->label('Foto pembuka')
                    ->helperText('Foto gedung atau kegiatan sekolah. Pilih foto yang tidak menonjolkan wajah murid dari dekat.'),
                TextInput::make('beranda_keterangan_foto')->label('Keterangan foto')->maxLength(150)
                    ->helperText('Dibacakan untuk pengguna pembaca layar. Contoh: Halaman depan gedung sekolah.'),
            ]),
            Section::make('Bagian yang ditampilkan')
                ->description('Pengumuman yang disematkan dan berita terbaru selalu tampil bila ada. Bagian lain hanya muncul bila isinya sudah ada.')
                ->columns(2)->schema([
                    Toggle::make('beranda_tampil_sambutan')->label('Sambutan kepala sekolah'),
                    Toggle::make('beranda_tampil_agenda')->label('Agenda terdekat'),
                    Toggle::make('beranda_tampil_program')->label('Program TEBAK AKSI'),
                    Toggle::make('beranda_tampil_prestasi')->label('Prestasi terbaru'),
                    Toggle::make('beranda_tampil_galeri')->label('Galeri terbaru'),
                ]),
            Section::make('Warna website')->schema([
                Radio::make('tema_warna')->label('Palet warna')->required()->live()
                    ->options(PaletWarna::opsi())
                    ->descriptions(array_map(fn ($palet) => $palet['keterangan'], PaletWarna::DAFTAR))
                    ->helperText('Satu warna dipakai untuk tombol, tautan, dan menu aktif. Setiap palet sudah diuji agar tulisan tetap mudah dibaca. Sebaiknya pilih yang paling dekat dengan warna logo sekolah.'),
                Placeholder::make('pratinjau_warna')->label('Pratinjau')
                    ->content(fn (Get $get) => new HtmlString(view('filament.pratinjau-warna', ['palet' => PaletWarna::DAFTAR[$get('tema_warna')] ?? PaletWarna::DAFTAR['jingga']])->render())),
            ]),
        ]);
    }

    protected function aksiTambahan(): array
    {
        return [Action::make('lihat')->label('Lihat Beranda')->color('gray')->url(route('beranda'), shouldOpenInNewTab: true)];
    }
}
