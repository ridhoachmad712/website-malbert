<?php

namespace App\Filament\Pages;

use App\Filament\Support\HalamanPengaturan;
use App\Filament\Support\Isian;
use App\Models\Pengaturan;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Informasi Sistem Penerimaan Murid Baru. Pendaftaran berlangsung di portal Dinas Pendidikan Kota Makassar,
 * jadi website tidak menyimpan data calon murid sama sekali.
 */
class Spmb extends HalamanPengaturan
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Program & SPMB';

    protected static ?string $navigationLabel = 'SPMB';

    protected static ?string $title = 'Penerimaan Murid Baru (SPMB)';

    protected static ?string $slug = 'spmb';

    protected static ?int $navigationSort = 2;

    protected static array $kunci = [
        'spmb_tampil', 'spmb_status', 'spmb_tahun_ajaran', 'spmb_ringkasan', 'spmb_portal_url',
        'spmb_jadwal', 'spmb_jalur', 'spmb_syarat', 'spmb_alur', 'spmb_tanya', 'spmb_narahubung',
    ];

    protected static array $daftar = ['spmb_jadwal', 'spmb_jalur', 'spmb_alur', 'spmb_tanya'];

    protected static array $saklar = ['spmb_tampil'];

    protected static string $pesanTersimpan = 'Informasi SPMB disimpan';

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Section::make('Tampil di website')->columns(2)->schema([
                Toggle::make('spmb_tampil')->label('Tampilkan menu SPMB')
                    ->helperText('Matikan di luar masa penerimaan. Halamannya tetap bisa dibuka lewat tautan langsung.'),
                Radio::make('spmb_status')->label('Status pendaftaran')->options(Pengaturan::STATUS_SPMB)->required()->inline(),
                TextInput::make('spmb_tahun_ajaran')->label('Tahun ajaran')->maxLength(20)->placeholder('Contoh: 2027/2028'),
                TextInput::make('spmb_portal_url')->label('Alamat portal SPMB Dinas Pendidikan Kota Makassar')->url()->maxLength(255)
                    ->helperText('Tombol "Daftar di portal SPMB" hanya muncul bila alamat ini diisi dan status Pendaftaran dibuka.'),
                Textarea::make('spmb_ringkasan')->label('Ringkasan')->rows(3)->maxLength(500)->columnSpanFull(),
            ]),
            Section::make('Jadwal')->schema([
                Repeater::make('spmb_jadwal')->hiddenLabel()->schema([
                    TextInput::make('kegiatan')->required()->maxLength(100),
                    TextInput::make('tanggal')->required()->maxLength(60)->placeholder('Contoh: 2–6 Juni 2027'),
                ])->columns(2)->reorderable()->addActionLabel('Tambah jadwal')->defaultItems(0),
            ]),
            Section::make('Jalur pendaftaran dan kuota')->schema([
                Repeater::make('spmb_jalur')->hiddenLabel()->schema([
                    TextInput::make('nama')->label('Jalur')->required()->maxLength(60)->placeholder('Contoh: Domisili'),
                    TextInput::make('kuota')->maxLength(40)->placeholder('Contoh: 56 kursi'),
                    Textarea::make('keterangan')->rows(2)->maxLength(300)->columnSpanFull(),
                ])->columns(2)->reorderable()->addActionLabel('Tambah jalur')->defaultItems(0),
            ]),
            Section::make('Persyaratan')->schema([
                Isian::teks('spmb_syarat')->hiddenLabel()->helperText('Misalnya usia, dokumen yang disiapkan, dan ketentuan lain dari dinas.'),
            ]),
            Section::make('Langkah pendaftaran')->schema([
                Repeater::make('spmb_alur')->hiddenLabel()->schema([
                    TextInput::make('judul')->required()->maxLength(60),
                    Textarea::make('keterangan')->rows(2)->maxLength(300),
                ])->columns(2)->reorderable()->addActionLabel('Tambah langkah')->defaultItems(0),
            ]),
            Section::make('Tanya jawab')->schema([
                Repeater::make('spmb_tanya')->hiddenLabel()->schema([
                    TextInput::make('tanya')->label('Pertanyaan')->required()->maxLength(200),
                    Textarea::make('jawab')->label('Jawaban')->required()->rows(2)->maxLength(800),
                ])->reorderable()->addActionLabel('Tambah pertanyaan')->defaultItems(0)
                    ->itemLabel(fn (array $state) => $state['tanya'] ?? null)->collapsible(),
            ]),
            Section::make('Narahubung')->schema([
                Textarea::make('spmb_narahubung')->hiddenLabel()->rows(2)->maxLength(300)
                    ->helperText('Contoh: Panitia SPMB sekolah, setiap hari kerja pukul 08.00–12.00 di ruang tata usaha. Gunakan nomor resmi sekolah.'),
            ]),
        ]);
    }

    protected function aksiTambahan(): array
    {
        return [Action::make('lihat')->label('Lihat halaman SPMB')->color('gray')->url(route('spmb'), shouldOpenInNewTab: true)];
    }
}
