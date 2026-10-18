<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrestasiResource\Pages\ManagePrestasi;
use App\Filament\Support\Isian;
use App\Models\Prestasi;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PrestasiResource extends Resource
{
    protected static ?string $model = Prestasi::class;

    protected static ?string $slug = 'prestasi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'prestasi';

    protected static ?string $pluralModelLabel = 'Prestasi';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('judul')->label('Nama lomba atau penghargaan')->required()->maxLength(200)->columnSpanFull()
                ->placeholder('Contoh: Lomba Bercerita Bahasa Indonesia'),
            Select::make('jenis_peraih')->label('Diraih oleh')->options(Prestasi::JENIS_PERAIH)->default('murid')->required()->native(false)->live(),
            TextInput::make('peraih')->label('Nama peraih')->required()->maxLength(200)
                ->helperText(fn (Get $get) => $get('jenis_peraih') === 'murid'
                    ? 'Pastikan orang tua sudah setuju nama anaknya ditampilkan. Boleh ditulis dengan kelas, misalnya "Ahmad Fauzan (Kelas V)".'
                    : null),
            TextInput::make('peringkat')->maxLength(60)->placeholder('Contoh: Juara 1'),
            Select::make('tingkat')->options(Prestasi::TINGKAT)->default('kota')->required()->native(false),
            TextInput::make('penyelenggara')->maxLength(150),
            DatePicker::make('tanggal')->native(false)->displayFormat('d/m/Y'),
            Textarea::make('keterangan')->rows(2)->maxLength(500)->columnSpanFull(),
            Isian::foto('foto', 'prestasi')->label('Foto')->columnSpanFull()->live(),
            Isian::fotoDiperiksa()->columnSpanFull()
                ->accepted(fn (Get $get) => $get('terbit') && filled($get('foto')))
                ->validationMessages(['accepted' => 'Periksa foto terlebih dahulu sebelum menampilkan prestasi.']),
            Toggle::make('terbit')->label('Tampilkan di website')->default(true)->live(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                ImageColumn::make('foto')->label('')->disk('public')->imageWidth(56)->imageHeight(40),
                TextColumn::make('judul')->searchable()->weight('bold')->wrap()
                    ->description(fn (Prestasi $record) => trim($record->peringkat.' · '.$record->peraih, ' ·')),
                TextColumn::make('tingkat')->badge()->color('gray')->formatStateUsing(fn ($state) => Prestasi::TINGKAT[$state] ?? $state),
                TextColumn::make('tanggal')->date('d/m/Y')->sortable()->placeholder('-'),
                ToggleColumn::make('terbit')->label('Tampil'),
            ])
            ->filters([
                SelectFilter::make('jenis_peraih')->label('Diraih oleh')->options(Prestasi::JENIS_PERAIH),
                SelectFilter::make('tingkat')->options(Prestasi::TINGKAT),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->emptyStateHeading('Belum ada prestasi')
            ->emptyStateDescription('Catat lomba yang dimenangkan murid, guru, atau sekolah melalui tombol Tambah prestasi.');
    }

    public static function getPages(): array
    {
        return ['index' => ManagePrestasi::route('/')];
    }
}
