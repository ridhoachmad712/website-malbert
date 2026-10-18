<?php

namespace App\Filament\Pages;

use App\Filament\Support\HalamanPengaturan;
use App\Filament\Support\Isian;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Halaman program unggulan TEBAK AKSI. Website hanya menautkan ke aplikasinya, tanpa mengambil data apa pun. */
class ProgramTebakAksi extends HalamanPengaturan
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Program & SPMB';

    protected static ?string $navigationLabel = 'Program TEBAK AKSI';

    protected static ?string $title = 'Program Unggulan TEBAK AKSI';

    protected static ?string $slug = 'program-tebak-aksi';

    protected static ?int $navigationSort = 1;

    protected static array $kunci = ['tebakaksi_kepanjangan', 'tebakaksi_ringkasan', 'tebakaksi_poin', 'tebakaksi_isi', 'tebakaksi_url', 'tebakaksi_gambar'];

    protected static array $daftar = ['tebakaksi_poin'];

    protected static array $berkas = ['tebakaksi_gambar'];

    protected static string $pesanTersimpan = 'Halaman TEBAK AKSI disimpan';

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Section::make('Pengenalan')->schema([
                TextInput::make('tebakaksi_kepanjangan')->label('Kepanjangan nama program')->required()->maxLength(120),
                Textarea::make('tebakaksi_ringkasan')->label('Ringkasan')->required()->rows(3)->maxLength(400)
                    ->helperText('Tampil di Beranda dan bagian atas halaman program.'),
                Isian::foto('tebakaksi_gambar', 'pengaturan')->label('Foto kegiatan')
                    ->helperText('Misalnya suasana Teras Baca. Hindari foto murid dari dekat.'),
            ]),
            Section::make('Cara kerja program')->schema([
                Repeater::make('tebakaksi_poin')->hiddenLabel()->schema([
                    TextInput::make('judul')->required()->maxLength(50),
                    Textarea::make('keterangan')->required()->rows(2)->maxLength(250),
                ])->columns(2)->maxItems(6)->reorderable()->addActionLabel('Tambah langkah')
                    ->itemLabel(fn (array $state) => $state['judul'] ?? null)->collapsible()
                    ->helperText('Tampil berurutan dengan nomor. Paling banyak 6 langkah.'),
            ]),
            Section::make('Penjelasan lengkap')->schema([
                Isian::teks('tebakaksi_isi')->hiddenLabel()
                    ->helperText('Latar belakang, tujuan, dan hasil program. Boleh dikosongkan.'),
            ]),
            Section::make('Tautan ke aplikasi')->schema([
                TextInput::make('tebakaksi_url')->label('Alamat aplikasi TEBAK AKSI')->url()->required()->maxLength(255),
            ]),
        ]);
    }

    protected function aksiTambahan(): array
    {
        return [Action::make('lihat')->label('Lihat halaman program')->color('gray')->url(route('tebakaksi'), shouldOpenInNewTab: true)];
    }
}
