<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnduhanResource\Pages\ManageUnduhan;
use App\Models\Unduhan;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class UnduhanResource extends Resource
{
    protected static ?string $model = Unduhan::class;

    protected static ?string $slug = 'unduhan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'berkas unduhan';

    protected static ?string $pluralModelLabel = 'Unduhan';

    protected static ?int $navigationSort = 5;

    /** Jenis berkas yang boleh diunggah: dokumen kantor dan gambar. Berkas program (.exe, .php, dst.) ditolak. */
    public const JENIS_BERKAS = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg',
        'image/png',
    ];

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('judul')->required()->maxLength(200)->columnSpanFull()->placeholder('Contoh: Kalender Pendidikan 2026/2027'),
            TextInput::make('kategori')->maxLength(60)->datalist(fn () => Unduhan::query()->distinct()->pluck('kategori')->filter()->all())
                ->placeholder('Contoh: Kalender, Formulir, Brosur')
                ->helperText('Berkas dengan kategori sama dikelompokkan di halaman Unduhan.'),
            TextInput::make('urutan')->numeric()->default(0)->minValue(0),
            Textarea::make('keterangan')->rows(2)->maxLength(300)->columnSpanFull(),
            FileUpload::make('berkas')->required()->disk('public')->directory('unduhan')
                ->acceptedFileTypes(self::JENIS_BERKAS)->maxSize(10240)
                ->preserveFilenames(false)->storeFileNamesIn('nama_berkas')->columnSpanFull()
                ->helperText('PDF, Word, Excel, JPG, atau PNG. Paling besar 10 MB. Jangan mengunggah dokumen berisi data pribadi murid.'),
            Toggle::make('terbit')->label('Tampilkan di website')->default(true),
        ]);
    }

    /** Ukuran berkas dicatat agar pengunjung tahu sebelum mengunduh. */
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
            ->defaultSort('urutan')
            ->reorderable('urutan')
            ->columns([
                TextColumn::make('judul')->searchable()->weight('bold')->wrap()->description(fn (Unduhan $record) => $record->kategori),
                TextColumn::make('jenis')->state(fn (Unduhan $record) => $record->jenisBerkas())->badge()->color('gray'),
                TextColumn::make('ukuran')->state(fn (Unduhan $record) => $record->ukuranTeks())->placeholder('-'),
                ToggleColumn::make('terbit')->label('Tampil'),
            ])
            ->recordActions([
                Action::make('buka')->icon(Heroicon::OutlinedEye)->color('gray')->url(fn (Unduhan $record) => $record->url(), shouldOpenInNewTab: true),
                EditAction::make()->mutateDataUsing(fn (array $data) => self::lengkapiData($data)),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada berkas unduhan')
            ->emptyStateDescription('Unggah kalender pendidikan, brosur, atau formulir melalui tombol Tambah berkas.');
    }

    public static function getPages(): array
    {
        return ['index' => ManageUnduhan::route('/')];
    }
}
