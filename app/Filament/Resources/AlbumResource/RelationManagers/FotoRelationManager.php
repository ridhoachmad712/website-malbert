<?php

namespace App\Filament\Resources\AlbumResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
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

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('urutan')
            ->defaultSort('urutan')
            ->contentGrid(['sm' => 2, 'lg' => 4])
            ->columns([
                ImageColumn::make('path')->label('')->disk('public')->imageHeight(140)->extraImgAttributes(['class' => 'w-full object-cover rounded-lg']),
                TextColumn::make('keterangan')->placeholder('Tanpa keterangan')->wrap()->color('gray'),
            ])
            ->recordActions([
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
