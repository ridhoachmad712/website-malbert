<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatatanAktivitasResource\Pages\ManageCatatanAktivitas;
use App\Models\CatatanAktivitas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Riwayat perubahan konten, hanya untuk dibaca. */
class CatatanAktivitasResource extends Resource
{
    protected static ?string $model = CatatanAktivitas::class;

    protected static ?string $slug = 'catatan-aktivitas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $modelLabel = 'catatan';

    protected static ?string $pluralModelLabel = 'Catatan Aktivitas';

    protected static ?int $navigationSort = 6;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with('user'))
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime('d/m/Y H.i')->sortable(),
                TextColumn::make('user.name')->label('Admin')->placeholder('Sistem'),
                TextColumn::make('aksi')->badge()->color(fn ($state) => match ($state) {
                    'menghapus', 'menurunkan' => 'danger',
                    'menerbitkan' => 'success',
                    default => 'gray',
                }),
                TextColumn::make('jenis'),
                TextColumn::make('judul')->wrap()->searchable(),
            ])
            ->filters([
                SelectFilter::make('jenis')->options(array_combine(CatatanAktivitas::JENIS, CatatanAktivitas::JENIS)),
            ])
            ->emptyStateHeading('Belum ada aktivitas')
            ->emptyStateDescription('Setiap kali admin menambah, mengubah, atau menghapus konten, catatannya muncul di sini.');
    }

    public static function getPages(): array
    {
        return ['index' => ManageCatatanAktivitas::route('/')];
    }
}
