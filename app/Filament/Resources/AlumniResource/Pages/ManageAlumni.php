<?php

namespace App\Filament\Resources\AlumniResource\Pages;

use App\Filament\Resources\AlumniResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAlumni extends ManageRecords
{
    protected static string $resource = AlumniResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Tambah alumni')->mutateDataUsing(fn (array $data) => [...$data, 'sumber' => 'admin', 'setuju_tampil' => true])];
    }
}
