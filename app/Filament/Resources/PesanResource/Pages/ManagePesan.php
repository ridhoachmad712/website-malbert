<?php

namespace App\Filament\Resources\PesanResource\Pages;

use App\Filament\Resources\PesanResource;
use Filament\Resources\Pages\ManageRecords;

class ManagePesan extends ManageRecords
{
    protected static string $resource = PesanResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
