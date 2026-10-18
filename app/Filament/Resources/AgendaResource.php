<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendaResource\Pages\ManageAgenda;
use App\Models\Agenda;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AgendaResource extends Resource
{
    protected static ?string $model = Agenda::class;

    protected static ?string $slug = 'agenda';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'agenda';

    protected static ?string $pluralModelLabel = 'Agenda';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('judul')->required()->maxLength(200)->columnSpanFull()->placeholder('Contoh: Asesmen Sumatif Akhir Semester'),
            DatePicker::make('mulai')->label('Tanggal mulai')->required()->native(false)->displayFormat('d/m/Y')->live(),
            DatePicker::make('selesai')->label('Tanggal selesai')->native(false)->displayFormat('d/m/Y')
                ->afterOrEqual('mulai')->helperText('Kosongkan bila hanya satu hari.')
                ->minDate(fn (Get $get) => $get('mulai')),
            TextInput::make('waktu')->maxLength(60)->placeholder('Contoh: 07.30–10.00 WITA'),
            TextInput::make('tempat')->maxLength(150),
            Textarea::make('keterangan')->rows(2)->maxLength(500)->columnSpanFull(),
            Toggle::make('libur')->label('Hari libur atau sekolah tidak ada kegiatan belajar')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('mulai', 'desc')
            ->columns([
                TextColumn::make('mulai')->label('Tanggal')->sortable()
                    ->formatStateUsing(fn (Agenda $record) => $record->tanggalTeks()),
                TextColumn::make('judul')->searchable()->weight('bold')->wrap()->description(fn (Agenda $record) => $record->tempat),
                TextColumn::make('waktu')->placeholder('-'),
                IconColumn::make('libur')->boolean()->trueIcon(Heroicon::OutlinedSun)->falseIcon(null),
            ])
            ->filters([
                TernaryFilter::make('mendatang')->label('Waktu')->placeholder('Semua')
                    ->trueLabel('Yang akan datang')->falseLabel('Sudah lewat')
                    ->queries(
                        true: fn (Builder $query) => $query->mendatang(),
                        false: fn (Builder $query) => $query->lewat(),
                    ),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->emptyStateHeading('Belum ada agenda')
            ->emptyStateDescription('Tambahkan kegiatan dan hari libur dari kalender pendidikan melalui tombol Tambah agenda.');
    }

    public static function getPages(): array
    {
        return ['index' => ManageAgenda::route('/')];
    }
}
