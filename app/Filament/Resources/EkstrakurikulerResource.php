<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EkstrakurikulerResource\Pages;
use App\Filament\Support\Isian;
use App\Models\Album;
use App\Models\Ekstrakurikuler;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class EkstrakurikulerResource extends Resource
{
    protected static ?string $model = Ekstrakurikuler::class;

    protected static ?string $slug = 'ekstrakurikuler';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static string|UnitEnum|null $navigationGroup = 'Profil Sekolah';

    protected static ?string $modelLabel = 'ekstrakurikuler';

    protected static ?string $pluralModelLabel = 'Ekstrakurikuler';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Group::make()->columnSpan(['lg' => 2])->schema([
                Section::make()->columns(2)->schema([
                    TextInput::make('nama')->required()->maxLength(120)->columnSpanFull()->placeholder('Contoh: Pramuka'),
                    Textarea::make('ringkasan')->rows(2)->maxLength(300)->columnSpanFull()
                        ->helperText('Satu atau dua kalimat untuk kartu di halaman Ekstrakurikuler.'),
                    TextInput::make('jadwal')->maxLength(120)->placeholder('Contoh: Jumat, 14.00–15.30'),
                    TextInput::make('tempat')->maxLength(120),
                    TextInput::make('pembina')->maxLength(150),
                    TextInput::make('peserta')->maxLength(120)->placeholder('Contoh: Kelas III–VI'),
                ]),
                Section::make('Penjelasan')->schema([
                    Isian::teks('deskripsi')->hiddenLabel(),
                ]),
            ]),
            Group::make()->columnSpan(['lg' => 1])->schema([
                Section::make('Foto')->schema([
                    Isian::foto('foto', 'ekskul')->hiddenLabel()->live(),
                    Isian::fotoDiperiksa()
                        ->accepted(fn (Get $get) => $get('tampil') && filled($get('foto')))
                        ->validationMessages(['accepted' => 'Periksa foto terlebih dahulu sebelum menampilkan.']),
                    Select::make('album_id')->label('Album galeri terkait')->native(false)->searchable()
                        ->options(fn () => Album::terbit()->latest('tanggal')->pluck('judul', 'id'))
                        ->helperText('Opsional. Foto album ini tampil di halaman ekstrakurikuler.'),
                ]),
                Section::make('Tampilan')->schema([
                    Toggle::make('tampil')->label('Tampilkan di website')->default(true)->live(),
                    TextInput::make('urutan')->numeric()->default(0)->minValue(0),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('urutan')
            ->reorderable('urutan')
            ->columns([
                ImageColumn::make('foto')->label('')->disk('public')->imageWidth(56)->imageHeight(40),
                TextColumn::make('nama')->searchable()->weight('bold')->description(fn (Ekstrakurikuler $record) => $record->jadwal),
                TextColumn::make('pembina')->placeholder('-'),
                ToggleColumn::make('tampil'),
            ])
            ->recordActions([
                Action::make('lihat')->icon(Heroicon::OutlinedEye)->color('gray')->url(fn (Ekstrakurikuler $record) => $record->url(), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada ekstrakurikuler')
            ->emptyStateDescription('Tambahkan kegiatan seperti Pramuka, seni tari, atau olahraga melalui tombol Tambah ekstrakurikuler.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEkstrakurikuler::route('/'),
            'create' => Pages\CreateEkstrakurikuler::route('/buat'),
            'edit' => Pages\EditEkstrakurikuler::route('/{record}/ubah'),
        ];
    }
}
