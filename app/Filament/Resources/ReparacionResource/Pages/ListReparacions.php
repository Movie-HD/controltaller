<?php

namespace App\Filament\Resources\ReparacionResource\Pages;

use App\Filament\Resources\ReparacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReparacions extends ListRecords
{
    protected static string $resource = ReparacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
