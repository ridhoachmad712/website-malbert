<?php

namespace App\Filament\Resources\HalamanResource\Pages;

use App\Filament\Resources\HalamanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHalaman extends ListRecords
{
    protected static string $resource = HalamanResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Buat halaman')];
    }
}
