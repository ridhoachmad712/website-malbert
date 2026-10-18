<?php

namespace App\Filament\Pages;

use App\Filament\Support\HalamanPengaturan;
use App\Filament\Support\Isian;
use App\Services\SimpanFoto;
use App\Support\BagianBeranda;
use App\Support\PaletWarna;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use UnitEnum;

/**
 * Pengaturan Beranda: hero, urutan dan judul setiap bagian, ajakan penutup, dan warna website.
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
        'beranda_judul', 'beranda_paragraf', 'beranda_foto', 'beranda_keterangan_foto', 'beranda_fokus',
        'beranda_tombol1_label', 'beranda_tombol1_jenis', 'beranda_tombol1_tujuan', 'beranda_tombol1_url', 'beranda_tombol1_tab_baru',
        'beranda_tombol2_label', 'beranda_tombol2_jenis', 'beranda_tombol2_tujuan', 'beranda_tombol2_url', 'beranda_tombol2_tab_baru',
        'beranda_bagian',
        'ajakan_judul', 'ajakan_teks',
        'ajakan_tombol1_label', 'ajakan_tombol1_jenis', 'ajakan_tombol1_tujuan', 'ajakan_tombol1_url', 'ajakan_tombol1_tab_baru',
        'ajakan_tombol2_label', 'ajakan_tombol2_jenis', 'ajakan_tombol2_tujuan', 'ajakan_tombol2_url', 'ajakan_tombol2_tab_baru',
        'tema_warna',
    ];

    protected static array $daftar = ['beranda_bagian'];

    protected static array $saklar = ['beranda_tombol1_tab_baru', 'beranda_tombol2_tab_baru', 'ajakan_tombol1_tab_baru', 'ajakan_tombol2_tab_baru'];

    protected static array $berkas = ['beranda_foto'];

    protected static string $pesanTersimpan = 'Tampilan Beranda disimpan';

    /** Urutan bagian selalu lengkap, termasuk bagian baru yang belum pernah disimpan admin. */
    protected function nilaiAwal(array $nilai): array
    {
        return ['beranda_bagian' => BagianBeranda::semua()] + $nilai;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Tabs::make('beranda')->persistTabInQueryString()->tabs([
                Tab::make('Hero')->icon(Heroicon::OutlinedPhoto)->schema([
                    Section::make('Foto')->description('Foto tampil selebar layar di bagian paling atas, dengan lapisan gelap agar tulisan putih terbaca.')->schema([
                        Isian::foto('beranda_foto', 'pengaturan', SimpanFoto::SISI_HERO)->label('Foto hero')
                            ->helperText('Foto mendatar dengan lebar minimal 1920 piksel, misalnya gedung atau halaman sekolah. Karena tampil paling besar, pilih foto yang tidak menonjolkan wajah murid dari dekat.'),
                        Radio::make('beranda_fokus')->label('Bagian foto yang diutamakan')->inline()->required()
                            ->options(['top' => 'Atas', 'center' => 'Tengah', 'bottom' => 'Bawah'])
                            ->helperText('Di layar HP foto terpotong. Pilih bagian yang paling penting agar tetap terlihat.'),
                        TextInput::make('beranda_keterangan_foto')->label('Keterangan foto')->maxLength(150)
                            ->helperText('Dibacakan untuk pengguna pembaca layar. Contoh: Halaman depan gedung sekolah.'),
                    ]),
                    Section::make('Tulisan')->schema([
                        TextInput::make('beranda_judul')->label('Judul')->required()->maxLength(90)
                            ->helperText('Kalimat singkat, sebaiknya tidak lebih dari 10 kata.'),
                        Textarea::make('beranda_paragraf')->label('Paragraf pembuka')->required()->rows(3)->maxLength(400),
                    ]),
                    Section::make('Tombol')->columns(2)->schema([
                        Fieldset::make('Tombol utama')->columns(1)->schema([
                            TextInput::make('beranda_tombol1_label')->label('Tulisan tombol')->maxLength(30)->helperText('Kosongkan untuk menyembunyikan.'),
                            ...Isian::tautan('beranda_tombol1_', wajib: false),
                        ]),
                        Fieldset::make('Tombol kedua')->columns(1)->schema([
                            TextInput::make('beranda_tombol2_label')->label('Tulisan tombol')->maxLength(30)->helperText('Kosongkan untuk menyembunyikan.'),
                            ...Isian::tautan('beranda_tombol2_', wajib: false),
                        ]),
                    ]),
                ]),

                Tab::make('Bagian Beranda')->icon(Heroicon::OutlinedRectangleStack)->schema([
                    Repeater::make('beranda_bagian')->hiddenLabel()
                        ->schema([
                            Hidden::make('kunci'),
                            Hidden::make('nama'),
                            Toggle::make('tampil')->label('Tampilkan')->inline(false),
                            TextInput::make('label')->label('Label kecil di atas judul')->maxLength(40),
                            TextInput::make('judul')->label('Judul bagian')->maxLength(80),
                        ])
                        ->columns(3)->reorderable()->addable(false)->deletable(false)->collapsible()
                        ->itemLabel(fn (array $state) => ($state['nama'] ?? '').(filter_var($state['tampil'] ?? true, FILTER_VALIDATE_BOOLEAN) ? '' : ' — disembunyikan'))
                        ->helperText('Seret untuk mengubah urutan bagian di Beranda. Bagian yang isinya belum ada (misalnya belum ada prestasi) tidak tampil walaupun saklarnya menyala. Judul bagian Ajakan penutup diatur di tab Ajakan Penutup.'),
                ]),

                Tab::make('Ajakan Penutup')->icon(Heroicon::OutlinedMegaphone)->schema([
                    Section::make()->description('Pita sebelum kaki halaman yang mengajak pengunjung menghubungi atau mengenal sekolah.')->schema([
                        TextInput::make('ajakan_judul')->label('Judul')->required()->maxLength(90),
                        Textarea::make('ajakan_teks')->label('Kalimat pendukung')->rows(2)->maxLength(300),
                    ]),
                    Section::make('Tombol')->columns(2)->schema([
                        Fieldset::make('Tombol utama')->columns(1)->schema([
                            TextInput::make('ajakan_tombol1_label')->label('Tulisan tombol')->maxLength(30)->helperText('Kosongkan untuk menyembunyikan.'),
                            ...Isian::tautan('ajakan_tombol1_', wajib: false),
                        ]),
                        Fieldset::make('Tombol kedua')->columns(1)->schema([
                            TextInput::make('ajakan_tombol2_label')->label('Tulisan tombol')->maxLength(30)->helperText('Kosongkan untuk menyembunyikan.'),
                            ...Isian::tautan('ajakan_tombol2_', wajib: false),
                        ]),
                    ]),
                ]),

                Tab::make('Warna')->icon(Heroicon::OutlinedSwatch)->schema([
                    Radio::make('tema_warna')->label('Palet warna')->required()->live()
                        ->options(PaletWarna::opsi())
                        ->descriptions(array_map(fn ($palet) => $palet['keterangan'], PaletWarna::DAFTAR))
                        ->helperText('Satu warna dipakai untuk tombol, tautan, dan menu aktif. Setiap palet sudah diuji agar tulisan tetap mudah dibaca. Sebaiknya pilih yang paling dekat dengan warna logo sekolah.'),
                    Placeholder::make('pratinjau_warna')->label('Pratinjau')
                        ->content(fn (Get $get) => new HtmlString(view('filament.pratinjau-warna', ['palet' => PaletWarna::DAFTAR[$get('tema_warna')] ?? PaletWarna::DAFTAR['jingga']])->render())),
                ]),
            ]),
        ]);
    }

    protected function aksiTambahan(): array
    {
        return [Action::make('lihat')->label('Lihat Beranda')->color('gray')->url(route('beranda'), shouldOpenInNewTab: true)];
    }
}
