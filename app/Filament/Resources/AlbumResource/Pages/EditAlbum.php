<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use App\Filament\Support\HapusAman;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditAlbum extends EditRecord
{
    protected static string $resource = AlbumResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $foto = $data['foto_baru'] ?? [];
        unset($data['foto_baru']);

        $record->update($data);
        AlbumResource::simpanFotoBaru($record, $foto);

        return $record;
    }

    /** Daftar foto di bawah formulir ikut diperbarui setelah foto baru disimpan. */
    protected function afterSave(): void
    {
        $this->form->fill([...$this->form->getRawState(), 'foto_baru' => []]);
        $this->dispatch('refresh-foto');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat')->label('Lihat di website')->icon(Heroicon::OutlinedEye)->color('gray')
                ->url(fn () => $this->record->url(), shouldOpenInNewTab: true),
            HapusAman::aksi(),
        ];
    }
}
