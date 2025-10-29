<?php

namespace App\Filament\Resources\Mecanicos\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Mecanicos\MecanicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMecanicos extends ListRecords
{
    protected static string $resource = MecanicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
