<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesanResource\Pages\ManagePesan;
use App\Models\Pesan;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PesanResource extends Resource
{
    protected static ?string $model = Pesan::class;

    protected static ?string $slug = 'pesan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'pesan';

    protected static ?string $pluralModelLabel = 'Pesan Masuk';

    protected static ?int $navigationSort = 6;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return ($jumlah = Pesan::baru()->count()) ? (string) $jumlah : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pesan baru yang belum dibaca';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(2)->schema([
                TextEntry::make('nama')->placeholder('Tidak menyebutkan nama'),
                TextEntry::make('kontak')->placeholder('Tidak meninggalkan kontak'),
                TextEntry::make('kategori')->formatStateUsing(fn ($state) => Pesan::KATEGORI[$state] ?? $state),
                TextEntry::make('created_at')->label('Dikirim')->dateTime('l, d F Y H.i'),
                TextEntry::make('isi')->label('Isi pesan')->columnSpanFull(),
            ]),
            Radio::make('status')->options(Pesan::STATUS)->required()->inline(),
            Textarea::make('catatan')->label('Catatan tindak lanjut')->rows(3)->maxLength(1000)
                ->helperText('Hanya terlihat oleh admin. Misalnya: sudah dibalas lewat WhatsApp sekolah.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Masuk')->since()->sortable()
                    ->tooltip(fn (Pesan $record) => $record->created_at->format('d/m/Y H.i')),
                TextColumn::make('isi')->label('Pesan')->limit(90)->wrap()->weight(fn (Pesan $record) => $record->status === 'baru' ? 'bold' : null)
                    ->description(fn (Pesan $record) => trim(($record->nama ?: 'Tanpa nama').($record->kontak ? ' · '.$record->kontak : ''))),
                TextColumn::make('kategori')->badge()->color('gray')->formatStateUsing(fn ($state) => Pesan::KATEGORI[$state] ?? $state),
                TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => Pesan::STATUS[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'baru' => 'warning',
                        'diproses' => 'info',
                        default => 'success',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->options(Pesan::STATUS),
                SelectFilter::make('kategori')->options(Pesan::KATEGORI),
            ])
            ->recordActions([
                EditAction::make()->label('Buka')->icon(Heroicon::OutlinedEnvelopeOpen)->modalHeading('Pesan masuk')
                    ->mutateRecordDataUsing(fn (array $data) => [...$data, 'status' => $data['status'] === 'baru' ? 'diproses' : $data['status']]),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada pesan masuk')
            ->emptyStateDescription('Pesan dari formulir di halaman Kontak akan muncul di sini.');
    }

    public static function getPages(): array
    {
        return ['index' => ManagePesan::route('/')];
    }
}
