<?php

namespace App\Filament\Resources\AlbumResource\RelationManagers;

use App\Models\Foto;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Attributes\On;

class FotoRelationManager extends RelationManager
{
    protected static string $relationship = 'foto';

    protected static ?string $title = 'Foto';

    protected static ?string $modelLabel = 'foto';

    #[On('refresh-foto')]
    public function segarkan(): void
    {
        $this->resetTable();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('keterangan')->maxLength(200)->columnSpanFull()
                ->helperText('Sebutkan kegiatannya. Jangan menuliskan nama murid pada keterangan foto.'),
        ]);
    }

    /** Apakah foto ini yang tampil sebagai sampul album di website. */
    protected function sampul(Foto $record): bool
    {
        return $this->getOwnerRecord()->sampul?->is($record) ?? false;
    }

    /** Mengganti sampul album; null berarti kembali memakai foto pertama. */
    protected function aturSampul(?int $fotoId, string $pesan): void
    {
        $album = $this->getOwnerRecord();
        $album->update(['sampul_foto_id' => $fotoId]);
        $album->unsetRelation('fotoSampul');

        Notification::make()->success()->title($pesan)->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('urutan')
            ->defaultSort('urutan')
            ->contentGrid(['sm' => 2, 'lg' => 4])
            ->description('Foto bertanda "Sampul album" tampil di halaman Galeri dan Beranda. Pilih foto lain dengan tombol Jadikan sampul. Bila belum dipilih, foto pertama yang menjadi sampul.')
            // Kolom dibungkus Stack agar tampil sebagai kartu; tanpa ini tombol baris tidak tergambar di mode kisi
            ->columns([
                Stack::make([
                    ImageColumn::make('path')->label('')->disk('public')->imageHeight(150)
                        ->extraImgAttributes(['class' => 'w-full object-cover rounded-lg']),
                    TextColumn::make('penanda_sampul')->label('')->badge()->color('success')
                        ->state(fn (Foto $record) => $this->sampul($record) ? 'Sampul album' : null),
                    TextColumn::make('keterangan')->placeholder('Tanpa keterangan')->wrap()->color('gray'),
                ])->space(2),
            ])
            ->headerActions([
                Action::make('sampulOtomatis')->label('Pakai foto pertama sebagai sampul')->icon(Heroicon::OutlinedArrowUturnLeft)->color('gray')
                    ->visible(fn () => filled($this->getOwnerRecord()->sampul_foto_id))
                    ->action(fn () => $this->aturSampul(null, 'Sampul kembali memakai foto pertama')),
            ])
            ->recordActions([
                Action::make('jadikanSampul')->label('Jadikan sampul')->icon(Heroicon::OutlinedStar)->color('gray')
                    ->visible(fn (Foto $record) => ! $this->sampul($record))
                    ->action(fn (Foto $record) => $this->aturSampul($record->id, 'Sampul album diganti')),
                EditAction::make()->label('Keterangan'),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->emptyStateHeading('Album ini belum berisi foto')
            ->emptyStateDescription('Unggah foto di bagian Tambah foto di atas, lalu tekan Simpan.');
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
