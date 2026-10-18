<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DokumenAnggaranResource\Pages\ManageDokumenAnggaran;
use App\Models\DokumenAnggaran;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

/** Dokumen transparansi anggaran sekolah (RKAS dan laporan realisasi BOSP) untuk halaman Transparansi. */
class DokumenAnggaranResource extends Resource
{
    protected static ?string $model = DokumenAnggaran::class;

    protected static ?string $slug = 'transparansi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Program & Layanan';

    protected static ?string $modelLabel = 'dokumen anggaran';

    protected static ?string $pluralModelLabel = 'Transparansi Anggaran';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('tahun')->label('Tahun anggaran')->numeric()->required()->minValue(2000)->maxValue(2100)->default(now()->year),
            Select::make('sumber_dana')->label('Sumber dana')->options(DokumenAnggaran::SUMBER_DANA)->default('bosp_reguler')->required()->native(false),
            Select::make('jenis')->label('Jenis dokumen')->options(DokumenAnggaran::JENIS)->required()->native(false)->columnSpanFull(),
            TextInput::make('judul')->required()->maxLength(200)->columnSpanFull()
                ->placeholder('Contoh: Laporan Realisasi Penggunaan Dana BOSP Reguler Tahap 1 Tahun 2026'),
            TextInput::make('periode')->maxLength(60)->placeholder('Contoh: Januari–Juni 2026'),
            DatePicker::make('tanggal')->label('Tanggal dokumen')->native(false)->displayFormat('d/m/Y'),
            Section::make('Ringkasan angka (opsional)')
                ->description('Bila diisi, tampil di halaman Transparansi agar masyarakat bisa membaca ringkasannya tanpa membuka berkas. Angka harus sama dengan dokumen.')
                ->columns(2)->columnSpanFull()->schema([
                    TextInput::make('penerimaan')->label('Total penerimaan (Rp)')->numeric()->minValue(0)->prefix('Rp'),
                    TextInput::make('belanja')->label('Total penggunaan/belanja (Rp)')->numeric()->minValue(0)->prefix('Rp'),
                ]),
            Textarea::make('keterangan')->rows(2)->maxLength(500)->columnSpanFull(),
            FileUpload::make('berkas')->label('Berkas dokumen')->required()->disk('public')->directory('anggaran')
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])->maxSize(10240)
                ->storeFileNamesIn('nama_berkas')->columnSpanFull()
                ->helperText('PDF hasil pindaian dokumen yang sudah ditandatangani, atau foto papan informasi. Paling besar 10 MB. Pastikan tidak ada data pribadi murid atau nomor rekening pribadi di dalamnya.'),
            Toggle::make('terbit')->label('Tampilkan di website')->default(true),
        ]);
    }

    public static function lengkapiData(array $data): array
    {
        if (filled($data['berkas'] ?? null) && Storage::disk('public')->exists($data['berkas'])) {
            $data['ukuran'] = Storage::disk('public')->size($data['berkas']);
        }

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tahun', 'desc')
            ->defaultGroup('tahun')
            ->columns([
                TextColumn::make('judul')->searchable()->weight('bold')->wrap()
                    ->description(fn (DokumenAnggaran $record) => $record->namaJenis().' · '.$record->namaSumber()),
                TextColumn::make('periode')->placeholder('-'),
                TextColumn::make('belanja')->label('Penggunaan')->formatStateUsing(fn ($state) => DokumenAnggaran::rupiah($state))->placeholder('-'),
                ToggleColumn::make('terbit')->label('Tampil'),
            ])
            ->filters([
                SelectFilter::make('tahun')->options(fn () => DokumenAnggaran::query()->distinct()->orderByDesc('tahun')->pluck('tahun', 'tahun')->all()),
                SelectFilter::make('jenis')->options(DokumenAnggaran::JENIS),
            ])
            ->recordActions([
                Action::make('buka')->icon(Heroicon::OutlinedEye)->color('gray')->url(fn (DokumenAnggaran $record) => $record->url(), shouldOpenInNewTab: true),
                EditAction::make()->mutateDataUsing(fn (array $data) => self::lengkapiData($data)),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada dokumen anggaran')
            ->emptyStateDescription('Unggah RKAS dan laporan realisasi penggunaan dana BOSP setiap tahap melalui tombol Tambah dokumen.');
    }

    public static function getPages(): array
    {
        return ['index' => ManageDokumenAnggaran::route('/')];
    }
}
