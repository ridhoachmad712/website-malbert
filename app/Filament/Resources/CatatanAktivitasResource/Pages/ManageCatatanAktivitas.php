<?php

namespace App\Filament\Resources\CatatanAktivitasResource\Pages;

use App\Filament\Resources\CatatanAktivitasResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCatatanAktivitas extends ManageRecords
{
    protected static string $resource = CatatanAktivitasResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
