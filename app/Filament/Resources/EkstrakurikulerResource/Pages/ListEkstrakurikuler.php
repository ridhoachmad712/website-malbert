<?php

namespace App\Filament\Resources\EkstrakurikulerResource\Pages;

use App\Filament\Resources\EkstrakurikulerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEkstrakurikuler extends ListRecords
{
    protected static string $resource = EkstrakurikulerResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Tambah ekstrakurikuler')];
    }
}
