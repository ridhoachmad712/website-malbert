<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAlbum extends CreateRecord
{
    protected static string $resource = AlbumResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $foto = $data['foto_baru'] ?? [];
        unset($data['foto_baru']);

        $album = static::getModel()::create($data);
        AlbumResource::simpanFotoBaru($album, $foto);

        return $album;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
