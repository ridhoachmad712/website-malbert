<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlbumResource\Pages;
use App\Filament\Resources\AlbumResource\RelationManagers\FotoRelationManager;
use App\Filament\Support\HapusAman;
use App\Filament\Support\Isian;
use App\Models\Album;
use App\Services\SimpanFoto;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class;

    protected static ?string $slug = 'galeri';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'album';

    protected static ?string $pluralModelLabel = 'Galeri';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(2)->schema([
                TextInput::make('judul')->required()->maxLength(200)->columnSpanFull()->placeholder('Contoh: Peringatan Hari Guru 2026'),
                DatePicker::make('tanggal')->label('Tanggal kegiatan')->native(false)->displayFormat('d/m/Y')->default(today()),
                TextInput::make('video_youtube')->label('Video YouTube (opsional)')->url()->maxLength(255)
                    ->placeholder('https://youtube.com/watch?v=...')
                    ->rule(fn () => function (string $attribute, mixed $value, \Closure $fail) {
                        if (filled($value) && (new Album(['video_youtube' => $value]))->kodeYoutube() === null) {
                            $fail('Tautan YouTube tidak dikenali. Salin alamat dari tombol Bagikan di YouTube.');
                        }
                    }),
                Textarea::make('keterangan')->rows(2)->maxLength(500)->columnSpanFull(),
            ]),
            Section::make('Tambah foto')->schema([
                FileUpload::make('foto_baru')->hiddenLabel()->image()->multiple()->maxFiles(30)
                    ->disk('local')->directory('unggah-sementara')->maxSize(SimpanFoto::MAKS_KB)->panelLayout('grid')
                    ->helperText('Bisa memilih beberapa foto sekaligus (paling banyak 30). Foto otomatis dikecilkan dan data lokasinya dibuang. Foto yang sudah tersimpan dikelola di bagian Foto di bawah.'),
            ]),
            Section::make()->schema([
                Isian::fotoDiperiksa()
                    ->accepted(fn ($get) => (bool) $get('terbit'))
                    ->validationMessages(['accepted' => 'Periksa foto terlebih dahulu sebelum menampilkan album.']),
                Toggle::make('terbit')->label('Tampilkan di website')->default(true)->live(),
            ]),
        ]);
    }

    /** Memindahkan foto unggahan baru ke album setelah dikecilkan. */
    public static function simpanFotoBaru(Album $album, array $berkas): void
    {
        $urutan = (int) $album->foto()->max('urutan');

        foreach ($berkas as $path) {
            $penuh = Storage::disk('local')->path($path);
            $album->foto()->create([
                'path' => SimpanFoto::simpan(new UploadedFile($penuh, basename($penuh), test: true), 'galeri'),
                'urutan' => ++$urutan,
            ]);
            Storage::disk('local')->delete($path);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->withCount('foto')->with('sampul'))
            ->columns([
                ImageColumn::make('sampul.path')->label('')->disk('public')->imageWidth(64)->imageHeight(44),
                TextColumn::make('judul')->searchable()->weight('bold')->wrap(),
                TextColumn::make('tanggal')->date('d/m/Y')->sortable(),
                TextColumn::make('foto_count')->label('Foto')->suffix(' foto'),
                ToggleColumn::make('terbit')->label('Tampil')->disabled(fn (Album $record) => ! $record->foto_diperiksa)
                    ->tooltip(fn (Album $record) => $record->foto_diperiksa ? null : 'Buka album dan centang pemeriksaan foto terlebih dahulu.'),
            ])
            ->recordActions([
                Action::make('lihat')->icon(Heroicon::OutlinedEye)->color('gray')
                    ->url(fn (Album $record) => $record->url(), shouldOpenInNewTab: true),
                EditAction::make(),
                HapusAman::aksi(),
            ])
            ->emptyStateHeading('Belum ada album')
            ->emptyStateDescription('Buat album untuk setiap kegiatan, lalu unggah fotonya sekaligus melalui tombol Buat album.');
    }

    public static function getRelations(): array
    {
        return [FotoRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlbum::route('/'),
            'create' => Pages\CreateAlbum::route('/buat'),
            'edit' => Pages\EditAlbum::route('/{record}/ubah'),
        ];
    }
}
